<?php
/**
 * class AddressForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class AddressForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('account');

        $this->setAttributes(array(
            'action' => 'editaddress',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));

        $this->add(array(
            'name' => 'idAddress',
            'type' => 'Hidden',
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
                'label' => 'Ulica',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'house_number',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Numer domu',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'flat_number',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
            ),
            'options' => array(
                'label' => 'Numer mieszkania',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));


        $this->add(array(
            'name' => 'postal_code',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Kod pocztowy',
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
                'label' => 'Miasto',
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
                'value' => 'Zmień adres',
                'id' => 'submitbutton',
            ),
        ));
    }
}