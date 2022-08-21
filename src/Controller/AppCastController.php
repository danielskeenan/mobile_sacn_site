<?php

namespace App\Controller;

use App\Release\ReleaseInfoProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class AppCastController extends AbstractController
{

    public function __construct(
        private readonly ReleaseInfoProvider $releaseInfoProvider,
        private readonly EncoderInterface $encoder,
        private readonly string $environment,
        private readonly string $public_baseurl,
    ) {
    }

    #[Route('/appcast.xml', name: 'appcast')]
    public function appcast(Request $request): Response
    {
        $manifest = $this->releaseInfoProvider->getManifest();

        $appcast = [
            '@version' => '2.0',
            '@xmlns:sparkle' => 'http://www.andymatuschak.org/xml-namespaces/sparkle',
            '@xmlns:dc' => 'http://purl.org/dc/elements/1.1/',

            'channel' => [
                'title' => 'Mobile sACN',
                'link' => $this->public_baseurl,
                'description' => 'Most recent changes with links to updates.',
                'language' => 'en',
            ],
        ];

        if ($manifest !== null) {
            $appcast['channel']['item'] = [];
            $platformsSeen = [];
            foreach ($manifest->getAssets() as $asset) {
                if (in_array($asset->getPlatform(), $platformsSeen)) {
                    continue;
                }
                $appcast['channel']['item'][] = [
                    'title' => $manifest->getTitle(),
                    'link' => $this->public_baseurl,
                    'sparkle:channel' => $manifest->getChannel(),
                    'sparkle:os' => $asset->getPlatform()->value,
                    'sparkle:version' => $manifest->getVersion(),
                    'pubDate' => $manifest->getPublished()->toRssString(),
                    'enclosure' => [
                        '@url' => $asset->url,
                        '@sparkle:version' => $manifest->getVersion(),
                        '@sparkle:dsaSignature' => $asset->getDsa(),
                        '@length' => $asset->getSize(),
                        '@type' => $asset->contentType,
                    ],
                    'description' => $manifest->description,
                ];
                $platformsSeen[] = $asset->getPlatform();
            }
        }

        $responseBody = $this->encoder->encode($appcast, 'xml', [
            'xml_format_output' => $this->environment !== 'prod',
            'xml_root_node_name' => 'rss',
        ]);
        $response = new Response($responseBody);
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
        $response->prepare($request);

        return $response;
    }
}
