<?php
namespace ZfcExplore\Decorator\Methodes;


class Callback extends AbstractMethod{

    /**
     * 
     * @var \Closure
     */
    private $callback;
    
	/* (non-PHPdoc)
     * @see \ZfcExplore\Decorator\Methodes\MethodInterface::getValue()
     */
    public function getValue(){
        return call_user_func($this->callback, $this->getActualRow());
    }
    
    /**
     * @param \Closure
     */
    public function setCallback($callback){
        $this->callback = $callback;
    }
}