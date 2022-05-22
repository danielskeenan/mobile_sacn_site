<?php

namespace App\Collections;

enum ReleasePlatform: string
{
    case Windows = 'windows';
    case MacOs = 'macos';
    case Ubuntu = 'ubuntu';

    public function humanName(): string
    {
        return match ($this) {
            ReleasePlatform::Windows => 'Windows',
            ReleasePlatform::MacOs => 'macOS',
            ReleasePlatform::Ubuntu => 'Linux (Ubuntu)',
        };
    }

    public function sortOrder(): int
    {
        return match ($this) {
            ReleasePlatform::Windows => 1,
            ReleasePlatform::MacOs => 2,
            ReleasePlatform::Ubuntu => 3,
        };
    }

    public function usesUpdater(): bool
    {
        return match ($this) {
            ReleasePlatform::Windows, ReleasePlatform::MacOs => true,
            default => false,
        };
    }
}
