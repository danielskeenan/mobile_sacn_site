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
            ...$this->getReleases(fn($releaseInfo) => !$releaseInfo['prerelease']),
        ];
    }

    /**
     * @param callable $filterFn Receives the release info from the Github API. (See
     *     https://docs.github.com/en/rest/releases/releases).
     *
     * @return array
     */
    private function getReleases(callable $filterFn): array
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
                if (!$filterFn($releaseInfo)) {
                    continue;
                }

                $releases[] = GitHubRelease::createFromGithubApi($releaseInfo)->toCollectionItem();
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
}
