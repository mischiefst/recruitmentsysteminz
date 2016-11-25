<?php
/**
 * class ContactForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class ContactForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('');

        $this->setAttributes(array(
            'action' => '',
            'method' => 'post',
            'class' => 'form-horizontal',
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Tytuł*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'person',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Imię i Nazwisko*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));


        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Adres e-mail*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));


        $this->add(array(
            'name' => 'text',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 315px',
                'rows' => 8,
                'required' => true,
            ),
            'options' => array(
                'label' => 'Treść maila*:',
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
                'value' => 'Wyślij wiadomość',
                'id' => 'submitbutton',
            ),
        ));
    }
}