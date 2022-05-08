<?php

namespace App\Collections;

enum ReleasePlatform
{
    case Windows;
    case MacOs;
    case Debian;

    public function filenameRegex(): string
    {
        return match ($this) {
            ReleasePlatform::Windows => '`^.+-Windows\.msi$`',
            ReleasePlatform::MacOs => '`^.+-Darwin\.dmg$`',
            ReleasePlatform::Debian => '`^.+-Linux\.deb$`',
        };
    }

    public function humanName(): string
    {
        return match ($this) {
            ReleasePlatform::Windows => 'Windows',
            ReleasePlatform::MacOs => 'macOS',
            ReleasePlatform::Debian => 'Linux (Debian)',
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
