<?php

// src/Controller/HistoryController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Vnn\WpApiClient\Http\GuzzleAdapter;
use Vnn\WpApiClient\WpClient;

class HistoryController extends BaseController
{
    var $siteStructure = [
        '1555' => 'Vorgeschichte',
        '2172' => 'Bücherverbrennungen 1933',
    ];

    /**
     * @Route("/geschichte", name="history", options={"sitemap" = true})
     */
    public function indexAction(Request $request)
    {
        return $this->render('History/index.html.twig', [
            'structure' => $this->siteStructure,
        ]);
    }

    protected function extractContent($crawler)
    {
        // we don't wont the body-tag
        return preg_replace('/<\/?body>/', '',  $crawler->html());
    }

    /**
     * Adjust page and img links on $wordpressBaseUrl to internal ones
     */
    protected function adjustUrls($html, UrlGeneratorInterface $urlGenerator)
    {
        $baseUrlComponents = parse_url($this->wordpressBaseUrl);

        $crawler = new \Symfony\Component\DomCrawler\Crawler();
        $crawler->addHtmlContent($html);

        $crawler->filter('a')->each(function ($node, $i) use ($urlGenerator) {
            $href = $node->attr('href');
            if (empty($href)) {
                return;
            }

            $urlComponents = parse_url($href);

            if (!empty($urlComponents['query'])
                && preg_match('/page_id=(\d+)/', $urlComponents['query'], $matches))
            {
                $node->getNode(0)->setAttribute('href', $urlGenerator->generate('history-page', [
                    'page' => $matches[1],
                ]));
            }
        });

        $crawler->filter('img')->each(function ($node, $i) use ($urlGenerator, $baseUrlComponents) {
            $src = $node->attr('src');
            if (empty($src)) {
                return;
            }

            $srcComponents = parse_url($src);

            if ($baseUrlComponents['host'] == $srcComponents['host']) {
                $node->getNode(0)->setAttribute('src', $urlGenerator->generate('imgproxy', [
                    'path' => $srcComponents['path'],
                ]));

                if ($node->getNode(0)->hasAttribute('srcset')) {
                    $node->getNode(0)->removeAttribute('srcset');
                }
            }
        });

        return $this->extractContent($crawler);
    }

    /**
     * @Route("/geschichte/{page}", name="history-page")
     */
    public function pageAction(Request $request, $page,
                               UrlGeneratorInterface $urlGenerator)
    {
        $client = new WpClient(new GuzzleAdapter(new \GuzzleHttp\Client()),
                               $this->wordpressBaseUrl);

        $pageInfo = $client->pages()->get($page);
        $content = $this->adjustUrls($pageInfo['content']['rendered'], $urlGenerator);

        return $this->render('History/page.html.twig', [
            'title' => $pageInfo['title']['rendered'],
            'content' => $content,
        ]);
    }
}
