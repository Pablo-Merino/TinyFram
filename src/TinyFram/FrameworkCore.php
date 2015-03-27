<?php

namespace TinyFram;

require __DIR__."/../../vendor/autoload.php";

class FrameworkCore {

    /**
     * Stores the route => function array
     *
     * @access
     * @var
     */
    private $route_array;
    /**
     * Stores the app config
     *
     * @access
     * @var array
     */
    private $app_config = array(
        "views" => "views/"
    );
    /**
     * Specifies a catch_all function
     *
     * @access
     * @var
     */
    private $catch_all;

    private $mustache;

    /**
     * @param $version
     * @param $name
     */
    public function __construct($app_options) {
        $this->app_config = array_merge($this->app_config, $app_options);

        $this->mustache = new \Mustache_Engine(array(
            'loader' => new \Mustache_Loader_FilesystemLoader($this->app_config["views"]),
        ));
    }

    /**
     * Setter for catch_all
     *
     * @param mixed $catch_all New value for property
     *
     * @access
     * @return void
     */
    public function setCatchAll($catch_all)
    {
        $this->catch_all = $catch_all;
    }

    /**
     * Specifies a route, with a function
     *
     * @param $route
     * @param $reference
     *
     * @access
     * @return void
     */
    public function route($route, $reference)
    {
        $this->route_array[] = array($route, $reference);
    }

    /**
     * Sets a route array to match
     *
     * @throws \Exception
     * @access
     * @return void
     */

    public function dispatch()
    {
        foreach($this->route_array as $route) {
            $request_url = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : $_SERVER['REQUEST_URI'];
            $name = $route[0];
            $func = $route[1];
            $pattern = "@^" . preg_replace(
                    '/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($name)
                ) . "$@D";
            $matches = array();
            // check if the current request matches the expression
            if (preg_match($pattern, $request_url, $matches)) {
                array_shift($matches);
                return $func($matches);
            }
        }
        if(is_callable($this->catch_all))
        {
            return $this->catch_all->__invoke($request_url);
        } else {
            throw new \Exception("Couldn't match ".$request_url);
        }
    }

    /**
     * Gets the app name
     *
     *
     * @access
     * @return mixed
     */
    public function getAppName() {
		return $this->appName;
	}

    /**
     * Gets the app version
     *
     *
     * @access
     * @return mixed
     */
    public function getAppVer() {
		return $this->appVer;
	}

    /**
     * Method to render a template
     *
     * @param $view
     *
     * @throws \Exception
     * @access
     * @return void
     */
    public function render($view) {
		if(!file_exists($this->app_config["views"].$view.".mustache")) {
            throw new \Exception("View file ".$this->app_config["views"].$view.".mustache not found");
		} else {
            return $this->mustache->render("layout", array("yield" => $this->mustache->render($view)));
		}
	}
}
