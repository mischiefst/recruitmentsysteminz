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
 * Class TestTable
 * @package Test\Model
 */
class TestTable
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

    public function getTest($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idTest' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getTestsByAdvertisement($idAdvertisement)
    {
        $id = (int)$idAdvertisement;
        $rowset = $this->tableGateway->select(array('Advertisement_idAdvertisement' => $id));

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
     * @param Test $test
     * @throws \Exception
     */
    public function addTest(Test $test)
    {
        $data = array(
            'idTest' => $test->idTest,
            'name' => $test->name,
            'description' => $test->description,
            'time' => $test->time,
            'password' => $test->password,
            'Advertisement_idAdvertisement' => $test->idAdvertisement,
        );

        $id = (int)$test->idTest;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getTest($id)) {
                $this->tableGateway->update($data, array('idTest' => $id));
            } else {
                throw new \Exception('Test id does not exist');
            }
        }
    }

    public function delete($idTest)
    {
        $this->tableGateway->delete(array('idTest' => (int)$idTest));
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
}