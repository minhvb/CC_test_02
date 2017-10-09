<?php

namespace Application\Utils;

use ZipArchive;

/**
 * Class CommonUtils
 *
 * @package Application\Utils
 */
class CommonUtils
{

    public static function encrypt($data)
    {
        return md5($data);
    }

    public static function validatePassword($password)
    {
        // todo: check length >= 8
        return preg_match(ApplicationConst::passwordRegex, $password);
    }

    public static function validateEmail($email)
    {
        // todo: check length <= 256
        if (strlen($email) > 256) {
            return false;
        } else {
            return preg_match(ApplicationConst::emailRegex, $email);
        }
    }

    public static function validateUserAdminRegex($userId)
    {
        // todo: check length <= 256
        return preg_match(ApplicationConst::userAdminRegex, $userId);
    }

    public static function validateUserEmailRegex($userId)
    {
        // todo: check length <= 256
        return preg_match(ApplicationConst::userEmailRegex, $userId);
    }

    public static function validateUserViewRegex($userId)
    {
        // todo: check length <= 256
        return preg_match(ApplicationConst::userViewRegex, $userId);
    }

    public static function validateFullSize($str){
        return !preg_match(ApplicationConst::notFullSizeRegex, $str);
    }

    public static function validateSecurityAnswer($answer)
    {
        if ($answer === null || preg_match(ApplicationConst::notFullSizeRegex, $answer) || self::lengthFullSize($answer) > 10) {
            return false;
        }

        return true;
    }

    public static function lengthFullSize($str, $encode = 'utf-8')
    {
        return mb_strlen($str, $encode);
    }

    public static function validateSecurityQuestion($questionId)
    {
        if (intval($questionId) == null) {
            return false;
        }

        return true;
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateToken($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * ex: $sorted = arrayOrderBy($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
     *
     * @return mixed
     */
    public static function arrayOrderBy()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);

        return array_pop($args);
    }

    static function createFileZip($files = array(), $destination = '', $overwrite = false)
    {
        $response = array('status' => false, 'message' => 'Fail');
        try {
            //if the zip file already exists and overwrite is false, return false
            if (file_exists($destination) && !$overwrite) {
                return $response;
            }
            $validFiles = array();
            //if files were passed in...
            if (is_array($files)) {
                //cycle through each file
                foreach ($files as $file) {
                    //make sure the file exists
                    if (file_exists($file)) {
                        $validFiles[] = $file;
                    }
                }
            }
            //if we have good files...
            if (count($validFiles)) {
                //create the archive
                $zip = new ZipArchive();
                if ($zip->open($destination, ($overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE)) !== true) {
                    return $response;
                }
                //add the files
                foreach ($validFiles as $file) {
                    $zip->addFile($file, basename($file));
                }
                //close the zip -- done!
                @$zip->close();
                //check to make sure the file exists
                return file_exists($destination);
            } else {
                return $response;
            }
        } catch (\Exception $ex) {
            return array('status' => false, 'message' => $ex->getMessage());
        }

    }

    static function replaceMessage($msg, $field, $value)
    {
        return str_replace("[" . $field . "]", "[" . $value . "]", $msg);
    }
     
    static function getPreviosPage($currentUrl, $currentPage)
    {
        $currentUrl = strpos($currentUrl, '?') !== false ? $currentUrl . '&' : $currentUrl . '?';
        $currentUrl = str_replace("?page=" . $currentPage . "&", "?", $currentUrl);
        $currentUrl = str_replace("&page=" . $currentPage . "&", "&", $currentUrl);
        $currentUrl .= 'page=' . ($currentPage - 1);
        return $currentUrl;
    }

    public static function validateFieldFullSizeAndLengh($value, $ruleLength)
    {
        if ($value) {
            if (preg_match(ApplicationConst::notFullSizeRegex, $value) || self::lengthFullSize($value) > $ruleLength) {
                return false;
            }
        }
        return true;
    }
    
    public static function getConfigFromFileIni($configName, $serviceLocator){
        $locationFile = BASE_PATH . $serviceLocator->get('Config')['configIni'];
        $f = @fopen($locationFile, "r");        
        $configValue = '';
        while ($line = fgets($f)) {
            $line = str_replace(array("\r\n", "\r", "\n"), "", $line);
            if(preg_match("/" . $configName . ":(.*)/si", $line, $result)){
                if(!empty($result)){
                    $configValue = trim($result[1]);    
                }
            } 
        }
        return $configValue;  
    }
    
    public static function convertRecruitmentTimeStatus($policyType){
        $policyStatus = array();
        foreach($policyType as $key => $attributeId){
            if($attributeId == ApplicationConst::IN_RECRUITMENT_TIME){
                $policyStatus[] = ApplicationConst::TYPE_IN_RECRUITMENT_TIME;
            } else if($attributeId == ApplicationConst::BEFORE_RECRUITMENT_TIME){
                $policyStatus[] = ApplicationConst::TYPE_BEFORE_RECRUITMENT_TIME;
            } else if($attributeId == ApplicationConst::AFTER_RECRUITMENT_TIME){
                $policyStatus[] = ApplicationConst::TYPE_AFTER_RECRUITMENT_TIME;
            }
        }
        return $policyStatus;
    }
    
    public function chboData($num) {
        $data = dechex($num);
        if (strlen($data) <= 2) {
            return $num;
        }
        $u = unpack("H*", strrev(pack("H*", $data)));
        $f = hexdec($u[1]);
        return $f;
    }
}
