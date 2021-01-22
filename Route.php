<?php

namespace App\SimpleRoutePhp;

class Route
{
    private array $routes = [];
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

        // $route = localhost/test/{any}/hello 
        $route = trim("{$this->baseUrl}{$group}{$route}");

        // $routeKey = localhost/test/([^/]+)/hello
        $routeKey = preg_replace('~{([^}]*)}~', "([^/]+)", $route);

        $this->routes[$http_verb][$routeKey] = [
            "namespace" => $this->namespace,
            "handler" => $handler,
            "route" => $route,
            "name" => $name
        ];
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


    public function dispatch()
    {
        // $urlNow = localhost/teste/5
        $urlNow = "{$this->baseUrl}{$this->path}";

        $this->setError(true, "Route not found: {$urlNow}", 404);

        if (isset($this->routes[$this->method])) {

            foreach ($this->routes[$this->method] as $key => $value) {
                // $key = localhost/teste/([^/]+)
                if (preg_match("~^" . $key . "$~", $urlNow, $found)) {
                    //has $key at $urlNow
                    $this->execute($value, $urlNow);
                    break;
                }
            }
        }
    }

    private function execute(array $data, string $path)
    {

        /**
         * $keysRoute = Array(
         *      [0] => Array([0] => {id}, [1] => id),
         *      [1] => Array([0] => {slug}, [1] => slug)
         * )
         */
        preg_match_all("~\{\s* ([a-zA-Z_][a-zA-Z0-9_-]*) \}~x", $data["route"], $keysRoute, PREG_SET_ORDER);

        // Has in url
        // $valuesRoute = Array([0] => 3, [1] => "test-product")
        $valuesRoute = array_values(array_diff(explode("/", $path), explode("/", $data["route"])));

        $paramsUrl = [];

        for ($i = 0; $i < count($keysRoute); $i++) {
            $paramsUrl[$keysRoute[$i][1]] = $valuesRoute[$i];
        }

        $paramsForm = ($paramsUrl == [] ? [$this->getDataRequest()] : [...array_values($paramsUrl), $this->getDataRequest()]);

        if (is_callable($data["handler"])) {
            $method = $data["handler"];

            call_user_func($method, ...$paramsForm);

            $this->setError(false, "Not Error");
            return true;
        }

        list($class, $method) = explode(":", $data["handler"]);

        $class = $data['namespace'] . "\\{$class}";

        if (class_exists($class)) {

            if (method_exists($class, $method)) {

                $obj = new $class($this);

                $obj->$method(...$paramsForm);

                $this->setError(false, "Not Error");
                return true;
            }
            $this->setError(true, "Method not exists: {$method}()", 500);
            return false;
        }

        $this->setError(true, "Class not exist: {$class}", 500);
        return false;
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
