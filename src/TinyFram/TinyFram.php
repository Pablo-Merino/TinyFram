<?php

namespace TinyFram;

class Core {
	
		
	private $appVersion;
	private $appName;
	private $appLayout;
	private $ignorePaths;
	private $urlMatched;

	public function __construct($version, $name) {
       	$this->appVersion = $version;
       	$this->appName = $name;
       	
    }
    public function pathIgnore($paths) {
    	if(is_array($paths)) {
    		$this->ignorePaths = $paths;
    	} else {
    		throw new Exception("The paths array is not an array!");
    	}
    }
    public function appConfig($layoutPath) {

    	define("APPLAYOUT", "$layoutPath");

    }
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
	public function getAppName() {
		return $this->appName;
	}
	public function getAppVer() {
		return $this->appVer;
	}
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