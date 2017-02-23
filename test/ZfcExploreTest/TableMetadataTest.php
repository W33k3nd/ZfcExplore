<?php

namespace ZfcExploreTest;

use ZfcExplore\TableMetadata;
class TableMetadataTest extends \PHPUnit_Framework_TestCase{
    
    private $tablemetaoptions =[
        'id' => array('knr', 'dezent'),
        'table'=>'dozent',
        'path' => '/../../data/explore/dozent.asc',
        'mode' => \ZfcExplore\Explore::UPDATEMODE,
        'where' => 'knr = 165',
        'heel_clear' => true,
        'columns' => array(
            array('index'=>0, 'name'=>'knr'),
            array('index'=>1, 'name'=>'dezent'),
            array('index'=>2, 'name'=>'nachname', 'methods'=>array(['name'=>'convert', 'options'=>array('decode'=>'ISO-8859-15')])),
            array('index'=>3, 'name'=>'vorname', 'method'=>array('name'=>'convert', 'options'=>array('decode'=>'ISO-8859-15'))),
            array('index'=>4, 'name'=>'geschlecht', 'method'=>['name'=>'stringtolower']),
            array('name'=>'id')
        )];
    
    
    public function testConstructTableMetadata(){
        
        $stub = $this->getMockBuilder('ZfcExplore\ExploreManager')->getMock();
        $meta = new TableMetadata($stub, $this->tablemetaoptions);
    }
}