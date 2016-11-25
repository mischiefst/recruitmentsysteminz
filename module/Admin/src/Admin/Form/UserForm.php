<?php
/**
 * class DataForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UserForm extends Form
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
            'name' => 'idUser',
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
                'label' => 'Imię',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'surname',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Nazwisko',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));
        $this->add(array(
            'name' => 'login',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Login',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));


        $this->add(array(
            'name' => 'gender',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => array(
                'class' => 'radio-inline',
                'required' => true,
                'value' => 'M'
            ),
            'options' => array(
                'label' => 'Płeć',
                'label_attributes' => array(
                    'class' => 'radio-inline col-md-4 control-label ',
                ),
                'value_options' => array(
                    'M' => 'Mężczyzna',
                    'K' => 'Kobieta',
                ),

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
                'label' => 'Adres e-mail',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'birthday_date',
            'type' => 'Date',
            'attributes' => array(
                'class' => 'form-control input-md',
            ),
            'options' => array(
                'label' => 'Data urodzenia',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));


        //WALIDACJA
        $this->add(array(
            'name' => 'phone',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control input-md',
            ),
            'options' => array(
                'label' => 'Numer telefonu',
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