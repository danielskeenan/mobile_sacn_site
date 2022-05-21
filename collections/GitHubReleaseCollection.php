<?php

namespace App\Collections;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Support\Collection;

/**
 * Fetch releases from GitHub.
 */
class GitHubReleaseCollection
{
    private \Github\Client $github;

    private string $username;
    private string $repo;

    public function __construct(Collection $config)
    {
        $this->github = new \Github\Client();
        $githubToken = $config->get('github_api_token');
        $githubUsername = $config->get('github_username');
        $githubRepo = $config->get('github_repo');
        if ($githubUsername === null) {
            throw new \RuntimeException('github_username must be set.');
        }
        if ($githubRepo === null) {
            throw new \RuntimeException('github_repo must be set.');
        }
        $this->username = $githubUsername;
        $this->repo = $githubRepo;
        if ($githubToken !== null) {
            $this->github->authenticate($githubToken, \Github\AuthMethod::ACCESS_TOKEN);
        }
    }

    public function getItems(): array
    {
        return [
            ...$this->getDevRelease(),
            ...$this->getReleases(),
        ];
    }

    private function getDevRelease(): array
    {
        try {
            $runInfo = $this->github->repo()->workflowRuns()->listRuns($this->username, $this->repo, 'main.yml', [
                'branch' => 'main',
                'status' => 'success',
                'per_page' => 1,
            ]);
            $releaseInfo = $this->github->repo()->releases()->tag($this->username, $this->repo, "dev-latest");
        } catch (\Http\Client\Exception) {
            return [];
        }
        if (!self::workflowRunValid($runInfo) || !self::releaseValid($releaseInfo)) {
            return [];
        }
        $release = self::createReleaseFromGithub($releaseInfo);
        $release->commitSha = $runInfo['workflow_runs'][0]['head_sha'];

        return [$release->toCollectionItem()];
    }

    private function getReleases(): array
    {
        $releases = [];
        $paginator = new \Github\ResultPager($this->github);
        do {
            try {
                $releasesInfo = $paginator->fetch(
                    $this->github->repo()->releases(),
                    'all',
                    [$this->username, $this->repo]
                );
            } catch (\Http\Client\Exception) {
                return [];
            }
            foreach ($releasesInfo as $releaseInfo) {
                if (!self::releaseValid($releaseInfo)) {
                    return [];
                }
                if ($releaseInfo['prerelease']) {
                    continue;
                }

                $releases[] = self::createReleaseFromGithub($releaseInfo);;
            }
        } while ($paginator->hasNext());

        return $releases;
    }

    private static function workflowRunValid($workflowRuns): bool
    {
        if (!is_array($workflowRuns) ||
            empty($workflowRuns['workflow_runs'])
        ) {
            return false;
        }
        foreach ($workflowRuns['workflow_runs'] as $workflowRun) {
            if (!isset($workflowRun['head_sha'])) {
                return false;
            }
        }

        return true;
    }

    private static function releaseValid($release): bool
    {
        if (!is_array($release) ||
            !isset($release['name']) ||
            empty($release['assets']) ||
            !isset($release['body'])
        ) {
            return false;
        }
        foreach ($release['assets'] as $asset) {
            if (!isset($asset['name']) ||
                !isset($asset['state']) ||
                $asset['state'] !== 'uploaded' ||
                !isset($asset['updated_at']) ||
                !isset($asset['browser_download_url'])
            ) {
                return false;
            }
        }

        return true;
    }

    private static function createReleaseFromGithub(array $releaseInfo): GitHubRelease
    {
        $publishedDate = Carbon::createFromTimestampUTC(0);
        $version = '';
        $assets = [];
        foreach ($releaseInfo['assets'] as $assetInfo) {
            $platform = ReleasePlatform::fromFilename($assetInfo['name']);
            if ($platform !== null) {
                $asset = ReleaseAsset::createFromGitHub($assetInfo);
                if ($publishedDate->isBefore($asset->pubDate)) {
                    $publishedDate = $asset->pubDate;
                }
                if (empty($version)
                    && preg_match('`[-_](\d+\.\d+\.\d+\.\d+)[-_]`', $assetInfo['name'], $matches) == 1) {
                    $version = $matches[1];
                }
                $assets[] = $asset;
            }
        }
        $release = new GitHubRelease();
        $release->title = $releaseInfo['name'];
        $release->version = $version;
        $release->pubDate = $publishedDate;
        $release->channel = ReleaseChannel::Dev;
        $release->assets = $assets;
        $release->notes = trim($releaseInfo['body']);

        return $release;
    }
}
