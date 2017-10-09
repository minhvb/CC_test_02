<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Application\Utils\ApplicationConst;
use Application\Utils\DateHelper;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    const STATUS_ENABLE = 1;
    const TOTAL_LOGIN_RESET = 0;
    const TOTAL_LOGIN_INCREASE = 1;

    public function getUserInfo($username) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.id as userId, u.userName as username, u.email, u.nextEmail, u.roleId, u.password, u.business,
         u.lastLoginFail, u.totalLoginFail, u.passwordUpdateDate, u.passwordHistory, u.questionId, u.answer,
         u.bureauId, u.departmentId, u.divisionId, u.isActive, u.isSettingMail, u.token, u.tokenExpireDate')
            ->from('\Application\Entity\User', 'u')
            ->where('u.userName = :userName')
            ->setParameter(':userName', $username);

        return $qb->getQuery()->getSingleResult();
    }
    
    public function getUserInfoById($userId) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.id as userId, u.userName as username, u.email, u.nextEmail, u.roleId, u.password, u.business,
         u.lastLoginFail, u.totalLoginFail, u.passwordUpdateDate, u.passwordHistory, u.questionId, u.answer,
         u.bureauId, u.departmentId, u.divisionId, u.isActive, u.isSettingMail, u.token, u.tokenExpireDate')
            ->from('\Application\Entity\User', 'u')
            ->where('u.id = :userId')
            ->setParameter(':userId', $userId);

        return $qb->getQuery()->getSingleResult();
    }

    public function updateInfoLoginSuccess($username) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.totalLoginFail', intval(self::TOTAL_LOGIN_RESET))
            ->where('u.userName = :userName')
            ->setParameter(':userName', $username);

        return $qb->getQuery()->execute();
    }

    public function updateInfoLoginFail($username, $totalLoginFail) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.totalLoginFail', ':totalLoginFail')
            ->set('u.lastLoginFail', DateHelper::getCurrentTimeStamp())
            ->where('u.userName = :userName')
            ->setParameter(':userName', $username)
            ->setParameter(':totalLoginFail', $totalLoginFail);

        return $qb->getQuery()->execute();
    }

    public function getUserQuestion($username) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.userName as username, u.questionId, u.answer, u.roleId, u.lastWrongQuestion, u.totalWrongQuestion, u.email, u.isActive')
            ->from('\Application\Entity\User', 'u')
            ->where('u.userName = :userName')
            ->setParameter(':userName', $username);

        return $qb->getQuery()->getSingleResult();
    }
    
    public function checkEmailExist($userId, $email){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(u.id)')
            ->from('\Application\Entity\User', 'u')
            ->where($qb->expr()->andX(
                $qb->expr()->neq('u.id', ':userId'),
                $qb->expr()->eq('u.email', ":email")
            ))
            ->setParameter(':userId', $userId)
            ->setParameter(':email', $email);

        return $qb->getQuery()->getSingleScalarResult();    
    }

    public function getAllUsers($search, $currentPage, $resultPerPage) {
        $startRecord = ($currentPage - 1) * $resultPerPage;

        $parameters = array();
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.id, u.userName, u.roleId, u.password, u.updateDate')
            ->from('\Application\Entity\User', 'u');

        if ($search["keyword"]!="") {
            $qb->andWhere($qb->expr()->like("u.userName", ':userName'));
            $parameters["userName"] = '%' . $search["keyword"] . '%';
        }

        if (!empty($search["roleId"])) {
            $qb->andWhere($qb->expr()->eq("u.roleId", ":roleId"));
            $parameters["roleId"] = $search["roleId"];
        }

        $qb->setFirstResult($startRecord)->setMaxResults($resultPerPage);
        if (!empty($parameters)) {
            $qb->setParameters($parameters);
        }
        $qb->orderBy("u.updateDate", "DESC");

        $rows = $qb->getQuery()->getArrayResult();
        $list_user_id = array();
        foreach($rows as $row){
            $list_user_id[] = $row["id"];
        }

        // get role
        $qb = $em->createQueryBuilder();
        $qb->select('r.id, r.title')
            ->from('\Application\Entity\Role', 'r');

        $rowRoles = $qb->getQuery()->getArrayResult();

        $replaceRoles = array();
        foreach ($rowRoles as $row) {
            $replaceRoles[$row["id"]]["id"] = $row["id"];
            $replaceRoles[$row["id"]]["title"] = $row["title"];
        }
        
        // Get last login
        $qb = $em->createQueryBuilder();
        $qb->select('max(uh.loginDate) as loginDate, uh.userId')
            ->from('\Application\Entity\UserHistory', 'uh')
            ->where($qb->expr()->in('uh.userId', ':list_user_id'))
            ->groupBy("uh.userId")
            ->setParameter(":list_user_id", $list_user_id);
            
        $rowsLoginDate = $qb->getQuery()->getArrayResult();
//        var_dump($rowsLoginDate);die;
        $replaceLoginDate = array();
        foreach ($rowsLoginDate as $row) {
            $replaceLoginDate[$row["userId"]] = $row["loginDate"];
        }
        
        foreach ($rows as $i => $row) {
            $rows[$i]["roleTitle"] = isset($replaceRoles[$row["roleId"]]["title"]) ? $replaceRoles[$row["roleId"]]["title"] : "";
            $rows[$i]["lastLogindate"] = isset($replaceLoginDate[$row["id"]]) ? $replaceLoginDate[$row["id"]] : 0;
        }

        return $rows;
    }

    public function getTotalUsers($search) {
        $parameters = array();
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(u.id)')
            ->from('\Application\Entity\User', 'u');

        if ($search["keyword"]!="") {
            $qb->andWhere($qb->expr()->like("u.userName", ':userName'));
            $parameters["userName"] = '%' . $search["keyword"] . '%';
        }

        if (!empty($search["roleId"])) {
            $qb->andWhere($qb->expr()->eq("u.roleId", ":roleId"));
            $parameters["roleId"] = $search["roleId"];
        }

        if (!empty($parameters)) {
            $qb->setParameters($parameters);
        }

        $totalResults = $qb->getQuery()->getSingleScalarResult();

        return $totalResults;
    }

    public function getRolePasswordHistory($username) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.passwordHistory, u.roleId')
            ->from('\Application\Entity\User', 'u')
            ->where('u.userName = :userName')
            ->setParameter(':userName', $username);

        return $qb->getQuery()->getSingleResult();
    }

    public function changePassword($username, $password, $passwordHistory) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.password', ':password')
            ->set('u.passwordHistory', ':passwordHistory')
            ->set('u.passwordUpdateDate', DateHelper::getCurrentTimeStamp())
            ->where('u.userName = :userName')
            ->setParameter(':userName', $username)
            ->setParameter(':passwordHistory', json_encode($passwordHistory))
            ->setParameter(':password', $password);

        return $qb->getQuery()->execute();
    }

    public function resetPasswordByUserId($userId, $password) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.password', ':password')
            ->where('u.id = :userId')
            ->setParameter(':userId', $userId)
            ->setParameter(':password', $password);

        return $qb->getQuery()->execute();
    }
    
    public function changePasswordByUserId($userId, $password, $passwordHistory) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.password', ':password')
            ->set('u.passwordHistory', ':passwordHistory')
            ->set('u.passwordUpdateDate', DateHelper::getCurrentTimeStamp())
            ->where('u.id = :userId')
            ->setParameter(':userId', $userId)
            ->setParameter(':passwordHistory', json_encode($passwordHistory))
            ->setParameter(':password', $password);

        return $qb->getQuery()->execute();
    }

    public function deleteByUserId($userId) {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('\Application\Entity\User', 'u')
            ->where($qb->expr()->eq('u.id', $userId));

        $resultUser = $qb->getQuery()->getResult();
        
        if($resultUser>0){
            /* Delete mail content */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\MailContent', 'mc')
                ->where($qb->expr()->eq('mc.userId', $userId));
            $result = $qb->getQuery()->getResult();
            
            /* Delete Policy favourite */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\PolicyFavourite', 'pf')
                ->where($qb->expr()->eq('pf.userId', $userId));
            $result = $qb->getQuery()->getResult();
            
            /* Delete Policy view */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\PolicyView', 'pv')
                ->where($qb->expr()->eq('pv.userId', $userId));
            $result = $qb->getQuery()->getResult();
            
            /* Delete response */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\Response', 'res')
                ->where($qb->expr()->eq('res.userId', $userId));
            $result = $qb->getQuery()->getResult();
            
            /* Delete Search history */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\SearchHistory', 'sh')
                ->where($qb->expr()->eq('sh.userId', $userId));
            $result = $qb->getQuery()->getResult();
            
            /* delete setting mail */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\SettingMail', 'sm')
                ->where($qb->expr()->eq('sm.userId', $userId));
            $result = $qb->getQuery()->getResult();
            
            /* delete user history */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\UserHistory', 'uh')
                ->where($qb->expr()->eq('uh.userId', $userId));
            $result = $qb->getQuery()->getResult();
        }

        return $resultUser;
    }

    public function getUserByEmailOrUsername($email) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.id')
            ->from('\Application\Entity\User', 'u')
            ->where('u.email = :email')
            ->orWhere('u.userName = :email')
            ->setParameter(':email', $email);

        return $qb->getQuery()->getArrayResult();
    }

    public function updateUserInfo($data){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.email', ':email')
            ->set('u.nextEmail', ':nextEmail')
            ->set('u.business', ':business')
            ->set('u.questionId', ":questionId")
            ->set('u.answer', ":answer")
            ->set('u.token', ":token")
            ->set('u.updateDate', ":updateDate")
            ->where('u.id = :userId')
            ->setParameter(':email', $data["email"])
            ->setParameter(':nextEmail', $data["nextEmail"])
            ->setParameter(':business', json_encode($data["attributes"]))
            ->setParameter(':questionId', $data["securityQuestionId"])
            ->setParameter(':answer', $data["securityAnswer"])
            ->setParameter(':token', $data["token"])
            ->setParameter(':updateDate', time())
            ->setParameter(':userId', $data["userId"]);
                 
        return $qb->getQuery()->execute();    
    }

    public function updateToken($username, $token, $deadline) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.token', ':token')
            ->set('u.tokenExpireDate', ':tokenExpireDate')
            ->where('u.userName = :userName')
            ->setParameter(':tokenExpireDate', $deadline)
            ->setParameter(':token', $token)
            ->setParameter(':userName', $username);

        return $qb->getQuery()->execute();
    }

    public function setTokenExpire($username) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.token', ':token')
            ->set('u.tokenExpireDate', ':tokenExpireDate')
            ->where('u.userName = :userName')
            ->setParameter(':tokenExpireDate', null)
            ->setParameter(':token', null)
            ->setParameter(':userName', $username);

        return $qb->getQuery()->execute();
    }

    public function activeUser($username){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.isActive', ':isActive')
            ->where('u.userName = :userName')
            ->setParameter(':isActive', ApplicationConst::USER_ACTIVE)
            ->setParameter(':userName', $username);

        return $qb->getQuery()->execute();
    }

    public function updateSecurityQuestion($username, $questionId, $answer){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.questionId', ':questionId')
            ->set('u.answer', ':answer')
            ->where('u.userName = :userName')
            ->setParameter(':questionId', $questionId)
            ->setParameter(':answer', $answer)
            ->setParameter(':userName', $username);

        return $qb->getQuery()->execute();
    }

    public function updateQuestionFailInfo($username, $totalFail){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.totalWrongQuestion', ':totalWrongQuestion')
            ->set('u.lastWrongQuestion', DateHelper::getCurrentTimeStamp())
            ->where('u.userName = :userName')
            ->setParameter(':userName', $username)
            ->setParameter(':totalWrongQuestion', $totalFail);

        return $qb->getQuery()->execute();
    }

    public function updateQuestionSuccess($username) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.totalWrongQuestion', intval(self::TOTAL_LOGIN_RESET))
            ->where('u.userName = :userName')
            ->setParameter(':userName', $username);

        return $qb->getQuery()->execute();
    }
    
    public function updateIsSettingMail($userId) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\User', 'u')
            ->set('u.isSettingMail', ":isSettingMail")
            ->where('u.id = :userId')
            ->setParameter(':isSettingMail', 1)
            ->setParameter(':userId', $userId);

        return $qb->getQuery()->execute();
    }
    
    public function getUserByUsernameToken($username, $token)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.id, u.password, u.roleId, u.email, u.nextEmail')
            ->from('\Application\Entity\User', 'u')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('u.userName', ':userName'),
                $qb->expr()->eq('u.token', ":token")
            ))
            ->setParameter(':userName', $username)
            ->setParameter(':token', $token);

        return $qb->getQuery()->getOneOrNullResult();   
    }
    
    public function activeEmail($username, $email, $roleId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        if(in_array($roleId, \Application\Utils\ApplicationConst::USER_USE_EMAIL_FOR_ID)){
            $qb->update('\Application\Entity\User', 'u')
                ->set('u.email', ":email")
                ->set('u.nextEmail', ":nextEmail")
                ->set('u.token', ":token")
                ->set('u.userName', ":userNameChange")
                ->where('u.userName = :userName')
                ->setParameter(':email', $email)
                ->setParameter(':nextEmail', "")
                ->setParameter(':token', "")
                ->setParameter(':userNameChange', $email)
                ->setParameter(':userName', $username);    
        } else {
            $qb->update('\Application\Entity\User', 'u')
                ->set('u.email', ":email")
                ->set('u.nextEmail', ":nextEmail")
                ->set('u.token', ":token")
                ->where('u.userName = :userName')
                ->setParameter(':email', $email)
                ->setParameter(':nextEmail', "")
                ->setParameter(':token', "")
                ->setParameter(':userName', $username);
        }
        
        return $qb->getQuery()->execute();   
    }
    
    public function getUserInList($list_user_id){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.id, u.userName as username, u.email')
            ->from('\Application\Entity\User', 'u')
            ->where($qb->expr()->in('u.userName', ':list_user_id'))
            ->setParameter(':list_user_id', $list_user_id);
//        return $qb->getQuery()->getSQL();
        return $qb->getQuery()->getArrayResult();     
    }
    
    public function getUserInListByUserIdNRole($list_unique_user_delete){
        $sql = "";
        if (count($list_unique_user_delete) > 200) {
            $list_unique_user_delete = array_chunk($list_unique_user_delete, 200);
            foreach ($list_unique_user_delete as $list_id) {
                $list_id = implode("', '", $list_id);
                $list_id = "'" . $list_id . "'";
                $sql .= " UNION ALL (SELECT id, MD5(CONCAT(roleId, '-', userName)) as `unique` FROM user WHERE MD5(CONCAT(roleId, '-', userName)) IN (" . $list_id . ") )";
            }
            $sql = ltrim($sql, " UNION ALL ");
        } else {
            $list_id = implode("', '", $list_unique_user_delete);
            $list_id = "'" . $list_id . "'";
            $sql .= "SELECT id, MD5(CONCAT(roleId, '-', userName)) as `unique` FROM user WHERE MD5(CONCAT(roleId, '-', userName)) IN (" . $list_id . ")";
        }

        $connection = $this->getEntityManager()->getConnection();
        $rows = $connection->fetchAll($sql);
        
        $data = array();
        foreach ($rows as $row) {
            $data[$row["unique"]] = $row["id"];
        }

        return $data;
    }
    
    public function getEmailInList($list_email){
        $sql = "";
        if (count($list_email) > 200) {
            $list_email = array_chunk($list_email, 200);
            foreach ($list_email as $list_id) {
                $list_id = implode("', '", $list_id);
                $list_id = "'" . $list_id . "'";
                $sql .= " UNION ALL (SELECT id, MD5(UPPER(email)) as `unique` FROM user WHERE MD5(UPPER(email)) IN (" . $list_id . ") )";
            }
            $sql = ltrim($sql, " UNION ALL ");
        } else {
            $list_id = implode("', '", $list_email);
            $list_id = "'" . $list_id . "'";
            $sql .= "SELECT id, MD5(UPPER(email)) as `unique` FROM user WHERE MD5(UPPER(email)) IN (" . $list_id . ")";
        }

        $connection = $this->getEntityManager()->getConnection();
        $rows = $connection->fetchAll($sql);
        
        $data = array();
        foreach ($rows as $row) {
            $data[$row["unique"]] = $row["id"];
        }

        return $data;
    }
    
    public function insertMultipleUser($dataInsert){
        
        $sqlValue = "";
        foreach ($dataInsert as $row) {
            $sqlValue .= ", (" . $row["roleId"] . ", '" . $row["userId"] . "', '" . $row["bureauId"] . "', '" . $row["departmentId"] . "', '" . $row["divisionId"] . "', '" . $row["email"] . "', '" . $row["password"] . "', '" . $row["businessId"] . "', '" . $row["companySizeId"] . "', " . $row["region"] . ", " . $row["updateDate"] . ", '" . json_encode(array($row["password"])) . "', " . $row["createDate"] . ", " . $row["updateDate"] . ", 1)";
        }
        
        $sqlValue = ltrim($sqlValue, ", ");

        if (empty($sqlValue)) {
            return 0;
        }

        $sql = "INSERT INTO user (`roleId`, `userName`, `bureauId`, `departmentId`, `divisionId`, `email`, `authenticationString`, `business`, `companySize`, `region`, `passwordUpdateDate`, `passwordHistory`, `createDate`, `updateDate`, `isActive`) VALUES " . $sqlValue;
        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeUpdate($sql);
        $userId = $connection->lastInsertId();

        return $result;
    }
    
    public function deleteMultipleUser($list_user_id){
        if(empty($list_user_id)){
            return 0;
        }
        
        /* get user id by user name */
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.id')
            ->from('\Application\Entity\User', 'u')
            ->where($qb->expr()->in('u.userName', ':list_user_id'))
            ->setParameter(':list_user_id', $list_user_id);
        
        $users = $qb->getQuery()->getArrayResult();
        $list_id = array();
        foreach($users as $row){
            $list_id[] = $row["id"];
        }
        
        /* Delete user */
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('\Application\Entity\User', 'u')
            ->where($qb->expr()->in('u.id', ":list_id"))
            ->setParameter(":list_id", $list_id);
        $resultUser = $qb->getQuery()->getResult();
        if($resultUser>0){
            /* Delete mail content */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\MailContent', 'mc')
                ->where($qb->expr()->in('mc.userId', ":list_id"))
                ->setParameter(":list_id", $list_id);
            $result = $qb->getQuery()->getResult();
            
            /* delete policy favourite */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\PolicyFavourite', 'pf')
                ->where($qb->expr()->in('pf.userId', ":list_id"))
                ->setParameter(":list_id", $list_id);
            $result = $qb->getQuery()->getResult();
            
            /* delete policy view */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\PolicyView', 'pv')
                ->where($qb->expr()->in('pv.userId', ":list_id"))
                ->setParameter(":list_id", $list_id);
            $result = $qb->getQuery()->getResult();
            
            /* delete response */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\Response', 'res')
                ->where($qb->expr()->in('res.userId', ":list_id"))
                ->setParameter(":list_id", $list_id);
            $result = $qb->getQuery()->getResult();
            
            /* Delete search history */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\SearchHistory', 'sh')
                ->where($qb->expr()->in('sh.userId', ":list_id"))
                ->setParameter(":list_id", $list_id);
            $result = $qb->getQuery()->getResult();
            
            /* Delete setting mail */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\SettingMail', 'sm')
                ->where($qb->expr()->in('sm.userId', ":list_id"))
                ->setParameter(":list_id", $list_id);
            $result = $qb->getQuery()->getResult();
            
            /* Delete user history */
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->delete('\Application\Entity\UserHistory', 'uh')
                ->where($qb->expr()->in('uh.userId', ":list_id"))
                ->setParameter(":list_id", $list_id);
            $result = $qb->getQuery()->getResult();   
        }
        
        return $resultUser;
    }
}
