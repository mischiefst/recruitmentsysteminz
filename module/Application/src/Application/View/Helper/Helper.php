<?php
/**
 * class Helper
 * User: Lukasz Marszalek
 * Date: 2016-09-04
 * Time: 12:05
 */
namespace Application\View\Helper;

use Account\Model\User;
use Account\Model\UserTable;
use Advertisement\Model\Skill;
use Advertisement\Model\SkillTable;
use PHPMailer;
use Recruitment\Model\Application;
use Recruitment\Model\ApplicationTable;
use Recruitment\Model\Result;
use Recruitment\Model\ResultTable;
use Test\Model\Answer;
use Test\Model\AnswerTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\View\Helper\AbstractHelper;


class Helper extends AbstractHelper
{
    protected $userTable;

    public function isLogged()
    {
        return isset($_SESSION['user']) && !is_null($_SESSION['user']['storage']);
    }

    public function isRuleAdmin()
    {
        if ($this->isLogged()) {

            $adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new User());
            $tableGateway = new TableGateway('user', $adapter, null, $resultSetPrototype);


            $userTable = new UserTable($tableGateway);
            $username = $_SESSION['user']['storage'];
            return ($userTable->getRuleByLogin($username) > 3);

        }
    }

    public function getUserData()
    {
        if ($this->isLogged()) {
            $adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new User());
            $tableGateway = new TableGateway('user', $adapter, null, $resultSetPrototype);


            $userTable = new UserTable($tableGateway);
            $username = $_SESSION['user']['storage'];

            return $userTable->getUserByLogin($username);
        }
    }

    public function getResult($idApplication)
    {
        if ($this->isLogged()) {
            $adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Result());
            $tableGateway = new TableGateway('result', $adapter, null, $resultSetPrototype);


            $resultTable = new ResultTable($tableGateway);

            return $resultTable->getResultByApplication($idApplication);
        }
    }


    public function getClosedAnswer($idAnswer)
    {
        if ($this->isLogged()) {

            $answerTable = $this->getAnswerTable();

            return $answerTable->getAnswer($idAnswer)->answer;
        }
    }

    /**
     * @param $choice
     * @return bool
     * @throws \Exception
     */
    public function checkCorrectAnswer($choice)
    {

        if ($choice['type'] == 'closed') {
            $answer = $this->getAnswerTable()->getAnswer($choice['answer']);
            $correct = ($answer->amount_of_points > 0);
        } else {
            $answer = $this->getAnswerTable()->getSemiAnswer($choice['Question_idQuestion']);
            $correct = ($answer->answer == $choice['answer']); //ZMIENIC WIELKOSC< biale znaki itd
        }
        return $correct;
    }

    public function formError($render, $form, $name)
    {
        return $render->formElementErrors()
            ->setMessageOpenFormat('<div class="alert alert-warning customize-warning">')
            ->setMessageSeparatorString('</div><div class="alert alert-warning customize-warning">')
            ->setMessageCloseString('</div>')
            ->render($form->get($name));
    }

    public function changeKnowledge($valueKnowledge)
    {
        $name = '';

        switch ((int)$valueKnowledge) {
            case 1:
                $name = 'Słaba znajomości';
                break;
            case 2:
                $name = 'Podstawowa znajomość';
                break;
            case 3:
                $name = 'Przeciętna znajomość';
                break;
            case 4:
                $name = 'Dobra znajomość';
                break;
            case 5:
                $name = 'Bardzo dobra znajomość';
                break;
        }

        return $name;
    }

    public function rejectApplication($idApplication)
    {
        $adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Application());
        $tableGateway = new TableGateway('application', $adapter, null, $resultSetPrototype);

        $applicationTable = new ApplicationTable($tableGateway);

        $application = $applicationTable->getApplication($idApplication);

        $application->status = 'odrzucona';
        $applicationTable->addApplication($application);
    }

    public function getAnswerTable()
    {
        $adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Answer());
        $tableGateway = new TableGateway('answer', $adapter, null, $resultSetPrototype);

        $answerTable = new AnswerTable($tableGateway);

        return $answerTable;
    }

    public function getSkillName($idSkill)
    {
        $adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Skill());
        $tableGateway = new TableGateway('skill', $adapter, null, $resultSetPrototype);

        $skillTable = new SkillTable($tableGateway);

        $skill = $skillTable->getSkill($idSkill);

        return $skill->name;
    }

    public function sendEmail($from, $to, $title, $body)
    {
        require 'PHPMailer/PHPMailerAutoload.php';

        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kontakt.rekrutacja.rs@gmail.com';
        $mail->Password = 'kontaktrs';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($from);
        $mail->addAddress($to);


        $mail->isHTML(true);

        $bodyContent = '<p>' . $body . '</p>';

        $mail->Subject = 'Komunikat z systemu rekrutacji: ' . $title;
        $mail->Body = $bodyContent;

        return $mail->send();
    }
}