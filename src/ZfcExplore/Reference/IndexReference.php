<?php

namespace ZfcExplore\Reference;

use Zend\Db\ResultSet\AbstractResultSet;
class IndexReference extends AbstractReference{
    
    /**
     * 
     * @var array
     */
    private $index;
    
	/* (non-PHPdoc)
     * @see \ZfcExplore\Reference\ReferenceInterface::refer()
     * @return array | false
     */
    public function refer(\ZfcExplore\ActualRow $actualRow)
    {
        
        $cur = [];
        foreach ($this->index as $key => $name){
            $cur[$name] = $actualRow->getIndex($key);
        }
        
        $match = false;
        foreach ($this->referenceData as $row){
            
            foreach ($cur as $name => $value){
                
                if($row[$name] != $value){
                    continue 2;
                }
            }
            $match = $row[$this->referenceColumn];
            break;
        }
        
        return $match;
    }

    /**
     * @return array
     */
    public function getIndex()
    {
        return $this->index;
    }

	/**
     * @param multitype: $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

	/**
     * @param AbstractResultSet $referenceData
     */
    public function setReferenceData(AbstractResultSet $referenceData)
    {
        parent::setReferenceData($referenceData);
        
        if(!$this->hasData){
            return ;
        }
        
        $current = $this->referenceData->current();
        
        if(is_object($current)){
            $current = get_object_vars($current);
        } elseif (is_array($current)){
            $current = array_keys($current);
        }
        
        $index = $this->index;
        $index[-1] = $this->referenceColumn;
        foreach ($index as $key){
            // + performance
            if(isset($current[$key]) || array_key_exists($key, $current)){
                continue;
            }
            throw new \Exception(sprintf('Column name "%s" does not exist in result. Given column [%s]', $key, implode(', ', array_keys($current))));
        }
    }
    
    
}