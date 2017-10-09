<?php
namespace Application\Utils;

use PHPMailer;

/**
 * Class MailHelper
 *
 * @package Application\Utils
 */
class MailHelper
{
    
    const TEMPLATE_FORGOT_PASS = 1;
    const TEMPLATE_REGISTER = 2;
    const TEMPLATE_UPDATE_USER_INFO = 3;
    const TEMPLATE_RESET_PASSWORD = 4;
    const TEST = 5;

    protected $phpMailer;
    protected $serviceLocator;
    protected $mailConfig;

    public function __construct($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        $this->mailConfig = $this->serviceLocator->get('Config')['mailConfig'];

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Timeout = !empty($this->mailConfig['timeout']) ? $this->mailConfig['timeout'] : $mail->Timeout;
        $mail->Host = !empty($this->mailConfig['host']) ? $this->mailConfig['host'] : $mail->Host ;
        $mail->Port = !empty($this->mailConfig['port']) ? $this->mailConfig['port'] : $mail->Port;
        $mail->Username = CommonUtils::getConfigFromFileIni("FROM_EMAIL", $this->serviceLocator);                 // SMTP username
        $mail->Password = CommonUtils::getConfigFromFileIni("FROM_EMAIL_PASSWORD", $this->serviceLocator);                           // SMTP password
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; 
        PHPMailer::$validator = 'html5';

        
        /*                              
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        */
        $mail->smtpConnect($this->mailConfig['smtp']);
        
        
        $mail->setFrom(CommonUtils::getConfigFromFileIni("FROM_EMAIL",$this->serviceLocator), CommonUtils::getConfigFromFileIni("EMAIL_NAME",  $this->serviceLocator));
        $this->phpMailer = $mail;

    }

    public function sendEmail($address, $data, $mailTemplate = self::TEMPLATE_FORGOT_PASS) {
        $this->phpMailer->addAddress($address);     // Add a recipient
        $this->prepareMailBody($data, $mailTemplate);

        if (!$this->phpMailer->send()) {
            throw new \Exception($this->phpMailer->ErrorInfo);
        }

        return true;
    }

    private function prepareMailBody($data, $template = self::TEMPLATE_FORGOT_PASS) {
        $basePath = $this->mailConfig['basepath'];
        switch ($template) {
            case self::TEMPLATE_FORGOT_PASS  :
                $username = $data['username'];
                $token = $data['token'];
                $mailBody = "<a href=\"$basePath/forgot-password/new-password?id=$username&token=$token\">ここをクリックしパスワードを変更します。</a>";
                $this->phpMailer->Subject = '【産労AP】 メールパスワードの変更';
                $this->phpMailer->Body = $mailBody;
                break;
            case self::TEMPLATE_REGISTER :
                $username = $data['username'];
                $token = $data['token'];
                $mailBody = "<a href=\"$basePath/verify-email?id=$username&token=$token\">ここをクリックしメールを明確します。</a>";
                $this->phpMailer->Subject = '【産労AP】 メール登録確認';
                $this->phpMailer->Body = $mailBody;
                break;
            case self::TEMPLATE_UPDATE_USER_INFO :
                $userId = $data["userId"];
                $token = $data["token"];
                $mailBody = "<a href=\"$basePath/active-email?id=$userId&token=$token\">ここをクリックしメールを明確します。</a>";
                $this->phpMailer->Subject = '【産労AP】 新しいメール確認';
                $this->phpMailer->Body = $mailBody;
                break;
            case self::TEMPLATE_RESET_PASSWORD :
                $userId = $data["userId"];
                $password = $data["password"];
                $mailBody = "パスワードは「$password」に変更されました。 ";
                $this->phpMailer->Subject = '【産労AP】パスワード変更のお知らせ';
                $this->phpMailer->Body = $mailBody;
                break;
            case self::TEST :
                $password = $data["password"];
                $mailBody = $this->renderEmailTemplate("test.phtml", array("password"=>$password));
                $this->phpMailer->Subject = '【産労AP】 管理者によりリセットされたパスワード';
                $this->phpMailer->Body = $mailBody;
                break;
            default :
                break;
        }
    }
    
    public function renderEmailTemplate($templateName, $viewData = array()){
        $templatePath = BASE_PATH . "/module/Application/view/application/mail-template";
        
        $renderer = new \Zend\View\Renderer\PhpRenderer();
        $viewModel = new \Zend\View\Model\ViewModel($viewData);
        $renderer->resolver()->addPath($templatePath);
        $viewModel->setTemplate($templateName);
        
        return $renderer->render($viewModel);
    }
}
