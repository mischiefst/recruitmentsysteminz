<?php
/**
 * class Advertisement
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */

namespace Advertisement\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\AbstractDb;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Class Advertisement
 * @package Advertisement\Model
 */
class Advertisement implements InputFilterAwareInterface
{
    public $idAdvertisement;
    public $job_title;
    public $text;
    public $date_added;

    protected $inputFilter;


    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idAdvertisement = (!empty($data['idAdvertisement'])) ? $data['idAdvertisement'] : null;
        $this->job_title = (!empty($data['job_title'])) ? $data['job_title'] : null;
        $this->text = (!empty($data['text'])) ? $data['text'] : null;
        $this->date_added = (!empty($data['date_added'])) ? $data['date_added'] : date('Y-m-d');

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

    public function getFullAdvertisementInputFilter()
    {
        $advertismentInputFilter = $this->getInputFilter();

        $advertismentInputFilter->add(
            array(
                'name' => 'skills',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Podaj przynajmniej jedną umiejętność',
                            ),
                        ),
                    ),
                ),
            )
        );

        return $advertismentInputFilter;
    }

    /**
     * @return InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'idAdvertisement',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'job_title',
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
                                StringLength::TOO_SHORT => 'Nazwa stanowiska jest za krótka. Minimum 5 znaków.',
                                StringLength::TOO_LONG => 'Nazwa stanowiska jest za długa. Maksymalnie 100 znaków',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Nazwa stanowiska nie może być pusta',
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