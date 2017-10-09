SET NAMES UTF8;
-- Update Action and Set Action To Role
DROP PROCEDURE IF EXISTS appendRoleAction;
delimiter //
CREATE PROCEDURE appendRoleAction(IN linkAction VARCHAR(256), IN roleList VARCHAR(256))
BEGIN
    SET @titleAction = REPLACE(linkAction, '/', '.');
        
    -- Insert Action record
    IF (EXISTS (SELECT id FROM `action` WHERE `name` = @titleAction COLLATE utf8_unicode_ci) ) THEN
        SET @lastInsertActionID = (SELECT id FROM `action` WHERE `name` = @titleAction COLLATE utf8_unicode_ci);
    ELSE
        INSERT INTO `action` (`name`, `link`, `description`, `status`) VALUES
        (@titleAction, linkAction, 'Description', '1');
        SET @lastInsertActionID = LAST_INSERT_ID(); 
    END IF;

    -- Insert record for each specified role
    SET @roleId = 1;
    SET @roleIndex = FIND_IN_SET(@roleId, roleList COLLATE utf8_unicode_ci);
    IF (@roleIndex != 0 AND NOT EXISTS (SELECT `id` FROM `authenticate` WHERE `roleId` = @roleId AND `actionId` = @lastInsertActionID LIMIT 1) ) THEN
        INSERT INTO `authenticate`(`roleId`, `actionId`, `status`) VALUES (@roleId, @lastInsertActionID, '1');
    END IF;
    
    SET @roleId = 2;
    SET @roleIndex = FIND_IN_SET(@roleId, roleList COLLATE utf8_unicode_ci);
    IF (@roleIndex != 0 AND NOT EXISTS (SELECT `id` FROM `authenticate` WHERE `roleId` = @roleId AND `actionId` = @lastInsertActionID LIMIT 1) ) THEN
        INSERT INTO `authenticate`(`roleId`, `actionId`, `status`) VALUES (@roleId, @lastInsertActionID, '1');
    END IF;   

    SET @roleId = 3;
    SET @roleIndex = FIND_IN_SET(@roleId, roleList COLLATE utf8_unicode_ci);
    IF (@roleIndex != 0 AND NOT EXISTS (SELECT `id` FROM `authenticate` WHERE `roleId` = @roleId AND `actionId` = @lastInsertActionID LIMIT 1) ) THEN
        INSERT INTO `authenticate`(`roleId`, `actionId`, `status`) VALUES (@roleId, @lastInsertActionID, '1');
    END IF;

    SET @roleId = 4;
    IF (@roleIndex != 0 AND NOT EXISTS (SELECT `id` FROM `authenticate` WHERE `roleId` = @roleId AND `actionId` = @lastInsertActionID LIMIT 1) ) THEN
        INSERT INTO `authenticate`(`roleId`, `actionId`, `status`) VALUES (@roleId, @lastInsertActionID, '1');
    END IF;

    SET @roleId = 5;
    SET @roleIndex = FIND_IN_SET(@roleId, roleList COLLATE utf8_unicode_ci);
    IF (@roleIndex != 0 AND NOT EXISTS (SELECT `id` FROM `authenticate` WHERE `roleId` = @roleId AND `actionId` = @lastInsertActionID LIMIT 1) ) THEN
        INSERT INTO `authenticate`(`roleId`, `actionId`, `status`) VALUES (@roleId, @lastInsertActionID, '1');
    END IF;
    SET @roleId = 6;
    SET @roleIndex = FIND_IN_SET(@roleId, roleList COLLATE utf8_unicode_ci);
    IF (@roleIndex != 0 AND NOT EXISTS (SELECT `id` FROM `authenticate` WHERE `roleId` = @roleId AND `actionId` = @lastInsertActionID LIMIT 1) ) THEN
        INSERT INTO `authenticate`(`roleId`, `actionId`, `status`) VALUES (@roleId, @lastInsertActionID, '1');
    END IF;

    SET @roleId = 7;
    SET @roleIndex = FIND_IN_SET(@roleId, roleList COLLATE utf8_unicode_ci);
    IF (@roleIndex != 0 AND NOT EXISTS (SELECT `id` FROM `authenticate` WHERE `roleId` = @roleId AND `actionId` = @lastInsertActionID LIMIT 1) ) THEN
        INSERT INTO `authenticate`(`roleId`, `actionId`, `status`) VALUES (@roleId, @lastInsertActionID, '1');
    END IF;

    SET @roleId = 8;
    SET @roleIndex = FIND_IN_SET(@roleId, roleList COLLATE utf8_unicode_ci);
    IF (@roleIndex != 0 AND NOT EXISTS (SELECT `id` FROM `authenticate` WHERE `roleId` = @roleId AND `actionId` = @lastInsertActionID LIMIT 1) ) THEN
        INSERT INTO `authenticate`(`roleId`, `actionId`, `status`) VALUES (@roleId, @lastInsertActionID, '1');
    END IF;

