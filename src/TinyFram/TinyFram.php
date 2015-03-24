<?php

namespace TinyFram;

class FrameworkCore {


    /**
     * Sets the app version
     *
     * @access
     * @var
     */
    private $appVersion;
    /**
     * Sets the app name
     *
     * @access
     * @var
     */
    private $appName;
    /**
     * Sets the ignore paths
     *
     * @access
     * @var
     */
    private $ignorePaths;
    /**
     * Knows if an URL was successfully matched
     *
     * @access
     * @var
     */
    private $urlMatched;

    /**
     * @param $version
     * @param $name
     */
    public function __construct($version, $name) {
       	$this->appVersion = $version;
       	$this->appName = $name;
       	
    }

    /**
     * Sets the path ignore
     *
     * @param $paths
     *
     * @throws Exception
     * @access
     * @return void
     */
    public function pathIgnore($paths) {
    	if(is_array($paths)) {
    		$this->ignorePaths = $paths;
    	} else {
    		throw new Exception("The paths array is not an array!");
    	}
    }

    /**
     * Sets the default app layout name
     *
     * @param $layoutPath
     *
     * @access
     * @return void
     */
    public function appConfig($layoutPath) {

    	define("APPLAYOUT", "$layoutPath");

    }

    /**
     * Sets a route array to match
     *
     * @param $routesArr
     *
     * @throws Exception
     * @access
     * @return void
     */
    public function setRoutes($routesArr) {
		$reqURI = $_SERVER['REQUEST_URI'];
		$this->urlMatched = false;
		
		if(is_array($this->ignorePaths)) {
			foreach ($this->ignorePaths as $key => $value) {
				$reqURI = str_replace($value, '', $reqURI);
			}
		}
		if(is_array($routesArr)) {
			foreach($routesArr as $name=>$func) {
				$reqURI = str_replace($this->appName."/", '', $reqURI);
				$urlR = str_replace('/', '\/', $name);
				$urlR = '^' . $urlR . '\/?$';
				if (preg_match("/$urlR/i", $reqURI, $rMatch)) {
					$this->urlMatched = true;
					$func($name);
				} 
			}
		} else { 
			throw new Exception("No route array");
		}
		if(!$this->urlMatched) {
			notFound($reqURI);
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