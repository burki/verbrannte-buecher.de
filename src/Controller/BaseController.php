<?php

// src/Controller/BaseController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected $wordpressBaseUrl = 'http://www.verbrannte-buecher.de';
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function getProjectDir()
    {
        return $this->projectDir;
    }

    public function getDataDir()
    {
        return $this->projectDir . '/data';
    }
}