END //
delimiter ;

TRUNCATE TABLE `action`;
TRUNCATE TABLE authenticate;

CALL appendRoleAction('application/home/index', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/home/changepassword', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/home/download', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/home/addfavourite', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/home/removefavourite', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/home/getlistsearchhistory', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/home/addsearchhistory', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/home/loadsearchhistory', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/home/exportcomparepolicy', '1,2,3,4,5,6,7,8');

CALL appendRoleAction('application/mypage/updateuserinfo', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/mypage/changepassword', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/mypage/confirmsettingmail', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/mypage/savesettingmail', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/mypage/checkemail', '1,2,3,4,5,6,7,8');

CALL appendRoleAction('application/notice/index', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/notice/noticenormal', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/notice/votenoticesurvey', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/notice/save', '1,2,3,4,5,6,7,8');

CALL appendRoleAction('application/policy/index', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/policy/detail', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/policy/confirmprint', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/policy/addfpolicy', '1,2,3,4,5,6,7,8');

CALL appendRoleAction('application/survey/vote', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/survey/addsvote', '1,2,3,4,5,6,7,8');

CALL appendRoleAction('application/guide/index', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/guide/sitepolicy', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/guide/relatedlink', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/guide/privacypolicy', '1,2,3,4,5,6,7,8');
CALL appendRoleAction('application/guide/inquiries', '1,2,3,4,5,6,7,8');

CALL appendRoleAction('administrator/policymanagement/index', '1,2,7,8');
CALL appendRoleAction('administrator/policymanagement/add', '1,2,7,8');
CALL appendRoleAction('administrator/policymanagement/save', '1,2,7,8');
CALL appendRoleAction('administrator/policymanagement/success', '1,2,7,8');
CALL appendRoleAction('administrator/policymanagement/edit', '1,2,7,8');
CALL appendRoleAction('administrator/policymanagement/clone', '1,2,7,8');
CALL appendRoleAction('administrator/policymanagement/delete', '1,2,7,8');
CALL appendRoleAction('administrator/policymanagement/public', '1,2,7,8');
CALL appendRoleAction('administrator/policymanagement/private', '1,2,7,8');

CALL appendRoleAction('administrator/menu/index', '1,2,7,8');
CALL appendRoleAction('administrator/menu/mainmanagement', '1,2,7,8');

CALL appendRoleAction('administrator/usermanagement/index', '1,7');
CALL appendRoleAction('administrator/usermanagement/deleteuser', '1,7');
CALL appendRoleAction('administrator/usermanagement/resetpassword', '1,7');
CALL appendRoleAction('administrator/usermanagement/importuser', '1,7');
CALL appendRoleAction('administrator/usermanagement/importuserproceed', '1,7');

CALL appendRoleAction('administrator/report/index', '1,7');

CALL appendRoleAction('administrator/noticemanagement/index', '1,7');
CALL appendRoleAction('administrator/noticemanagement/addnoticenormal', '1,7');
CALL appendRoleAction('administrator/noticemanagement/editnoticenormal', '1,7');
CALL appendRoleAction('administrator/noticemanagement/savenoticenormal', '1,7');
CALL appendRoleAction('administrator/noticemanagement/addnoticesurvey', '1,7');
CALL appendRoleAction('administrator/noticemanagement/savenoticesurvey', '1,7');
CALL appendRoleAction('administrator/noticemanagement/editnoticesurvey', '1,7');
CALL appendRoleAction('administrator/noticemanagement/reviewnotice', '1,7');
CALL appendRoleAction('administrator/noticemanagement/deletenotice', '1,7');
CALL appendRoleAction('application/policy/addtotalprint', '1,2,3,4,5,6,7,8');

CALL appendRoleAction('administrator/surveymanagement/index', '1,7');
CALL appendRoleAction('administrator/surveymanagement/add', '1,7');
CALL appendRoleAction('administrator/surveymanagement/edit', '1,7');
CALL appendRoleAction('administrator/surveymanagement/save', '1,7');