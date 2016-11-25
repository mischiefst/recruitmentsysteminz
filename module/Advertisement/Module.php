<?php
/**
 * class Module
 *
 * User: Lukasz Marszalek
 * Date: 2016-08-30
 * Time: 19:21
 */
namespace Advertisement;

use Advertisement\Model\Advertisement;
use Advertisement\Model\AdvertisementTable;
use Advertisement\Model\Skill;
use Advertisement\Model\SkillAdvertisement;
use Advertisement\Model\SkillAdvertisementTable;
use Advertisement\Model\SkillTable;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;


use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Mvc\MvcEvent;

use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;

/**
 * Class Module
 * @package Advertisement
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
                'Advertisement\Model\SkillTable' => function ($sm) {
                    $tableGateway = $sm->get('SkillTableGateway');
                    $table = new SkillTable($tableGateway);
                    return $table;
                },
                'SkillTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Skill());
                    return new TableGateway('skill', $dbAdapter, null, $resultSetPrototype);
                },
                'Advertisement\Model\AdvertisementTable' => function ($sm) {
                    $tableGateway = $sm->get('AdvertisementTableGateway');
                    $table = new AdvertisementTable($tableGateway);
                    return $table;
                },
                'AdvertisementTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Advertisement());
                    return new TableGateway('advertisement', $dbAdapter, null, $resultSetPrototype);
                },
                'Advertisement\Model\SkillAdvertisementTable' => function ($sm) {
                    $tableGateway = $sm->get('SkillAdvertisementTableGateway');
                    $table = new SkillAdvertisementTable($tableGateway);
                    return $table;
                },
                'SkillAdvertisementTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SkillAdvertisement());
                    return new TableGateway('skill_advertisement', $dbAdapter, null, $resultSetPrototype);
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