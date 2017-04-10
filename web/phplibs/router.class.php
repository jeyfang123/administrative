<?php
class Router
{
    private $controller;
    private $method;
    private $parameters;

    public function  __construct(){
        $requestURI = $_SERVER["REQUEST_URI"];
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $uri = substr($uri, 1);
        $segments = explode("/", $uri);
        if (count($segments) > 0) {
            try {
                if (isset($segments[0])) {
                    $this->controller = strtolower($segments[0]);
                }

                $this->controller = isset($segments[0]) ? $segments[0] : "";
                $this->method  = isset($segments[1]) ? $segments[1] : "";
                $this->parameters = array_slice($segments, 2);
            } catch(Exception $e) {
            }
        }
    }

    public function dispatch(){
        if($this->controller == ""){
            header("location:/login");
            exit();
        }
        $filename = $this->controller . '.controller.php';
        $file = CONTROLLER_PATH . $filename;
        if (file_exists($file)) {
            include($file);
        } else {
            exit();
        }
        $controllerClass = $this->controller;
        $controller = new $controllerClass();
        $methodName = $this->method;
        if ($methodName == '') {
            $methodName = 'render';
        }
        $methodName .= 'Action';
        if (method_exists($controller, $methodName)) {
            if (is_callable(array($controller, $methodName))) {
                $controller->$methodName($this->parameters);
            } else {
                $controller->error();
            }
        }
    }

    public function getController(){
        return $this->controller;
    }
}
?>
