<?php

namespace App\Release;

use Carbon\CarbonImmutable;

class ReleaseManifest implements \JsonSerializable
{
    public ?string $description;

    /**
     * @param string                      $title
     * @param string                      $version
     * @param CarbonImmutable             $published
     * @param string                      $channel
     * @param array<string, ReleaseAsset> $assets
     */
    public function __construct(
        private readonly string $title,
        private readonly string $version,
        private readonly CarbonImmutable $published,
        private readonly string $channel,
        private readonly array $assets,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            version: $data['version'],
            published: CarbonImmutable::parse($data['published']),
            channel: $data['channel'],
            assets: ReleaseAsset::fromList($data['assets']),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->getTitle(),
            'version' => $this->getVersion(),
            'published' => $this->getPublished(),
            'channel' => $this->getChannel(),
            'assets' => $this->getAssets(),
        ];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return CarbonImmutable
     */
    public function getPublished(): CarbonImmutable
    {
        return $this->published;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return array<string, ReleaseAsset>
     */
    public function getAssets(): array
    {
        return $this->assets;
    }
}
