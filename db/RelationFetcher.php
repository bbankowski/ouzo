<?php
namespace Thulium\Db;

use InvalidArgumentException;
use Thulium\Model;
use Thulium\Utilities\Arrays;
use Thulium\Utilities\FluentArray;
use Thulium\Utilities\Functions;
use Thulium\Utilities\Objects;

class RelationFetcher
{
    private $_relation;
    private $_foreignKey;
    private $_destinationField;
    private $_referencedColumn;
    private $_allowMissing;

    function __construct($relation, $foreignKey, $destinationField, $referencedColumn, $allowMissing)
    {
        $this->_destinationField = $destinationField;
        $this->_foreignKey = $foreignKey;
        $this->_relation = $relation;

        $this->_relationObject = Model::newInstance('\Model\\' . $this->_relation);
        $relationPrimaryKey = $this->_relationObject->getIdName();
        $this->_referencedColumn = $referencedColumn ? $referencedColumn : $relationPrimaryKey;
        $this->_allowMissing = $allowMissing;
    }

    public function transform(&$results)
    {
        $foreignKeyName = $this->_foreignKey;

        $foreignKeys = FluentArray::from($results)
            ->map(Functions::extractFieldRecursively($foreignKeyName))
            ->filter(Functions::notEmpty())
            ->unique()
            ->toArray();

        $relationObjectsById = $this->_loadRelationObjectsIndexedById($foreignKeys);

        foreach($results as $result) {
            $destinationField = $this->_destinationField;
            $foreignKey = Objects::getFieldRecursively($result, $foreignKeyName);
            if ($foreignKey) {
                $result->$destinationField = $this->_findRelationObject($result, $relationObjectsById, $foreignKey);
            }
        }
    }

    private function _loadRelationObjectsIndexedById($foreignKeys)
    {
        $relationObject = $this->_relationObject;
        $relationObjects =  $relationObject::where(array($this->_referencedColumn => $foreignKeys))->fetchAll();
        return Arrays::toMap($relationObjects, Functions::extractField($this->_referencedColumn));
    }

    private function _findRelationObject($result, $relationObjectsById, $foreignKey)
    {
        if (!isset($relationObjectsById[$foreignKey])) {
            if ($this->_allowMissing) {
                return null;
            }
            throw new InvalidArgumentException("Cannot find {$this->_relation} with {$this->_referencedColumn} = $foreignKey for {$result->inspect()}");
        }
        return $relationObjectsById[$foreignKey];
    }
}