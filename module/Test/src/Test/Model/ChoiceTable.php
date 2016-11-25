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
class ChoiceTable
{
    protected $tableGateway;

    /**
     * ChoiceTable constructor.
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
    public function getChoice($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idChoice' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param $idResult
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function getChoices($idResult)
    {
        $id = (int)$idResult;
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('Result_idResult' => $id));
        $select->join('question', 'question.idQuestion=choice.Question_idQuestion', array('*'), 'left');
        $select->order('question.type ASC');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet;
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
     * @param Choice $choice
     * @throws \Exception
     */
    public function addChoice(Choice $choice)
    {
        $data = array(
            'idChoice' => $choice->idChoice,
            'answer' => $choice->answer,
            'points' => $choice->points,
            'Question_idQuestion' => $choice->idQuestion,
            'User_idUser' => $choice->idUser,
            'Result_idResult' => $choice->idResult,
        );

        $id = (int)$choice->idChoice;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getChoice($id)) {
                $this->tableGateway->update($data, array('idChoice' => $id));
            } else {
                throw new \Exception('Choice id does not exist');
            }
        }
    }

    /**
     * @param $idResult
     */
    public function deleteByResult($idResult)
    {
        $this->tableGateway->delete(array('Result_idResult' => (int)$idResult));
    }

    /**
     * @param $idQuestion
     */
    public function deleteByQuestion($idQuestion)
    {
        $this->tableGateway->delete(array('Question_idQuestion' => (int)$idQuestion));
    }


}