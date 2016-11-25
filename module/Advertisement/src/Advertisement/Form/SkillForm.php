<?php
/**
 * class SkillForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Advertisement\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class SkillForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('advertisement');

        $this->setAttributes(array(
            'action' => 'add',
            'method' => 'post',
            'class' => 'form-inline'
        ));

        $this->add(array(
            'name' => 'idSkill',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Nazwa umiejętności',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Dodaj',
                'id' => 'submitbutton',
            ),
        ));
    }
}