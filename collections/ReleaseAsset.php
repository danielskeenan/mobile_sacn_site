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
    public ReleasePlatform $platform;
    public string $kind;
    public string $url;
    public int $size;
    public string $contentType;
    public string $dsa;

    static public function createFromGithubApi(array $assetInfo, array $manifestAssetInfo): ?self
    {
        $asset = new self();
        $platform = ReleasePlatform::tryFrom($manifestAssetInfo['platform']);
        if ($platform === null) {
            return null;
        }
        $asset->platform = $platform;
        $asset->kind = $manifestAssetInfo['kind'];
        $asset->url = $assetInfo['browser_download_url'];
        $asset->size = $assetInfo['size'] ?? 0;
        $asset->contentType = $assetInfo['content_type'] ?? "application/octet-stream";
        $asset->dsa = $manifestAssetInfo['dsa'];

        return $asset;
    }

    public function toArray(): array
    {
        // Jigsaw won't pass collection item values that are objects, so we must convert
        // to an array.
        return [
            'platform' => $this->platform->value,
            'platformTitle' => $this->platform->humanName(),
            'kind' => $this->kind,
            'url' => $this->url,
            'size' => $this->size,
            'contentType' => $this->contentType,
            'dsa' => $this->dsa,
        ];
    }
}
