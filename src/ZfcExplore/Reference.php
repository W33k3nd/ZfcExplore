<?php
/**
 * Created by PhpStorm.
 * User: ralf
 * Date: 05.01.17
 * Time: 14:11
 */

namespace ZfcExplore;


use Zend\Stdlib\AbstractOptions;

class Reference extends AbstractOptions
{
    const ONETOONE  = 'OneToOne';
    const MANYTOONE = 'ManyToOne';


    /**
     * @return mixed
     */
    public function getTable(){
        return $this->table;
    }
}