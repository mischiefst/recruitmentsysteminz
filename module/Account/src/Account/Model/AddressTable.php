<?php
/**
 * Class AddressTable
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 18:06
 */
namespace Account\Model;

use Zend\Db\TableGateway\TableGateway;

class AddressTable
{
    protected $tableGateway;

    /**
     * AddressTable constructor.
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
    public function getAddress($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idAddress' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param Address $address
     * @throws \Exception
     */
    public function saveAddress(Address $address)
    {
        $data = array(
            'street' => $address->street,
            'house_number' => $address->house_number,
            'flat_number' => $address->flat_number,
            'postal_code' => $address->postal_code,
            'city' => $address->city
        );


        $id = (int)$address->idAddress;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAddress($id)) {
                $this->tableGateway->update($data, array('idAddress' => $id));
            } else {
                throw new \Exception('Address id does not exist');
            }
        }
    }

    public function deleteAddress($id)
    {
        $this->tableGateway->delete(array('idAddress' => (int)$id));
    }

    /**
     * @return int
     */
    public function getLastIndex()
    {
        $lastId = $this->tableGateway->getLastInsertValue();

        return $lastId;
    }
}