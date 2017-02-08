<?php
/**
 * User: ralf
 * Date: 02.01.17
 * Time: 13:05
 */

namespace ZfcExplore\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExploreManagerService implements FactoryInterface{

 /**
 * Create service
 *
 * @param ServiceLocatorInterface $serviceLocator
 * @return mixed
 */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, 'ZfcExplore\ExploreManager');
    }


    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * TODO Validate
         */

        //Database Adapter
        $db = $container->get('Zend\Db\Adapter\Adapter');

        $manager = new $requestedName();
        $manager->setDbAdapter($db);

        //Config
        $option = $container->get('config')['explore'];
        foreach ($option as $name => $table){
            $manager->attachTable($name, $table);
        }
        return $manager;
    }
}