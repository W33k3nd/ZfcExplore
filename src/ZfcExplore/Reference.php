<?php
/**
 * Created by PhpStorm.
 * User: ralf
 * Date: 05.01.17
 * Time: 14:11
 */

namespace ZfcExplore;


use Zend\Stdlib\AbstractOptions;
use Zend\Db\TableGateway\AbstractTableGateway;

class Reference
{
    
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;
    
    const ONETOONE  = 'OneToOne';
    const MANYTOONE = 'ManyToOne';

    /**
     * REFERENCE_TYPE_TABLE: Die Tabellendate existieren bereits, es muss 
     * lediglich die Id anhand der Such-Parameter gefunden werden.
     * 
     * REFERENCE_TYPE_IMPORT: Die Tabellendaten müssen noch importiert werden.
     * 
     */
    const REFERENCE_TYPE_TABLE = 2;
    const REFERENCE_TYPE_IMPORT = 4;

    /**
     * This detected the real ID from the Table
     * Index: Column number from data file
     * Value: fix own value
     * @var array
     */
    private $primary_detection = array();
    
    /**
     * 
     * @var AbstractTableGateway
     */
    private $table;
    
    /**
     * @default REFERENCE_TYPE_TABLE 
     * @var int
     */
    private $type = self::REFERENCE_TYPE_TABLE;
    
    
    
    public function __construct(AbstractTableGateway $table){
        
        $this->table = $table;
    }

    /**
     * @return AbstractTableGateway
     */
    public function getTable(){
        return $this->table;
    }
    
    /**
     * 
     * @param unknown $table
     */
    public function setTable(AbstractTableGateway $table){
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function getPrimaryDetection(){
        return $this->primary_detection;
    }
    
    /**
     * 
     * @param array $detected
     */
    public function setPrimaryDetection(array $detected){
        $this->primary_detection = $detected;
    }

}