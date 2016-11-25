<?php
/**
 * class User
 * User: Lukasz Marszalek
 * Date: 2016-08-31
 * Time: 21:14
 */
namespace Account\Model;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Date;
use Zend\Validator\Db\AbstractDb;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class User
{
    public $idUser;
    public $name;
    public $surname;
    public $gender;
    public $email;
    public $birthday_date;
    public $phone;
    public $login;
    public $password;
    public $rule;
    public $date_registration;
    public $idAddress;

    protected $inputFilter;


    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idUser = (!empty($data['idUser'])) ? $data['idUser'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->surname = (!empty($data['surname'])) ? $data['surname'] : null;
        $this->gender = (!empty($data['gender'])) ? $data['gender'] : 'm';
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->birthday_date = (!empty($data['birthday_date'])) ? $data['birthday_date'] : null;
        $this->phone = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->login = (!empty($data['login'])) ? $data['login'] : null;
        $this->password = (!empty($data['password'])) ? (preg_match('/^[a-f0-9]{32}$/', $data['password']) ? $data['password'] : md5($data['password'])) : null;
        $this->rule = (!empty($data['rule'])) ? $data['rule'] : 1;
        $this->date_registration = (!empty($data['date_registration'])) ? $data['date_registration'] : date('Y-m-d');
        $this->idAddress = (!empty($data['Address_idAddress'])) ? $data['Address_idAddress'] : null;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }


    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }


    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'idUser',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            if (is_null($this->idUser)) {
                $inputFilter->add(array(
                    'name' => 'login',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 5,
                                'max' => 20,
                                'messages' => array(
                                    StringLength::TOO_SHORT => 'Nazwa użytkownika jest za krótka. Minimum 5 znaków.',
                                    StringLength::TOO_LONG => 'Nazwa użytkownika jest za długa. Maksymalnie 20 znaków.',
                                ),
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'Nazwa użytkownika nie może być pusta.',
                                ),
                            ),

                        ),
                        array(
                            'name' => 'Db\NoRecordExists',
                            'options' => array(
                                'table' => 'user',
                                'field' => 'login',
                                'adapter' => \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter(),
                                'messages' => array(
                                    AbstractDb::ERROR_RECORD_FOUND => 'Użytkownik o tej nazwie już istnieje w bazie.',
                                )
                            ),
                        ),

                    ),
                ));
            }


            $inputFilter->add(array(
                'name' => 'password',
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
                                NotEmpty::IS_EMPTY => 'Hasło nie może być puste.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 6,
                            'max' => 50,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Hasło jest za krótkie. Minimum 6 znaków.',
                                StringLength::TOO_LONG => 'Hasło jest za długie. Maksymalnie 50 znaków.',
                            ),
                        ),
                    ),

                ),
            ));
            $inputFilter->add(array(
                'name' => 'passwordRepeat',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password',
                            'messages' => array(
                                Identical::NOT_SAME => 'Hasła nie są identyczne',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Hasło nie może być puste.',
                            ),
                        ),
                    ),

                ),
            ));

            $inputFilter->add(array(
                'name' => 'name',
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
                                NotEmpty::IS_EMPTY => 'Imię nie może być puste.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 30,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Imię jest za krótkie. Minimum 3 znaki.',
                                StringLength::TOO_LONG => 'Imię jest za długie. Maksymalnie 30 znaków.',
                            ),
                        ),
                    ),

                ),
            ));

            $expr = '/^\+?(\(?[0-9]{3}\)?|[0-9]{3})[-\.\s]?[0-9]{3}[-\.\s]?[0-9]{4}$/';

            $inputFilter->add(array(
                'name' => 'phone',
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
                                NotEmpty::IS_EMPTY => 'Numer telefonu nie może być pusty.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]*$/',
                            'messages' => array(
                                Regex::NOT_MATCH => 'Błędny format numeru telefonu.',
                            ),
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'surname',
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
                                NotEmpty::IS_EMPTY => 'Nazwisko nie może być puste.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 30,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Nazwisko jest za krótkie. Minimum 3 znaki.',
                                StringLength::TOO_LONG => 'Nazwisko jest za długie. Maksymalnie 30 znaków.',
                            ),
                        ),
                    ),

                ),
            ));

            $inputFilter->add(array(
                'name' => 'birthday_date',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'messages' => array(
                                Date::INVALID_DATE => 'Niepoprawny format daty urodzin.',
                            ),
                        ),

                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Email nie może być pusty.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'EmailAddress',
                        'options' => array(
                            'messages' => array(
                                EmailAddress::INVALID => 'Zły adres email. (np. nazwa@host.pl))',
                                EmailAddress::INVALID_FORMAT => 'Zły adres email. (np. nazwa@host.pl))',
                            ),
                        ),
                    ),

                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/',
                            'messages' => array(
                                Regex::NOT_MATCH => 'Zły format email.',
                            ),
                        ),
                    ),

                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }


    public function getInputFilterEdit()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'idUser',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'name',
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
                                NotEmpty::IS_EMPTY => 'Imię nie może być puste.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 30,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Imię jest za krótkie. Minimum 3 znaki.',
                                StringLength::TOO_LONG => 'Imię jest za długie. Maksymalnie 30 znaków.',
                            ),
                        ),
                    ),

                ),
            ));
            $inputFilter->add(array(
                'name' => 'surname',
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
                                NotEmpty::IS_EMPTY => 'Nazwisko nie może być puste.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 30,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Nazwisko jest za krótkie. Minimum 3 znaki.',
                                StringLength::TOO_LONG => 'Nazwisko jest za długie. Maksymalnie 30 znaków.',
                            ),
                        ),
                    ),

                ),
            ));

            $inputFilter->add(array(
                'name' => 'birthday_date',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'messages' => array(
                                Date::INVALID_DATE => 'Niepoprawny format daty urodzin.',
                            ),
                        ),

                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Email nie może być pusty.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'EmailAddress',
                        'options' => array(
                            'messages' => array(
                                EmailAddress::INVALID => 'Zły adres email. (np. nazwa@host.pl))',
                                EmailAddress::INVALID_FORMAT => 'Zły adres email. (np. nazwa@host.pl))',
                            ),
                        ),
                    ),

                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/',
                            'messages' => array(
                                Regex::NOT_MATCH => 'Zły format email.',
                            ),
                        ),
                    ),

                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }


    public function getRegisterInputFilter()
    {
        $registerInputFilter = $this->getInputFilter();

        $registerInputFilter->add(array(
            'name' => 'idUser',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $registerInputFilter->add(array(
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
        $registerInputFilter->add(array(
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
        $registerInputFilter->add(array(
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

        $registerInputFilter->add(array(
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
        $registerInputFilter->add(array(
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

        return $registerInputFilter;
    }


    public function getLoginInputFilter()
    {

        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'idUser',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'login',
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
                                NotEmpty::IS_EMPTY => 'Nazwa użytkownika nie może być pusta.',
                            ),
                        ),

                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'password',
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
                                NotEmpty::IS_EMPTY => 'Hasło nie może być puste.',
                            ),
                        ),
                    ),

                )

            ));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}