<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-13
 * Time: 19:56
 */
namespace Test\Model;


use Zend\Db\TableGateway\TableGateway;

class QuestionTestTable
{
    protected $tableGateway;

    /**
     * QuestionTestTable constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
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
     * @param QuestionTest $questionTest
     */
    public function add(QuestionTest $questionTest)
    {
        $data = array(
            'Question_idQuestion' => $questionTest->question_idQuestion,
            'Test_idTest' => $questionTest->test_idTest
        );

        $this->tableGateway->insert($data);
    }

    public function deleteByTest($idTest)
    {
        $this->tableGateway->delete(array('Test_idTest' => (int)$idTest));
    }

    public function deleteByQuestion($idQuestion)
    {
        $this->tableGateway->delete(array('Question_idQuestion' => (int)$idQuestion));
    }

    /**
     * @param $idTest
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     * @throws \Exception
     */
    public function getQuestionsIdForTest($idTest)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->where(array('Test_idTest' => (int)$idTest));
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        if (!$resultSet) {
            throw new \Exception("Could not find row $idTest");
        }

        return $resultSet;
    }
}