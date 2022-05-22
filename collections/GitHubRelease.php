<?php

namespace App\Collections;

use Carbon\Carbon;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;

class GitHubRelease
{
    public string $title;
    public string $version;
    public ?string $commitSha = null;
    public Carbon $pubDate;
    public ReleaseChannel $channel;
    public array $assets;
    public string $notes;

    public static function createFromGithubApi(array $releaseInfo): ?self
    {
        // Look for manifest.
        $manifest = null;
        $assetsInfo = [];
        foreach ($releaseInfo['assets'] as $assetInfo) {
            // Map api asset info by filename for use later.
            $assetsInfo[$assetInfo['name']] = $assetInfo;
            if ($assetInfo['name'] === 'manifest.json') {
                $client = new RetryableHttpClient(HttpClient::create());
                $resp = $client->request('GET', $assetInfo['browser_download_url']);
                try {
                    $manifest = json_decode($resp->getContent(), flags: JSON_OBJECT_AS_ARRAY);
                } catch (HttpClientExceptionInterface) {
                    // Do nothing.
                }
            }
        }
        if ($manifest === null) {
            return null;
        }
        $release = new self();
        $release->title = $manifest['title'];
        $release->version = $manifest['version'];
        $release->pubDate = Carbon::createFromIsoFormat(
            "YYYY-MM-DD\THH:mm:ss.SSSSSSZ",
            $manifest['published'],
            "UTC"
        );
        $channel = ReleaseChannel::tryFrom($manifest['channel']);
        if ($channel === null) {
            return null;
        }
        $release->channel = $channel;
        $release->notes = $releaseInfo['body'];
        $release->assets = [];
        foreach ($manifest['assets'] as $assetInfo) {
            if (isset($assetsInfo[$assetInfo['filename']])) {
                $asset = ReleaseAsset::createFromGithubApi($assetsInfo[$assetInfo['filename']], $assetInfo);
                if ($asset !== null) {
                    $release->assets[] = $asset;
                }
            }
        }
        usort($release->assets, function (ReleaseAsset $a, ReleaseAsset $b) {
            if ($a->platform != $b->platform) {
                return $a->platform->sortOrder() - $b->platform->sortOrder();
            }
            if ($a->kind != $b->kind) {
                return strnatcasecmp($a->kind, $b->kind);
            }

            return 0;
        });

        return $release;
    }

    public function toCollectionItem()
    {
        return [
            'title' => $this->title,
            'version' => $this->version,
            'commit' => $this->commitSha,
            'published' => $this->pubDate,
            'channel' => strtolower($this->channel->name),
            // Jigsaw will only pass this to templates if it is an array.
            'assets' => array_map(fn(ReleaseAsset $asset): array => $asset->toArray(), $this->assets),
            'content' => $this->notes,
        ];
    }
}
