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
 * Class Question
 * @package Test\Model
 */
class Question
{
    public $idQuestion;
    public $type;
    public $text;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->idQuestion = (!empty($data['idQuestion'])) ? $data['idQuestion'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->text = (!empty($data['text'])) ? $data['text'] : null;
    }
}
