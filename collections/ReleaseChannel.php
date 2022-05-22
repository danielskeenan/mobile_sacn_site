<?php

namespace App\Collections;

enum ReleaseChannel: string
{
    case Main = 'main';
    case Dev = 'dev';
}
