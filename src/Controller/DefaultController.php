<?php

// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Vnn\WpApiClient\WpClient;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="home", options={"sitemap" = true})
     */
    public function homeAction(Request $request, WpClient $wpClient)
    {
        $events = $this->buildEvents($wpClient);

        return $this->render('Default/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/ueber/projekt", name="about-project", options={"sitemap" = true})
     */
    public function aboutTeamAction(Request $request)
    {
        return $this->render('Default/about-project.html.twig');
    }

    /**
     * @Route("/ueber/initiativen", name="about-related", options={"sitemap" = true})
     */
    public function aboutRelatedAction(Request $request)
    {
        return $this->render('Default/about-related.html.twig');
    }

    /**
     * @Route("/impressum", name="imprint")
     */
    public function imprintAction(Request $request)
    {
        return $this->render('Default/imprint.html.twig');
    }
}
