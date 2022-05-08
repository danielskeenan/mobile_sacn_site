<?php

use Carbon\Carbon;
use Illuminate\Support\Str;

return [
    'baseUrl' => '',
    'production' => false,
    'siteName' => 'Mobile sACN',
    'siteDescription' => 'Remote sACN testing tool',

    // Algolia DocSearch credentials
    'docsearchApiKey' => env('DOCSEARCH_KEY'),
    'docsearchIndexName' => env('DOCSEARCH_INDEX'),

    // Github credentials
    'github_username' => env('GITHUB_USERNAME'),
    'github_api_token' => env('GITHUB_API_TOKEN'),
    'github_repo' => 'mobile_sacn',

    // navigation menu
    'navigation' => require_once('navigation.php'),

    // Collections
    'collections' => [
        'releases' => [
            'extends' => '_layouts.release',
            'sort' => 'published',
            'path' => 'releases/{version}',
            'getDate' => function ($page) {
                return Carbon::createFromTimestampUTC($page->published);
            },
            'items' => function ($config) {
                $github = new \App\Collections\GitHubReleaseCollection($config);

                return $github->getItems();
            },
        ],
    ],

    // helpers
    'isActive' => function ($page, $path) {
        return Str::endsWith(trimPath($page->getPath()), trimPath($path));
    },
    'isActiveParent' => function ($page, $menuItem) {
        if (is_object($menuItem) && $menuItem->children) {
            return $menuItem->children->contains(function ($child) use ($page) {
                return trimPath($page->getPath()) == trimPath($child);
            });
        }
    },
    'url' => function ($page, $path) {
        return Str::startsWith($path, 'http') ? $path : '/'.trimPath($path);
    },
];
