<?php
ini_set('display_errors', 'On');
error_reporting(E_ERROR | E_PARSE);

define('ROOT', __DIR__ . '/..');

function get($route, $callback) {
    Bones::register($route, $callback, 'GET');
}

function post($route, $callback) {
    Bones::register($route, $callback, 'POST');
}

function put($route, $callback) {
    Bones::register($route, $callback, 'PUT');
}

function delete($route, $callback) {
    Bones::register($route, $callback, 'DELETE');
}

class Bones {
    private static $instance;
    public static $route_found = false;
    public $route = '';
    public $method = '';
    public $content = '';
    public $vars = array();
    
    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new Bones();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->route = $this->get_route();
        $this->method = $this->get_method();
    }
    
    protected function get_route() {
        parse_str($_SERVER['QUERY_STRING'], $route);
        if ($route) {
            return '/' . $route['request'];
        } else {
            return '/';
        }
    }
    
    protected function get_method() {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }
    
    public function set($index, $value) {
        $this->vars[$index] = $value;
    }
    
    public function render($view, $layout = "layout") {
        $this->content = ROOT. '/views/' . $view . '.php';
        foreach ($this->vars as $key => $value) {
            $$key = $value;
        }
        if (!$layout) {
            include($this->content);
        } else {
            include(ROOT. '/views/' . $layout . '.php');
        }
    }
    
    public static function register($route, $callback, $method) {
        $bones = self::get_instance();
        if ($route == $bones->route && !self::$route_found && $bones->method == $method) {
            self::$route_found = true;
            echo $callback($bones);
        } else {
            return false;
        }
    }
    
    public function form($key) {
        return $_POST[$key];
    }
    
    public function make_route($path = '') {
        $url = explode("/", $_SERVER['PHP_SELF']);
        //var_dump($url);
        if ($url[1] == "index.php") {
            return $path;
        } else {
            return '/' . $url[2] . $path;
        }
    }
}