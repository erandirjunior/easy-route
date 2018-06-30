<?php

namespace EasyRoute;

/**
 * Class EasyRouteConfig
 * @package Core\ServiceRoutes
 */
class EasyRouteConfig
{
    /**
     * Receive routes.
     *
     * @var array
     */
    private $routes;

    /**
     * Recebe os valores da url que são dinâmicas nas rotas.
     *
     * @var array
     */
    private $piecesUrl;

    /**
     * Receivethe number
     * Get the number of errors per url not found.
     *
     * @var int
     */
    private $urlError;

    /**
     * Receive the namespace.
     *
     * @var string
     */
    private $namespace;

    private $matches;

    /**
     * EasyRoute constructor.
     *
     * @param $namespace
     */
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Recebe os valores das rotas.
     *
     * @param $route string
     * @param $callback string
     */
    public function addRoute($route, $callback, $type)
    {
        $this->routes[] = ['route' => $route, 'callback' => $callback, 'type' => $type];
    }

    /**
     * @param string $url
     * @see isDynamicRoute
     * @see processRoutes
     * @see execute
     * @see countError
     */
    private function index(string $url)
    {
        try {
            array_walk($this->routes, function ($route) use ($url) {
                if ($this->isDynamic($route['route'])) {
                    $this->processRoutes($route, $url);
                } else {
                    $this->execute($url, $route);
                }
                $this->countError($this->routes);
            });
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Verifica se a rota atual é dinâmica ou não.
     *
     * @param $route
     *
     * @return bool
     */
    private function isDynamic($route)
    {
        preg_match_all('({.+?}/?)', $route, $this->matches);
        return count($this->matches[0]) > 0 ? true : false;
    }

    /**
     * Faz o tratamento da rota dinâmica.
     *
     * @param array $route
     * @param string $url
     *
     * @see execute
     */
    private function processRoutes($route, $url)
    {
        $route['route'] = str_replace(['{', '}'], '', $route['route']);
        $urlArray = explode('/', $url);
        $routeArray = explode('/', $route['route']);

        $this->matches = $this->cleanMatch($this->matches);
        $indice = $this->valueIndiceRoute($routeArray);

        foreach ($routeArray as $k => $v) {
            if (empty($urlArray[$k])) {
                $urlArray[$k] = '';
            }

            if ($v != $urlArray[$k] && $indice[$k] == $v) {
                $urlValues[] = $urlArray[$k];
                $this->piecesUrl[] = $urlArray[$k];
                continue;
            }

            $urlValues[] = $v;
        }

        $route['route'] = implode('/', $urlValues);

        $this->execute($url, $route);
    }

    /**
     * Limpa o matches.
     *
     * @param $matches
     *
     * @return mixed
     */
    private function cleanMatch($matches)
    {
        foreach ($matches[0] as $k => $v) {
            $matches[$k] = str_replace(['/{', '{', '}', '}/', '/'], '', $v);
        }

        return $matches;
    }

    /**
     * Retorna os valores dos indice que são dinâmicos nas rotas.
     *
     * @param $routeArray
     * @param $matches
     *
     * @return mixed
     */
    private function valueIndiceRoute($routeArray)
    {
        $matches = $this->matches;
        array_walk($routeArray, function ($k, $v) use ($matches, &$indice) {
            foreach ($matches as $j => $value) {
                if ($k == $value) {
                    $indice[$v] = $k;
                }
            }
        });

        return $indice;
    }

    /**
     * Verifica se o valor da rota é igual a url.
     * Verifica e executa se o valor passado é um callable.
     * Invoca um criador de objetos.
     *
     * @param string $url
     * @param array $route
     *
     * @return mixed
     */
    private function execute(string $url, array $route)
    {
        if ($url === $route['route']) {
            if (is_callable($route['callback']) && $route['type'] === 'GET') {
                return $route['callback']($this->piecesUrl);
            }

            $callback = explode(".", $route['callback']);
            $class = $callback[0];
            $method = $callback[1];
            $instance = $this->createInstance($class);

            return $this->action($instance, $method);
        }

        $this->urlError++;
    }

    public function run()
    {
        $this->index($this->getUrl());
    }

    /**
     * Retorna a intância de uma classe controller.
     *
     * @param $class
     *
     * @return mixed
     * @throws \Exception
     */
    private function createInstance($class)
    {
        $class = $this->namespace . ucfirst($class);

        if (!class_exists($class)) {
            throw new \Exception("Error: class {$class} does not exist. Check the name of your ");
        }

        $instance = new $class;

        return $instance;
    }

    /**
     * Verifica se o método existe na classe.
     * Chama o método da classe.
     *
     * @param $instance instancia do objeto.
     * @param $method método da classe.
     *
     * @throws \Exception
     */
    private function action($instance, $method)
    {
        if (method_exists($instance, $method)) {
            $instance->$method();
            //exit();
        } else {
            throw new \Exception("Error: method $method does not exist. Check the name of your method.");
        }
    }

    /**
     * Verifica se os valores do atributo urlError é igual ao número de rotas passadas, caso seja, lança um exceção.
     *
     * @param $value
     *
     * @throws \Exception
     */
    private function countError($value)
    {
        if (count($value) == $this->urlError) {
            throw new \Exception("Error: route does not exist");
        }
    }

    /**
     * Retorna o path da uri da página.
     *
     * @return string path da url
     */
    private function getUrl()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}