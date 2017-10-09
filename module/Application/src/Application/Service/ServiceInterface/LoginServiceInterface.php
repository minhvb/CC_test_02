<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Service\ServiceInterface;

interface LoginServiceInterface {
    /**
     * @return mixed
     */
    public function clearUserInfoSession();

    /**
     * @param $userInfo
     * @return mixed
     */
    public function saveUserInfoSession($userInfo);

    /**
     * @param $username
     * @param $password
     * @return mixed
     */
    public function authenticateUser($username, $password);

    public function getUserRepository();

    public function validateUserQuestion($username, $questionId, $answer);

    public function changePassword($username, $password);
}
