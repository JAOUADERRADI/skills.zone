<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class MaintenanceListener
{
    private $maintenance;
    private $twig;

    public function __construct($maintenance, Environment $twig)
    {
        $this->maintenance = $maintenance;
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // Check if the maintenance file exists
        if (!file_exists($this->maintenance)) {
            return;
        }

        // If maintenance mode is enabled, render the 503 error page
        $event->setResponse(
            new Response(
                $this->twig->render('bundles/TwigBundle/Exception/error503.html.twig'), // 503 template
                Response::HTTP_SERVICE_UNAVAILABLE // HTTP 503 status code
            )
        );

        // Stop further event handling
        $event->stopPropagation();
    }
}
