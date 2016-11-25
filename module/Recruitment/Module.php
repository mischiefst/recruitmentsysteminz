<?php
/**
 * class Module
 *
 * User: Lukasz Marszalek
 * Date: 2016-08-30
 * Time: 19:21
 */
namespace Recruitment;

use Recruitment\Model\Application;
use Recruitment\Model\ApplicationTable;
use Recruitment\Model\Result;
use Recruitment\Model\ResultTable;
use Recruitment\Model\SkillApplication;
use Recruitment\Model\SkillApplicationTable;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;


use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Mvc\MvcEvent;

use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;

/**
 * Class Module
 * @package Recruitment
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
                'Recruitment\Model\ApplicationTable' => function ($sm) {
                    $tableGateway = $sm->get('ApplicationTableGateway');
                    $table = new ApplicationTable($tableGateway);
                    return $table;
                },
                'ApplicationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application());
                    return new TableGateway('application', $dbAdapter, null, $resultSetPrototype);
                },
                'Recruitment\Model\ResultTable' => function ($sm) {
                    $tableGateway = $sm->get('ResultTableGateway');
                    $table = new ResultTable($tableGateway);
                    return $table;
                },
                'ResultTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Result());
                    return new TableGateway('result', $dbAdapter, null, $resultSetPrototype);
                },
                'Recruitment\Model\SkillApplicationTable' => function ($sm) {
                    $tableGateway = $sm->get('SkillApplicationTableGateway');
                    $table = new SkillApplicationTable($tableGateway);
                    return $table;
                },
                'SkillApplicationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SkillApplication());
                    return new TableGateway('skill_application', $dbAdapter, null, $resultSetPrototype);
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

}