<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 3/28/2020
 * Time: 4:21 PM
 */

namespace CovidTrack\Domain\DataContracts;

interface IDataContract
{
    /**
     * Returns an array of all fields in the object
     * @return array
     */
    public static function getFields() : array ;

    /**
     * Gets the name of the resource the data object corresponds to
     * @return string
     */
    public static function getName();

    /**
     * gets the unique id field name of the resource the data object corresponds to
     * @return string
     */
    public static function getIdFieldName();

    /**
     * gets the data as an array
     * @return array data
     */
    public function getArray();


    /**
     * Retrieves a value from the object if it exists
     * @param string $field
     * @return mixed
     */
    public function getValue(string $field);


    /**
     * Returns a boolean indicating whether the object has a given value
     * @param string $field
     * @return bool
     */
    public function has(string $field) : bool;

    /**
     * creates from an array
     * @param array $array
     * @return bool
     */
    public static function createFromArray(array $array);

}