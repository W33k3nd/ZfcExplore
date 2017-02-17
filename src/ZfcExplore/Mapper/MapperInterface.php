<?php

namespace ZfcExplore\Mapper;

use ZfcExplore\ActualRow;
interface MapperInterface{
    
    public function map(ActualRow $actualRow);
}