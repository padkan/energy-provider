<?php

namespace Core;
 
 /**
  * class Router
  */
  class Router {

    protected $routers;
    public  $params;

    /**
     * add a route to routing table
     * @param string $route URL
     * @param array $params parametes controller,action,...
     * 
     * @return void
     */
    public function add(string $route,array $params = []) :void {
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * get all routes
     * 
     * @return array
     */
    public function getRoutes() :array {
        return $this->routes;
    }

    /**
     * get all params
     * 
     * @return array
     */
    public function getParams() :array {
        return $this->params;
    }

    /**
     * match route
     * @param String $url
     * 
     * @return bool
     */
    public function match(string $url) :bool {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                
                foreach ($matches as $key => $match) {

                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * dispatch router
     * @param string $url
     * 
     * @return void
     */
    public function dispatch(string $url) :void {
        $url = $this->removeQueryStringVaraible($url);
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudyCaps($controller);
            $controller = $this->getNameSpace() . $controller;
            if (class_exists($controller)) {
                $this->params['action'] = $this->convertToCamelCase($this->params['action']);
                $controllerObject = new $controller($this->params);
                $action = $this->params['action'];
                if (is_callable([$controllerObject, $action])) {
                    $controllerObject->$action();
                } else {
                    //echo "Method $action (in controller $controller) not found";
                    throw new \Exception("Method $action (in controller $controller) not found");
                }
            } else {
                //echo "Controller class $controller not found";
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            //echo "No route matched.";
            throw new \Exception("No route matched.", 404);
        }
    }

    /**
     * @param string $string
     * 
     * @return string
     */
    protected function convertToStudyCaps(string $string) :string {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * @param string $string
     * 
     * @return string
     */
    protected function convertToCamelCase(string $string) :string {
        return lcfirst($this->convertToStudyCaps($string));
    }

    /**
     * @param string $url
     * 
     * @return string
     */
    protected function removeQueryStringVaraible(string $url) :string {
        if ($url != '') {
            $parts= explode('&', $url, 2);
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        return $url;
    }

    /**
     * get namespace from router
     * 
     * @return string
     */
    protected function getNameSpace() :string {
        $namespace = 'App\Controllers\\';
        if (array_key_exists('namespace', $this->params)) {
            $namespace .=  $this->params['namespace'] . '\\';
        }
        return $namespace;
    }
}