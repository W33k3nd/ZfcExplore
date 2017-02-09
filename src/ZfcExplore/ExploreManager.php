<?php
/**
 * Created by PhpStorm.
 * User: ralf
 * Date: 03.01.17
 * Time: 15:06
 */

namespace ZfcExplore;

use Zend\Stdlib\ArrayObject;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Db\Adapter\AdapterAwareInterface;


class ExploreManager implements EventManagerAwareInterface, AdapterAwareInterface
{

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Zend\Stdlib\ArrayObject
     */
    private $tables;

    /**
     *
     * @var \Zend\Db\Adapter\Adapter
     */
    private $adapter;

    /**
     *
     */
    public function __construct(){

        $this->clear();

    }

    /**
     * Set the event manager instance used by this context
     *
     * @param EventManagerInterface $eventManager
     * @return mixed
     * @internal param EventManagerInterface $events
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(get_called_class()));

        $this->eventManager = $eventManager;
    }

    /**
     * Retrieve the event manager
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->eventManager instanceof EventManagerInterface) {

            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }

    /**
     * @see execute all|selected tables
     * @param array|string $tables
     */
    public function executeMode($tables = array()){

        if(!$this->tables->count())
            return;

        if(empty($tables)){

            $tables = $this->tables;
        }
        elseif (is_string($tables)){

            $tables = $this->getTable($tables);
            $tables->executeMode();
            return;
        }
        elseif (is_array($tables) && !empty($tables)){

            $tables = array_flip($tables);
            foreach ($tables as $table => $key){
                $tables[$table] = $this->getTable($table);
            }
        }

        /* @var $table Explore */
        foreach ($tables as $table){

            if($table->hasReferences()){

                foreach ($table->getOption()->getReferences() as $ref){
                    $refTable = $this->getTable($ref->getTable());
                }
            }
            $table->executeMode();
        }
    }

    /**
     * This method execute an Explore
     *
     * @param Explore $explore
     */
    private function executeTable(Explore $explore){

        if($explore->hasReferences()){
            foreach ($explore->getReferences() as $reference){

            }
        }
    }

    /**
     * @param string $name
     */
    public function detachTable($name){

        if($this->tables->offsetExists($name))
            $this->tables->offsetUnset($name);
    }

    /**
     * Add new Csv File
     * @param string $name
     * @param array | Explore $table
     */
    public function attachTable($name, $table){

        if(is_array($table)){
            $options = new Options($table);
            $table = new Explore($this->adapter, $options);
        }

        if($table instanceof Explore){
            $this->tables->offsetSet($name, $table);
        }

    }


    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @return \Zend\Db\Adapter\Adapter
     */
    public function setDbAdapter(\Zend\Db\Adapter\Adapter $adapter) {
        $this->adapter = $adapter;
        return $this->adapter;
    }

    /**
     * @param $name
     * @return Explore
     * @throws \Exception
     */
    public function getTable($name){

        if(!$this->tables->offsetExists($name)){
            throw new \Exception(sprintf('Tablename %s not found', $name));
        }

        return $this->tables[$name];
    }

    /**
     * remove all tables;
     */
    public function clear(){

        $this->tables = new ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
    }
}