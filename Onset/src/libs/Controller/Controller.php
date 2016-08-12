<?php
namespace App\Onset\Controller;

use Slim\Container;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Controller
{
    protected $container;
    protected $view;
    protected $logger;
    protected $config;
    protected $onset;

    public function __construct(
        Container $c
    ) {
        $this->container    = $c;
        $this->view         = $c->get('view');
        $this->logger       = $c->get('logger');
        $this->config       = $c->get('config');
        $this->onset        = new \App\Onset\Onset($c);
    }
}
