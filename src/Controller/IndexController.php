<?php

namespace App\Controller;

use App\Release\ReleaseManifest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IndexController extends AbstractController
{

    public function __construct(
        private readonly string $githubRepoOwner,
        private readonly string $githubRepoName,
        private readonly HttpClientInterface $githubClient
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $manifest = $this->getReleaseManifest();

        return $this->render('index/index.html.twig', [
            'releaseManifest' => $manifest,
        ]);
    }

    /**
     * Get manifest for the latest release.
     *
     * @return ReleaseManifest|null
     */
    private function getReleaseManifest(): ?ReleaseManifest
    {
        $releaseInfo = $this->githubClient->request('GET', "https://api.github.com/repos/{$this->githubRepoOwner}/{$this->githubRepoName}/releases/latest")
            ->toArray();
        // Find manifest.
        $manifest = null;
        foreach ($releaseInfo['assets'] as $assetInfo) {
            if ($assetInfo['name'] === 'manifest.json') {
                $manifest = $this->githubClient->request('GET', $assetInfo['url'], [
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

        return $manifest;
    }
}
