<?php
/**
 * class Module
 *
 * User: Lukasz Marszalek
 * Date: 2016-08-30
 * Time: 19:21
 */
namespace Account;

use Account\Model\Education;
use Account\Model\EducationTable;
use Account\Model\Experience;
use Account\Model\ExperienceTable;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

use Account\Model\User;
use Account\Model\UserTable;
use Account\Model\Address;
use Account\Model\AddressTable;

use View\Helper\Helper;


use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Mvc\MvcEvent;

use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;

/**
 * Class Module
 * @package Account
 */
class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Account\Model\AuthStorage' => function ($sm) {
                    return new \Account\Model\AuthStorage('user');
                },

                'AuthServiceUser' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter,
                        'user', 'login', 'password', 'md5(?)');

                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Account\Model\AuthStorage'));

                    return $authService;
                },

                'Account\Model\UserTable' => function ($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },

                'Account\Model\AddressTable' => function ($sm) {
                    $tableGateway = $sm->get('AddressTableGateway');
                    $table = new AddressTable($tableGateway);
                    return $table;
                },
                'AddressTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Address());
                    return new TableGateway('address', $dbAdapter, null, $resultSetPrototype);
                },

                'Account\Model\EducationTable' => function ($sm) {
                    $tableGateway = $sm->get('EducationTableGateway');
                    $table = new EducationTable($tableGateway);
                    return $table;
                },
                'EducationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Education());
                    return new TableGateway('education', $dbAdapter, null, $resultSetPrototype);
                },
                'Account\Model\ExperienceTable' => function ($sm) {
                    $tableGateway = $sm->get('ExperienceTableGateway');
                    $table = new ExperienceTable($tableGateway);
                    return $table;
                },
                'ExperienceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Experience());
                    return new TableGateway('experience', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }


    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $adapter = $sm->get('Zend\Db\Adapter\Adapter');
        \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($adapter);
    }


    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'test_helper' => function ($sm) {
                    $helper = new View\Helper\Helper;
                    return $helper;
                }
            )
        );
    }
}