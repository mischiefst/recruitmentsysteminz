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
class QuestionTable
{
    protected $tableGateway;

    /**
     * TestTable constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getQuestion($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idQuestion' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @return int
     */
    public function getLastIndex()
    {
        $lastId = $this->tableGateway->getLastInsertValue();
        return $lastId;
    }

    public function addQuestion(Question $question)
    {
        $data = array(
            'idQuestion' => $question->idQuestion,
            'type' => $question->type,
            'text' => $question->text,
        );

        $id = (int)$question->idQuestion;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getQuestion($id)) {
                $this->tableGateway->update($data, array('idQuestion' => $id));
            } else {
                throw new \Exception('Question id does not exist');
            }
        }
    }

    /**
     * @param $idQuestion
     */
    public function delete($idQuestion)
    {
        $this->tableGateway->delete(array('idQuestion' => (int)$idQuestion));
    }
}