<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-13
 * Time: 19:52
 */
namespace Test\Model;

class QuestionTest
{

    public $test_idTest;
    public $question_idQuestion;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->test_idTest = (!empty($data['Test_idTest'])) ? $data['Test_idTest'] : null;
        $this->question_idQuestion = (!empty($data['Question_idQuestion'])) ? $data['Question_idQuestion'] : null;
    }

}