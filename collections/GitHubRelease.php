<?php

namespace App\Collections;

use Carbon\Carbon;

class GitHubRelease
{
    public string $title;
    public string $version;
    public ?string $commitSha = null;
    public Carbon $pubDate;
    public ReleaseChannel $channel;
    public \WeakMap $assets;
    public string $notes;

    public function toCollectionItem()
    {
        $assets = [];
        /** @var ReleasePlatform $platform */
        /** @var string $asset */
        foreach ($this->assets as $platform => $asset) {
            $assets[] = [
                'platform' => $platform->value,
                'platformTitle' => $platform->humanName(),
                'url' => $asset,
            ];
        }

        return [
            'title' => $this->title,
            'version' => $this->version,
            'commit' => $this->commitSha,
            'published' => $this->pubDate,
            'channel' => strtolower($this->channel->name),
            'assets' => $assets,
            'content' => $this->notes,
        ];
    }
}
