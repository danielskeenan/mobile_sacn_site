<?php

namespace App\Release;

enum ReleasePlatform: string
{

    case Ubuntu = 'ubuntu';

    case MacOs = 'macos';

    case Windows = 'windows';

    public function title(): string
    {
        return match ($this) {
            self::Ubuntu => 'Ubuntu Linux',
            self::MacOs => 'macOS',
            self::Windows => 'Windows',
        };
    }
}
