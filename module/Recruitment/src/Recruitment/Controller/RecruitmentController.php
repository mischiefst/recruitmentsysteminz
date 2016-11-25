<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-05
 * Time: 20:08
 */
namespace Recruitment\Controller;

use Application\View\Helper\Helper;
use DateTime;
use Recruitment\Form\ApplicationForm;
use Recruitment\Form\InviteForm;
use Recruitment\Model\Application;
use Recruitment\Model\Result;
use Recruitment\Model\SkillApplication;
use Zend\Db\Sql\Predicate\In;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RecruitmentController extends AbstractActionController
{
    protected $addressTable;
    protected $userTable;
    protected $testTable;
    protected $advertisementTable;
    protected $educationTable;
    protected $experienceTable;
    protected $skillAdvertisementTable;
    protected $skillTable;
    protected $choiceTable;
    protected $applicationTable;
    protected $resultTable;
    protected $skillApplicationTable;

    public function applyAction()
    {
//        $this->forwardingToCorrectPlace();

        $globalHelper = new \Application\View\Helper\Helper();

        if ($globalHelper->isLogged()) {
            $idAdvertisement = (int)$this->params()->fromRoute('id', 0);
            $advertisement = $this->getAdvertisementTable()->getAdvertisement($idAdvertisement);
            $user = $globalHelper->getUserData();

            if (!$this->getApplicationTable()->existApplication($idAdvertisement, $user->idUser)) {

                $form = new ApplicationForm();

                $userAddress = $this->getAddressTable()->getAddress($user->idAddress);

                $educations = $this->getEducationTable()->getUserEducation($user->idUser);
                $experiences = $this->getExperienceTable()->getUserExperience($user->idUser);
                $idsSkill = $this->getSkillAdvertisementTable()->getSkillsIdForAdvertisement($idAdvertisement);

                $skills = [];
                foreach ($idsSkill as $idSkill) {
                    $skills[] = $this->getSkillTable()->getSkill($idSkill['Skill_idSkill']);
                }

                $request = $this->getRequest();
                if ($request->isPost()) {

                    $application = new Application();
                    $form->setInputFilter($application->getInputFilter());

                    $form->setData($request->getPost());
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $data['Advertisement_idAdvertisement'] = $idAdvertisement;
                        $data['User_idUser'] = $user->idUser;
                        $application->exchangeArray($data);

                        $this->getApplicationTable()->addApplication($application);

                        $idApplication = $this->getApplicationTable()->getLastIndex();
                        $idsSkill = $this->getSkillAdvertisementTable()->getSkillsIdForAdvertisement($idAdvertisement);

                        foreach ($idsSkill as $idSkill) {
                            $skillApplication = new SkillApplication();
                            $dataSA['Application_idApplication'] = $idApplication;
                            $dataSA['Skill_idSkill'] = $idSkill['Skill_idSkill'];
                            $dataSA['knowledge'] = $_REQUEST['rating-' . $idSkill['Skill_idSkill']];
                            $skillApplication->exchangeArray($dataSA);

                            $this->getSkillApplicationTable()->add($skillApplication);
                        }

                        $this->flashmessenger()->addMessage("Aplikacja zgłoszona pomyślnie. Poinformujemy Cię mailowo o dalszej rekrutacji.");
                        return $this->redirect()->toRoute('advertisement', array('action' => 'details', 'id' => $idAdvertisement));
                    }
                }

                return new ViewModel(['form' => $form, 'advertisement' => $advertisement, 'userData' => $user, 'userAddress' => $userAddress, 'educations' => $educations, 'experiences' => $experiences, 'skills' => $skills]);

            } else {
                $this->flashmessenger()->addMessage('Juz złożyłeś aplikację na to stanowisko. Nie możesz złozyć ponownie.');
                return $this->redirect()->toRoute('advertisement', array('action' => 'index'));
            }
        }

        $this->flashmessenger()->addMessage("Aby móc złożyć aplikacje, musisz być zalogowany!");
        return $this->redirect()->toRoute('account', array('action' => 'login'));
    }

    public function myApplicationsAction()
    {

        $globalHelper = new \Application\View\Helper\Helper();

        if ($globalHelper->isLogged()) {

            $user = $globalHelper->getUserData();

            $applications = $this->getApplicationTable()->getApplicationsByUserId($user->idUser);

            return new ViewModel(['applications' => $applications]);
        }

        return $this->redirect()->toRoute('account', array('action' => 'login'));
    }

    public function changeStatusAction()
    {
        $this->forwardingToCorrectPlace();

        $idApplication = (int)$this->params()->fromRoute('id', 0);

        $status = $this->params()->fromQuery('status');

        $application = $this->getApplicationTable()->getApplication($idApplication);

        $helper = new Helper();

        if ($status == 'speak') {
            $application->status = 'rozmowa';
            $this->flashmessenger()->addMessage("Kandydat przeszedł pomyślnie wstępna rekrutację i został poinformowany o rozmowie . ");
            $user = $this->getUserTable()->getUser($application->idUser);
            $helper->sendEmail('kontakt.rekrutacja.rs@gmail.com', $user->email, "zmiana statusu", 'Dzień dobry, <br /> Pragniemy poinformować, że przeszedłeś pomyślnie etapy rekrutacji w naszym systemie. <br /> Niedługo do Ciebie zadzwonimy i zaprosimy na rozmowę. <br /> <br /> Pozdrawiamy <br/> Zespół RecruitmentSystem ');
        }

        if ($status == 'reject') {
            $user = $this->getUserTable()->getUser($application->idUser);
            $helper->sendEmail('kontakt.rekrutacja.rs@gmail.com', $user->email, "zmiana statusu", "Twoja aplikacja nie spełniła naszych wymagań i została odrzucona . ");
            $application->status = 'odrzucona';
            $this->flashmessenger()->addMessage("Aplikacja została odrzucona . ");
        }
        $this->applicationTable->addApplication($application);

        return $this->redirect()->toRoute('advertisement', array('action' => 'list'));
    }

    public function detailsApplicationAction()
    {
        $this->forwardingToCorrectPlace();
        $idApplication = (int)$this->params()->fromRoute('id', 0);

        $application = $this->getApplicationTable()->getDetailsApplication($idApplication);
        if ($application) {
            $educations = $this->getEducationTable()->getUserEducation($application['User_idUser']);
            $experiences = $this->getExperienceTable()->getUserExperience($application['User_idUser']);
            $idsSkill = $this->getSkillAdvertisementTable()->getSkillsIdForAdvertisement($application['Advertisement_idAdvertisement']);

            $skills = [];
            foreach ($idsSkill as $idSkill) {
                $skills[]['skill'] = $this->getSkillTable()->getSkill($idSkill['Skill_idSkill']);
                $skills[sizeof($skills) - 1]['knowledge'] = $this->getSkillApplicationTable()->getKnowledge($idApplication, $idSkill['Skill_idSkill']);
            }
        } else {
            return $this->redirect()->toRoute('application', array('action' => 'index'));
        }

        return new ViewModel(['application' => $application, 'educations' => $educations, 'experiences' => $experiences, 'skills' => $skills]);
    }

    public function detailsAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {
            $idApplication = (int)$this->params()->fromRoute('id', 0);

            try {
                $user = $helper->getUserData();
                $application = $this->getApplicationTable()->getApplication($idApplication);

            } catch (\Exception $ex) {
                $this->flashmessenger()->addMessage("Oups!Coś poszło nie tak");
                return $this->redirect()->toRoute('application', array(
                    'action' => 'index'
                ));
            }
            if ($application->idUser == $user->idUser) {
                $application = $this->getApplicationTable()->getDetailsApplication($idApplication);
                $educations = $this->getEducationTable()->getUserEducation($application['User_idUser']);
                $experiences = $this->getExperienceTable()->getUserExperience($application['User_idUser']);
                $idsSkill = $this->getSkillAdvertisementTable()->getSkillsIdForAdvertisement($application['Advertisement_idAdvertisement']);

                $skills = [];
                foreach ($idsSkill as $idSkill) {
                    $skills[]['skill'] = $this->getSkillTable()->getSkill($idSkill['Skill_idSkill']);
                    $skills[sizeof($skills) - 1]['knowledge'] = $this->getSkillApplicationTable()->getKnowledge($idApplication, $idSkill['Skill_idSkill']);
                }

                return new ViewModel(['application' => $application, 'educations' => $educations, 'experiences' => $experiences, 'skills' => $skills]);

            }
            $this->flashmessenger()->addMessage("Nie posiadasz uprawnień!");

        }
        return $this->redirect()->toRoute('application', array(
            'action' => 'index'
        ));
    }

    public function applicationsAction()
    {
        $this->forwardingToCorrectPlace();

        $idAdvertisement = (int)$this->params()->fromRoute('id', 0);

        $applicationsNew = $this->getApplicationTable()->getApplicationsByAdvertisementIdAndStatus($idAdvertisement, 'nowa');
        $applicationsTest = $this->getApplicationTable()->getApplicationsByAdvertisementIdAndStatus($idAdvertisement, 'test');
        $applicationsCheck = $this->getApplicationTable()->getApplicationsByAdvertisementIdAndStatus($idAdvertisement, 'po tescie');
        $applicationsInterview = $this->getApplicationTable()->getApplicationsByAdvertisementIdAndStatus($idAdvertisement, 'rozmowa');
        $applicationsReject = $this->getApplicationTable()->getApplicationsByAdvertisementIdAndStatus($idAdvertisement, 'odrzucona');

        return new ViewModel(['applicationsNew' => $applicationsNew, 'applicationsTest' => $applicationsTest, 'applicationsCheck' => $applicationsCheck, 'applicationsInterview' => $applicationsInterview, 'applicationsReject' => $applicationsReject]);
    }

    public function deleteApplicationAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {
            $idApplication = (int)$this->params()->fromRoute('id', 0);

            try {
                $user = $helper->getUserData();
                $application = $this->getApplicationTable()->getApplication($idApplication);

            } catch (\Exception $ex) {
                $this->flashmessenger()->addMessage("Oups!Coś poszło nie tak");
                return $this->redirect()->toRoute('application', array(
                    'action' => 'index'
                ));
            }

            if ($application->idUser == $user->idUser) {
                try {
                    $this->getSkillApplicationTable()->deleteByApplication($idApplication);

                    $results = $this->getResultTable()->getResultsByApplication($idApplication);

                    foreach ($results as $result) {
                        $this->getChoiceTable()->deleteByResult($result->idResult);
                    }
                    $this->getResultTable()->deleteResultByApplication($idApplication);
                    $this->getApplicationTable()->deleteApplication($idApplication);


                } catch (\Exception $ex) {
                    $this->flashmessenger()->addMessage("Oups!Coś poszło nie tak");
                    return $this->redirect()->toRoute('application', array(
                        'action' => 'index'
                    ));
                }
                $this->flashmessenger()->addMessage("Aplikacja została anulowana pomyślnie . ");
                return $this->redirect()->toRoute('recruitment', array(
                    'action' => 'myApplications'
                ));

            }
            $this->flashmessenger()->addMessage("Nie masz uprawnień!");
        }
        return $this->redirect()->toRoute('application', array(
            'action' => 'index'
        ));

    }

    public function inviteAction()
    {
        $globalHelper = new \Application\View\Helper\Helper();

        if ($globalHelper->isLogged()) {
            $idApplication = (int)$this->params()->fromRoute('id', 0);
            $application = $this->getApplicationTable()->getDetailsApplication($idApplication);
            $idAdvertisement = $application['Advertisement_idAdvertisement'];

            $form = new InviteForm(null, $idAdvertisement);

            if (sizeof($form->getTestForAdvertisement($idAdvertisement)) < 1) {
                $form = null;
            }
            $request = $this->getRequest();
            if ($request->isPost()) {
                $invite = new Result();
                $form->setInputFilter($invite->getInputFilter());

                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData();
                    $data['User_idUser'] = $application['User_idUser'];
                    $data['Test_idTest'] = $_REQUEST['test'];
                    $data['Application_idApplication'] = $application['idApplication'];
                    $invite->exchangeArray($data);

                    $this->getResultTable()->add($invite);

                    $updateApplication = $this->getApplicationTable()->getApplication($idApplication);
                    $updateApplication->status = 'test';
                    $this->getApplicationTable()->addApplication($updateApplication);


                    $user = $this->getUserTable()->getUser($application['User_idUser']);
                    $dateInvite = new DateTime($invite->date);
                    $test = $this->getTestTable()->getTest($data['Test_idTest']);
                    $globalHelper->sendEmail('kontakt.rekrutacja.rs@gmail.com', $user->email, "zaproszenie do testu", 'Dzień dobry <br /> <br /> Pragniemy poinformować, że przeszedłeś pomyślnie etap rekrutacji i chcemy zaprosić Cię na wstępny test, który odbędzie się: ' . $dateInvite->format('Y-m-d, H:i')
                        . '. Do testu możesz przystąpić maksymalnie 15 minut od rozpoczęcia. <br /> Hasło dostępu: ' . $test->password . '<br /><br /> Pozdrawiamy <br /> Zespół RecruitmentSystem ');


                    $this->flashmessenger()->addMessage("Zaproszenie zostało wysłane!");
                    return $this->redirect()->toRoute('advertisement', array('action' => 'details', 'id' => $idAdvertisement));

                }

            }
            return new ViewModel(['form' => $form]);
        }
        return new ViewModel();
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
    public function getAddressTable()
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
    public function getUserTable()
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
    public function getTestTable()
    {
        if (!$this->testTable) {
            $sm = $this->getServiceLocator();
            $this->testTable = $sm->get('Test\Model\TestTable');
        }
        return $this->testTable;
    }

    /**
     * @return mixed
     */
    public
    function getAdvertisementTable()
    {
        if (!$this->advertisementTable) {
            $sm = $this->getServiceLocator();
            $this->advertisementTable = $sm->get('Advertisement\Model\AdvertisementTable');
        }
        return $this->advertisementTable;
    }

    /**
     * @return mixed
     */
    public
    function getChoiceTable()
    {
        if (!$this->choiceTable) {
            $sm = $this->getServiceLocator();
            $this->choiceTable = $sm->get('Test\Model\ChoiceTable');
        }
        return $this->choiceTable;
    }

    /**
     * @return mixed
     */
    public
    function getResultTable()
    {
        if (!$this->resultTable) {
            $sm = $this->getServiceLocator();
            $this->resultTable = $sm->get('Recruitment\Model\ResultTable');
        }
        return $this->resultTable;
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

    /**
     * @return mixed
     */
    public
    function getSkillAdvertisementTable()
    {
        if (!$this->skillAdvertisementTable) {
            $sm = $this->getServiceLocator();
            $this->skillAdvertisementTable = $sm->get('Advertisement\Model\SkillAdvertisementTable');
        }
        return $this->skillAdvertisementTable;
    }

    /**
     * @return mixed
     */
    public
    function getSkillTable()
    {
        if (!$this->skillTable) {
            $sm = $this->getServiceLocator();
            $this->skillTable = $sm->get('Advertisement\Model\SkillTable');
        }
        return $this->skillTable;
    }

    /**
     * @return mixed
     */
    public
    function getApplicationTable()
    {
        if (!$this->applicationTable) {
            $sm = $this->getServiceLocator();
            $this->applicationTable = $sm->get('Recruitment\Model\ApplicationTable');
        }
        return $this->applicationTable;
    }

    /**
     * @return mixed
     */
    public
    function getSkillApplicationTable()
    {
        if (!$this->skillApplicationTable) {
            $sm = $this->getServiceLocator();
            $this->skillApplicationTable = $sm->get('Recruitment\Model\SkillApplicationTable');
        }
        return $this->skillApplicationTable;
    }
}