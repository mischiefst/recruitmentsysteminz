<?php
/**
 * class EditForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Test\Form;

use Zend\Db\Sql\Sql;
use Zend\Form\Form;
use Zend\Form\Element;

class EditForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('test');

        $this->setAttributes(array(
            'action' => '',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));

        $this->add(array(
            'name' => 'idTest',
            'type' => 'Hidden',
        ));


        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Nazwa testu*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'form-control myTextEditor',
                'rows' => 8,
            ),
            'options' => array(
                'label' => 'Opis testu',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'time',
            'type' => 'number',
            'attributes' => array(
                'class' => 'form-control input-md',
                'min' => 0,
                'step' => 5,
            ),
            'options' => array(
                'label' => 'Czas wykonania',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Hasło dostępu*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Zapisz zmiany',
                'id' => 'submitbutton',
            ),
        ));
    }
}