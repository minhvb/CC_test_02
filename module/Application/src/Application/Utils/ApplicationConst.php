<?php
namespace Application\Utils;

/**
 * Class ApplicationConst
 * @package Application\Utils
 */
class ApplicationConst
{
    //to be define constant
    const CONSTANT_1 = '';

    //name of session contain logged user information
    const USER_INFO = 'USER_IDENTITY';
    const REFERER_PAGE = 'REFERER_PAGE';

    const ERROR_AJAX_EXPIRED_SESSION = 402;
    const ERROR_AJAX_ACCESS_DENIED = 401;

    //login fail type
    const LOGIN_SUCCESS = 1;
    const LOGIN_WRONG_ACCOUNT = 2;
    const LOGIN_LOCKED_30MINUTES_ADMIN = 31;
    const LOGIN_LOCKED_30MINUTES_OTHER = 32;
    const LOGIN_PASSWORD_EXPIRE = 4;
    const LOGIN_FIRST_TIME = 5;

    //user type
    const USER_ADMIN = 1;
    const USER_INPUT1 = 2;
    const USER_INPUT2 = 8;
    const USER_VIEW_1 = 3;
    const USER_VIEW_2 = 4;
    const USER_VIEW_3 = 5;
    const USER_VIEW_4 = 6;
    const USER_SYSTEM = 7;
    const USER_VIEW_ARRAY = array(self::USER_VIEW_1, self::USER_VIEW_2, self::USER_VIEW_3, self::USER_VIEW_4);
    const USER_USE_EMAIL_FOR_ID = array(self::USER_VIEW_1, self::USER_VIEW_2, self::USER_VIEW_4);
    const USER_ADMIN_ARRAY = array(self::USER_ADMIN, self::USER_SYSTEM);
    const USER_INPUT_ARRAY = array(self::USER_INPUT1, self::USER_INPUT2);

    const USER_CAN_DELETE = array(SELF::USER_VIEW_1, SELF::USER_VIEW_2, SELF::USER_VIEW_3, SELF::USER_VIEW_4);
    const USER_CANNOT_RESET_PASS = array(SELF::USER_SYSTEM);
    const USER_CAN_SHOW_SQ = array(SELF::USER_INPUT1, self::USER_INPUT2, SELF::USER_VIEW_3);
    const USER_CHECK_PASSWORD_HISTORY = array(SELF::USER_ADMIN);

    const USER_INACTIVE = 0;
    const USER_ACTIVE = 1;

    const RESULT_PER_PAGE = 10;
    const MAX_SURVEY_QUESTION = 50;

    const EMPTY_QUESTION_OPTION = '-- 秘密の質問 --';

    const POLICY_TYPE_EDITING = 1;
    const POLICY_TYPE_WAITING_PUBLIC = 2;
    const POLICY_TYPE_PUBLIC = 3;
    const POLICY_TYPE_PRIVATE = 4;

    // regex
    const emailRegex = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';
    const passwordRegex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/';
    const notFullSizeRegex = "/(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F])|[\x20-\x7E]/";
    const halfSizeRegex = "/(?:[\x{ff5F}-\x{ff9F}])/u";
    const userAdminRegex = "/^([a-zA-Z]{3,3})([0-9]{6,6})$/u";
    const userEmailRegex = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';
    const userViewRegex = "/^([a-zA-Z]{3,3})([0-9]{7,7})$/u";
//    const fullSizeRegex = '/(?:[a-zA-Z0-9-_\\\'!@#$%^&*()\x{ff5F}-\x{ff9F}\x{0020}])/u'; 

    const NUMBER_SECURITY_QUESTION = 3;
    const NUMBER_PASSWORD_HISTORY_ADMIN = 5;
    const NUMBER_PASSWORD_HISTORY_USER = 1;

    const NOTICE_RECRUITMENT_FORM_TYPE_1 = 53;
    const NOTICE_RECRUITMENT_FORM_TYPE_2 = 54;
    const NOTICE_RECRUITMENT_FORM_TYPE_3 = 55;
    
    const IN_RECRUITMENT_TIME = 57;
    const BEFORE_RECRUITMENT_TIME = 58;
    const AFTER_RECRUITMENT_TIME = 59;

    const TYPE_RECRUITMENT_EXPIRE_SOON = 1;
    const TYPE_IN_RECRUITMENT_TIME = 2;
    const TYPE_BEFORE_SOON_RECRUITMENT_TIME = 3;
    const TYPE_BEFORE_RECRUITMENT_TIME = 4;
    const TYPE_AFTER_RECRUITMENT_TIME = 5;
    const TYPE_ANYTIME_RECRUITMENT = 100;

    const FILE_DOWNLOAD_PATH = 'policy-upload';

    const DEBUG = TRUE;
    const FILE_UPLOAD_LIMIT = 1048576000;
    const USER_REGION = array(1 => "都内", 2 => "都外");

    const MAX_FAVOURITE_POLICY = 20;
    const MAX_COMPARE_POLICY = 10;
    const MIN_COMPARE_POLICY = 2;
    const LIST_ARRTRIBUTES_SEARCH = array(1, 2, 3, 5, 4);
    const NOTICE_NORMAL = 0;
    const NOTICE_SURVEY = 1;
    const QUESSTION_NOT_ASSWER = 1;
    const QUESSTION_SELLECT_ONE_ASSWER = 2;
    const QUESSTION_SELLECT_MULTILE_ASSWER = 3;
    
    // number records user view policy
    const NUMBER_POLICY_VIEW =  20;
    

    const VERSION_STATIC_SOURCE = '1.6';

    const FILE_PDF_UPLOAD_LIMIT = 128 * 1024 * 1024;
    
    const SURVEY_POLICY_ID = 1;
    const SURVEY_NAME = 'init survey demo';
    const SURVEY_DESCRIPT = 'description survey demo';

    const POLICY_ATTRIBUTE_FAVOURITE_TYPE = 10;
    const POLICY_ATTRIBUTE_FAVOURITE_VALUE = 56;

    const SEARCH_AMOUNT = array(42=>array(42), 43=>array(42, 43), 44=>array(42, 43, 44), 45=>array(42, 43, 44, 45), 46=>array(42, 43, 44, 45, 46));
    const SEARCH_NUMBER_PEOPLE = array(48=>array(48), 49=>array(49, 50, 51), 50=>array(50, 51), 51=>array(51));
}
