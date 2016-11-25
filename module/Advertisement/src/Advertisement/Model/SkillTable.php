<?php
/**
 * class SkillTable
 * User: Lukasz Marszalek
 * Date: 2016-09-05
 * Time: 21:36
 */
namespace Advertisement\Model;

use Zend\Db\TableGateway\TableGateway;


/**
 * Class SkillTable
 * @package Advertisement\Model
 */
class SkillTable
{
    protected $tableGateway;

    /**
     * SkillTable constructor.
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
        $select = $this->tableGateway->getSql()->select()->order('name ASC');

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
    public function getSkill($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idSkill' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }


    /**
     * @param Skill $skill
     * @throws \Exception
     */
    public function addSkill(Skill $skill)
    {
        $data = array(
            'idSkill' => $skill->idSkill,
            'name' => $skill->name,
        );

        $id = (int)$skill->idSkill;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getSkill($id)) {
                $this->tableGateway->update($data, array('idSkill' => $id));
            } else {
                throw new \Exception('Skill id does not exist');
            }
        }
    }

    /**
     * @param $id
     */
    public function deleteSkill($id)
    {
        $this->tableGateway->delete(array('idSkill' => (int)$id));
    }

}