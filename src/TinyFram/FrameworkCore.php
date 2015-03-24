<?php

namespace TinyFram;
use TinyFram\Exceptions;

class FrameworkCore {


    /**
     * Sets the app version
     *
     * @access
     * @var
     */
    private $app_version;
    /**
     * Sets the app name
     *
     * @access
     * @var
     */
    private $app_name;

    private $route_array;
    private $app_config;
    private $catch_all;

    /**
     * @param $version
     * @param $name
     */
    public function __construct($version, $name) {
       	$this->app_version = $version;
       	$this->app_name = $name;
       	
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
     * Sets app config
     *
     * @param $config_array
     *
     * @access
     * @return void
     */
    public function appConfig($config_array) {
        $this->app_config = array_merge(array(
            "views" => "views/"
        ), $config_array);
    }

    public function route($route, $reference)
    {
        $this->route_array[] = array($route, $reference);
    }

    /**
     * Sets a route array to match
     *
     * @throws RoutingError
     * @access
     * @return void
     */
    public function dispatch() {
        $request_uri = $_SERVER['REQUEST_URI'];
		$matched_url = false;
		
		if(is_array($this->route_array)) {
			foreach($this->route_array as $name=>$func) {
                $request_uri = str_replace($this->appName."/", '', $request_uri);
				$urlR = str_replace('/', '\/', $name);
				$urlR = '^' . $urlR . '\/?$';
				if (preg_match("/$urlR/i", $request_uri, $rMatch)) {
                    $matched_url = true;
					$func($name);
				} 
			}
		} else { 
			throw new RoutingError("No route array");
		}
		if(!$matched_url) {
			if(is_callable($this->catch_all))
            {
                $this->catch_all($request_uri);
            } else {
                throw new RoutingError("Couldn't match ".$request_uri);

            }
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
     * @access
     * @return void
     */
    public function render($view) {
		if(!file_exists(APPLAYOUT.$view.".php")) {
			$content = "View file not found!";
		} else {
			$content = file_get_contents(APPLAYOUT.$view.".php")."\n";

		}
		require APPLAYOUT."layout.php";
	}
}

?>