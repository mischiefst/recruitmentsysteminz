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
 * Class Answer
 * @package Test\Model
 */
class Answer
{
    public $idAnswer;
    public $answer;
    public $amount_of_points;
    public $idQuestion;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idAnswer = (!empty($data['idAnswer'])) ? $data['idAnswer'] : null;
        $this->answer = (!empty($data['answer'])) ? $data['answer'] : null;
        $this->amount_of_points = (!empty($data['amount_of_points'])) ? $data['amount_of_points'] : 0;
        $this->idQuestion = (!empty($data['Question_idQuestion'])) ? $data['Question_idQuestion'] : null;

    }
}
