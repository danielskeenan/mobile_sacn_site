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
    public array $assets;
    public string $notes;

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
