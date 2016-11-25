<?php
/**
 * class AccountController
 * User: Lukasz Marszalek
 * Date: 2016-08-31
 * Time: 20:46
 */
namespace Account\Controller;

use Account\Form\LoginForm;
use Account\Model\Address;
use Account\View\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Account\Model\User;
use Account\Form\RegisterForm;

class AccountController extends AbstractActionController
{
    protected $userTable;
    protected $addressTable;
    protected $authservice;
    protected $storage;


    public function indexAction()
    {
        $helper = new Helper();

        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
        }
        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public
    function registerAction()
    {
        $helper = new Helper();
        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
        }
        $form = new RegisterForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $address = new Address();
            $user = new User();
            $form->setInputFilter($user->getRegisterInputFilter());

            $form->setData($request->getPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $address->exchangeArray($data);
                $this->getAddressTable()->saveAddress($address);

                $data['Address_idAddress'] = $this->getAddressTable()->getLastIndex();

                $user->exchangeArray($data);
                $this->getUserTable()->saveUser($user);

                $globalHelper = new \Application\View\Helper\Helper();
                $globalHelper->sendEmail('kontakt.rekrutacja.rs@gmail.com', $user->email, "potwierdzenie rejestracji", "Dziękujemy za rejestracje w systemie. ");

                $this->flashmessenger()->addMessage("Rejestracja zakończona pomyślnie.");
                return $this->redirect()->toRoute('application');
            }
        }

        return new ViewModel(array(
            'form' => $form));
    }


    public
    function loginAction()
    {
        $helper = new Helper();
        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
        }

        $form = new LoginForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getLoginInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $this->getAuthService()->getAdapter()
                    ->setIdentity($request->getPost('login'))
                    ->setCredential($request->getPost('password'));

                $result = $this->getAuthService()->authenticate();
                if ($result->isValid()) { //get_object_vars($this->getAuthService()->getAdapter()->getResultRowObject("role"))['role'] > 2
                    $this->getAuthService()->getStorage()->write($request->getPost('login'));
                    $this->flashmessenger()->addMessage("Witaj! Zostałeś zalogowany");
                    $this->forwardingToCorrectPlace();
                } else {
                    $this->getAuthService()->clearIdentity();
                    $this->flashmessenger()->addMessage("Niepoprawne dane do logowania");

                    return $this->redirect()->toRoute('account', array('action' => 'login'));
                }

            }
        }
        return array('form' => $form);

    }


    public
    function logoutAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        $this->flashmessenger()->addMessage("Zostałeś wylogowany");
        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }


    /**
     * @return mixed
     */
    public
    function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Account\Model\UserTable');
        }
        return $this->userTable;
    }

    /**
     * @return mixed
     */
    public
    function getAddressTable()
    {
        if (!$this->addressTable) {
            $sm = $this->getServiceLocator();
            $this->addressTable = $sm->get('Account\Model\AddressTable');
        }
        return $this->addressTable;
    }

    public
    function getAuthService()
    {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()
                ->get('AuthServiceUser');
        }
        return $this->authservice;
    }

    public
    function getSessionStorage()
    {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()
                ->get('Account\Model\AuthStorage');
        }
        return $this->storage;
    }

    public
    function forwardingToCorrectPlace()
    {
        $globalHelper = new \Application\View\Helper\Helper();

        if ($globalHelper->isRuleAdmin()) {
            return $this->redirect()->toRoute('admin', array('action' => 'index'));
        }
        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }
}