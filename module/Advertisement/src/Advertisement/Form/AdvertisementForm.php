<?php
/**
 * class AdvertisementForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Advertisement\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class AdvertisementForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('advertisement');

        $this->setAttributes(array(
            'action' => 'create',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));

        $this->add(array(
            'name' => 'idAdvertisement',
            'type' => 'Hidden',
        ));


        $this->add(array(
            'name' => 'job_title',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Stanowisko - tytuł ogłoszenia*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'text',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'form-control myTextEditor',
                'rows' => 8,
            ),
            'options' => array(
                'label' => 'Treść ogłoszenia',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));


        $this->add(array(
            'name' => 'skills',
            'type' => 'text',
            'options' => array(
                'label' => 'Wymagane umiejętności*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                ),
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'tags'
            ),
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