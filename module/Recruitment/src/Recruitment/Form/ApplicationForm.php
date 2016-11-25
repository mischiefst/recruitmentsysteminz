<?php
/**
 * class  ApplicationForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Recruitment\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class ApplicationForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('recruitment');

        $this->setAttributes(array(
            'action' => 'apply',
            'method' => 'post',
            'class' => 'form-inline'
        ));

        $this->add(array(
            'name' => 'idApplication',
            'type' => 'Hidden',
        ));


        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Aplikuj',
                'id' => 'submitbutton',
            ),
        ));
    }
}