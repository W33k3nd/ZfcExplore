<?php

namespace ZfcExplore\Reference;

use Zend\Stdlib\AbstractOptions;
use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\ResultSet\ResultSet;
abstract class AbstractReference extends AbstractOptions implements ReferenceInterface{
    
    
    /**
     * 
     * @var string
     */
    protected $table;
    
    /**
     * 
     * @var string
     */
    protected $referenceColumn;
    
    /**
     * 
     * @var ResultSet
     */
    protected $referenceData;
    
    /**
     * 
     */
    private $relation = ReferenceInterface::ONETOONE;
    
    /**
     * 
     * @var boolean
     */
    protected $hasData = FALSE;
    
    
	/**
     * @return the $table
     */
    public function getTable()
    {
        return $this->table;
    }

	/**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

	/**
     * @return the $referenceData
     */
    public function getReferenceData()
    {
        return $this->referenceData;
    }

	/**
     * @param AbstractResultSet $referenceData
     */
    public function setReferenceData(AbstractResultSet $referenceData)
    {
        if($referenceData->count() != 0){
            $this->hasData = TRUE;
        }
        $this->referenceData = $referenceData;
    }
    
	/**
     * @return the $referenceColumn
     */
    public function getReferenceColumn()
    {
        return $this->referenceColumn;
    }

	/**
     * @param string $referenceColumn
     */
    public function setReferenceColumn($referenceColumn)
    {
        $this->referenceColumn = $referenceColumn;
    }

	/**
     * @return the $relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

	/**
     * @param string $relation
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;
    }

}