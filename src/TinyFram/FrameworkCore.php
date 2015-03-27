<?php

namespace TinyFram;

require __DIR__."/../../vendor/autoload.php";

/**
 * Class FrameworkCore
 *
 * @category   TinyFram
 * @package    TinyFram
 * @subpackage FrameworkCore
 * @author     Pablo Merino <pablo.perso1995@gmail.com>
 * @copyright  2015 pablo.xyz
 * @license    Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    Release: <0.1.0>
 * @link       http://pablo.xyz
 * @since      Class available since Release 0.1.0
 */
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
    public $app_config = array(
        "views" => "views/",
        "db" => array(
            "host" => "localhost",
            "username" => "root",
            "password" => "",
            "dbname" => ""
        )
    );

    /**
     * Specifies a error_handler function
     *
     * @access
     * @var
     */
    private $error_handler;

    /**
     * Stores the Mustache rendering engine
     *
     * @access
     * @var \Mustache_Engine
     */
    private $mustache;

    /**
     * Stores self, for the singleton model
     *
     * @static
     * @access
     * @var FrameworkCore
     */
    private static $self_instance;

    /**
     * Used to reload the config of the mustache rendered for example when the config changes
     *
     *
     * @access
     * @return void
     */
    public function reloadMustacheAfterConfigChange()
    {
        $this->mustache = new \Mustache_Engine(array(
            'loader' => new \Mustache_Loader_FilesystemLoader($this->app_config["views"]),
        ));
    }

    /**
     * Setter for app_config
     *
     * @param array $app_config New value for property
     *
     * @access
     * @return void
     */
    public function setAppConfig($app_config)
    {
        $this->app_config = array_merge($this->app_config, $app_config);
        $this->reloadMustacheAfterConfigChange();
    }

    /**
     * Singleton model
     *
     *
     * @static
     * @access
     * @return FrameworkCore
     */
    public static function getInstance()
    {
        if (  !self::$self_instance instanceof self)
        {
            self::$self_instance = new self;
        }
        return self::$self_instance;
    }

    /**
     * Setter for error_handler
     *
     * @param mixed $error_handler New value for property
     *
     * @access
     * @return void
     */
    public function setErrorHandler($error_handler)
    {
        $this->error_handler = $error_handler;
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
    public function route($route, $method, $reference)
    {
        $this->route_array[] = array(
            "route" => $route,
            "callable" => $reference,
            "method" => $method
        );
    }

    /**
     * This function performs all the heavy weight lifting. This is the last function called on index.php, and it's
     * job is to actually pair a request to a route with a controller->function, or a Closure. This uses some complex
     * Regex to match either the REQUEST_URI or PATH_INFO. Then it looks for the specified callback in the routes array
     * and just call_user_func_array's the specified Closure or controller->function.
     *
     * @throws \Exception
     * @access
     * @return void
     */
    public function dispatch()
    {
        foreach($this->route_array as $route) {
            $request_url = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : $_SERVER['REQUEST_URI'];
            $request_method = $_SERVER["REQUEST_METHOD"];

            $name = $route["route"];
            $callable = $route["callable"];
            $method = $route["method"];

            if($method == "*")
                $method = $request_method;

            $pattern = "@^" . preg_replace(
                    '/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($name)
                ) . "$@D";
            $matches = array();
            // check if the current request matches the expression
            if (preg_match($pattern, $request_url, $matches) && $request_method == $method) {
                array_shift($matches);
                if(is_callable($callable))
                {
                    /**
                     * Trickery here: this instantiates the class specified in the router, with the $app and $request
                     * arguments, and then calls the function also specified in the router with the $matches variable
                     */
                    if(is_array($callable)) {
                        echo call_user_func_array(
                            array(
                                new $callable[0]($this, $_SERVER), /** This is the class name instantiation */
                                $callable[1]/** This is the method name */
                            ),
                            array($matches)
                        );
                    } else if(is_callable($callable)) {
                        $callable($matches);
                    }
                    return;
                } else {
                    http_response_code(500);
                    if(is_callable($this->error_handler))
                    {
                        echo $this->error_handler->__invoke(
                            array(
                                "error_code" => 500,
                                "error_message" => "Couldn't call the specified callable"
                            )
                        );
                    } else {
                        throw new \Exception("Couldn't call the specified <pre>callable</pre>");
                    }

                }
            }
        }
        http_response_code(404);
        if(is_callable($this->error_handler))
        {
            echo $this->error_handler->__invoke(
                array(
                    "error_code" => 404,
                    "error_message" => "Couldn't match ".$request_url
                )
            );
        } else {
            throw new \Exception("Couldn't match ".$request_url);
        }
    }

    /**
     * Renders a mustache views & template with the specified context
     *
     * @param      $view
     * @param null $variables
     *
     * @throws \Exception
     * @access
     * @return string
     */
    public function render($view, $variables = null) {
		if(!file_exists($this->app_config["views"].$view.".mustache")) {
            throw new \Exception("View file ".$this->app_config["views"].$view.".mustache not found");
		} else {
            return $this->mustache->render("layout", array("yield" => $this->mustache->render($view, $variables)));
		}
	}
}
