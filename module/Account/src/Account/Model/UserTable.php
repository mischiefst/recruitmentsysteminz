<?php
/**
 * Class UserTable
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 18:06
 */
namespace Account\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable
{
    protected $tableGateway;

    /**
     * UserTable constructor.
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
    public function getUser($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idUser' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param $login
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getUserByLogin($login)
    {
        $rowset = $this->tableGateway->select(array('login' => $login));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $login");
        }
        return $row;
    }


    /**
     * @param $login
     * @return mixed
     * @throws \Exception
     */
    public function getRuleByLogin($login)
    {
        $user = $this->getUserByLogin($login);

        return $user->rule;
    }

    public function getUsers($search)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where->like('name', '%' . $search . '%');
        $select->where->or->like('surname', '%' . $search . '%');
        $select->where->or->like('login', '%' . $search . '%');


        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        return $resultSet;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function saveUser(User $user)
    {
        $data = array(
            'name' => $user->name,
            'surname' => $user->surname,
            'gender' => $user->gender,
            'email' => $user->email,
            'birthday_date' => $user->birthday_date,
            'phone' => $user->phone,
            'login' => $user->login,
            'password' => $user->password,
            'rule' => $user->rule,
            'date_registration' => $user->date_registration,
            'Address_idAddress' => $user->idAddress
        );


        $id = (int)$user->idUser;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('idUser' => $id));
            } else {
                throw new \Exception('User id does not exist');
            }
        }
    }

    //UZUPELNIC USUNIECIE ADRESU
    public function deleteUser($id)
    {
        $this->tableGateway->delete(array('idUser' => (int)$id));
    }
}