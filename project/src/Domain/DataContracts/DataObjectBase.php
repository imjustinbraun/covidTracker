<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 12/19/2019
 * Time: 2:43 PM
 */

namespace CovidTrack\Domain\DataContracts;


abstract class DataObjectBase implements IDataContract
{
    public static function getName()
    {
        return self::$_name;
    }

    public static function getIdFieldName()
    {
        return self::$_idFieldName;
    }

    public function getArray()
    {
        return $this->_data;
    }

    public function getValue(string $field)
    {
        return $this->_data['field'];
    }

    public function has(string $field) : bool
    {
        return array_key_exists($field, $this->_data);
    }

    protected $_data;
    protected static $_name;
    protected static $_idFieldName;
}