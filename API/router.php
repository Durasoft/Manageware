<?php
     /*
     /
     /         D U R A S O F T W A R E
     /            P H P  R o u t e r
     /
     /    Description:
     /    
     /    Defines a new router class
     /    to fetch URI, parse it and then
     /    if the route is set, return it.
     /
     /
     /    Documentation:
     /
     /         Creating new Router:
     /              Simply create a variable and set it as new Router:
     /              $my_router = new Router();
     /
     /         Adding new route:
     /              Call newRoute function inside your object
     /              $my_router->newRoute("key", "location"); //eg: newRoute("homepage", "/mysite/index.php");
     /         Returning current route (or 404):
     /              Call master function, Route(). Returns which document has to be called as a string. Eg.:
     /              $my_router->Route(); >> "/mysite/images/elephpant.png"
     */  

define("IS_ADMIN", true);//for test!


class Router {
     public $routes = array();

     public function newRoute($key, $location, $case = null) {
          if ($case == null)
               $route_object = array(
                    'key' => $key,
                    'loc' => $location
               );
          else
               $route_object = array(
                    'key' => $key,
                    'loc' => $location,
                    'case' => $case
               );

          array_push($this->routes, $route_object);

          return $route_object;
     }
     public function matchRoutes($key) {
          $route = array_search($key, array_column($this->routes, 'key'));

          if (gettype($route) == "boolean") return false; //if no matches found, the $route var is set to false. this checks that.
          else {
               return $this->routes[$route];
          }
     }
     private function createRouteSet($uri) {
          $routes = array();
          $routes = explode('/', $uri);

          $routeset = array();
          foreach($routes as $route){
               if(trim($route) != '') array_push($routeset, $route);
          }

          return $routeset;
     }

     public function Route() {
          $routeset = $this->createRouteSet($_SERVER[REQUEST_URI]);

          return $routeset;
     }
}