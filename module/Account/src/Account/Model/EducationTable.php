<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-17
 * Time: 14:10
 */
namespace Account\Model;

use Zend\Db\TableGateway\TableGateway;

class EducationTable
{
    protected $tableGateway;

    /**
     * EducationTable constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @return mixed
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getEducation($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idEducation' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param $idUser
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getUserEducation($idUser)
    {
        $rowset = $this->tableGateway->select(array('User_idUser' => $idUser));
        return $rowset;
    }

    public function saveEducation(Education $education)
    {
        $data = array(
            'school_name' => $education->school_name,
            'date_from' => $education->date_from,
            'date_to' => $education->date_to,
            'description' => $education->description,
            'User_idUser' => $education->idUser,
        );

        $id = (int)$education->idEducation;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getEducation($id)) {
                $this->tableGateway->update($data, array('idEducation' => $id));
            } else {
                throw new \Exception('Education id does not exist');
            }
        }
    }

    /**
     * @param $id
     */
    public function deleteEducation($id)
    {
        $this->tableGateway->delete(array('idEducation' => (int)$id));
    }


}