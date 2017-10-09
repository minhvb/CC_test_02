<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Service;

use Application\Entity\Repository\UserRepository;
use Application\Entity\User;
use Application\Entity\UserHistory;
use Application\Exception\ValidationException;
use Application\Service\ServiceInterface\LoginServiceInterface;
use Application\Utils\ApplicationConst;
use Application\Utils\CommonUtils;
use Application\Utils\DateHelper;
use Application\Utils\MailHelper;
use Application\Utils\UserHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class LoginService implements LoginServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const LOGIN_FAIL_TIME = 1800; // 30 minutes
    const LOGIN_FAIL_INCREASE = 1;
    const LOGIN_FAIL_MAX_ADMIN = 5;
    const LOGIN_FAIL_MAX_OTHER_USER = 10;
    const QUESTION_FAIL_MAX = 10;
    const PASSWORD_EXPIRE_ADMIN = 90 * 24 * 60 * 60; // 90 days
    const PASSWORD_EXPIRE_OTHER_USER = 365 * 24 * 60 * 60; // 365 days
    const TOKEN_EXPIRE_DURATION = 24 * 60 * 60; // 24 hours

    const ROLE_ADMIN = 'administrator';
    const ROLE_INPUT = 'input';
    const ROLE_VIEWER = 'viewer';

    /**
     *
     */
    public function clearUserInfoSession() {
        PrivateSession::clear(ApplicationConst::USER_INFO);
    }

    /**
     * @param              $username
     * @param              $password
     * @param array|string $role
     * @return mixed|void
     * @throws ValidationException
     */
    public function authenticateUser($username, $password, $role = null) {
        /** @var UserRepository $userRepo */

        $userRepo = $this->getUserRepository();
        try {
            $userInfo = $userRepo->getUserInfo($username);
            $currentTimeStamp = DateHelper::getCurrentTimeStamp();
            $lastUpdatedTimeStamp = $userInfo['passwordUpdateDate'];
            $lastFailTimeStamp = intval($userInfo['lastLoginFail'], 0);
            $totalLoginFail = intval($userInfo['totalLoginFail'], 0);
            $durationLoginFail = $currentTimeStamp - $lastFailTimeStamp;
            $userRole = $userInfo['roleId'];

            if (($role === self::ROLE_VIEWER && !UserHelper::isViewer($userRole))
                || ($role === self::ROLE_ADMIN && !UserHelper::isAdministrator($userRole))
                || ($role === self::ROLE_INPUT && !UserHelper::isInputRole($userRole))
            ) {
                throw new ValidationException('MSG_LG_001_WrongIdPass');
            }

            // check account active
            if ($userInfo['isActive'] != ApplicationConst::USER_ACTIVE) {
                throw new ValidationException('MSG_LG_021_AccountInActive');
            }

            // check account has been locked 30 minutes
            if ($this->isOverLoginFail($userRole, $totalLoginFail) && ($durationLoginFail <= self::LOGIN_FAIL_TIME)) {
                if (UserHelper::isAdministrator($userRole)) {
                    throw new ValidationException('MSG_LG_003_WrongMore10', ApplicationConst::LOGIN_LOCKED_30MINUTES_ADMIN);
                }

                throw new ValidationException('MSG_LG_002_WrongMore5', ApplicationConst::LOGIN_LOCKED_30MINUTES_OTHER);
            }

            // password wrong
            $encryptedPassword = CommonUtils::encrypt($password);
            if ($encryptedPassword != $userInfo['password']) {
                // check total login
                if ($durationLoginFail > self::LOGIN_FAIL_TIME) {
                    $totalLoginFail = self::LOGIN_FAIL_INCREASE;
                } else {
                    $totalLoginFail = $totalLoginFail + self::LOGIN_FAIL_INCREASE;
                }
                $userRepo->updateInfoLoginFail($username, $totalLoginFail);
                if ($this->isOverLoginFail($userRole, $totalLoginFail) && UserHelper::isAdministrator($userRole)) {
                    throw new ValidationException('MSG_LG_003_WrongMore10', ApplicationConst::LOGIN_LOCKED_30MINUTES_ADMIN);

                }
                if ($this->isOverLoginFail($userRole, $totalLoginFail) && !UserHelper::isAdministrator($userRole)) {
                    throw new ValidationException('MSG_LG_002_WrongMore5', ApplicationConst::LOGIN_LOCKED_30MINUTES_OTHER);
                }
                throw new NoResultException();
            }

            if ($this->isFirstTimeLogin($userInfo['userId']) && empty($userInfo['email']) && empty($userInfo['questionId'])) {
                throw new ValidationException($username, ApplicationConst::LOGIN_FIRST_TIME);
            }

            if ($this->isPasswordExpired($userRole, $lastUpdatedTimeStamp)) {
                throw new ValidationException($username, ApplicationConst::LOGIN_PASSWORD_EXPIRE);
            }

            $this->saveUserInfoSession($userInfo);
            $userRepo->updateInfoLoginSuccess($username);
            $this->saveLoginHistory($userInfo['userId'], $userInfo['username'], $userInfo['roleId']);
        } catch (NoResultException $ex) {
            throw new ValidationException('MSG_LG_001_WrongIdPass');
        }

        return ApplicationConst::LOGIN_SUCCESS;
    }

    public function getUserRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\User');
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     * Check user login fail over max
     * admin: 5
     * other: 10
     *
     * @param $userRoleId
     * @param $totalLoginFail
     * @return bool
     */
    private function isOverLoginFail($userRoleId, $totalLoginFail) {
        return (UserHelper::isAdministrator($userRoleId) && $totalLoginFail >= self::LOGIN_FAIL_MAX_ADMIN)
            || (!UserHelper::isAdministrator($userRoleId) && $totalLoginFail >= self::LOGIN_FAIL_MAX_OTHER_USER);
    }

    public function isFirstTimeLogin($userId) {
        $histories = $this->getUserHistoryRepository()->getUserHistories($userId);

        return empty($histories) ? true : false;
    }

    public function getUserHistoryRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\UserHistory');
    }

    /**
     * @param $userRoleId
     * @param $lastUpdatedTimeStamp
     * @return bool
     */
    private function isPasswordExpired($userRoleId, $lastUpdatedTimeStamp) {
        $currentTimeStamp = DateHelper::getCurrentTimeStamp();
        $duration = $currentTimeStamp - $lastUpdatedTimeStamp;

        return (UserHelper::isAdministrator($userRoleId) && $duration >= self::PASSWORD_EXPIRE_ADMIN)
            || (!UserHelper::isAdministrator($userRoleId) && $duration >= self::PASSWORD_EXPIRE_OTHER_USER);
    }

    public function saveUserInfoSession($userInfo) {
        PrivateSession::setData(ApplicationConst::USER_INFO, array('username' => $userInfo['username'], 'roleId' => $userInfo['roleId'], 'userId' => $userInfo['userId'], 'bureauId' => $userInfo['bureauId'], 'departmentId' => $userInfo['departmentId'], 'divisionId' => $userInfo['divisionId'], 'email' => $userInfo['email'], 'isSettingMail' => $userInfo['isSettingMail'],));
    }

    public function saveLoginHistory($userId, $username, $roleId) {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $userHistory = new UserHistory();
        $userHistory->setRoleId($roleId);
        $userHistory->setUserId($userId);
        $userHistory->setUsername($username);
        $userHistory->setLoginDate(DateHelper::getCurrentTimeStamp());

        $em->persist($userHistory);
        $em->flush();
        $em->clear();

        return true;
    }

    public function validateUserQuestion($username, $questionId, $answer) {
        /** @var UserRepository $userRepo */
        $userRepo = $this->getUserRepository();
        try {
            $userInfo = $userRepo->getUserQuestion($username, $questionId, $answer);
            $lastFailTimeStamp = empty($userInfo['lastWrongQuestion']) ? 0 : $userInfo['lastWrongQuestion'];
            $totalWrong = $userInfo['totalWrongQuestion'];
            $currentTimeStamp = DateHelper::getCurrentTimeStamp();
            $durationQuestionFail = $currentTimeStamp - $lastFailTimeStamp;
            if (empty($userInfo['isActive'])){
                throw new ValidationException('MSG_LG_021_AccountInActive');
            }
            if (UserHelper::isAdministrator($userInfo['roleId'])) {
                throw new ValidationException('MSG_LG_044_CanNotResetAdmin');
            }
            if (!empty($userInfo['email'])){
                throw new ValidationException('MSG_LG_046_HaveEmail');
            }
            if ($totalWrong >= self::QUESTION_FAIL_MAX && $durationQuestionFail <= self::LOGIN_FAIL_TIME) {
                throw new ValidationException('MSG_LG_045_WrongQuestionMax');
            }
            if (($questionId != $userInfo['questionId']) || ($answer != $userInfo['answer'])) {
                if ($durationQuestionFail > self::LOGIN_FAIL_TIME) {
                    $totalWrong = self::LOGIN_FAIL_INCREASE;
                } else {
                    $totalWrong = $totalWrong + self::LOGIN_FAIL_INCREASE;
                }
                $userRepo->updateQuestionFailInfo($username, $totalWrong);
                if ($totalWrong >= self::QUESTION_FAIL_MAX) {
                    throw new ValidationException('MSG_LG_045_WrongQuestionMax');
                }
                throw new ValidationException('MSG_LG_005_WrongSecretAnswer');
            }

            $userRepo->updateQuestionSuccess($username);

        } catch (NoResultException $ex) {
            throw new ValidationException('MSG_LG_005_WrongSecretAnswer');
        }

        return $userInfo;
    }

    public function changePassword($username, $password, $reset = false) {
        /** @var UserRepository $userRepo */
        $userRepo = $this->getUserRepository();
        $userInfo = $userRepo->getRolePasswordHistory($username);
        $encodedPassword = CommonUtils::encrypt($password);
        $passwordHistory = json_decode($userInfo['passwordHistory']);
        $isAdmin = UserHelper::isAdministrator($userInfo['roleId']);

        // if forgot password, don't check password history
        if (!$reset && in_array($encodedPassword, $passwordHistory)) {
            throw $isAdmin
                ? new ValidationException('MSG_LG_018_AdminChangePassError')
                : new ValidationException('MSG_LG_019_UserChangePassError');
        }

        $maxHistory = $isAdmin ? ApplicationConst::NUMBER_PASSWORD_HISTORY_ADMIN : ApplicationConst::NUMBER_PASSWORD_HISTORY_USER;

        while (count($passwordHistory) >= $maxHistory) {
            array_shift($passwordHistory);
        }
        array_push($passwordHistory, $encodedPassword);


        $userRepo->changePassword($username, $encodedPassword, $passwordHistory);

        return true;
    }

    public function validateUserInfo($params) {
        $email = empty($params['email']) ? null : trim($params['email']);
        $password = empty($params['password']) ? null : trim($params['password']);
        $questionId = empty($params['questionId']) ? null : $params['questionId'];
        $answer = empty($params['answer']) ? null : trim($params['answer']);
        if (!CommonUtils::validateEmail($email)
            || !CommonUtils::validatePassword($password)
            || !CommonUtils::validateSecurityAnswer($answer)
            || !CommonUtils::validateSecurityQuestion($questionId)
        ) {
            throw new ValidationException('validation fail');
        }

        return true;
    }

    public function registerUser($params) {
        $email = trim($params['email']);
        $password = trim($params['password']);
        $questionId = trim($params['questionId']);
        $answer = trim($params['answer']);
        try {
            $this->getEntityManager()->getConnection()->beginTransaction();
            /** @var UserRepository $userRepo */
            $userRepo = $this->getUserRepository();
            $users = $userRepo->getUserByEmailOrUsername($email);
            if (count($users) > 0) {
                throw new ValidationException('MSG_LG_020_EmailExisted');
            }

            $token = $this->createNewUserView($email, $password, $questionId, $answer);

            // send token verify
            $mailer = new MailHelper($this->getServiceLocator());
            $mailer->sendEmail($email, array('username' => $email, 'token' => $token), MailHelper::TEMPLATE_REGISTER);
            $this->getEntityManager()->getConnection()->commit();
        }catch (\Exception $ex){
            $this->getEntityManager()->getConnection()->rollBack();
            throw $ex;
        }
        return true;
    }

    /**
     * Insert new user into DB
     * Return token string
     *
     * @param $email
     * @param $password
     * @param $questionId
     * @param $answer
     * @return string
     */
    protected function createNewUserView($email, $password, $questionId, $answer) {
        $em = $this->getEntityManager();
        $encryptedPassword = CommonUtils::encrypt($password);
        $token = CommonUtils::generateToken();

        // set dât
        $user = new User();
        $user->setEmail($email);
        $user->setUserName($email);
        $user->setRoleId(ApplicationConst::USER_VIEW_4);
        $user->setPassword($encryptedPassword);
        $user->setPasswordHistory(json_encode(array($encryptedPassword)));
        $user->setPasswordUpdateDate(DateHelper::getCurrentTimeStamp());
        $user->setQuestionId($questionId);
        $user->setAnswer($answer);
        $user->setIsActive(ApplicationConst::USER_INACTIVE);
        $user->setUpdateDate(DateHelper::getCurrentTimeStamp());
        $user->setCreateDate(DateHelper::getCurrentTimeStamp());
        $user->setToken($token);
        $user->setTokenExpireDate(DateHelper::getCurrentTimeStamp() + self::TOKEN_EXPIRE_DURATION);

        // insert
        $em->persist($user);
        $em->flush();
        $em->clear();

        return $token;
    }

    public function checkOldPassword($username, $oldPassword) {
        if (empty($username) || $oldPassword === null) {
            throw new ValidationException('Validation fail!');
        }
        try {
            $userInfo = $this->getUserRepository()->getUserInfo($username);
            if (CommonUtils::encrypt($oldPassword) != $userInfo['password']) {
                throw new ValidationException('MSG_LG_022_WrongOldPass');
            }

            return true;
        } catch (NoResultException $ex) {
            throw new ValidationException('MSG_LG_001_WrongIdPass');
        }

        return true;
    }

    public function sendMailChangePass($username) {
        try {
            $this->getEntityManager()->getConnection()->beginTransaction();
            $userInfo = $this->getUserRepository()->getUserInfo($username);
            if (empty($userInfo['isActive'])){
                throw new ValidationException('MSG_LG_021_AccountInActive');
            }
            if (empty($userInfo['email'])) {
                throw new ValidationException('MSG_LG_023_AccountMailEmpty');
            }
            if (UserHelper::isAdministrator($userInfo['roleId'])) {
                throw new ValidationException('MSG_LG_044_CanNotResetAdmin');
            }
            $token = CommonUtils::generateToken();
            $deadline = DateHelper::getCurrentTimeStamp() + self::TOKEN_EXPIRE_DURATION;
            $this->getUserRepository()->updateToken($username, $token, $deadline);
            $mailer = new MailHelper($this->getServiceLocator());
            $mailer->sendEmail($userInfo['email'], array('username' => $username, 'token' => $token), MailHelper::TEMPLATE_FORGOT_PASS);
            $this->getEntityManager()->getConnection()->commit();
        } catch (NoResultException $ex) {
            throw new ValidationException('MSG_LG_006_WrongUserId');
        } catch (\Exception $ex) {
            $this->getEntityManager()->getConnection()->rollBack();
            throw $ex;
        }

        return true;
    }

    public function isAdmin($username) {
        try {
            $userInfo = $this->getUserRepository()->getUserInfo($username);
            if (empty($userInfo['email']) || !UserHelper::isAdministrator($userInfo['roleId'])) {
                return false;
            }
        } catch (NoResultException $ex) {
            return false;
        }

        return true;
    }

    public function activeUser($username, $token) {
        $this->validateToken($username, $token);
        $this->setTokenExpire($username);
        $this->getUserRepository()->activeUser($username);

        return true;
    }

    public function validateToken($username, $token) {
        try {
            $userInfo = $this->getUserRepository()->getUserInfo($username);
            $currentTime = DateHelper::getCurrentTimeStamp();
            $expiredTokenTime = $userInfo['tokenExpireDate'];

            if (empty($userInfo['token']) || $token != $userInfo['token'] || $currentTime > $expiredTokenTime) {
                throw new ValidationException('MSG_LG_024_TokenExpired');
            }
        } catch (NoResultException $ex) {
            throw new ValidationException('MSG_LG_024_TokenExpired');
        }

        return $userInfo;
    }

    public function setTokenExpire($username) {
        $this->getUserRepository()->setTokenExpire($username);

        return true;
    }

    public function updateSecurityQuestion($username, $questionId, $answer) {
        if (!CommonUtils::validateSecurityQuestion($questionId) || !CommonUtils::validateSecurityAnswer($answer)) {
            throw new ValidationException('validation fail');
        }

        $this->getUserRepository()->updateSecurityQuestion($username, $questionId, $answer);

        return true;
    }

    public function checkUserHasEmail($username) {
        try {
            $userInfo = $this->getUserRepository()->getUserInfo($username);
            if (empty($userInfo['isActive'])){
                throw new ValidationException('MSG_LG_021_AccountInActive');
            }
            if (empty($userInfo) || empty($userInfo['email'])) {
                return false;
            }

            return true;
        } catch (NoResultException $ex) {
            throw new ValidationException('MSG_LG_006_WrongUserId');
        }
    }
    
    public function getUserByUsernameToken($username, $token)
    {
        return $this->getUserRepository()->getUserByUsernameToken($username, $token);
    }
    
    public function activeEmail($username, $email, $roleId)
    {
        return $this->getUserRepository()->activeEmail($username, $email, $roleId);
    }
}
