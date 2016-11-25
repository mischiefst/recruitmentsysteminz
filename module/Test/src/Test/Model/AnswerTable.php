<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-26
 * Time: 13:56
 */
namespace Test\Model;

use Zend\Db\TableGateway\TableGateway;


/**
 * Class QuestionTable
 * @package Test\Model
 */
class AnswerTable
{
    protected $tableGateway;

    /**
     * AnswerTable constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getAnswer($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idAnswer' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getSemiAnswer($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('Question_idQuestion' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    /**
     * @param $idQuestion
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getAnswerClosed($idQuestion)
    {
        $id = (int)$idQuestion;
        $rowset = $this->tableGateway->select(array('Question_idQuestion' => $id));

        return $rowset;
    }

    /**
     * @return int
     */
    public function getLastIndex()
    {
        $lastId = $this->tableGateway->getLastInsertValue();
        return $lastId;
    }

    /**
     * @param Answer $answer
     * @throws \Exception
     */
    public function addAnswer(Answer $answer)
    {
        $data = array(
            'idAnswer' => $answer->idAnswer,
            'answer' => $answer->answer,
            'amount_of_points' => $answer->amount_of_points,
            'Question_idQuestion' => $answer->idQuestion,
        );

        $id = (int)$answer->idAnswer;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAnswer($id)) {
                $this->tableGateway->update($data, array('idAnswer' => $id));
            } else {
                throw new \Exception('Answer id does not exist');
            }
        }
    }

    /**
     * @param $idQuestion
     */
    public function deleteByQuestion($idQuestion)
    {
        $this->tableGateway->delete(array('Question_idQuestion' => (int)$idQuestion));
    }
}