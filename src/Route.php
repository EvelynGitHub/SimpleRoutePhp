<?php

namespace SimplePhp\SimpleRoute;

use Exception;

class Route
{
    private array $routes = [];
    private array $params = [];
    private string $method = "";
    private string $baseUrl = "";
    private string $path = "";
    private string $group = "";
    private string $namespace = "";
    private array $error = [];


    public function __construct($baseUrl)
    {
        $this->baseUrl = (substr($baseUrl, -1) != "/") ? $baseUrl : substr($baseUrl, 0, -1);
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->path = (filter_input(INPUT_GET, "route", FILTER_DEFAULT) ?? "/");
    }

    public function getMethod()
    {
        return $this->path;
    }

    public function group(string $group)
    {
        $this->group = (substr($group, 1) == "/") ? $group : "/{$group}";
    }

    public function namespace(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function get(string $route, $handler, string $name = null)
    {
        $this->addRoute("GET", $route, $handler, $name);
    }

    public function post(string $route, $handler, string $name = null)
    {
        $this->addRoute("POST", $route, $handler, $name);
    }

    public function put(string $route, $handler, string $name = null)
    {
        $this->addRoute("PUT", $route, $handler, $name);
    }

    public function patch(string $route, $handler, string $name = null)
    {
        $this->addRoute("PATCH", $route, $handler, $name);
    }

    public function delete(string $route, $handler, string $name = null)
    {
        $this->addRoute("DELETE", $route, $handler, $name);
    }

    private function addRoute(string $http_verb, string $route, $handler, string $name = null)
    {
        $group = empty($this->group) ? "" : "{$this->group}";

        $route = trim("{$this->baseUrl}{$group}{$route}");

        $this->routes[$http_verb][$route] = [
            "namespace" => $this->namespace,
            "handler" => $handler,
            "route" => $route,
            "name" => $name
        ];
    }

    public function execute()
    {
        $data = $this->getHandler();

        if (!$data) throw new Exception("Nenhuma rota encontrada do tipo {$this->method}");

        $paramsForm = ($this->params == [] ? [$this->getDataRequest()] : [...array_values($this->params), $this->getDataRequest()]);

        if (is_callable($data["handler"])) {
            $method = $data["handler"];

            call_user_func($method, ...$paramsForm);

            return true;
        }

        list($class, $method) = explode(":", $data["handler"]);

        $class = $data['namespace'] . "\\{$class}";

        if (class_exists($class)) {

            if (method_exists($class, $method)) {

                $obj = new $class($this);

                $obj->$method(...$paramsForm);

                return true;
            }
            $this->setError(true, "Method not exists: {$method}()", 500);
            return false;
        }

        $this->setError(true, "Class not exist: {$class}", 500);
        return false;
    }



    private function getHandler()
    {
        if (empty($this->routes[$this->method])) {
            return false;
        }

        if (isset($this->routes[$this->method][$this->path])) {
            return $this->routes[$this->method][$this->path];
        }

        // Para rotas com parâmetros
        foreach ($this->routes[$this->method] as $route => $action) {
            $result = $this->checkUrlWithParams($route, $this->path);

            if ($result >= 1) {
                return $action;
            }
        }

        return false;
    }

    private function checkUrlWithParams(string $route, string $path)
    {
        preg_match_all('/\{([^\}]*)\}/', $route, $variables);

        $regex = str_replace('/', '\/', $route);

        foreach ($variables[0] as $k => $variable) {
            $replacement = '([a-zA-Z0-9\-\_\ ]+)';
            $regex = str_replace($variable, $replacement, $regex);
        }

        $regex = preg_replace('/{([a-zA-Z]+)}/', '([a-zA-Z0-9+])', $regex);

        $result = preg_match('/^' . $regex . '$/', $path, $params);

        $this->params = $params;

        return $result;
    }

    private function getDataRequest(): ?array
    {
        $typeRequest = array("GET", "PUT", "DELETE", "PATCH");

        if ($this->method == "POST") {
            $array = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            return $array;
        }

        if (in_array($this->method, $typeRequest)) {
            //parse_str tranforma a string em array associativo
            parse_str(file_get_contents("php://input"), $array);
            return $array;
        }

        //https://www.php.net/manual/pt_BR/wrappers.php.php
    }


    public function getError()
    {
        return $this->error;
    }

    private function setError(bool $error, $msg, $codeStatus = 200)
    {
        $this->error["error"] = $error;
        $this->error["status_code"] = $codeStatus;
        $this->error["message"] = $msg;
    }
}
