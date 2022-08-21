<?php

namespace App\Controller;

use App\Release\ReleaseInfoProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    public function __construct(
        private readonly ReleaseInfoProvider $manifestProvider,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $manifest = $this->manifestProvider->getManifest();

        return $this->render('index/index.html.twig', [
            'releaseManifest' => $manifest,
        ]);
    }
}
