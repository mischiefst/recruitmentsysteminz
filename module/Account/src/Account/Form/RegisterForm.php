<?php
/**
 * class RegisterForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class RegisterForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('account');

        $this->setAttributes(array(
            'action' => 'register',
            'method' => 'post',
            'class' => 'form-horizontal'
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
                'label' => 'Imię*',
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
                'label' => 'Nazwisko*',
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
                'label' => 'Login*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Hasło*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'passwordRepeat',
            'type' => 'password',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Powtórz hasło*',
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
                'label' => 'Płeć*',
                'label_attributes' => array(
                    'class' => 'radio-inline col-md-4 control-label gender-label ',
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
                'label' => 'Adres e-mail*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'birthday_date',
            'type' => 'Date',
            'attributes'=> array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Data urodzenia*',
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
                'label' => 'Numer telefonu*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        /** ADDRESS DATA */

        $this->add(array(
            'name' => 'street',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Ulica*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'house_number',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md col-md-5',
//                'style' => 'width: 50%',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Numer domu*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label ',
                )
            ),
        ));

        $this->add(array(
            'name' => 'flat_number',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md col-xs-6',
                'style' => 'width: 60%',
            ),
            'options' => array(
                'label' => 'Numer mieszkania',
                'label_attributes' => array(
                    'class' => 'col-md-2 control-label ',
                    'style' => 'width: 14%',
                )
            ),
        ));


        $this->add(array(
            'name' => 'postal_code',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
                'style' => 'width: 70%',

            ),
            'options' => array(
                'label' => 'Kod pocztowy*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'city',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Miasto*',
                'label_attributes' => array(
                    'class' => 'col-md-2 control-label',
                    'style' => 'width: 3%; margin-left: -60px;',

                )
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Zarejestruj',
                'id' => 'submitbutton',
            ),
        ));
    }
}