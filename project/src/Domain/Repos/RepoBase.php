<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 3/28/2020
 * Time: 4:18 PM
 */

namespace CovidTrack\Domain\DataRepos;

use CovidTrack\Domain\DataContracts\DataObjectBase;
use CovidTrack\Domain\DataContracts\IDataContract;
use Opulence\Orm\Repositories\IRepository;
use CovidTrack\Domain\Helpers\ArrayHelpers;
use CovidTrack\Infrastructure\Databases\MySQL\DataMapperBase;

abstract class RepoBase implements IRepository
{
    protected $_dataMapper;

    /**
     * @var IDataObject
     */
    protected $_dataObject;

    protected $_idFieldName = 'id';

    protected function __construct(DataMapperBase $dataMapper, $dataObject)
    {
        $this->_dataObject = $dataObject;
        $this->_dataMapper = $dataMapper;
    }

    public function search($terms)
    {
        return $this->_dataMapper->search($terms);
    }

    public function add($entity)
    {
        return $this->_dataMapper->add($entity);
    }

    public function delete($entity)
    {
        return $this->_dataMapper->delete($entity);
    }

    public function deleteById($id)
    {
        return $this->_dataMapper->deleteById($id);
    }

    public function getAll() : array
    {
        $arr = $this->_dataMapper->getAll();
        array_walk_recursive( $arr,
            [ArrayHelpers::class, 'replaceNullValueWithEmptyString']
        );
        return $arr;
    }

    public function getByOwner($ownerId) : array
    {
        return $this->_dataMapper->search(['owner' => $ownerId]);
    }

    public function getById($id)
    {
        return $this->_dataMapper->getById($id);
    }

    public function update($entity)
    {
        return $this->_dataMapper->update($entity);
    }

    public function exists($entity)
    {
        return $this->_dataMapper->exists($entity);
    }

    public function addOrUpdate(DataObjectBase $entity)
    {
        $existsResult = $this->exists($entity);

        if ($existsResult)
        {
            $entityArray = $entity->getArray();
            $result = [];

            if (array_key_exists($this->_idFieldName, $existsResult))
            {
                $entityArray[$this->_idFieldName] = $existsResult[$this->_idFieldName];
                $result['lastInsertId'] = $existsResult[$this->_idFieldName];
            }
            else {
                return $this->add($entity);
            }

            $updatedAt = '';
            if (array_key_exists('updatedAt', $entityArray))
            {
                $updatedAt = $entityArray['updatedAt'];
                unset($entityArray['updatedAt']);
                //print "\nSET UPDATED AT: $updatedAt\n";
            }
            else
            {
                $updatedAt = (new \DateTime('now'))->format('Y-m-d H:i:s');
                //print "\nUPDATED AT: $updatedAt\n";
            }

            //if the item exists, update it only if it's been updated more recently than what we have in the db
            $dbContactEntity = $this->_dataMapper->getById($entityArray[$this->_idFieldName]);
            $dbContactEntityArray = $dbContactEntity->getArray();
            if (array_key_exists('createdAt', $entityArray))
                unset($entityArray['createdAt']);

            if (array_key_exists('updatedAt', $dbContactEntityArray))
            {
                $dbUpdateDateTime = $dbContactEntityArray['updatedAt'];
                //print "$dbUpdateDateTime vs $updatedAt";

                //if the new item was older than what we have, skip it
                if (strtotime($dbUpdateDateTime) < strtotime($updatedAt))
                {
                    $entityArray['updatedAt'] =  $updatedAt;
                    $result = $this->update($this->_dataObject::createFromArray($entityArray));
                    return $result;
                }
                else
                {
                    $result['Action'] = 'Skip';
                    return $result;
                }
            }
        }
        return $this->add($entity);
    }

    public function getIdFieldName()
    {
        return $this->_idFieldName;
    }
}