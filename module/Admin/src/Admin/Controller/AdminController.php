<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-05
 * Time: 20:08
 */
namespace Admin\Controller;

use Admin\Form\ContactForm;
use Admin\Form\UserForm;
use Application\View\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    protected $userTable;

    public function indexAction()
    {
        return new ViewModel();
    }

    public function usersAction()
    {
        $helper = new Helper();
        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();

            $request = $this->getRequest();
            $users = $this->getUserTable()->getUsers('');

            if ($request->isPost()) {
                $data = $request->getPost();

                $search = $data['search'];

                $users = $this->getUserTable()->getUsers($search);
            }

            return new ViewModel(['users' => $users]);

        }
        return $this->redirect()->toRoute('application', array('action' => 'index'));

    }

    public function changeRoleAction()
    {
        $helper = new Helper();
        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
            $idUser = (int)$this->params()->fromRoute('id', 0);

            try {
                $user = $this->getUserTable()->getUser($idUser);

                $user->rule = ($user->rule > 1) ? 1 : 5;

                $this->getUserTable()->saveUser($user);

                $this->flashmessenger()->addMessage('Zmieniono prawa użytkownikowi!');
                return $this->redirect()->toRoute('admin', array('action' => 'users'));


            } catch (\Exception $e) {
                $this->flashmessenger()->addMessage("Nie posiadasz uprawnień!");

                return $this->redirect()->toRoute('application', array('action' => 'index'));
            }

        }

    }

    public function detailsUserAction()
    {
        $helper = new Helper();
        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
            $idUser = (int)$this->params()->fromRoute('id', 0);
            try {
                $user = $this->getUserTable()->getUser($idUser);

            } catch (\Exception $e) {
                $this->flashmessenger()->addMessage("Nie posiadasz uprawnień!");

                return $this->redirect()->toRoute('application', array('action' => 'index'));
            }

            $form = new UserForm();

            $form->bind($user);
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $form->setInputFilter($user->getInputFilterEdit());
                $form->setData($data);

                $addressId = $user->idAddress;
                $rule = $user->rule;
                $login = $user->login;
                $password = $user->password;
                if ($form->isValid()) {
                    $user->idAddress = $addressId;
                    $user->rule = $rule;
                    $user->login = $login;
                    $user->password = $password;

                    $this->getUserTable()->saveUser($user);
                    $this->flashmessenger()->addMessage("Zmiany zostały zapisane poprawnie.");

                    return $this->redirect()->toRoute('admin', ['action' => 'users']);

                }
            }

            return new ViewModel(['form' => $form]);

        }
        return $this->redirect()->toRoute('application', array('action' => 'index'));

    }


    public function contactAction()
    {
        $form = new ContactForm();
        $helper = new Helper();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid()) {

                $bodyText = $data['text'] . '<br /><br /> Napisał: ' . $data['person'] . '(' . $data['email'] . ')';
                if ($helper->sendEmail($data['email'], 'kontakt.rekrutacja.rs@gmail.com', 'mail od ' . $data['person'], $bodyText)) {
                    $this->flashmessenger()->addMessage("Wiadomość została wysłana do właściciela strony.");

                    return $this->redirect()->toRoute('admin', ['action' => 'contact']);
                }

            } else {
                $this->flashmessenger()->addMessage("Oups! Coś poszło nie tak, waidomość nie została wysłana.");

                return $this->redirect()->toRoute('admin', ['action' => 'contact']);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function forwardingToCorrectPlace()
    {
        $globalHelper = new \Application\View\Helper\Helper();

        if (!$globalHelper->isRuleAdmin()) {
            return $this->redirect()->toRoute('application', array('action' => 'index'));
        }
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
}