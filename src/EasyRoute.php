<?php

namespace EasyRoute;

/**
 * Class EasyRoute
 * @package EasyRoute
 */
class EasyRoute
{
    private $config;

    private $routes;

    public function __construct($namespace)
    {
        $this->config = new EasyRouteConfig($namespace);
    }

    /**
     * Receive requests type post.
     *
     * @param array $post
     */
    public function post($route, $callback)
    {
        $this->routes[] = [
            'route' => $route,
            'callback' => $callback,
            'type' => 'POST'
        ];
        //$this->easyRouteConfig->addRoute($route, $callback, 'POST');
    }

    /**
     * Receive requests type get.
     *
     * @param array $get
     */
    public function get($route, $callback)
    {
        $this->routes[] = [
            'route' => $route,
            'callback' => $callback,
            'type' => 'GET'
        ];
        //$this->easyRouteConfig->addRoute($route, $callback, 'GET');
    }

    /**
     * Receive a group of types requests
     *
     * @param $route string
     * @param $callback callable
     */
    public function group($route, $callback)
    {
        $routesBeforeGroup = $this->routes;
        $callback($this);

        foreach ($this->routes as $key => $value) {
            if (empty($routesBeforeGroup[$key])) {
                $pathRoute = "{$route}/{$value['route']}";
                $pathRoute = preg_replace('/\/{2,}/', '/', $pathRoute);
                $this->routes[$key]['route'] = $pathRoute;
            }
        }

    }

    /**
     * Cria uma instancia de EasyRoute.
     *
     * @param $namespace
     */
    public function on()
    {
        $this->config->setRoutes($this->routes);
        $this->config->run();
    }
}