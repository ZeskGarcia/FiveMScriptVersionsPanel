<?php

class Router {
	private static $routes 	 			= array();
	private static $methods 	 		= array();
	private static $actions 			= array();
	private static $matchedRoute		= array();
	private static $matchedRouteParams 	= array();
	private static $routeFound 			= false;
	private static $fallback			= false;
	private static $currentRoute		= array();
	private static $currentRouteAction	= array();
	public static function get($route, $action){
		self::addRoute(['GET'], $route, $action);
	}
	public static function post($route, $action){
		self::addRoute(['POST'], $route, $action);
	}
	public static function put($route, $action){
		self::addRoute(['PUT'], $route, $action);
	}
	public static function delete($route, $action){
		self::addRoute(['DELETE'], $route, $action);
	}
	public static function any($route, $action){
		self::addRoute(['GET','POST', 'PUT', 'PATCH', 'DELETE',], $route, $action);
	}
	public static function match($methods = [], $route, $action){
		self::addRoute($methods, $route, $action);
	}
	private static function addRoute($method, $route, $action){
		$method = array_map('strtoupper', $method);
		array_push(self::$methods, $method);
		array_push(self::$routes, $route);
		array_push(self::$actions, $action);
	}
	private static function startRoute($routeIndex){
		self::$routeFound = !self::$routeFound ?? true;
		self::$matchedRouteParams = array_slice(array_unique(self::$matchedRoute), 1);
		self::$currentRoute = self::$matchedRoute[0];
		self::$currentRouteAction = self::$actions[$routeIndex];
		self::runRoute();
	}
	private static function runRoute(){
		if(is_string(self::$currentRouteAction)) 
			return self::classMethod();
		if(is_object(self::$currentRouteAction) && (self::$currentRouteAction instanceof \Closure))
			return self::closureMethod();
	}
	private static function classMethod(){
		$class = explode('@', self::$currentRouteAction);
		call_user_func(array(new $class[0], $class[1]), self::$matchedRouteParams);
	}
	private static function closureMethod(){
		call_user_func_array(self::$currentRouteAction, self::$matchedRouteParams);
	}
	public static function fallback($closure = false){
		self::$fallback = $closure;
	}
	private static function routeNotFound(){
		if(self::$routeFound) return false;
		if(is_callable(self::$fallback)){
			call_user_func(self::$fallback);
		}else{
			throw new \Exception('Page not found!');
		}
	}
	private static function checkMethod($routeIndex){
		$normalyMethod 	= in_array($_SERVER['REQUEST_METHOD'], self::$methods[$routeIndex]);
		$hiddenMethod	= (isset($_REQUEST['_method']) && in_array($_REQUEST['_method'], self::$methods[$routeIndex]));
		return ($normalyMethod || $hiddenMethod) ? true : false;
	}
	private static function routeMatch($route){
		$requestURI = trim($_GET['url'], '/');
		$routePattern = preg_replace("/\{(.*?)\}/", "(?'$1'[\w-]+)", $route);
		$routePattern = "#^" . trim($routePattern, '/') . "$#";
		preg_match($routePattern, $requestURI, $matchedRoute);
		return $matchedRoute ?? false;
	}
  public static function dispatch() {

		foreach (self::$routes as $routeIndex => $route) {
			self::$matchedRoute = self::routeMatch($route);
			if (!self::$matchedRoute) continue;
			if(self::checkMethod($routeIndex))
				self::startRoute($routeIndex);
  		}
  		self::routeNotFound();
	}
}
