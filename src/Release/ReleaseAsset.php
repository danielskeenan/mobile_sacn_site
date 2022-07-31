<?php

namespace App\Release;

class ReleaseAsset implements \JsonSerializable
{

    public ?string $url = null;

    public function __construct(
        private readonly string $filename,
        private readonly int $size,
        private readonly string $kind,
        private readonly ReleasePlatform $platform,
        private readonly string $dsa,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            filename: $data['filename'],
            size: $data['size'],
            kind: $data['kind'],
            platform: ReleasePlatform::from($data['platform']),
            dsa: $data['dsa'],
        );
    }

    /**
     * @param array $list
     *
     * @return array<string, self>
     */
    public static function fromList(array $list): array
    {
        $items = [];
        foreach ($list as $item) {
            $asset = self::fromArray($item);
            $items[$asset->getFilename()] = $asset;
        }

        return $items;
    }

    public function jsonSerialize(): array
    {
        return [
            'filename' => $this->getFilename(),
            'size' => $this->getSize(),
            'kind' => $this->getKind(),
            'platform' => $this->getPlatform()->value,
            'platformTitle' => $this->getPlatform()->title(),
            'dsa' => $this->getDsa(),
            'url' => $this->url,
        ];
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getKind(): string
    {
        return $this->kind;
    }

    /**
     * @return ReleasePlatform
     */
    public function getPlatform(): ReleasePlatform
    {
        return $this->platform;
    }

    /**
     * @return string
     */
    public function getDsa(): string
    {
        return $this->dsa;
    }
}
