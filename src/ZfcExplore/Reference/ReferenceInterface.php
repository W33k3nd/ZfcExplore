<?php

namespace ZfcExplore\Reference;

use ZfcExplore\ActualRow;
interface ReferenceInterface{
    
    const ONETOONE  = 'OneToOne';
    const MANYTOONE = 'ManyToOne';
    
    /**
     * 
     * @param ActualRow $actualRow
     */
    public function refer(ActualRow $actualRow);
}