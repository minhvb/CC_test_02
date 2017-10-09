<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Administrator\Controller;

use Application\Controller\BaseController;
use Administrator\Service\ServiceInterface\ReportServiceInterface;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;

use Application\Utils\BrowserHelper;
use Application\Utils\CharsetConverter;

class ReportController extends BaseController {

    /**
     * @var \Administrator\Service\ReportService
     */
    protected $reportService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(ReportServiceInterface $reportService, EntityManager $em) {
        $this->reportService = $reportService;
        $this->em = $em;
    }

    public function indexAction() {
        try {
            $exportType = (int) $this->params()->fromPost('exportType', 0);
            $startMonth = trim(strip_tags($this->params()->fromPost('startMonth', '')));
            $endMonth = trim(strip_tags($this->params()->fromPost('endMonth', '')));

            $error = "";
            $startTime = strtotime($startMonth . "-01 00:00:00");
            $endTime = strtotime($endMonth . "-01 00:00:00");            
            $error .= !empty($startMonth) && !empty($endMonth) && $startTime>$endTime ? "募集開始日が募集終了日より小さいこと。" : "";
            $error .= !empty($startMonth) && $startTime>time() ? "募集開始日が現在の日付以下であること。" : "";
            $error .= !empty($endMonth) && $endTime>time() ? "募集終了日が現在の日付以下であること。" : "";
          
            if(empty($error)){
                if($exportType==1) {
                    /* export total login by role. group by day */
                    $filename = 'ログイン数集計' . date("Ymd") . ".csv";
                    
                    /* create tmp file */
                    $fp = tempnam("tmp", "csv");
                    
                    /* create header */
                    $data = array('日付', 'ユーザ種別', 'ログイン回数');
                    $this->putDataToFile($fp, $data);

                    /* get data export */
                    $reportData = $this->reportService->getTotalLoginByRole($startMonth, $endMonth);                    
                    foreach ($reportData as $row) {
                        $data = array($row["loginDay"], $row["roleTitle"], $row["totalLogin"]);
                        $this->putDataToFile($fp, $data);                        
                    }
                     
                    $this->responseToBrowser($fp, $filename);
                    exit();
                } else if($exportType==2) {
                    /* export login data by user */
                    $filename = 'ログインデータ出力' . date("Ymd") . ".csv";
                    
                    /* create tmp file */
                    $fp = tempnam("tmp", "csv");
                    
                    /* create header */
                    $data = array('ログイン日時', 'ユーザ種別', 'ＩＤ');
                    $this->putDataToFile($fp, $data);

                    /* get data export */
                    $reportData = $this->reportService->getLoginData($startMonth, $endMonth);                    
                    foreach ($reportData as $row) {
                        $data = array($row["username"], $row["roleTitle"], date("Y-m-d H:i:s", $row["loginDate"]));
                        $this->putDataToFile($fp, $data);                        
                    }
                      
                    $this->responseToBrowser($fp, $filename);
                    exit();
                } else if($exportType==3) {
                    /* export policy data by policy. group by day */
                    $filename = '施策情報閲覧等回数集計' . date("Ymd") . ".csv";
                    
                    /* get data export */
                    $reportData = $this->reportService->getPolicyData($startMonth, $endMonth);
                    
                    $fp = tempnam("tmp", "csv");
                    $this->putDataToFile($fp, array('年月', '施策名', 'アクセス', '', '', '', 'ダウンロード', '印刷'));
                    $this->putDataToFile($fp, array('', '', '都', '金融', '関連団体', '一般', '', ''));
                    
                    foreach ($reportData as $date=>$rows) {
                        foreach ($rows as $row) {
                            $data = array(
                                $date, $row["policyName"], $row["totalAccessRole3"], $row["totalAccessRole5"], $row["totalAccessRole4"], $row["totalAccessRole6"], $row["totalDownloadPDF"], $row["totalPrint"]
                            );
                            $this->putDataToFile($fp, $data);
                        }
                    }
                      
                    $this->responseToBrowser($fp, $filename);
                    exit();
                }    
            }
        } catch (\Exception $e){
            throw $e;   
        }
        exit();       
    }
    
    private function putDataToFile($fp, $data){
        $string = pack("S", 0xfeff) . \Application\Utils\CommonUtils::chboData(iconv("UTF-8", "UTF-16LE", implode("\t", $data) . PHP_EOL));
        file_put_contents($fp, pack("S", 0xfeff) . $string, FILE_APPEND);    
    }

    private function responseToBrowser($fp, $filename){
        /* clear buffer */
        $objFileName = new CharsetConverter();
        $browser = BrowserHelper::getBrowserName($_SERVER['HTTP_USER_AGENT']);
        $filename = $browser != BrowserHelper::INTERNET_EXPLORER ? $objFileName->toUtf8($filename) : $objFileName->utf8ToShiftJis($filename);
        
        ob_clean();

        header('Content-Description: File Transfer');
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);       
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header('Content-Length: ' . filesize($fp));
        @readfile($fp);   
    }
}
