<?php

namespace EasyRoute;

/**
 * Class Route
 * @package EasyRoute
 */
class Route
{
    /**
     * Recebe as requisições post.
     *
     * @param array $post
     */
    public static function post($routes, $callback)
    {
        Bootstrap::setRoutes($routes, $callback, 'POST');
    }

    /**
     * Recebe as requisições get.
     *
     * @param array $get
     */
    public static function get($route, $callback)
    {
        Bootstrap::setRoutes($route, $callback, 'GET');
    }

    /**
     * Cria uma instancia de Bootstrap.
     *
     * @param $namespace
     */
    public static function on($namespace)
    {
        new Bootstrap($namespace);
    }
}