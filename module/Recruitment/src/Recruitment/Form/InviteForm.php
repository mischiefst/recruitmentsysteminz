<?php
/**
 * class  InviteForm
 * User: Lukasz Marszalek
 * Date: 2016-09-02
 * Time: 21:31
 */
namespace Recruitment\Form;

use Zend\Db\Sql\Sql;
use Zend\Form\Form;
use Zend\Form\Element;

class InviteForm extends Form
{
    public function __construct($name = null, $idAdvertisement)
    {
        parent::__construct('recruitment');
        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'form-horizontal'
        ));

        $this->add(array(
            'name' => 'idResult',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'test',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'form-control input-md',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Wybierz test*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                ),
                'value_options' => $this->getTestForAdvertisement($idAdvertisement),
            ),
        ));

        $this->add(array(
            'name' => 'date',
            'type' => 'text',
            'attributes' => array(
                'class' => 'datepicker-date form-control input-md',
                'style' => 'width: 200px;',
                'required' => true,
                'type' => 'datetime-local'
            ),
            'options' => array(
                'label' => 'Termin*',
                'label_attributes' => array(
                    'class' => 'col-md-4 control-label',
                )
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Wyślij zaproszenie',
                'id' => 'submitbutton',
            ),
        ));
    }

    public
    function getTestForAdvertisement($idAdvertisement)
    {
        $adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();

        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('test')->where(['Advertisement_idAdvertisement' => $idAdvertisement]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        $result = array();

        foreach ($results as $test) {
            $result[$test['idTest']] = $test['name'];
        }

        return $result;
    }
}