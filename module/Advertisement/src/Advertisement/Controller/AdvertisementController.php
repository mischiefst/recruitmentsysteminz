<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-05
 * Time: 20:08
 */
namespace Advertisement\Controller;

use Advertisement\Form\AdvertisementForm;
use Advertisement\Model\Advertisement;
use Advertisement\Model\SkillAdvertisement;
use Application\View\Helper\Helper;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdvertisementController extends AbstractActionController
{
    protected $skillTable;
    protected $advertisementTable;
    protected $skillAdvertisementTable;
    protected $testTable;
    protected $questionTable;
    protected $questionTestTable;
    protected $skillApplicationTable;
    protected $applicationTable;
    protected $resultTable;
    protected $choiceTable;

    public function indexAction()
    {
        $advertisements = $this->getAdvertisementTable()->fetchAll();
        return new ViewModel(array('advertisements' => $advertisements));
    }

    public function listAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            $this->forwardingToCorrectPlace();

            $advertisements = $this->getAdvertisementTable()->fetchAll();

            return new ViewModel(array('advertisements' => $advertisements));
        }
        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    public function createAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            $this->forwardingToCorrectPlace();
            $form = new  AdvertisementForm();

            $skillsAvailable = $this->getSkillTable()->fetchAll();

            $skillsArray = [];
            foreach ($skillsAvailable as $item) {
                $skillsArray[] = $item;
            }

            $request = $this->getRequest();

            if ($request->isPost()) {
                $advertisement = new Advertisement();
                $form->setInputFilter($advertisement->getFullAdvertisementInputFilter());
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData();

                    $advertisement->exchangeArray($data);
                    $this->getAdvertisementTable()->addAdvertisement($advertisement);

                    $skills = explode(',', $data['skills']);

                    $idAdvertisement = $this->getAdvertisementTable()->getLastIndex();

                    foreach ($skills as $idSkill) {
                        $skillAdvertisement = new SkillAdvertisement();
                        $skillAdvertisement->advertisement_idAdvertisement = $idAdvertisement;
                        $skillAdvertisement->skill_idSkill = $idSkill;

                        $this->getSkillAdvertisementTable()->add($skillAdvertisement);
                    }

                    $this->flashmessenger()->addMessage("Ogłoszenie zostało dodane!");
                    return $this->redirect()->toRoute('advertisement', array('action' => 'list'));
                }
            }

            return new ViewModel(array('form' => $form, 'skillsArray' => $skillsArray));
        }
        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    public function detailsAction()
    {
        $idAdvertisement = (int)$this->params()->fromRoute('id', 0);

        if (!empty($idAdvertisement)) {
            try{
                $idsSkill = $this->getSkillAdvertisementTable()->getSkillsIdForAdvertisement($idAdvertisement);
            }
            catch(Exception $e){
                return $this->redirect()->toRoute('application', array('action' => 'index'));
            }

            if (count($idsSkill) >0 ) {
                $skills = [];
                foreach ($idsSkill as $idSkill) {
                    $skills[] = $this->getSkillTable()->getSkill($idSkill['Skill_idSkill']);
                }
                $advertisement = $this->getAdvertisementTable()->getAdvertisement($idAdvertisement);

                if ($advertisement) {
                    return new ViewModel(array('advertisement' => $advertisement, 'skills' => $skills));
                }
                else{
                    return $this->redirect()->toRoute('application', array('action' => 'index'));

                }
            }

        }

        return $this->redirect()->toRoute('advertisement', array('action' => 'index'));
    }

    public function editAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            $this->forwardingToCorrectPlace();
            $form = new  AdvertisementForm();

            $skillsAvailable = $this->getSkillTable()->fetchAll();

            $skillsArray = [];
            foreach ($skillsAvailable as $item) {
                $skillsArray[] = $item;
            }
            $idAdvertisement = (int)$this->params()->fromRoute('id', 0);

            try {
                $advertisement = $this->getAdvertisementTable()->getAdvertisement($idAdvertisement);
                $skillsAdv = $this->getSkillAdvertisementTable()->getSkillsIdForAdvertisement($idAdvertisement);

            } catch (Exception $e) {
                return $this->redirect()->toRoute('advertisement', array('action' => 'list'));
            }
            $form->bind($advertisement);

            $request = $this->getRequest();

            if ($request->isPost()) {
                $form->setInputFilter($advertisement->getFullAdvertisementInputFilter());
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $this->getAdvertisementTable()->addAdvertisement($advertisement);

                    $skillsNew = explode(',', $request->getPost()['skills']);

                    $skillsOld = [];
                    foreach ($skillsAdv as $skill) {
                        $skillsOld[] = $skill['Skill_idSkill'];
                    }

                    foreach ($skillsNew as $idSkill) {
                        $skillAdvertisement = new SkillAdvertisement();
                        $skillAdvertisement->advertisement_idAdvertisement = $idAdvertisement;
                        $skillAdvertisement->skill_idSkill = $idSkill;

                        if (!$this->getSkillAdvertisementTable()->isObject($skillAdvertisement)) {
                            $this->getSkillAdvertisementTable()->add($skillAdvertisement);

                        }
                    }

                    foreach ($skillsOld as $skill) {
                        if (!in_array($skill, $skillsNew)) {
                            $this->getSkillAdvertisementTable()->delete($skill, $idAdvertisement);
                        }
                    }

                    $this->flashmessenger()->addMessage("Ogłoszenie zostało dodane!");
                    return $this->redirect()->toRoute('advertisement', array('action' => 'list'));
                }
            }
            $form->setAttribute('action', '');
            $form->setAttribute('query', ['id' => $idAdvertisement]);
            return new ViewModel(array('form' => $form, 'skillsArray' => $skillsArray, 'skillsAdv' => $skillsAdv));
        }
        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }

    public function deleteAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
            $idAdvertisement = (int)$this->params()->fromRoute('id', 0);
            try {
                $user = $helper->getUserData();
                $advertisement = $this->getAdvertisementTable()->getAdvertisement($idAdvertisement);
                $this->getSkillAdvertisementTable()->deleteByAdvertisement($idAdvertisement);
                /** DELETING TEST */
                $tests = $this->getTestTable()->getTestsByAdvertisement($idAdvertisement);

                foreach ($tests as $test) {
                    $this->getQuestionTestTable()->deleteByTest($test->idTest);
                    $results = $this->getResultTable()->getResultsByTest($test->idTest);

                    foreach ($results as $result) {
                        $this->getChoiceTable()->deleteByResult($result->idResult);
                    }
                    $this->getResultTable()->deleteResultByTest($test->idTest);
                    $this->getTestTable()->delete($test->idTest);
                }

                $applications = $this->getApplicationTable()->getApplicationsByAdvertisement($idAdvertisement);

                foreach ($applications as $application) {
                    $this->getSkillApplicationTable()->deleteByApplication($application->idApplication);

                    $results = $this->getResultTable()->getResultsByApplication($application->idApplication);

                    foreach ($results as $result) {
                        $this->getChoiceTable()->deleteByResult($result->idResult);
                    }
                    $this->getResultTable()->deleteResultByApplication($application->idApplication);
                    $this->getApplicationTable()->deleteApplication($application->idApplication);
                }

                $this->getAdvertisementTable()->deleteAdvertisement($idAdvertisement);


            } catch (\Exception $ex) {
                $this->flashmessenger()->addMessage("Oups! Coś poszło nie tak");
                return $this->redirect()->toRoute('advertisement', array(
                    'action' => 'index'
                ));
            }

            $this->flashmessenger()->addMessage("Usunięcie ogłoszenia nastąpiło poprawnie!");
            return $this->redirect()->toRoute('advertisement', array(
                'action' => 'list'
            ));
        }
        $this->flashmessenger()->addMessage("Nie posiadasz uprawnień.");
        return $this->redirect()->toRoute('application', array(
            'action' => 'index'
        ));
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

    /**
     * @return \Zend\Http\Response
     */
    public
    function forwardingToCorrectPlace()
    {
        $globalHelper = new Helper();

        if (!$globalHelper->isRuleAdmin()) {
            return $this->redirect()->toRoute('application', array('action' => 'index'));
        }
    }

    /**
     * @return mixed
     */
    public
    function getTestTable()
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
    function getQuestionTestTable()
    {
        if (!$this->questionTestTable) {
            $sm = $this->getServiceLocator();
            $this->questionTestTable = $sm->get('Test\Model\QuestionTestTable');
        }
        return $this->questionTestTable;
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
    function getSkillAdvertisementTable()
    {
        if (!$this->skillAdvertisementTable) {
            $sm = $this->getServiceLocator();
            $this->skillAdvertisementTable = $sm->get('Advertisement\Model\SkillAdvertisementTable');
        }
        return $this->skillAdvertisementTable;
    }
}