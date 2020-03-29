<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 1/5/2020
 * Time: 12:46 PM
 */

namespace CovidTrack\Infrastructure\Databases\MySQL;

use CovidTrack\Domain\DataContracts\IDataContract;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Environments\Environment;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\Query;

abstract class DataMapperBase extends SqlDataMapper
{
    protected $_idFieldName;
    protected $_tableName;
    protected $_ignoreIdForDupDetection = false;

    //TODO: Consider throwing an exception on failure
    /**
     * Adds an object to the db
     * @param object $entity
     * @return array
     */
    public function add($entity) : array
    {
        $requestArray = (array)$entity->getArray();
        $query = (new QueryBuilder())->insert($this->_tableName, $requestArray);
        //print $query->getSql();
        $result =$this->genericWrite($query);
        if (array_key_exists('lastInsertId', $result))
            $requestArray[$this->_idFieldName] = $result['lastInsertId'];

        $result['RequestEntity'] = $requestArray;
        $result['Action'] = 'Add';
        return $result;
    }

    //TODO: Consider throwing an exception on failure
    //TODO: Consider voiding return type
    /**
     * Deletes an object from the db. Searches by id contained in the passed object
     * @param object $entity
     * @return array
     */
    public function delete($entity) : array
    {
        $query = (new QueryBuilder)->delete($this->_tableName)
            ->where("$this->_idFieldName = '".$entity->getArray()[$this->_idFieldName]."'");

        $result = $this->genericWrite($query);
        $result['Action'] = 'Delete';
        return $result;
    }

    //TODO: Consider throwing an exception on failure
    //TODO: Consider voiding return type
    /**
     * Deletes an object from the db
     * @param $id
     * @return array
     */
    public function deleteById($id) : array
    {
        $query = (new QueryBuilder)->delete($this->_tableName)
            ->where("$this->_idFieldName = '$id'");

        $result = $this->genericWrite($query);
        $result['Action'] = 'Delete';
        return $result;
    }

    //TODO: Consider throwing an exception on failure
    /**
     * Searches the db using a passed array ['column' => 'searchterm']
     * @param $searchTerms
     * @return array
     */
    public function search(array $searchTerms) : array
    {
        return $this->getMultiByFields($searchTerms);
    }

    /**
     * Returns an array of all fields from a given table.
     * Warning: Only use this if you really really have to
     * @return array
     */
    public function getAll() : array
    {
        $query =
            (new QueryBuilder)->select('*')->from($this->_tableName);
        return $this->getMulti($query);
    }

    /**
     * Returns a data object using the passed id
     * @param int|string $id
     * @return null|object
     */
    public function getById($id)
    {
        return $this->getByField($this->_idFieldName, $id);
    }

    /**
     * Updates a data object by id contained in the passed object
     * @param object $entity
     * @return array
     */
    public function update($entity)
    {
        $array = $entity->getArray();
        $query = (new QueryBuilder)->update(
            $this->_tableName, 'a', $array)
            ->where("$this->_idFieldName = ?")
            ->addUnnamedPlaceholderValue($array[$this->_idFieldName], \PDO::PARAM_STR);

        $query->getSql();

        $result = $this->genericWrite($query);
        $result['Action'] = 'Update';

        if (array_key_exists($this->_idFieldName, $array))
            $result['lastInsertId'] = $array[$this->_idFieldName];

        return $result;
    }

    /**
     * Loads the data object associated with the data mapper
     * @param $hash
     * @return object
     */
    public function getEntity($hash)
    {
        return $this->loadEntity($hash);
    }

    /**
     * Searches the database for the object. If it exists, you get an object. If not, you get null
     * @param IDataContract $object
     * @return null
     */
    public function exists(IDataContract $object)
    {
        $objectArray = $object->getArray();
        if ($this->_idFieldName === ''
            || $this->_ignoreIdForDupDetection
            || !array_key_exists($this->_idFieldName, $objectArray))
        {
            $query = (new QueryBuilder())->select('*')
                ->from($this->_tableName);

            foreach ($object::getFields() as $field)
            {
                if (array_key_exists($field, $objectArray))
                    $query->andWhere("$field = :$field")
                        ->addNamedPlaceholderValue( $field,
                            $objectArray[$field] , \PDO::PARAM_STR);
            }

            $result = $this->genericRead($query);
            if (count($result) > 0)
                return $result[0];
            return null;
        }
        $result = $this->getById($objectArray[$this->_idFieldName]);
        if ($result)
            return $result->getArray();

        return null;
    }

    public function __construct(ConnectionPool $connectionPool)
    {
        parent::__construct($connectionPool->getWriteConnection(), $connectionPool->getReadConnection());
    }

    public function getPage(int $pageNum)
    {
        $offsetNum = $pageNum * Environment::getVar('PAGINATION_QTY');
        $query = (new QueryBuilder)->select('*')
            ->from($this->_tableName)
            ->orderBy("$this->_idFieldName DESC")
            ->limit(Environment::getVar('PAGINATION_QTY'))
            ->offset($offsetNum);

        return $this->genericRead($query);
    }

    protected function getByField($field, $value)
    {

        $query = (new QueryBuilder)->select('*')
            ->from($this->_tableName)
            ->where($field." = '". $value."'");

        $result = $this->genericRead($query);

        if (count($result) == 1)
        {
            return $this->loadEntity($result[0]);
        }
        else
            return null;
    }

    protected function getMultiByAField($field, $value)
    {
        $query = (new QueryBuilder)->select('*')
            ->from($this->_tableName)
            ->where($field." = '". $value."'");

        return $this->getMulti($query);
    }

    protected function getMultiByFields(array $fields)
    {
        $query = (new QueryBuilder)->select('*')
            ->from($this->_tableName);

        $iter =0;
        foreach ($fields as $key => $value) {
            if ($iter >0)
                $query->andWhere($key." = '". $value."'");
            else
                $query->where($key." = '". $value."'");

            $iter++;
        }

        return $this->getMulti($query);
    }

    protected function getMulti(Query $query)
    {
        $result = $this->genericRead($query);

        if (count($result) > 0)
        {
            if ($this->_idFieldName !== '')
            {
                $output = [];
                foreach ($result as $row) {
                    $output[$row[$this->_idFieldName]] = $row;
                }
                return $output;
            }
            return $result;
        }
        else
            return [];
    }

    protected function genericRead(Query $query)
    {
        $queryString = $query->getSql();
        //print "$queryString \n";
        $sth = $this->readConnection->prepare($queryString);
        $sth->bindValues($query->getParameters());
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function genericWrite(Query $query)
    {
        $result = ['lastInsertId' => -1, 'error' => -1, 'errorInfo' => ''];
        $queryString = $query->getSql();
        //print "$queryString \n";

        $sth = $this->writeConnection->prepare($queryString);
        $sth->bindValues($query->getParameters());

        if( $sth->execute())
        {
            $result['lastInsertId'] = $this->writeConnection->lastInsertId();
            $result['error'] = 0;
        }
        else
        {
            $result['error'] = $sth->errorCode();
            $result['errorInfo'] = $sth->errorInfo();
        }

        return $result;
    }
}