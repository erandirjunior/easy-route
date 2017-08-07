<?php

namespace EasyRoute;

/**
 * Class Bootstrap
 * @package Core\ServiceRoutes
 */
class Bootstrap
{
    /**
     * Recebe as rotas.
     *
     * @var array
     */
    private static $routes;

    /**
     * Recebe os valores da url que são dinâmicas nas rotas.
     *
     * @var array
     */
    private $piecesUrl;

    /**
     * Rebece o número de erros por url não encontrada.
     *
     * @var int
     */
    private $urlError;

    /**
     * Recebe o namespace.
     *
     * @var string
     */
    private $namespace;

    private $matches;

    /**
     * Bootstrap constructor.
     *
     * @param $namespace
     */
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
        $this->index($this->getUrl());
    }

    /**
     * Recebe os valores das rotas.
     *
     * @param $routes string
     * @param $callback string
     */
    public static function setRoutes($routes, $callback, $type)
    {
        self::$routes[] = ['route' => $routes, 'callback' => $callback, 'type' => $type];
    }

    /**
     * @param string $url
     *
     * @see isDynamicRoute
     * @see treatDynamicRoute
     * @see run
     * @see countError
     */
    private function index(string $url)
    {
        try {
            array_walk(self::$routes, function ($route) use ($url) {
                if ($this->isDynamicRoute($route['route'])) {
                    $this->treatDynamicRoute($route, $url);
                } else {
                    $this->run($url, $route);
                }
                $this->countError(self::$routes);
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
    private function isDynamicRoute($route)
    {
        $valueRegex    = preg_match_all('/\/\{[a-z]+\}\/?/', $route, $matches);
        return $valueRegex > 0 ? true : false;
    }

    /**
     * Faz o tratamento da rota dinâmica.
     *
     * @param array $route
     * @param string $url
     *
     * @see run
     */
    private function treatDynamicRoute($route, $url)
    {
        preg_match_all('/\/\{[a-z]+\}\/?/', $route['route'], $matches);

        $route['route'] = str_replace(['{', '}'], '', $route['route']);
        $urlArray       = explode('/', $url);
        $routeArray     = explode('/', $route['route']);

        $matches        = $this->cleanMatche($matches);
        $indice         = $this->valueIndiceRoute($routeArray, $matches);

        foreach ($routeArray as $k => $v) {
            if (empty($urlArray[$k])) {
                $urlArray[$k] = '';
            }

            if ($v != $urlArray[$k] && $indice[$k] == $v) {
                $urlValues[]       = $urlArray[$k];
                $this->piecesUrl[] = $urlArray[$k];
                continue;
            }

            $urlValues[] = $v;
        }

        $route['route'] = implode('/', $urlValues);

        $this->run($url, $route);
    }

    /**
     * Limpa o matches.
     *
     * @param $matches
     *
     * @return mixed
     */
    private function cleanMatche($matches)
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
    private function valueIndiceRoute($routeArray, $matches)
    {
        foreach ($routeArray as $k => $v) {
            foreach ($matches as $j => $value) {
                if ($routeArray[$k] == $value) {
                    $indice[$k] = $v;
                }
            }
        }

        return $indice;
    }

    /**
     * Verifica se o valor da rota é igual a url.
     * Verifica e executa se o valor passado é um callable.
     * Invoca um criador de objetos.
     *
     * @param string $url
     * @param array  $route
     *
     * @return mixed
     */
    private function run(string $url, array $route)
    {
        if ($url === $route['route']) {
            if (is_callable($route['callback']) && $route['type'] === 'GET') {
                return $route['callback']($this->piecesUrl);
            }

            $callback = explode(".", $route['callback']);
            $class    = $callback[0];
            $method   = $callback[1];
            $instance = $this->createInstance($class);

            $this->action($instance, $method);
        } else {
            $this->urlError++;
        }
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
        $class = $this->namespace.ucfirst($class);

        if (!class_exists($class)) {
            throw new \Exception("Error: class $class does not exist. Check the name of your ");
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