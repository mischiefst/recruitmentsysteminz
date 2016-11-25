<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-17
 * Time: 13:53
 */
namespace Account\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\Date;


class Education
{
    public $idEducation;

    public $school_name;
    public $date_from;
    public $date_to;
    public $description;
    public $idUser;
    protected $inputFilter;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idEducation = (!empty($data['idEducation'])) ? $data['idEducation'] : null;
        $this->school_name = (!empty($data['school_name'])) ? $data['school_name'] : null;
        $this->date_from = (!empty($data['date_from'])) ? $data['date_from'] : null;
        $this->date_to = (!empty($data['date_to'])) ? $data['date_to'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->idUser = (!empty($data['User_idUser'])) ? $data['User_idUser'] : null;
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
                'name' => 'idEducation',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'school_name',
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
                                NotEmpty::IS_EMPTY => 'Nazwa szkoły nie może być pusta.',
                            ),
                        ),

                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 200,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Nazwa szkołyjest za krótka. Minimum 1 znak.',
                                StringLength::TOO_LONG => 'Nazwa szkoły jest za długa. Maksymalnie 200 znaków.',
                            ),
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'date_from',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Data rozpoczęcia szkoły jest wymagana.',
                            ),
                        ),

                    ),

                    array(
                        'name' => 'Date',
                        'options' => array(
                            'messages' => array(
                                Date::INVALID_DATE => 'Niepoprawny format daty.',
                            ),
                        ),

                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'date_to',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Data zakończenia szkoły jest wymagana.',
                            ),
                        ),
                    ),

                    array(
                        'name' => 'Date',
                        'options' => array(
                            'messages' => array(
                                Date::INVALID_DATE => 'Niepoprawny format daty.',
                            ),
                        ),
                    ),

                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\Callback::INVALID_VALUE => 'Data zakończenia powinna być późniejsza niż data rozpoczęcia',
                            ),
                            'callback' => function ($value, $context = array()) {
                                $startDate =$context['date_from'];
                                $endDate =  $value;
                                return $endDate >= $startDate;
                            },
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }


}