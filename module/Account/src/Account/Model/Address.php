<?php
/**
 * class Address
 * User: Lukasz Marszalek
 * Date: 2016-08-31
 * Time: 21:14
 */
namespace Account\Model;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\AbstractDb;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class Address
{
    public $idAddress;

    public $street;
    public $house_number;
    public $flat_number;
    public $postal_code;
    public $city;
    protected $inputFilter;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idAddress = (!empty($data['idAddress'])) ? $data['idAddress'] : null;
        $this->street = (!empty($data['street'])) ? $data['street'] : null;
        $this->house_number = (!empty($data['house_number'])) ? $data['house_number'] : null;
        $this->flat_number = (!empty($data['flat_number'])) ? $data['flat_number'] : null;
        $this->postal_code = (!empty($data['postal_code'])) ? $data['postal_code'] : null;
        $this->city = (!empty($data['city'])) ? $data['city'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @return InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'idAddress',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'street',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Ulica nie może być pusta.',
                            ),
                        ),

                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Ulica jest za krótka. Minimum 1 znak.',
                                StringLength::TOO_LONG => 'Ulica jest za długa. Maksymalnie 50 znaków.',
                            ),
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'house_number',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Numer domu nie może być pusty.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 5,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Numer domu jest za krótki. Minimum 1 znak.',
                                StringLength::TOO_LONG => 'Numer domu jest za długi. Maksymalnie 5 znaków.',
                            ),
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'flat_number',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 5,
                            'messages' => array(
                                StringLength::TOO_LONG => 'Numer mieszkania jest za długi. Maksymalnie 5 znaków.',
                            ),
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'postal_code',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Kod pocztowy nie może być pusty.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([0-9]{2})(-[0-9]{3})?$/i',
                            'messages' => array(
                                Regex::NOT_MATCH => 'Błędny format kodu pocztowego.',
                            ),
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'city',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Miasto nie może być puste.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 30,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Miasto jest za krótkie. Minimum 1 znak.',
                                StringLength::TOO_LONG => 'Miasto jest za długie. Maksymalnie 30 znaków.',
                            ),
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }


}