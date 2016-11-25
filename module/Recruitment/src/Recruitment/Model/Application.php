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


class Application
{
    public $idApplication;

    public $status;
    public $date_application;
    public $idAdvertisement;
    public $idUser;
    protected $inputFilter;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idApplication = (!empty($data['idApplication'])) ? $data['idApplication'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : 'nowa';
        $this->date_application = (!empty($data['date_application'])) ? $data['date_application'] : date('Y-m-d');
        $this->idAdvertisement = (!empty($data['Advertisement_idAdvertisement'])) ? $data['Advertisement_idAdvertisement'] : null;
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
                'name' => 'idApplication',
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