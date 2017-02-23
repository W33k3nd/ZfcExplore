<?php

namespace ZfcExplore\Decorator\Methodes;

class StringToLower extends AbstractMethod{
    
	/* (non-PHPdoc)
     * @see \ZfcExplore\Decorator\Methodes\MethodInterface::getValue()
     */
    public function getValue()
    {
        return strtolower($this->col->getActualName());        
    }

    
}