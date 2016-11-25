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
 * Class Choice
 * @package Test\Model
 */
class Choice
{
    public $idChoice;
    public $answer;
    public $idUser;
    public $idQuestion;
    public $points;
    public $idResult;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idChoice = (!empty($data['idChoice'])) ? $data['idChoice'] : null;
        $this->answer = (!empty($data['answer'])) ? $data['answer'] : null;
        $this->idQuestion = (!empty($data['Question_idQuestion'])) ? $data['Question_idQuestion'] : null;
        $this->idUser = (!empty($data['User_idUser'])) ? $data['User_idUser'] : null;
        $this->idResult = (!empty($data['Result_idResult'])) ? $data['Result_idResult'] : null;
        $this->points = (!empty($data['points'])) ? $data['points'] : null;
    }
}
