<?php
/**
 * class Helper
 * User: Lukasz Marszalek
 * Date: 2016-09-04
 * Time: 12:05
 */
namespace Account\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Helper extends AbstractHelper
{
    public function isLogged()
    {
        return isset($_SESSION['user']) && !is_null($_SESSION['user']['storage']);
    }

    public function getUsername()
    {
        return $_SESSION['user']['storage'];
    }

}