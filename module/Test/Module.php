<?php
/**
 * class Module
 *
 * User: Lukasz Marszalek
 * Date: 2016-08-30
 * Time: 19:21
 */
namespace Test;

use Test\Model\Answer;
use Test\Model\AnswerTable;
use Test\Model\Choice;
use Test\Model\ChoiceTable;
use Test\Model\Question;
use Test\Model\QuestionTable;
use Test\Model\QuestionTest;
use Test\Model\QuestionTestTable;
use Test\Model\Test;
use Test\Model\TestTable;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;


use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Mvc\MvcEvent;

use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;

/**
 * Class Module
 * @package Test
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
                'Test\Model\TestTable' => function ($sm) {
                    $tableGateway = $sm->get('TestTableGateway');
                    $table = new TestTable($tableGateway);
                    return $table;
                },
                'TestTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Test());
                    return new TableGateway('test', $dbAdapter, null, $resultSetPrototype);
                },

                'Test\Model\AnswerTable' => function ($sm) {
                    $tableGateway = $sm->get('AnswerTableGateway');
                    $table = new AnswerTable($tableGateway);
                    return $table;
                },
                'ChoiceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Choice());
                    return new TableGateway('choice', $dbAdapter, null, $resultSetPrototype);
                },

                'Test\Model\ChoiceTable' => function ($sm) {
                    $tableGateway = $sm->get('ChoiceTableGateway');
                    $table = new ChoiceTable($tableGateway);
                    return $table;
                },
                'AnswerTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Answer());
                    return new TableGateway('answer', $dbAdapter, null, $resultSetPrototype);
                },

                'Test\Model\QuestionTable' => function ($sm) {
                    $tableGateway = $sm->get('QuestionTableGateway');
                    $table = new QuestionTable($tableGateway);
                    return $table;
                },
                'QuestionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Question());
                    return new TableGateway('question', $dbAdapter, null, $resultSetPrototype);
                },

                'Test\Model\QuestionTestTable' => function ($sm) {
                    $tableGateway = $sm->get('QuestionTestTableGateway');
                    $table = new QuestionTestTable($tableGateway);
                    return $table;
                },
                'QuestionTestTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new QuestionTest());
                    return new TableGateway('question_test', $dbAdapter, null, $resultSetPrototype);
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