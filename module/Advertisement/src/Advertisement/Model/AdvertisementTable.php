<?php
/**
 * class AdvertisementTable
 * User: Lukasz Marszalek
 * Date: 2016-09-05
 * Time: 21:36
 */
namespace Advertisement\Model;

use Zend\Db\TableGateway\TableGateway;


/**
 * Class AdvertisementTable
 * @package Advertisement\Model
 */
class AdvertisementTable
{
    protected $tableGateway;

    /**
     * AdvertisementTable constructor.
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
        $select = $this->tableGateway->getSql()->select()->order('date_added DESC');

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
    public function getAdvertisement($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idAdvertisement' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param Advertisement $advertisement
     * @throws \Exception
     */
    public function addAdvertisement(Advertisement $advertisement)
    {
        $data = array(
            'idAdvertisement' => $advertisement->idAdvertisement,
            'job_title' => $advertisement->job_title,
            'text' => $advertisement->text,
            'date_added' => $advertisement->date_added,
        );

        $id = (int)$advertisement->idAdvertisement;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAdvertisement($id)) {
                $this->tableGateway->update($data, array('idAdvertisement' => $id));
            } else {
                throw new \Exception('Advertisement id does not exist');
            }
        }
    }

    /**
     * @param $id
     */
    public function deleteAdvertisement($id)
    {
        $this->tableGateway->delete(array('idAdvertisement' => (int)$id));
    }
}