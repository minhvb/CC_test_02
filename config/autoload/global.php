<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
     // ...
    'bureauId' => array(
        "029" => '産業労働局'
    ),
    'departmentId' => array(
        "01" => array(
            'value' => '総務部', 'parentId' => 'bureauId029'
            , "divisionId"=>array("00"=>'', "01"=>'総務課', '02'=>'職員課', '05'=>'企画計理課')
        ),
        "03" => array(
            'value' => '商工部', 'parentId' => 'bureauId029'
            , "divisionId"=>array("00"=>'', "01"=>'調整課', '02'=>'創業支援課', '04'=>'地域産業振興課', '12'=>'経営支援課')
        ),
        "06" => array(
            'value' => '観光部', 'parentId' => 'bureauId029'
            , "divisionId"=>array("00"=>'', "01"=>'企画課', "02"=>'振興課', '03'=>'受入環境課')
        ),
        "07" => array(
            'value' => '金融部', 'parentId' => 'bureauId029'
            , "divisionId"=>array("00"=>'', "01"=>'金融課', '02'=>'貸金業対策課')
        ),
        "08" => array(
            'value' => '農林水産部', 'parentId' => 'bureauId029'
            , "divisionId"=>array("00"=>'', "01"=>'調整課', '03'=>'農業振興課', "04"=>'水産課', '05'=>'森林課', '06'=>'食料安全課')
        ),
        "09" => array(
            'value' => '雇用就業部', 'parentId' => 'bureauId029'
            , "divisionId"=>array("00"=>'', "01"=>'調整課', '03'=>'就業推進課', '04'=>'労働環境課', '05'=>'能力開発課')
        ),
        "99" => array(
            'value' => 'その他', 'parentId' => 'bureauId029'
            , "divisionId"=>array("99"=>'その他')
        ),
    ),
    'divisionId' => array(
        "1" => array('value' => '', 'parentId' => 'departmentId01', 'code' => "00"),
        "2" => array('value' => '総務課', 'parentId' => 'departmentId01', 'code' => "01"),
        "3" => array('value' => '職員課', 'parentId' => 'departmentId01', 'code' => "02"),
        "4" => array('value' => '企画計理課', 'parentId' => 'departmentId01', 'code' => "05"),
        "5" => array('value' => '', 'parentId' => 'departmentId03', 'code' => "00"),
        "6" => array('value' => '調整課', 'parentId' => 'departmentId03', 'code' => "01"),
        "7" => array('value' => '創業支援課', 'parentId' => 'departmentId03', 'code' => "02"),
        "8" => array('value' => '地域産業振興課', 'parentId' => 'departmentId03', 'code' => "04"),
        "9" => array('value' => '経営支援課', 'parentId' => 'departmentId03', 'code' => "12"),
        "10" => array('value' => '', 'parentId' => 'departmentId06', 'code' => "00"),
        "11" => array('value' => '企画課', 'parentId' => 'departmentId06', 'code' => "01"),
        "12" => array('value' => '振興課', 'parentId' => 'departmentId06', 'code' => "02"),
        "13" => array('value' => '受入環境課', 'parentId' => 'departmentId06', 'code' => "03"),
        "14" => array('value' => '', 'parentId' => 'departmentId07', 'code' => "00"),
        "15" => array('value' => '金融課', 'parentId' => 'departmentId07', 'code' => "01"),
        "16" => array('value' => '貸金業対策課', 'parentId' => 'departmentId07', 'code' => "02"),
        "17" => array('value' => '', 'parentId' => 'departmentId08', 'code' => "00"),
        "18" => array('value' => '調整課', 'parentId' => 'departmentId08', 'code' => "01"),
        "19" => array('value' => '農業振興課', 'parentId' => 'departmentId08', 'code' => "03"),
        "20" => array('value' => '水産課', 'parentId' => 'departmentId08', 'code' => "04"),
        "21" => array('value' => '森林課', 'parentId' => 'departmentId08', 'code' => "05"),
        "22" => array('value' => '食料安全課', 'parentId' => 'departmentId08', 'code' => "06"),
        "23" => array('value' => '', 'parentId' => 'departmentId09', 'code' => "00"),
        "24" => array('value' => '調整課', 'parentId' => 'departmentId09', 'code' => "01"),
        "25" => array('value' => '就業推進課', 'parentId' => 'departmentId09', 'code' => "03"),
        "26" => array('value' => '労働環境課', 'parentId' => 'departmentId09', 'code' => "04"),
        "27" => array('value' => '能力開発課', 'parentId' => 'departmentId09', 'code' => "05"),
        "28" => array('value' => 'その他', 'parentId' => 'departmentId99', 'code' => "99"),
    ),
    'typePolicy' => array(
        1 => '編集中', //has editing
        2 => '公開待ち', //has waiting public
        3 => '公開中', //has public
        4 => '非公開', //has private
    ),
    'typeRecruitmentTime' => array(
        3 => '公開中（募集前）', // before recruitment time
        2 => '公開中（募集中）', //in recruitment time
        4 => '【公開中（募集終了）】', //after recruitment time
    ),
    'attributePolicyType' => array(
        1 => array('name' => 'financialSupport', 'nameSearch' => 'searchContent', 'value' => '支援内容', 'displayCreate' => 1, 'required' => 1, 'templateCreate' => 1, 'detailPolicy' => 1),
        2 => array('name' => 'category', 'nameSearch' => 'searchField', 'value' => '分野', 'displayCreate' => 1, 'required' => 1, 'templateCreate' => 1, 'detailPolicy' => 1),
        3 => array('name' => 'objectSupport', 'nameSearch' => 'searchTargetPeople', 'value' => '対象者', 'required' => 1, 'displayCreate' => 1, 'templateCreate' => 1),
        5 => array('name' => 'industrySupport', 'nameSearch' => 'searchTargetJob', 'value' => '対象業種', 'required' => 1, 'displayCreate' => 1, 'templateCreate' => 4),
        4 => array('name' => 'supportArea', 'nameSearch' => 'searchArea', 'value' => '対象地域', 'required' => 1, 'displayCreate' => 1, 'templateCreate' => 2, 'notDefault' => 1),
//        6 => array('name' => 'industrySupportDetail', 'value' => '対象業種', 'displayCreate' => 1, 'templateCreate' => 4),
        7 => array('name' => 'supportAmount', 'nameSearch' => 'searchAmount', 'value' => '支援額', 'required' => 0, 'displayCreate' => 1, 'templateCreate' => 1),
        8 => array('name' => 'numberPeopleSupport', 'nameSearch' => 'searchNumberPeople', 'value' => '支援対象者数', 'required' => 0, 'displayCreate' => 1, 'templateCreate' => 2),
        9 => array('name' => 'recruitmentTime', 'nameSearch' => '', 'value' => '募集形態', 'required' => 1, 'displayCreate' => 1, 'templateCreate' => 3),
        10 => array('name' => 'favoritePolicy', 'nameSearch' => 'searchFavourite', 'value' => 'お気に入り施策'),
        11 => array('name' => 'recruitmentTimeStatus', 'nameSearch' => 'searchPolicyType', 'value' => '事業（実施）期間の詳細'),
    ),
    'supportArea' => array(
        1 => '東京都内',
        2 => '東京都外も含む',
    ),
    'folderUploadPdf' => 'policy-upload',
    'passwordDefault' => 'SanroAP123456',
    'exportComparePoliciesHeader' => array(
        1 => 'shortName',
        2 => 'name',
        3 => 'financialSupport',
        4 => 'category',
        5 => 'purpose',
        6 => 'objectSupport',
        7 => 'industrySupport',
        8 => 'detailOfSupportArea',
        9 => 'supportAmount',
        10 => 'numberPeopleSupport',
        11 => 'supportArea',
        12 => 'content',
        13 => 'detailRecruitmentTime',
        14 => 'homePage',
        15 => 'contact',
    ),
    'mailConfig' => array(
        'host' => 'casarray.fsoft.fpt.vn',
        'port' => 25,
        'basepath' => 'http://test-pharse1.sandro-ap.com',
        'timeout' => 30,
        'smtp' => array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        ),
    ),
    'configIni' => '/config/autoload/conf.ini'
);
