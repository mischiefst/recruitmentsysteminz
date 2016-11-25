<?php
/**
 * class LoginForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('account');

        $this->setAttributes(array(
            'action' => 'login',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));

        $this->add(array(
            'name' => 'idUser',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'login',
            'type' => 'Text',
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
            'name' => 'password',
            'type' => 'Password',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Hasło',
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
                'value' => 'Zaloguj',
                'id' => 'submitbutton',
            ),
        ));
    }
}