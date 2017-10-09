<?php

namespace Application\Utils;

/**
 * Class UserHelper
 * @package Application\Utils
 */
class UserHelper
{
    static public function isAdministrator($roleId){
        return in_array(trim($roleId), ApplicationConst::USER_ADMIN_ARRAY);
    }

    static public function isInputRole($roleId){
        return in_array(trim($roleId), ApplicationConst::USER_INPUT_ARRAY);
    }

    static public function isViewer($roleId){
        return in_array(trim($roleId), ApplicationConst::USER_VIEW_ARRAY);
    }
}
