<?php
/**
 * class SolveForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Test\Form;

use Zend\Db\Sql\Sql;
use Zend\Form\Form;
use Zend\Form\Element;

class SolveForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('test');

        $this->setAttributes(array(
            'action' => 'solve',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));

        $this->add(array(
            'name' => 'idTest',
            'type' => 'Hidden',
        ));


        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Utwórz',
                'id' => 'submitbutton',
            ),
        ));
    }
}