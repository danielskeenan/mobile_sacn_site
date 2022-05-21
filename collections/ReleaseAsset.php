<?php

namespace App\Collections;

use Carbon\Carbon;

/**
 * A GitHub release asset.
 *
 * @see https://docs.github.com/en/rest/releases/assets
 *
 * @todo DSA signature
 */
class ReleaseAsset
{
    public string $platform;
    public string $platformTitle;
    public string $url;
    public int $size;
    public string $contentType;
    public Carbon $pubDate;

    static public function createFromGitHub(array $assetInfo): self
    {
        $asset = new ReleaseAsset();
        $platform = ReleasePlatform::fromFilename($assetInfo['name']);
        $asset->platform = $platform->value;
        $asset->platformTitle = $platform->humanName();
        $asset->url = $assetInfo['browser_download_url'];
        $asset->size = $assetInfo['size'] ?? 0;
        $asset->contentType = $assetInfo['content_type'] ?? "application/octet-stream";
        $asset->pubDate = Carbon::createFromIsoFormat(
            "YYYY-MM-DD\THH:mm:ss\Z",
            $assetInfo['updated_at'],
            "UTC"
        );

        return $asset;
    }

    public function toArray(): array
    {
        // Jigsaw won't pass collection item values that are objects, so we must convert
        // to an array.
        return [
            'platform' => $this->platform,
            'platformTitle' => $this->platformTitle,
            'url' => $this->url,
            'size' => $this->size,
            'contentType' => $this->contentType,
            'pubDate' => $this->pubDate,
        ];
    }
}
