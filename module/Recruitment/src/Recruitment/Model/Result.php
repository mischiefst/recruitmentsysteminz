<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-23
 * Time: 15:17
 */
namespace Recruitment\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\Date;


class Result
{
    public $idResult;
    public $date;
    public $score;
    public $idUser;
    public $idTest;
    public $idApplication;

    protected $inputFilter;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idResult = (!empty($data['idResult'])) ? $data['idResult'] : null;
        $this->date = (!empty($data['date'])) ? $data['date'] : date('Y-m-d H:i:s');
        $this->score = (!empty($data['score'])) ? $data['score'] : '-1';
        $this->idUser = (!empty($data['User_idUser'])) ? $data['User_idUser'] : null;
        $this->idTest = (!empty($data['Test_idTest'])) ? $data['Test_idTest'] : null;
        $this->idApplication = (!empty($data['Application_idApplication'])) ? $data['Application_idApplication'] : null;
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
                'name' => 'idResult',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}