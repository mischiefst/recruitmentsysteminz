<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-13
 * Time: 19:56
 */
namespace Advertisement\Model;


use Zend\Db\TableGateway\TableGateway;

class SkillAdvertisementTable
{
    protected $tableGateway;

    /**
     * SkillAdvertisementTable constructor.
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

    public function add(SkillAdvertisement $skillAdvertisement)
    {
        $data = array(
            'Advertisement_idAdvertisement' => $skillAdvertisement->advertisement_idAdvertisement,
            'Skill_idSkill' => $skillAdvertisement->skill_idSkill,
        );

        $this->tableGateway->insert($data);
    }

    public function isObject(SkillAdvertisement $skillAdvertisement)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->where(array('Advertisement_idAdvertisement' => (int)$skillAdvertisement->advertisement_idAdvertisement));
        $select->where(array('Skill_idSkill' => (int)$skillAdvertisement->skill_idSkill));

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();


        return (count($resultSet) > 0);
    }

    /**
     * @param $idAdvertisement
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getSkillsIdForAdvertisement($idAdvertisement)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->where(array('Advertisement_idAdvertisement' => (int)$idAdvertisement));
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        if (!$resultSet) {
            throw new \Exception("Could not find row $idAdvertisement");
        }

        return $resultSet;
    }

    public function deleteBySkill($idSkill)
    {
        $this->tableGateway->delete(array('Skill_idSkill' => (int)$idSkill));
    }

    public function deleteByAdvertisement($idAdvertisement)
    {
        $this->tableGateway->delete(array('Advertisement_idAdvertisement' => (int)$idAdvertisement));
    }

    public function delete($idSkill, $idAdvertisement)
    {
        $this->tableGateway->delete(array('Skill_idSkill' => (int)$idSkill, 'Advertisement_idAdvertisement' => $idAdvertisement));
    }
}