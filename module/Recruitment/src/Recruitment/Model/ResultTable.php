<?php
/**
 * class ResultTable
 * User: Lukasz Marszalek
 * Date: 2016-09-05
 * Time: 21:36
 */
namespace Recruitment\Model;

use Zend\Db\TableGateway\TableGateway;


/**
 * Class ResultTable
 * @package Recriutiment\Model
 */
class ResultTable
{
    protected $tableGateway;

    /**
     * ResultTable constructor.
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
        $select = $this->tableGateway->getSql()->select()->order('date_application DESC');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getResult($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idResult' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getResultByApplication($idApplication)
    {
        $rowset = $this->getResultsByApplication($idApplication);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $idApplication");
        }
        return $row;
    }


    public function getResultsByApplication($idApplication)
    {
        $id = (int)$idApplication;
        $rowset = $this->tableGateway->select(array('Application_idApplication' => $id));

        return $rowset;
    }

    public function getResultsByTest($idTest)
    {
        $id = (int)$idTest;
        $rowset = $this->tableGateway->select(array('Test_idTest' => $id));

        return $rowset;
    }


    /**
     * @param Result $result
     * @throws \Exception
     */
    public function add(Result $result)
    {
        $data = array(
            'idResult' => $result->idResult,
            'date' => $result->date,
            'score' => $result->score,
            'User_idUser' => $result->idUser,
            'Test_idTest' => $result->idTest,
            'Application_idApplication' => $result->idApplication
        );

        $id = (int)$result->idResult;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getResult($id)) {
                $this->tableGateway->update($data, array('idResult' => $id));
            } else {
                throw new \Exception('Result id does not exist');
            }
        }
    }

    /**
     * @param $idUser
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getResultsEndedByUserId($idUser)
    {
        $id = (int)$idUser;
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('result.User_idUser' => $id));
        $select->where($select->where->notEqualTo('application.status','test'));


        $select->join('test', 'test.idTest=result.Test_idTest', array('*'), 'left');
        $select->join('application', 'application.idApplication=result.Application_idApplication', array('status'), 'left');

        $select->join('advertisement', 'advertisement.idAdvertisement=test.Advertisement_idAdvertisement', array('job_title'), 'left');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet;
    }


    /**
     * @param $idUser
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getResultsNewByUserId($idUser)
    {
        $id = (int)$idUser;
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('result.User_idUser' => $id, 'application.status' => 'test'));
        $select->join('test', 'test.idTest=result.Test_idTest', array('*'), 'left');
        $select->join('application', 'application.idApplication=result.Application_idApplication', array('status'), 'left');

        $select->join('advertisement', 'advertisement.idAdvertisement=test.Advertisement_idAdvertisement', array('job_title'), 'left');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet;
    }


    public function deleteResultByApplication($idApplication)
    {
        $this->tableGateway->delete(array('Application_idApplication' => (int)$idApplication));
    }

    public function deleteResultByTest($idTest)
    {
        $this->tableGateway->delete(array('Test_idTest' => (int)$idTest));
    }

}