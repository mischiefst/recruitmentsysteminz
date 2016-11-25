<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-17
 * Time: 15:16
 */

namespace Account\Form;


use Zend\Form\Form;
use Zend\Form\Element;

class EducationForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('education');

        $this->setAttributes(array(
            'action' => 'addEducation',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));

        $this->add(array(
            'name' => 'idEducation',
            'type' => 'Hidden',
        ));


        $this->add(array(
            'name' => 'school_name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Nazwa szkoły*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
            ),
            'options' => array(
                'label' => 'Opcjonalny opis',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'date_from',
            'type' => 'Date',
            'attributes'=> array(
                'class' => 'form-control input-md',
            ),
            'options' => array(
                'label' => 'Data od*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'date_to',
            'type' => 'Date',
            'attributes'=> array(
                'class' => 'form-control input-md',
            ),
            'options' => array(
                'label' => 'Data do*',
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
                'value' => 'Dodaj',
                'id' => 'submitbutton',
            ),
        ));

    }
}