<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-17
 * Time: 14:10
 */
namespace Account\Model;

use Zend\Db\TableGateway\TableGateway;

class ExperienceTable
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
    public function getExperience($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idExperience' => $id));
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
    public function getUserExperience($idUser)
    {
        $rowset = $this->tableGateway->select(array('User_idUser' => $idUser));
        return $rowset;
    }

    public function saveExperience(Experience $experience)
    {
        $data = array(
            'company_name' => $experience->company_name,
            'date_from' => $experience->date_from,
            'date_to' => $experience->date_to,
            'job_title' => $experience->job_title,
            'description' => $experience->description,
            'User_idUser' => $experience->idUser,
        );

        $id = (int)$experience->idExperience;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getExperience($id)) {
                $this->tableGateway->update($data, array('idExperience' => $id));
            } else {
                throw new \Exception('Experience id does not exist');
            }
        }
    }


    /**
     * @param $id
     */
    public function deleteExperience($id)
    {
        $this->tableGateway->delete(array('idExperience' => (int)$id));
    }


}