<?php
/**
 * <strong>Name :  BaseModel.php</strong><br>
 * <strong>Desc :  </strong><br>
 *
 * PHP version 5.5
 *
 * @category  tinyfram-skeleton
<<<<<<< HEAD
 * @package   
 * @author    pmerino <desarrollo@hola-internet.com>
=======
 * @package
 * @author     pmerino <pablo.perso1995@gmail.com>
>>>>>>> 8c01810b1771822e93be1257fa5585195ca750d6
 * @copyright 2015
 * @license   Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version   GIT: $Id$
 * @link      
 * @since     File available since Release 0.1.0
 */ 
namespace TinyFram;

/**
 * Class BaseModel
 *
 * @package TinyFram
 * @subpackage BaseModel
 * @author     pmerino <pablo.perso1995@gmail.com>
 * @copyright  2015 pablo.xyz
 * @license    Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    Release: <0.1.0>
 * @link       http://pablo.xyz
 * @since      Class available since Release 0.1.0
 */
class BaseModel {

    /**
     * PHP's PDO database library
     *
     * @access
     * @var \PDO
     */
    protected $pdo;
    /**
     * Specified table for the model
     *
     * @access
     * @var bool|mixed
     */
    protected $table;
    /**
     * Whitelisted attributes that are not specific to the model
     *
     * @access
     * @var array
     */
    protected $superclass_variables = array(
        "pdo",
        "table",
        "superclass_variables"
    );

    public function __construct($default_values = array())
    {
        $db_config = \TinyFram\FrameworkCore::getInstance()->app_config["db"];
        $this->table = Tools::pluralize(strtolower(Tools::get_actual_class($this)));
        $this->pdo = new \PDO(
            "mysql:host=".$db_config["host"].";dbname=".$db_config["dbname"],
            $db_config["username"],
            $db_config["password"]
        );
        if(!empty($default_values))
            $this->_populateFromArray($default_values);
    }

    /**
     * Static function that retrieves a model with the specified id
     *
     * @param $id
     *
     * @static
     * @access
     * @return BaseModel|static
     */
    public static function find($id)
    {
        $klass = new static();
        $sql_sentence = $klass->pdo->prepare("SELECT * FROM ".$klass->table." where id = ?");
        if ($sql_sentence->execute(array($id))) {
            while ($row = $sql_sentence->fetch()) {
                $klass = static::populateFromArray($row);
            }
        }
        return $klass;
    }

    /**
     * Populates a model with the data from an array. Used for populating the models after the queries.
     *
     * @param $array
     *
     * @static
     * @access
     * @return static
     */
    private static function populateFromArray($array)
    {
        $klass = new static();
        $klass->_populateFromArray($array);
        return $klass;
    }

    private function _populateFromArray($array)
    {
        $variables = get_object_vars($this);
        foreach ($this->superclass_variables as $v) {
            unset($variables[$v]);
        }

        foreach ($variables as $key => $value) {
            if(array_key_exists($key, $array))
                $this->$key = $array[$key];
        }
    }
}