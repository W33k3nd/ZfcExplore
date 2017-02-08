<?php
namespace ZfcExplore\Decorator\Methodes;

class Concat extends AbstractMethod{
    
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
    
    public function __construct(){
        
        //todo: valid php extension is installed
    }
    
    
    
	/* (non-PHPdoc)
     * @see \ZfcExplore\Decorator\Methodes\MethodInterface::getValue()
     */
    public function getValue()
    {
        $value = $this->actualRow[$this->index];
        if(is_numeric($value) || mb_check_encoding($value, $this->decode))
			return $value;

	    return mb_convert_encoding($value, $this->encode, $this->decode);
        
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