<?php

namespace App\Release;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReleaseInfoProvider
{

    public function __construct(
        private readonly string $githubRepoOwner,
        private readonly string $githubRepoName,
        private readonly HttpClientInterface $githubClient
    ) {
    }

    /**
     * Get manifest for the latest release.
     *
     * @return ReleaseManifest|null
     */
    public function getManifest(): ?ReleaseManifest
    {
        // TODO: If this site is ever used *not* as part of a static site generator, this should be cached.
        $releaseInfo = $this->githubClient->request('GET', "https://api.github.com/repos/{$this->githubRepoOwner}/{$this->githubRepoName}/releases/latest")
            ->toArray();
        // Find manifest.
        $manifest = null;
        foreach ($releaseInfo['assets'] as $assetInfo) {
            if ($assetInfo['name'] === 'manifest.json') {
                $manifest = $this->githubClient->request('GET', $assetInfo['url'], [
                    'headers' => [
                        'Accept: application/octet-stream',
                    ],
                ])->toArray();
                $manifest = ReleaseManifest::fromArray($manifest);
                $manifest->description = $releaseInfo['body'];
                break;
            }
        }
        if ($manifest !== null) {
            foreach ($releaseInfo['assets'] as $assetInfo) {
                if (isset($manifest->getAssets()[$assetInfo['name']])) {
                    $asset = &$manifest->getAssets()[$assetInfo['name']];
                    $asset->url = $assetInfo['browser_download_url'];
                    $asset->contentType = $assetInfo['content_type'];
                }
            }
        }

        return $manifest;
    }
}
