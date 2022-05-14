<?php

namespace App\Collections;

enum ReleasePlatform: string
{
    case Windows = 'windows';
    case MacOs = 'macos';
    case Ubuntu = 'ubuntu';

    public function filenameRegex(): string
    {
        return match ($this) {
            ReleasePlatform::Windows => '`^.+-Windows\.msi$`',
            ReleasePlatform::MacOs => '`^.+-Darwin\.dmg$`',
            ReleasePlatform::Ubuntu => '`^.+\.deb$`',
        };
    }

    public function humanName(): string
    {
        return match ($this) {
            ReleasePlatform::Windows => 'Windows',
            ReleasePlatform::MacOs => 'macOS',
            ReleasePlatform::Ubuntu => 'Linux (Ubuntu)',
        };
    }

    public static function fromFilename(string $filename): ?self
    {
        foreach (self::cases() as $platform) {
            if (preg_match($platform->filenameRegex(), $filename) === 1) {
                return $platform;
            }
        }

        return null;
    }
}
