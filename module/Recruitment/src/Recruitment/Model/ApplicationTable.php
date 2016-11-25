<?php
/**
 * class ApplicationTable
 * User: Lukasz Marszalek
 * Date: 2016-09-05
 * Time: 21:36
 */
namespace Recruitment\Model;

use Zend\Db\TableGateway\TableGateway;


/**
 * Class ApplicationTable
 * @package Recriutiment\Model
 */
class ApplicationTable
{
    protected $tableGateway;

    /**
     * ApplicationTable constructor.
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
     * @return int
     */
    public function getLastIndex()
    {
        $lastId = $this->tableGateway->getLastInsertValue();
        return $lastId;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getApplication($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idApplication' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }


    public function getApplicationTest()
    {

        $select = $this->tableGateway->getSql()->select();
        $conditions = array('status' => array('test', 'po tescie', 'rozmowa'));

        $select->where($conditions, \Zend\Db\Sql\Predicate\PredicateSet::OP_OR);

        $select->join('user', 'user.idUser = application.User_idUser', array('*'), 'left');
        $select->join('result', 'result.Application_idApplication = application.idApplication', array('*'), 'left');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        return $resultSet;
    }


    public function getApplicationTestByStatus($status)
    {

        $select = $this->tableGateway->getSql()->select();
        $conditions = array('status' => array($status));

        $select->where($conditions, \Zend\Db\Sql\Predicate\PredicateSet::OP_OR);

        $select->join('user', 'user.idUser = application.User_idUser', array('*'), 'left');
        $select->join('result', 'result.Application_idApplication = application.idApplication', array('*'), 'left');
        $select->join('advertisement', 'application.Advertisement_idAdvertisement = advertisement.idAdvertisement', array('*'), 'left');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        return $resultSet;
    }


    /**
     * @param $idUser
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getApplicationsByUserId($idUser)
    {
        $id = (int)$idUser;
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('User_idUser' => $id));
        $select->join('advertisement', 'advertisement.idAdvertisement=application.Advertisement_idAdvertisement', array('job_title'), 'left');
        $select->order('date_application DESC');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet;
    }

    public function getDetailsApplication($idApplication)
    {
        $id = (int)$idApplication;
        $select = $this->tableGateway->getSql()->select();

        $select->where(array('idApplication' => $id));
        $select->join('advertisement', 'advertisement.idAdvertisement=application.Advertisement_idAdvertisement', array('job_title'), 'left');
        $select->join('user', 'user.idUser = application.User_idUser', array('*'), 'left');
        $select->join('address', 'user.Address_idAddress = address.idAddress', array('*'), 'left');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet->current();
    }

    /**
     * @param $idAdvertisement
     * @param $status
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function getApplicationsByAdvertisementIdAndStatus($idAdvertisement, $status)
    {
        $id = (int)$idAdvertisement;
        $select = $this->tableGateway->getSql()->select();


        $select->where(array('application.Advertisement_idAdvertisement' => $id, 'status' => $status));
        $select->join('user', 'user.idUser = application.User_idUser', array('*'), 'left');
        $select->join('result', 'result.Application_idApplication = application.idApplication', array('*'), 'left');
//        $select->join('test', 'test.Advertisement_idAdvertisement = application.Advertisement_idAdvertisement', array('idTest'), 'left');

        $select->order('date_application DESC');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet;
    }


    public function getApplicationsByAdvertisement($idAdvertisement)
    {
        $id = (int)$idAdvertisement;
        $rowset = $this->tableGateway->select(array('Advertisement_idAdvertisement' => $id));

        return $rowset;
    }

    /**
     * @param $idAdvertisement
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getApplicationsByAdvertisementId($idAdvertisement)
    {
        $id = (int)$idAdvertisement;
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('Advertisement_idAdvertisement' => $id));
        $select->join('user', 'user.idUser = application.User_idUser', array('*'), 'left');
        $select->order('date_application DESC');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet;
    }

    public function getApplicationsByStatus($status)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('status' => $status));
        $select->join('user', 'user.idUser = application.User_idUser', array('*'), 'left');
        $select->order('date_application DESC');

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet;
    }

    /**
     * @param Application $application
     * @throws \Exception
     */
    public function addApplication(Application $application)
    {
        $data = array(
            'idApplication' => $application->idApplication,
            'status' => $application->status,
            'date_application' => $application->date_application,
            'Advertisement_idAdvertisement' => $application->idAdvertisement,
            'User_idUser' => $application->idUser,
        );

        $id = (int)$application->idApplication;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getApplication($id)) {
                $this->tableGateway->update($data, array('idApplication' => $id));
            } else {
                throw new \Exception('Application id does not exist');
            }
        }
    }

    /**
     * @param $id
     */
    public function deleteApplication($id)
    {
        $this->tableGateway->delete(array('idApplication' => (int)$id));
    }

    public function existApplication($idAdvertisement, $idUser)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('Advertisement_idAdvertisement' => $idAdvertisement, 'User_idUser' => $idUser));

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return count($resultSet) > 0;

    }
}