<?php

namespace App\Controller;

use App\Release\ReleaseManifest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IndexController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(string $githubRepoOwner, string $githubRepoName, HttpClientInterface $githubClient): Response
    {
        // Get latest release
        $releaseInfo = $githubClient->request('GET', "https://api.github.com/repos/$githubRepoOwner/$githubRepoName/releases/latest")
            ->toArray();
        // Find manifest.
        $manifest = null;
        foreach ($releaseInfo['assets'] as $assetInfo) {
            if ($assetInfo['name'] === 'manifest.json') {
                $manifest = $githubClient->request('GET', $assetInfo['url'], [
                    'headers' => [
                        'Accept: application/octet-stream',
                    ],
                ])->toArray();
                $manifest = ReleaseManifest::fromArray($manifest);
                break;
            }
        }
        if ($manifest !== null) {
            foreach ($releaseInfo['assets'] as $assetInfo) {
                if (isset($manifest->getAssets()[$assetInfo['name']])) {
                    $manifest->getAssets()[$assetInfo['name']]->url = $assetInfo['browser_download_url'];
                }
            }
        }

        return $this->render('index/index.html.twig', [
            'releaseManifest' => $manifest,
        ]);
    }
}
