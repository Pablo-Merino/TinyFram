<?php
/**
 * <strong>Name :  ControllerInterface.php</strong><br>
 * <strong>Desc :  </strong><br>
 *
 * PHP version 5.5
 *
 * @category  tinyfram-skeleton
 * @package
<<<<<<< HEAD
 * @author     pmerino <pablo.perso1995@gmail.com>
=======
 * @author     Pablo Merino <pablo.perso1995@gmail.com>
>>>>>>> e6119d9... Added a base model, and ability to hook up databases (using PDO)
 * @copyright 2015
 * @license   Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version   GIT: $Id$
 * @link      
 * @since     File available since Release 0.1.0
 */ 
namespace TinyFram;

/**
 * Class BaseController
 *
 * @package TinyFram
 * @subpackage BaseController
<<<<<<< HEAD
 * @author     pmerino <pablo.perso1995@gmail.com>
=======
 * @author     Pablo Merino <pablo.perso1995@gmail.com>
>>>>>>> e6119d9... Added a base model, and ability to hook up databases (using PDO)
 * @copyright  2015 pablo.xyz
 * @license    Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    Release: <0.1.0>
 * @link       http://pablo.xyz
 * @since      Class available since Release 0.1.0
 */
class BaseController {

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
     * @param $app
     * @param $request
     */
    public function __construct($app, $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    protected function renderTemplate($temp)
    {
        return $this->app->render($temp);
    }
}