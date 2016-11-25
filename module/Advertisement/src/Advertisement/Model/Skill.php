<?php
/**
 * class Skill
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
 * Class Skill
 * @package Advertisement\Model
 */
class Skill implements InputFilterAwareInterface
{

    public $idSkill;
    public $name;

    protected $inputFilter;


    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idSkill = (!empty($data['idSkill'])) ? $data['idSkill'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
    }


    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");

    }

    /**
     * @return InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'idSkill',
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
                            'min' => 3,
                            'max' => 100,
                            'messages' => array(
                                StringLength::TOO_SHORT => 'Nazwa umiejętności jest za krótka. Minimum 1 znak.',
                                StringLength::TOO_LONG => 'Nazwa umiejętności jest za długa. Maksymalnie 100 znaków',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'skill',
                            'field' => 'name',
                            'adapter' => \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter(),
                            'messages' => array(
                                AbstractDb::ERROR_RECORD_FOUND => 'Istnieje już taka umiejętność.',
                            )
                        ),
                    ),

                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Nazwa umiejętności nie może być pusta',
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