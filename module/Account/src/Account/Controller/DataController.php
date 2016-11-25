<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-17
 * Time: 14:16
 */
namespace Account\Controller;

use Account\Form\AddressForm;
use Account\Form\DataForm;
use Account\Form\EducationForm;
use Account\Form\ExperienceForm;
use Account\Form\RegisterForm;
use Account\Model\Education;
use Account\Model\Experience;
use Account\View\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DataController extends AbstractActionController
{
    protected $addressTable;
    protected $userTable;
    protected $educationTable;
    protected $experienceTable;

    public function indexAction()
    {
        $helper = new Helper();

        if ($helper->isLogged()) {
            try {
                $user = $this->getUserTable()->getUserByLogin($helper->getUsername());
                $address = $this->getAddressTable()->getAddress($user->idAddress);

            } catch (\Exception $ex) {
                return $this->redirect()->toRoute('application', array(
                    'action' => 'index'
                ));
            }

            $formData = new DataForm();
            $addressForm = new AddressForm();
            $addressForm->bind($address);


            $formData->bind($user);

            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $formData->setInputFilter($user->getInputFilter());
                $formData->setData($data);

                $addressId = $address->idAddress;
                $rule = $user->rule;
                $login = $user->login;
                if ($formData->isValid()) {
                    $user->idAddress = $addressId;
                    $user->rule = $rule;
                    $user->login = $login;

                    $this->getUserTable()->saveUser($user);
                    $this->flashmessenger()->addMessage("Zmiany zostały zapisane poprawnie.");

                    return $this->redirect()->toRoute('data', ['action' => 'index']);

                }
            }

            return new ViewModel(['form' => $formData, 'addressForm' => $addressForm]);
        }

        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    public function educationAction()
    {
        $helper = new Helper();

        if ($helper->isLogged()) {
            $educations = $this->getEducationTable()->getUserEducation($this->getUserTable()->getUserByLogin($helper->getUsername())->idUser);

            return new ViewModel(array('educations' => $educations));
        }

        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    public function addEducationAction()
    {
        $helper = new Helper();

        if ($helper->isLogged()) {

            $form = new EducationForm();
            $request = $this->getRequest();

            if ($request->isPost()) {
                $education = new Education();

                $form->setInputFilter($education->getInputFilter());

                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData();

                    $data['User_idUser'] = $this->getUserTable()->getUserByLogin($helper->getUsername())->idUser;
                    $education->exchangeArray($data);

                    $this->getEducationTable()->saveEducation($education);

                    $this->flashmessenger()->addMessage("Wykształcenie dodane pomyślnie.");
                    return $this->redirect()->toRoute('data', array('action' => 'education'));
                }
            }
            return new ViewModel(array('form' => $form));
        }

        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    public function deleteEducationAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {

            $idEducation = (int)$this->params()->fromRoute('id', 0);
            try {
                $education = $this->getEducationTable()->getEducation($idEducation);

            } catch (\Exception $e) {
                $this->flashmessenger()->addMessage("Nie posiadasz uprawnień");
                return $this->redirect()->toRoute('application', array('action' => 'index'));

            }
            if ($education->idUser == $helper->getUserData()->idUser) {
                $this->getEducationTable()->deleteEducation($idEducation);

                $this->flashmessenger()->addMessage("Wykształcenie zostało usunięte");
                return $this->redirect()->toRoute('data', array('action' => 'education'));

            }

            $this->flashmessenger()->addMessage("Nie posiadasz uprawnień");
        }

        return $this->redirect()->toRoute('application', array('action' => 'index'));

    }

    public function editaddressAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $addressForm = new AddressForm();
            $address = $this->getAddressTable()->getAddress($data['idAddress']);
            $addressForm->bind($address);

            $addressForm->setInputFilter($address->getInputFilter());
            $addressForm->setData($data);

            if ($addressForm->isValid()) {

                $this->getAddressTable()->saveAddress($address);
                $this->flashmessenger()->addMessage("Zmiany zostały zapisane poprawnie.");

                return $this->redirect()->toRoute('data', ['action' => 'index']);
            }
        }

        $this->flashmessenger()->addMessage("Oups coś poszło nie tak");

        return $this->redirect()->toRoute('data', ['action' => 'index']);
    }

    public function experienceAction()
    {
        $helper = new Helper();

        if ($helper->isLogged()) {
            $experience = $this->getExperienceTable()->getUserExperience($this->getUserTable()->getUserByLogin($helper->getUsername())->idUser);

            return new ViewModel(array('experiences' => $experience));
        }

        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    public function deleteExperienceAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {

            $idExperience = (int)$this->params()->fromRoute('id', 0);
            try {
                $experience = $this->getExperienceTable()->getExperience($idExperience);

            } catch (\Exception $e) {
                $this->flashmessenger()->addMessage("Nie posiadasz uprawnień");
                return $this->redirect()->toRoute('application', array('action' => 'index'));

            }
            if ($experience->idUser == $helper->getUserData()->idUser) {
                $this->getExperienceTable()->deleteExperience($idExperience);

                $this->flashmessenger()->addMessage("Doświadczenie zostało usunięte");
                return $this->redirect()->toRoute('data', array('action' => 'experience'));

            }

            $this->flashmessenger()->addMessage("Nie posiadasz uprawnień");
        }

        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    public function addExperienceAction()
    {
        $helper = new Helper();

        if ($helper->isLogged()) {

            $form = new ExperienceForm();
            $request = $this->getRequest();

            if ($request->isPost()) {
                $experience = new Experience();

                $form->setInputFilter($experience->getInputFilter());

                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData();

                    $data['User_idUser'] = $this->getUserTable()->getUserByLogin($helper->getUsername())->idUser;
                    $experience->exchangeArray($data);

                    $this->getExperienceTable()->saveExperience($experience);

                    $this->flashmessenger()->addMessage("Doświadczenie dodane pomyślnie.");
                    return $this->redirect()->toRoute('data', array('action' => 'experience'));
                }
            }
            return new ViewModel(array('form' => $form));
        }

        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    public function editExperienceAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {

            $idExperience = (int)$this->params()->fromRoute('id', 0);

            try {
                $user = $helper->getUserData();
                $experience = $this->getExperienceTable()->getExperience($idExperience);

            } catch (\Exception $ex) {
                $this->flashmessenger()->addMessage("Coś poszło nie tak");
                return $this->redirect()->toRoute('application', array(
                    'action' => 'index'
                ));
            }

            if ($experience->idUser == $user->idUser) {
                $experienceForm = new ExperienceForm();
                $experienceForm->setAttribute('action',  '');
                $experienceForm->setAttribute('query', ['id'=>$idExperience]);

                $experienceForm->bind($experience);
                $request = $this->getRequest();
                if ($request->isPost()) {
                    $data = $request->getPost();
                    $experienceForm->setInputFilter($experience->getInputFilter());
                    $experienceForm->setData($data);

                    if ($experienceForm->isValid()) {
                        $experience->idUser = $user->idUser;
                        $this->getExperienceTable()->saveExperience($experience);
                        $this->flashmessenger()->addMessage("Zmiany zostały zapisane poprawnie.");

                        return $this->redirect()->toRoute('data', ['action' => 'experience']);

                    }
                }

                return new ViewModel(['form' => $experienceForm]);
            }
        }

        $this->flashmessenger()->addMessage("Nie posiadasz uprawnień!");
        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }
    public function editEducationAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {

            $idEducation = (int)$this->params()->fromRoute('id', 0);

            try {
                $user = $helper->getUserData();
                $education = $this->getEducationTable()->getEducation($idEducation);

            } catch (\Exception $ex) {
                $this->flashmessenger()->addMessage("Coś poszło nie tak");
                return $this->redirect()->toRoute('application', array(
                    'action' => 'index'
                ));
            }

            if ($education->idUser == $user->idUser) {
                $educationForm = new EducationForm();
                $educationForm->setAttribute('action',  '');
                $educationForm->setAttribute('query', ['id'=>$idEducation]);

                $educationForm->bind($education);
                $request = $this->getRequest();
                if ($request->isPost()) {
                    $data = $request->getPost();
                    $educationForm->setInputFilter($education->getInputFilter());
                    $educationForm->setData($data);

                    if ($educationForm->isValid()) {
                        $education->idUser = $user->idUser;
                        $this->getEducationTable()->saveEducation($education);
                        $this->flashmessenger()->addMessage("Zmiany zostały zapisane poprawnie.");

                        return $this->redirect()->toRoute('data', ['action' => 'education']);

                    }
                }

                return new ViewModel(['form' => $educationForm]);
            }
        }

        $this->flashmessenger()->addMessage("Nie posiadasz uprawnień!");
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

    /**
     * @return mixed
     */
    public
    function getEducationTable()
    {
        if (!$this->educationTable) {
            $sm = $this->getServiceLocator();
            $this->educationTable = $sm->get('Account\Model\EducationTable');
        }
        return $this->educationTable;
    }

    /**
     * @return mixed
     */
    public
    function getExperienceTable()
    {
        if (!$this->experienceTable) {
            $sm = $this->getServiceLocator();
            $this->experienceTable = $sm->get('Account\Model\ExperienceTable');
        }
        return $this->experienceTable;
    }

}