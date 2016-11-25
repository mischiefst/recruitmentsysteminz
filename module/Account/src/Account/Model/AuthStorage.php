<?php

/**
 * class AuthStorage
 * User: Lukasz Marszalek
 * Date: 2016-09-04
 * Time: 11:30
 */
namespace Account\Model;

use Zend\Authentication\Storage;
use Zend\Session\Container;

class AuthStorage extends Storage\Session
{

    /**
     * @param int $rememberMe
     * @param int $time
     */
    public function setRememberMe($rememberMe = 0, $time = 1209600)
    {
    }

    /**
     *
     */
    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
        $admin_session = new Container('user');
        $admin_session->getManager()->getStorage()->clear();
    }
}