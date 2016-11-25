<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-26
 * Time: 12:45
 */
namespace Test\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\AbstractDb;
use Zend\Validator\GreaterThan;
use Zend\Validator\LessThan;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Class Test
 * @package Test\Model
 */
class Test implements InputFilterAwareInterface
{
    public $idTest;
    public $name;
    public $description;
    public $time;
    public $password;
    public $idAdvertisement;

    protected $inputFilter;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idTest = (!empty($data['idTest'])) ? $data['idTest'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->time = (!empty($data['time'])) ? $data['time'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->idAdvertisement = (!empty($data['Advertisement_idAdvertisement'])) ? $data['Advertisement_idAdvertisement'] : null;
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
                'name' => 'idTest',
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
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 5,
                            'max' => 100,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Nazwa jest za krótka. Minimum 5 znaków.',
                                StringLength::TOO_LONG => 'Nazwa jest za długa. Maksymalnie 100 znaków',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Nazwa nie może być pusta',
                            ),
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'advertisement',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'GreaterThan',
                        'options' => array(
                            'min' => 0,
                            'messages' => array(
                                GreaterThan::NOT_GREATER => 'Musisz wybrać ogłoszenie',
                            ),
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'time',
                'required' => false,
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
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 5,
                            'max' => 20,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Hasło dostępu jest za krótkie. Minimum 5 znaków.',
                                StringLength::TOO_LONG => 'Hasło dostępu za długie. Maksymalnie 20znaków.',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Hasło dostępu nie może byc puste.',
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