<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-05
 * Time: 20:08
 */
namespace Advertisement\Controller;

use Advertisement\Form\SkillForm;
use Advertisement\Model\Skill;
use Application\View\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SkillController extends AbstractActionController
{

    protected $skillTable;
    protected $skillApplicationTable;
    protected $skillAdvertisementTable;

    public function addAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            $this->forwardingToCorrectPlace();

            $form = new SkillForm();

            $request = $this->getRequest();
            if ($request->isPost()) {
                $skill = new Skill();
                $form->setInputFilter($skill->getInputFilter());
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $skill->exchangeArray($form->getData());

                    $this->getSkillTable()->addSkill($skill);
                    $this->flashmessenger()->addMessage("Dodano umiejętność");
                    return $this->redirect()->toRoute('skill', array('action' => 'add'));
                }
            }

            $skills = $this->getSkillTable()->fetchAll();

            return new ViewModel(array('form' => $form, 'skills' => $skills));
        }

        return $this->redirect()->toRoute('application', array('action' => 'index'));
    }


    public function deleteAction()
    {
        $this->forwardingToCorrectPlace();

        $idSkill = (int)$this->params()->fromRoute('id', 0);

        try {
            $this->getSkillApplicationTable()->deleteBySkill($idSkill);
            $this->getSkillAdvertisementTable()->deleteBySkill($idSkill);

            $this->getSkillTable()->deleteSkill($idSkill);

        } catch (\Exception $e) {
            $this->flashmessenger()->addMessage("Oups! Coś poszło nie tak.");
            return $this->redirect()->toRoute('skill', array('action' => 'add'));

        }
        $this->flashmessenger()->addMessage("Umiejętność została usunięta.");
        return $this->redirect()->toRoute('skill', array('action' => 'add'));

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
    function getSkillApplicationTable()
    {
        if (!$this->skillApplicationTable) {
            $sm = $this->getServiceLocator();
            $this->skillApplicationTable = $sm->get('Recruitment\Model\SkillApplicationTable');
        }
        return $this->skillApplicationTable;
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


    public
    function forwardingToCorrectPlace()
    {
        $globalHelper = new Helper();

        if (!$globalHelper->isRuleAdmin()) {
            return $this->redirect()->toRoute('application', array('action' => 'index'));
        }
    }


}