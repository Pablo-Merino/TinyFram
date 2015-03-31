<?php
/**
 * <strong>Name :  ControllerInterface.php</strong><br>
 * <strong>Desc :  </strong><br>
 *
 * PHP version 5.5
 *
 * @category  tinyfram-skeleton
 * @package
 * @author     Pablo Merino <pablo.perso1995@gmail.com>
 * @copyright 2015
 * @license   Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version   GIT: $Id$
 * @link      
 * @since     File available since Release 0.1.0
 */ 
namespace TinyFram\Base;

/**
 * Class BaseController
 *
 * @package TinyFram
 * @subpackage BaseController
 * @author     Pablo Merino <pablo.perso1995@gmail.com>
 * @copyright  2015 pablo.xyz
 * @license    Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    Release: <0.1.0>
 * @link       http://pablo.xyz
 * @since      Class available since Release 0.1.0
 */
class Controller {

    /**
     * The app instance, so we can access methods such as `render'
     *
     * @access protected
     * @var FrameworkCore
     */
    protected $app;
    /**
     * The request info
     *
     * @access protected
     * @var array
     */
    protected $request;

    /**
     * Variable that contains both $_GET and $_POST (where applicable)
     *
     * @access
     * @var array
     */
    protected $params;

    /**
     * @param $app
     * @param $request
     */
    public function __construct($app, $request)
    {
        $this->app = $app;
        $this->request = $request;

        $this->params = array_merge($_GET, $_POST);
    }

    /**
     * Renders a template
     *
     * @param      $temp
     * @param null $variable
     *
     * @access
     * @return mixed
     */
    protected function renderTemplate($temp, $variable = null)
    {
        return $this->app->render($temp, $variable);
    }

    /**
     * Implements basic HTTP auth
     *
     * @param array  $auth_array
     * @param string $error_msg
     * @param string $realm
     *
     * @access
     * @return void
     */
    protected function httpBasicAuth($auth_array, $error_msg = "Unauthorized", $realm = "Password protected")
    {
        $valid_users = array_keys($auth_array);

        $user = $_SERVER['PHP_AUTH_USER'];
        $pass = $_SERVER['PHP_AUTH_PW'];

        $validated = (in_array($user, $valid_users)) && ($pass == $auth_array[$user]);

        if (!$validated) {
            header("WWW-Authenticate: Basic realm=\"$realm\"");
            header("HTTP/1.0 401 Unauthorized");
            die($error_msg);
        }
    }
}