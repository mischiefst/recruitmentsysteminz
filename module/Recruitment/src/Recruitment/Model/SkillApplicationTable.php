<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-13
 * Time: 19:56
 */
namespace Recruitment\Model;


use Zend\Db\TableGateway\TableGateway;

class SkillApplicationTable
{
    protected $tableGateway;

    /**
     * SkillApplicationTable constructor.
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
     * @param SkillApplication $skillApplication
     */
    public function add(SkillApplication $skillApplication)
    {
        $data = array(
            'Application_idApplication' => $skillApplication->application_idApplication,
            'Skill_idSkill' => $skillApplication->skill_idSkill,
            'knowledge' => $skillApplication->knowledge
        );

        $this->tableGateway->insert($data);
    }

    /**
     * @param $idApplication
     * @param $idSkill
     * @return mixed
     */
    public function getKnowledge($idApplication, $idSkill)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->where(array('Application_idApplication' => (int)$idApplication, 'Skill_idSkill' => (int)$idSkill));
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet->current()['knowledge'];
    }

    /**
     * @param $idSkill
     */
    public function deleteBySkill($idSkill)
    {
        $this->tableGateway->delete(array('Skill_idSkill' => (int)$idSkill));
    }

    /**
     * @param $idApplication
     */
    public function deleteByApplication($idApplication)
    {
        $this->tableGateway->delete(array('Application_idApplication' => (int)$idApplication));
    }

}