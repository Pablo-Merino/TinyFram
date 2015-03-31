<?php
/**
 * <strong>Name :  BaseModel.php</strong><br>
 * <strong>Desc :  </strong><br>
 *
 * PHP version 5.5
 *
 * @category  tinyfram-skeleton
 * @package
 * @author     pmerino <pablo.perso1995@gmail.com>
 * @copyright 2015
 * @license   Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version   GIT: $Id$
 * @link      
 * @since     File available since Release 0.1.0
 */ 
namespace TinyFram\Base;
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
class Model {
    use Tools;
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

    /**
     * @param array $default_values
     */
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
        $sql_sentence = $klass->pdo->prepare("SELECT * FROM ".$klass->table." where id = :id");
        $sql_sentence->bindParam(":id", $id);

        if ($sql_sentence->execute()) {
            while ($row = $sql_sentence->fetch()) {
                $klass = static::populateFromArray($row);
            }
        }
        return $klass;
    }

    /**
     * Finds an entity with the specified column->value pair
     *
     * @param $field
     * @param $value
     *
     * @static
     * @throws \Exception
     * @access
     * @return Model|static
     */
    public static function findBy($field, $value)
    {
        $klass = new static();
        $attributes = array_keys($klass->getModelAttributes());

        $query_string = "%$value%";

        if(in_array($field, $attributes))
        {
            $sql_sentence = $klass->pdo->prepare("SELECT * FROM ".$klass->table." where {$field} LIKE :field LIMIT 1");
            $sql_sentence->bindParam(":field", $query_string);
            if ($sql_sentence->execute()) {
                while ($row = $sql_sentence->fetch()) {
                    $klass = static::populateFromArray($row);
                }
            }
            return $klass;
        } else {
            throw new \Exception("The field name doesn't exist in the model");
        }
    }

    /**
     * Finds all the entries with the specified column->value pair
     *
     * @param $field
     * @param $value
     *
     * @static
     * @throws \Exception
     * @access
     * @return array
     */
    public static function where($field, $value)
    {
        $klass = new static();
        $attributes = array_keys($klass->getModelAttributes());

        $query_string = "%$value%";

        $results = array();

        if(in_array($field, $attributes))
        {
            $sql_sentence = $klass->pdo->prepare("SELECT * FROM ".$klass->table." WHERE {$field} LIKE :field");
            $sql_sentence->bindParam(":field", $query_string);
            if ($sql_sentence->execute()) {
                while ($row = $sql_sentence->fetch()) {
                    $klass = new static();
                    $klass->_populateFromArray($row);
                    $results[] = $klass;
                }
            }
            return $results;
        } else {
            throw new \Exception("The field name doesn't exist in the model");
        }
    }

    /**
     * Retrieves all the entries of the entity
     *
     *
     * @static
     * @access
     * @return array
     */
    public static function all()
    {
        $klass = new static();
        $results = array();

        $sql_sentence = $klass->pdo->prepare("SELECT * FROM ".$klass->table);

        if ($sql_sentence->execute()) {
            while ($row = $sql_sentence->fetch()) {
                $klass = new static();
                $klass->_populateFromArray($row);
                $results[] = $klass;
            }
        }
        return $results;
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

    /**
     * Class method to populate from array
     *
     * @param $array
     *
     * @access
     * @return void
     */
    private function _populateFromArray($array)
    {
        $variables = $this->getModelAttributes();

        foreach ($variables as $key => $value) {
            if(array_key_exists($key, $array))
                $this->$key = $array[$key];
        }
    }

    /**
     * Retrieves all the model's attributes (except the superclass ones)
     *
     *
     * @access
     * @return array
     */
    private function getModelAttributes()
    {
        $variables = get_object_vars($this);
        foreach ($this->superclass_variables as $v) {
            unset($variables[$v]);
        }

        return $variables;
    }
}
