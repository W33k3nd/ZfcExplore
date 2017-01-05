<?php

namespace ZfcExplore\Table;

use Zend\Stdlib\ArrayObject;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Db\Adapter\AdapterAwareInterface;

/**
 * Class GridManager
 * @package ZfcExplore\Table
 * @deprecated
 */
class GridManager implements EventManagerAwareInterface, AdapterAwareInterface{


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
	 * @param  EventManagerInterface $events
	 * @return mixed
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
	 * @see execute all tables
	 */
	public function executeMode(){

		if(!$this->tables->count())
			return;

		foreach ($this->tables as $table){

			$table->executeMode();
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
	 * @param array | CsvTable $table
	 */
	public function attachTable($name, $table){

		if(is_array($table)){
			$options = new CsvOptions($table);
			$table = new CsvTable($this->adapter, $options);
		}

		if($table instanceof CsvTable){
			$this->tables->offsetSet($name, $table);
		}

	}

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @return $this
     */
	public function setDbAdapter(\Zend\Db\Adapter\Adapter $adapter) {
		// TODO Auto-generated method stub
		$this->adapter = $adapter;
		return $this;

	}

	/**
	 *
	 * @param string $name
	 * @return \krCsvTable\CsvTable\CsvTable | bool
	 */
	public function getTable($name){

		if($this->tables->offsetExists($name))
			return $this->tables[$name];

		return FALSE;
	}

	/**
	 * remove all tables;
	 */
	public function clear(){

		$this->tables = new ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
	}

}
