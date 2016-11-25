<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-05
 * Time: 20:08
 */
namespace Test\Controller;

use Application\View\Helper\Helper;
use Test\Form\EditForm;
use Test\Form\SolveForm;
use Test\Form\TestForm;
use Test\Model\Answer;
use Test\Model\Choice;
use Test\Model\Question;
use Test\Model\QuestionTest;
use Test\Model\Test;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TestController extends AbstractActionController
{
    protected $testTable;
    protected $userTable;
    protected $questionTable;
    protected $answerTable;
    protected $applicationTable;
    protected $resultTable;
    protected $choiceTable;
    protected $skillApplicationTable;
    protected $questionTestTable;

    public function testsAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            $this->forwardingToCorrectPlace();

            $tests = $this->getTestTable()->fetchAll();

            return new ViewModel(['tests' => $tests]);
        }
        return new ViewModel();
    }

    public function solveAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            //SPRAWDZ CZY MOZESZ ROZWIAZYAC TEST - KONIECZNE

            $idInvite = (int)$this->params()->fromRoute('id', 0);
            try {
                $result = $this->getResultTable()->getResult($idInvite);
                $application = $this->getApplicationTable()->getApplication($result->idApplication);

            } catch (\Exception $e) {
                $this->flashmessenger()->addMessage("Nie masz uprawnień");
                return $this->redirect()->toRoute('test', array('action' => 'my-tests'));
            }


            if ($result->idUser == $helperGlobal->getUserData()->idUser && $application->status == 'test') {


                $test = $this->getTestTable()->getTest($result->idTest);
                if (!isset($_COOKIE['test-' . $result->idResult])) {
                    setcookie('test-' . $result->idResult, time(), time() + ($test->time * 60) + 30 * 60, "/");
                }

                $questions = $this->getQuestionTestTable()->getQuestionsIdForTest($test->idTest);

                $questionsOpen = [];
                $questionsSemi = [];
                $questionsClosed = [];
                $tmp = 0;

                foreach ($questions as $q) {
                    $question = $this->getQuestionTable()->getQuestion($q['Question_idQuestion']);

                    switch ($question->type) {
                        case 'open': {
                            $questionsOpen[] = $question;
                        }
                            break;
                        case 'semi-open': {
                            $questionsSemi[] = $question;
                        }
                            break;
                        case 'closed' : {
                            $answers = $this->getAnswerTable()->getAnswerClosed($question->idQuestion);
                            $questionsClosed[$tmp]['question'] = $question;
                            $questionsClosed[$tmp]['answers'] = $answers;
                            $tmp++;
                        }
                            break;
                    }
                }

                $request = $this->getRequest();

                if ($request->isPost()) {

                    foreach ($request->getPost() as $key => $value) {
                        $choice = new Choice();
                        $data = [];
                        $data['Result_idResult'] = $idInvite;
                        $data['User_idUser'] = $helperGlobal->getUserData()->idUser;
                        if (strpos($key, 'questionOpen') !== false) {
                            $idQuestion = (int)substr(strrchr($key, '-'), 1);
                            $data['answer'] = $value;
                            $data['Question_idQuestion'] = $idQuestion;
                            $data['points'] = null;

                            $choice->exchangeArray($data);

                            $this->getChoiceTable()->addChoice($choice);

                        } elseif (strpos($key, 'questionSemi') !== false) {
                            $idQuestion = (int)substr(strrchr($key, '-'), 1);
                            $data['answer'] = $value;
                            $data['Question_idQuestion'] = $idQuestion;
                            $data['points'] = null; //sprawdzac?

                            $choice->exchangeArray($data);

                            $this->getChoiceTable()->addChoice($choice);

                        } elseif (strpos($key, 'questionClosed') !== false) {
                            $idQuestion = (int)substr(strrchr($key, '-'), 1);
                            $data['answer'] = $value;
                            $data['Question_idQuestion'] = $idQuestion;
                            $data['points'] = null;

                            $choice->exchangeArray($data);

                            $this->getChoiceTable()->addChoice($choice);
                        }
                    }

                    $application = $this->getApplicationTable()->getApplication($result->idApplication);

                    $application->status = 'po tescie';
                    $this->getApplicationTable()->addApplication($application);

                    $this->flashmessenger()->addMessage("Odpowiedzi zostały wysłane!");
                    return $this->redirect()->toRoute('test', array('action' => 'my-tests'));
                }

                return new ViewModel(['idResult' => $idInvite, 'test' => $test, 'questionsOpen' => $questionsOpen, 'questionsSemi' => $questionsSemi, 'questionsClosed' => $questionsClosed,]);
            } else {
                $this->flashmessenger()->addMessage("Nie masz uprawnień");
                return $this->redirect()->toRoute('test', array('action' => 'my-tests'));
            }
        }

        return new ViewModel();
    }

    public function createAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            $this->forwardingToCorrectPlace();
            $form = new TestForm();

            $request = $this->getRequest();

            if ($request->isPost()) {
                $test = new Test();
                $form->setInputFilter($test->getInputFilter());
                $form->setData($request->getPost());
                if ($form->isValid()) {

                    $data = $form->getData();
                    $data['Advertisement_idAdvertisement'] = $data['advertisement'];
                    $test->exchangeArray($data);
                    $this->getTestTable()->addTest($test);


                    $questionsOpen = $request->getPost()['questions-open'];
                    $questionsSemi = $request->getPost()['questions-semi'];
                    $questionsClosed = $request->getPost()['questions-closed'];

                    /**  OPEN QUESTIONS **/
                    foreach ($questionsOpen as $qOpen) {
                        if (!empty($qOpen['question'])) {
                            $question = new Question();
                            $question->text = $qOpen['question'];
                            $question->type = 'open';

                            $this->getQuestionTable()->addQuestion($question);
                            $questionTest = new QuestionTest();
                            $questionTest->test_idTest = $this->getTestTable()->getLastIndex();
                            $questionTest->question_idQuestion = $this->getQuestionTable()->getLastIndex();

                            $this->getQuestionTestTable()->add($questionTest);
                        }
                    }

                    /** SEMI OPEN QUESTIONS **/
                    foreach ($questionsSemi as $qSemi) {
                        if (!empty($qSemi['question'])) {
                            $question = new Question();
                            $question->text = $qSemi['question'];
                            $question->type = 'semi-open';

                            $this->getQuestionTable()->addQuestion($question);
                            $questionTest = new QuestionTest();
                            $questionTest->test_idTest = $this->getTestTable()->getLastIndex();
                            $questionTest->question_idQuestion = $this->getQuestionTable()->getLastIndex();

                            $this->getQuestionTestTable()->add($questionTest);

                            $answer = new Answer();
                            $answer->answer = $qSemi['answer'];
                            $answer->amount_of_points = 1;
                            $answer->idQuestion = $this->getQuestionTable()->getLastIndex();

                            $this->getAnswerTable()->addAnswer($answer);
                        }
                    }

                    /**  CLOSED QUESTIONS **/
                    foreach ($questionsClosed as $qClosed) {
                        if (!empty($qClosed['question'])) {
                            $question = new Question();
                            $question->text = $qClosed['question'];
                            $question->type = 'closed';

                            $this->getQuestionTable()->addQuestion($question);
                            $questionTest = new QuestionTest();
                            $questionTest->test_idTest = $this->getTestTable()->getLastIndex();
                            $questionTest->question_idQuestion = $this->getQuestionTable()->getLastIndex();

                            $this->getQuestionTestTable()->add($questionTest);


                            $answer = new Answer();
                            $answer->answer = $qClosed['answer-correct'];
                            $answer->amount_of_points = 1;
                            $answer->idQuestion = $this->getQuestionTable()->getLastIndex();
                            $this->getAnswerTable()->addAnswer($answer);

                            $answer->answer = $qClosed['answer-incorrect1'];
                            $answer->amount_of_points = 0;
                            $this->getAnswerTable()->addAnswer($answer);


                            $answer->answer = $qClosed['answer-incorrect2'];
                            $answer->amount_of_points = 0;
                            $this->getAnswerTable()->addAnswer($answer);

                            $answer->answer = $qClosed['answer-incorrect3'];
                            $answer->amount_of_points = 0;
                            $this->getAnswerTable()->addAnswer($answer);

                        }
                    }


                    $this->flashmessenger()->addMessage("Test został utworzony!");
                    return $this->redirect()->toRoute('test', array('action' => 'tests'));
                }
            }

            return new ViewModel(['form' => $form]);
        }
    }

    public function answersAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            $this->forwardingToCorrectPlace();
            $idResult = (int)$this->params()->fromRoute('id', 0);

            $answers = $this->getChoiceTable()->getChoices($idResult);
            if (count($answers) > 0) {
                return new ViewModel(['answers' => $answers, 'idResult' => $idResult]);
            }
            else{
                return $this->redirect()->toRoute('application', array('action' => 'index'));

            }

        }
        $this->flashmessenger()->addMessage("Nie posiadasz odpowiednich uprawnień!");
        return new ViewModel();
    }

    public function countAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            $this->forwardingToCorrectPlace();

            $request = $this->getRequest();

            if ($request->isPost()) {
                $allPoints = 0;

                $idResult = (int)$this->params()->fromRoute('id', 0);
                $answers = $this->getChoiceTable()->getChoices($idResult);

                foreach ($answers as $answer) {
                    if ($answer['type'] != 'open') {
                        $idChoice = $answer['idChoice'];
                        $choice = $this->getChoiceTable()->getChoice($idChoice);
                        $choice->points = ($helperGlobal->checkCorrectAnswer($answer)) ? 1 : 0; //ZMIANA jesli polotwarte wiecej puntkoweane!!!!!
                        $allPoints += $choice->points;
                        $this->getChoiceTable()->addChoice($choice);
                    }
                }

                $data = $request->getPost();

                foreach ($data as $key => $points) {
                    $idChoice = (int)substr(strrchr($key, '-'), 1);
                    $choice = $this->getChoiceTable()->getChoice($idChoice);
                    $choice->points = $points;
                    $allPoints += $points;
                    $this->getChoiceTable()->addChoice($choice);
                }

                $result = $this->getResultTable()->getResult($idResult);
                $result->score = $allPoints;
                $this->getResultTable()->add($result);

                $this->flashmessenger()->addMessage("Punkty zostały podliczone. Powiadomiono Kandydata o wyniku.");

                $user = $this->getUserTable()->getUser($result->idUser);
                $helperGlobal->sendEmail('kontakt.rekrutacja.rs@gmail.com', $user->email, "wynik testu", "Pragniemy poinformować, że Twoj test został sprawdzony i uzyskałeś wynik: " . $allPoints . '. Niedługo zostaniesz poinformowany o statusie Twojej aplikacji.');
                return $this->redirect()->toRoute('test', array('action' => 'allTests'));

            }
            return $this->redirect()->toRoute('test', array('action' => 'tests'));
        }
        $this->flashmessenger()->addMessage("Nie posiadasz odpowiednich uprawnień!");
        return new ViewModel();
    }

    public function myTestsAction()
    {
        $helperGlobal = new Helper();
        if ($helperGlobal->isLogged()) {
            $userId = $helperGlobal->getUserData()->idUser;
            $testsNew = $this->getResultTable()->getResultsNewByUserId($userId);
            $testsEnded = $this->getResultTable()->getResultsEndedByUserId($userId);

            return new ViewModel(['testsNew' => $testsNew, 'testsEnded' => $testsEnded]);

        }
        $this->flashmessenger()->addMessage("Nie jesteś zalogowany w systemie");
        return $this->redirect()->toRoute('account', array('action' => 'login'));
    }

    public function editAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
            $idTest = (int)$this->params()->fromRoute('id', 0);
            $test = $this->getTestTable()->getTest($idTest);

            $questions = $this->getQuestionTestTable()->getQuestionsIdForTest($idTest);

            $questionsOpen = [];
            $questionsSemi = [];
            $questionsClosed = [];
            $tmp = 0;

            foreach ($questions as $q) {
                $question = $this->getQuestionTable()->getQuestion($q['Question_idQuestion']);

                switch ($question->type) {
                    case 'open': {
                        $questionsOpen[] = $question;
                    }
                        break;
                    case 'semi-open': {
                        $questionsSemi[] = $question;
                    }
                        break;
                    case 'closed' : {
                        $answers = $this->getAnswerTable()->getAnswerClosed($question->idQuestion);
                        $questionsClosed[$tmp]['question'] = $question;
                        $questionsClosed[$tmp]['answers'] = $answers;
                        $tmp++;
                    }
                        break;
                }
            }

            return new ViewModel(['test' => $test, 'questionsOpen' => $questionsOpen, 'questionsSemi' => $questionsSemi, 'questionsClosed' => $questionsClosed,]);
        }

        $this->flashmessenger()->addMessage("Nie posiadasz uprawnień.");
        return $this->redirect()->toRoute('application', array(
            'action' => 'index'
        ));
    }

    public function editDataAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();

            $form = new EditForm();
            $idTest = (int)$this->params()->fromRoute('id', 0);
            $test = $this->getTestTable()->getTest($idTest);
            $form->bind($test);

            $request = $this->getRequest();

            if ($request->isPost()) {
                $data = $request->getPost();
                $data['advertisement'] = $test->idAdvertisement;
                $form->setInputFilter($test->getInputFilter());
                $form->setData($data);
                if ($form->isValid()) {
                    $test->idAdvertisement = $data['advertisement'];
                    $this->getTestTable()->addTest($test);
                }

                $this->flashmessenger()->addMessage("Edycja przebiegła pomyślnie!");
                return $this->redirect()->toRoute('test', array(
                    'action' => 'edit', 'id' => $test->idTest
                ));
            }
            $form->setAttribute('action', '');
            $form->setAttribute('query', ['id' => $idTest]);
            return new ViewModel(['form' => $form]);

        }

        $this->flashmessenger()->addMessage("Nie posiadasz uprawnień.");
        return $this->redirect()->toRoute('application', array(
            'action' => 'index'
        ));
    }

    public function deleteQuestionAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
            $idQuestion = (int)$this->params()->fromRoute('id', 0);
            $idTest = $this->params()->fromQuery('test');

            try {
                $question = $this->getQuestionTable()->getQuestion($idQuestion);

                if ($question->type != 'open') {
                    $this->getAnswerTable()->deleteByQuestion($idQuestion);
                }
                $this->getQuestionTestTable()->deleteByQuestion($idQuestion);
                $this->getChoiceTable()->deleteByQuestion($idQuestion);
                $this->getQuestionTable()->delete($idQuestion);

            } catch (\Exception $ex) {
                $this->flashmessenger()->addMessage("Oups! Coś poszło nie tak");
                return $this->redirect()->toRoute('application', array(
                    'action' => 'index'
                ));
            }

            $this->flashmessenger()->addMessage("Pytanie zostało usunięte poprawnie!");
            return $this->redirect()->toRoute('test', array(
                'action' => 'edit', 'id' => $idTest
            ));

        }

        $this->flashmessenger()->addMessage("Nie posiadasz uprawnień.");
        return $this->redirect()->toRoute('application', array(
            'action' => 'index'
        ));
    }

    public function editQuestionAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
            $idQuestion = (int)$this->params()->fromRoute('id', 0);
            $idTest = $this->params()->fromQuery('test');

            try {
                $question = $this->getQuestionTable()->getQuestion($idQuestion);
                $answers = null;
                if ($question->type == 'semi-open') {
                    $answers['correct'] = $this->getAnswerTable()->getSemiAnswer($question->idQuestion);
                } elseif ($question->type == 'closed') {
                    $ans = $this->getAnswerTable()->getAnswerClosed($question->idQuestion);
                    $i = 1;
                    foreach ($ans as $a) {
                        if ($a->amount_of_points > 0) {
                            $answers['correct'] = $a;
                        } else {
                            $answers['incorrect-' . $i] = $a;
                            $i++;
                        }
                    }
                }
                $test = $this->getTestTable()->getTest($idTest);

                $request = $this->getRequest();

                if ($request->isPost()) {
                    $data = $request->getPost();
                    $quest = $this->getQuestionTable()->getQuestion($idQuestion);

                    foreach ($data as $d) {
                        $questionEdited = $d[$idQuestion];
                        if (!in_array('', $questionEdited)) {
                            if ($quest->type == 'open') {
                                $quest->text = $questionEdited['question'];
                                $this->getQuestionTable()->addQuestion($quest);
                            } else if ($quest->type == 'semi-open') {
                                $quest->text = $questionEdited['question'];
                                $answer = $this->getAnswerTable()->getAnswer($questionEdited['answerId']);
                                $answer->answer = $questionEdited['answer'];
                                $this->getAnswerTable()->addAnswer($answer);
                                $this->getQuestionTable()->addQuestion($quest);
                            } else {

                                //SPRAWDZIC CZY NIE PUSTE!!!
                                $quest->text = $questionEdited['question'];
                                $answerCorrect = $this->getAnswerTable()->getAnswer($questionEdited['answer-correctId']);
                                $answerCorrect->answer = $questionEdited['answer-correct'];
                                $this->getAnswerTable()->addAnswer($answerCorrect);

                                $answerIncorrect1 = $this->getAnswerTable()->getAnswer($questionEdited['answer-incorrect1Id']);
                                $answerIncorrect1->answer = $questionEdited['answer-incorrect1'];
                                $this->getAnswerTable()->addAnswer($answerIncorrect1);


                                $answerIncorrect2 = $this->getAnswerTable()->getAnswer($questionEdited['answer-incorrect2Id']);
                                $answerIncorrect2->answer = $questionEdited['answer-incorrect2'];
                                $this->getAnswerTable()->addAnswer($answerIncorrect2);

                                $answerIncorrect3 = $this->getAnswerTable()->getAnswer($questionEdited['answer-incorrect3Id']);
                                $answerIncorrect3->answer = $questionEdited['answer-incorrect3'];
                                $this->getAnswerTable()->addAnswer($answerIncorrect3);

                                $this->getQuestionTable()->addQuestion($quest);
                            }
                        } else {
                            $this->flashmessenger()->addMessage("Wprowadzono niepoprawne dane! Edycja nie powiodła się!");
                            return $this->redirect()->toRoute('test', array(
                                'action' => 'edit', 'id' => $idTest
                            ));
                        }

                    }
                    $this->flashmessenger()->addMessage("Zedytowano pytanie!");

                    return $this->redirect()->toRoute('test', array(
                        'action' => 'edit', 'id' => $idTest
                    ));
                }

            } catch
            (\Exception $ex) {
                $this->flashmessenger()->addMessage("Oups! Coś poszło nie tak");
                return $this->redirect()->toRoute('application', array(
                    'action' => 'index'
                ));
            }
            return new ViewModel(['test' => $test, 'question' => $question, 'answers' => $answers]);
        }

        $this->flashmessenger()->addMessage("Nie posiadasz uprawnień.");
        return $this->redirect()->toRoute('application', array(
            'action' => 'index'
        ));
    }

    public function newQuestionAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
            $idTest = (int)$this->params()->fromRoute('id', 0);
            try {
                $test = $this->getTestTable()->getTest($idTest);

                $request = $this->getRequest();
                if ($request->isPost()) {
                    $data = $request->getPost();
                    $questionsOpen = $request->getPost()['questions-open'];
                    $questionsSemi = $request->getPost()['questions-semi'];
                    $questionsClosed = $request->getPost()['questions-closed'];

                    $empty = true;
                    /**  OPEN QUESTIONS **/
                    foreach ($questionsOpen as $qOpen) {
                        if (!empty($qOpen['question'])) {
                            $question = new Question();
                            $question->text = $qOpen['question'];
                            $question->type = 'open';

                            $this->getQuestionTable()->addQuestion($question);
                            $questionTest = new QuestionTest();
                            $questionTest->test_idTest = $idTest;
                            $questionTest->question_idQuestion = $this->getQuestionTable()->getLastIndex();

                            $this->getQuestionTestTable()->add($questionTest);
                            $empty = false;
                        }
                    }

                    /** SEMI OPEN QUESTIONS **/
                    foreach ($questionsSemi as $qSemi) {
                        if (!empty($qSemi['question'])) {
                            $question = new Question();
                            $question->text = $qSemi['question'];
                            $question->type = 'semi-open';

                            $this->getQuestionTable()->addQuestion($question);
                            $questionTest = new QuestionTest();
                            $questionTest->test_idTest = $idTest;
                            $questionTest->question_idQuestion = $this->getQuestionTable()->getLastIndex();

                            $this->getQuestionTestTable()->add($questionTest);

                            $answer = new Answer();
                            $answer->answer = $qSemi['answer'];
                            $answer->amount_of_points = 1;
                            $answer->idQuestion = $this->getQuestionTable()->getLastIndex();

                            $this->getAnswerTable()->addAnswer($answer);
                            $empty = false;
                        }
                    }

                    /**  CLOSED QUESTIONS **/
                    foreach ($questionsClosed as $qClosed) {
                        if (!empty($qClosed['question'])) {
                            $question = new Question();
                            $question->text = $qClosed['question'];
                            $question->type = 'closed';

                            $this->getQuestionTable()->addQuestion($question);
                            $questionTest = new QuestionTest();
                            $questionTest->test_idTest = $idTest;
                            $questionTest->question_idQuestion = $this->getQuestionTable()->getLastIndex();

                            $this->getQuestionTestTable()->add($questionTest);


                            $answer = new Answer();
                            $answer->answer = $qClosed['answer-correct'];
                            $answer->amount_of_points = 1;
                            $answer->idQuestion = $this->getQuestionTable()->getLastIndex();
                            $this->getAnswerTable()->addAnswer($answer);

                            $answer->answer = $qClosed['answer-incorrect1'];
                            $answer->amount_of_points = 0;
                            $this->getAnswerTable()->addAnswer($answer);


                            $answer->answer = $qClosed['answer-incorrect2'];
                            $answer->amount_of_points = 0;
                            $this->getAnswerTable()->addAnswer($answer);

                            $answer->answer = $qClosed['answer-incorrect3'];
                            $answer->amount_of_points = 0;
                            $this->getAnswerTable()->addAnswer($answer);
                            $empty = false;
                        }
                    }

                    if ($empty) {
                        $this->flashmessenger()->addMessage("Pytanie nie zostało dodane, podano niepoprawne dane.");
                    } else {
                        $this->flashmessenger()->addMessage("Pytanie zostało dodane.");
                    }
                    return $this->redirect()->toRoute('test', array(
                        'action' => 'edit', 'id' => $idTest
                    ));

                }

            } catch
            (\Exception $ex) {
                $this->flashmessenger()->addMessage("Oups! Coś poszło nie tak");
                return $this->redirect()->toRoute('application', array(
                    'action' => 'index'
                ));
            }
            return new ViewModel();
        }

        $this->flashmessenger()->addMessage("Nie posiadasz uprawnień.");
        return $this->redirect()->toRoute('application', array(
            'action' => 'index'
        ));
    }

    public
    function deleteAction()
    {
        $helper = new \Application\View\Helper\Helper();

        if ($helper->isLogged()) {
            $this->forwardingToCorrectPlace();
            $idTest = (int)$this->params()->fromRoute('id', 0);

            try {
                $test = $this->getTestTable()->getTest($idTest);

                $this->getQuestionTestTable()->deleteByTest($idTest);

                $results = $this->getResultTable()->getResultsByTest($idTest);

                foreach ($results as $result) {
                    $this->getChoiceTable()->deleteByResult($result->idResult);
                    $this->getSkillApplicationTable()->deleteByApplication($result->idApplication);
                    $this->getResultTable()->deleteResultByApplication($result->idApplication);

                    $this->getApplicationTable()->deleteApplication($result->idApplication);
                }
                $this->getResultTable()->deleteResultByTest($idTest);
                $this->getTestTable()->delete($idTest);


            } catch (\Exception $ex) {
                $this->flashmessenger()->addMessage("Oups! Coś poszło nie tak");
                return $this->redirect()->toRoute('application', array(
                    'action' => 'index'
                ));
            }

            $this->flashmessenger()->addMessage("Test został usunięty pomyślnie.");
            return $this->redirect()->toRoute('test', array(
                'action' => 'tests'
            ));
        }

        $this->flashmessenger()->addMessage("Nie posiadasz uprawnień.");
        return $this->redirect()->toRoute('application', array(
            'action' => 'index'
        ));
    }

    public
    function allTestsAction()
    {
        $this->forwardingToCorrectPlace();
        $applications = $this->getApplicationTable()->getApplicationTest();

        $applicationsTest = $this->getApplicationTable()->getApplicationTestByStatus('test');
        $applicationsAfterTest = $this->getApplicationTable()->getApplicationTestByStatus('po tescie');
        $applicationsSpeak = $this->getApplicationTable()->getApplicationTestByStatus('rozmowa');


        return new ViewModel(['applications' => $applications, 'applicationsSpeak' => $applicationsSpeak, 'applicationsTest' => $applicationsTest, 'applicationsAfterTest' => $applicationsAfterTest]);
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
    function getAnswerTable()
    {
        if (!$this->answerTable) {
            $sm = $this->getServiceLocator();
            $this->answerTable = $sm->get('Test\Model\AnswerTable');
        }
        return $this->answerTable;
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
    function getQuestionTable()
    {
        if (!$this->questionTable) {
            $sm = $this->getServiceLocator();
            $this->questionTable = $sm->get('Test\Model\QuestionTable');
        }
        return $this->questionTable;
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

}