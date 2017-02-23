<?php

namespace ZfcExploreTest;

use ZfcExplore\ActualRow;
class ActualRowTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * 
     * @var ActualRow
     */
    private $actualRow;
    
    /**
     * setUp
     */
    public function setUp(){
        
        $this->actualRow = new ActualRow();
        parent::setUp();
    }
    

    
    public function testInitActualRow(){
        
        $columns = ['id' => 0, 'name' =>1, 'age'=>2, 'zip'=>3, 'street' => NULL, ];
        $map = [];
        foreach ($columns as $name => $index){
            $this->actualRow->addColumn($name, $index);
            if(isset($columns[$name])){
                $map[] = $name;
            }
        }
        
        $name = array_keys($columns);
        $this->assertEquals($name, array_keys($this->actualRow->getMap()));
        $this->assertEquals($map, $this->actualRow->getIntersectColumns());
        
        $row = [1234, 'Anne', 12, 56782];
        $this->actualRow->setActualRow($row);
        
        $this->assertEquals($row, $this->actualRow->getIndexData());
        $this->assertEquals(['id'=>1234, 'name'=>'Anne', 'age'=>12, 'zip'=>56782], $this->actualRow->getColumnData());
        
    }
}