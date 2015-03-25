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
            $callable = $route[1];
            $pattern = "@^" . preg_replace(
                    '/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($name)
                ) . "$@D";
            $matches = array();
            // check if the current request matches the expression
            if (preg_match($pattern, $request_url, $matches)) {
                array_shift($matches);
                if(is_callable($callable))
                {
                    /**
                     * Trickery here: this instantiates the class specified in the router, with the $app and $request
                     * arguments, and then calls the function also specified in the router with the $matches variable
                     */
                    echo call_user_func_array(
                        array(
                            new $callable[0]($this, $_SERVER), /** This is the class name instantiation */
                            $callable[1] /** This is the method name */
                        ),
                        array($matches) /** This contains the matches from the router */
                    );
                    return;
                } else {
                    if(is_callable($this->error_handler))
                    {
                        return $this->error_handler->__invoke(
                            array(
                                "error_code" => 500,
                                "error_message" => "Couldn't call the specified <pre>callable</pre>"
                            )
                        );
                    } else {
                        throw new \Exception("Couldn't call the specified <pre>callable</pre>");
                    }

                }
            }
        }
        if(is_callable($this->error_handler))
        {
            return $this->error_handler->__invoke(
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
     * Method to render a template
     *
     * @param $view
     *
     * @throws \Exception
     * @access
     * @return string
     */
    public function render($view) {
		if(!file_exists($this->app_config["views"].$view.".mustache")) {
            throw new \Exception("View file ".$this->app_config["views"].$view.".mustache not found");
		} else {
            return $this->mustache->render("layout", array("yield" => $this->mustache->render($view)));
		}
	}
}
