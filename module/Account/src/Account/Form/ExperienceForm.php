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

class ExperienceForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('experience');

        $this->setAttributes(array(
            'action' => 'addExperience',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));

        $this->add(array(
            'name' => 'idExperience',
            'type' => 'Hidden',
        ));


        $this->add(array(
            'name' => 'company_name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Nazwa firmy*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'job_title',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Stanowisko*',
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