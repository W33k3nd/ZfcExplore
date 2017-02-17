<?php
namespace ZfcExplore\Decorator\Methodes;

use Zend\Stdlib\StringWrapper\MbString;
use Zend\Stdlib\StringWrapper\Iconv;
use Zend\Stdlib\StringWrapper\AbstractStringWrapper;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
class Convert extends AbstractMethod{
    
    /**
     * 
     * @var string
     */
    private $encode = 'UTF-8';
    
    /**
     * ISO-Standard
     * @var string 
     */
    private $decode;
    
    /**
     * 
     * @var AbstractStringWrapper
     */
    private $wrapper;
    
    
    /**
     * 
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function __construct($options){
        
        parent::__construct($options);
        if(MbString::isSupported($this->encode, $this->decode)){
            $this->wrapper = new MbString();
        } elseif (Iconv::isSupported($this->encode, $this->decode)){
            $this->wrapper = new Iconv();
        } else {
            throw new InvalidArgumentException('No supported decode -> encode ['.$this->decode.'->'.$this->encode.']');
        }
        
        $this->wrapper->setEncoding($this->decode, $this->encode);
    }
    
    
    
	/* (non-PHPdoc)
     * @see \ZfcExplore\Decorator\Methodes\MethodInterface::getValue()
     */
    public function getValue(){
        return $this->wrapper->convert($this->getActualRow()[$this->getName()]);
    }

    /**
     * 
     * @param string $encode
     */
    public function setEncode($encode){
        $this->encode = $encode;
    }
    
    /**
     * 
     * @param string $decode
     */
    public function setDecode($decode){
        $this->decode = $decode;
    }
    
}