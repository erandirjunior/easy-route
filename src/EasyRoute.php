<?php

namespace EasyRoute;

/**
 * Class EasyRoute
 * @package EasyRoute
 */
class EasyRoute
{
    private $easyRouteConfig;

    public function __construct($namespace)
    {
        $this->easyRouteConfig = new EasyRouteConfig($namespace);
    }

    /**
     * Recebe as requisições post.
     *
     * @param array $post
     */
    public function post($route, $callback)
    {
        $this->easyRouteConfig->addRoute($route, $callback, 'POST');
    }

    /**
     * Recebe as requisições get.
     *
     * @param array $get
     */
    public function get($route, $callback)
    {
        $this->easyRouteConfig->addRoute($route, $callback, 'GET');
    }

    /**
     * Cria uma instancia de EasyRoute.
     *
     * @param $namespace
     */
    public function on()
    {
        $this->easyRouteConfig->run();
    }
}