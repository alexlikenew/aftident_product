<?php

namespace Traits;

use PHPMailer\PHPMailer\PHPMailer;;
use Controllers\ConfigController;
use Models\MailTemplate;

trait Mailer{

    public $mailerError;

    /**
     *
     * @var PHPMailer
     */
    protected $Mailer;
    /**
     *
     * @return PHPMailer
     */

    protected $templatesModel;

    public function getMailer()
    {
        if($this->Mailer)
            return $this->Mailer;
        else{
            $this->Mailer = new PHPMailer();
            // $this->Mailer->SMTPDebug = 3;
            $this->setupMailer();
            return $this->Mailer;
        }
    }

    protected function setupMailer()
    {

        if (ConfigController::getOptionExtra('auth_smtp') == '1') {
            $this->getMailer()->issmtp(true); // wysylamy przez funkcje mail();
        } else {
            $this->getMailer()->issmtp(false); // wysylamy przez funkcje mail();
        }

        if (ConfigController::getOptionExtra('auth_auth') == '1') {
            $this->getMailer()->SMTPAuth = true;
        } else {
            $this->getMailer()->SMTPAuth = false;
        }

        $this->getMailer()->SMTPAuth = true;

        $this->getMailer()->Username = ConfigController::getOptionExtra('auth_email')['auth_email'];
        $this->getMailer()->Password = ConfigController::getOptionExtra('auth_pass')['auth_pass'];
        $this->getMailer()->Host = ConfigController::getOptionExtra('auth_host')['auth_host'];
        $this->getMailer()->Port = (int) ConfigController::getOptionExtra('auth_port')['auth_port'];

        $this->getMailer()->Timeout = 15;
        $this->getMailer()->SMTPAutoTLS = false;

        $this->getMailer()->CharSet = 'utf-8';
        $this->getMailer()->setLanguage('pl', ROOT_PATH . '/includes/vendor/phpmailer/phpmailer/language/');

        //$this->getMailer()->isMail(true); // wysylamy przez funkcje mail();
    }

    function sendHTML($rcptTo = '', $replyTo = '') {
        $this->getMailer()->isHTML(true);
        $this->getMailer()->AltBody = $GLOBALS['_PAGE_EMAIL_HTML'];

        //jeśli używamy funkcji mail ograniczamy długość linii
        if ($this->getMailer()->Mailer != 'smtp') {
            $this->getMailer()->Body = $this->htmlWrap($this->getMailer()->Body);
        }

        return $this->MySend($rcptTo, $replyTo);
    }

    public function clearRecipients(){
        return $this->getMailer()->ClearAllRecipients();
    }

    /* funkcja wysyla e-mail jako zwykly tekst */

    function SendPlain($rcptTo = '', $replyTo = '') {
        $this->getMailer()->isHTML(false);
        unset($this->getMailer()->AltBody);

        return $this->MySend($rcptTo, $replyTo);
    }

    /* funkcja wysyla maila za pomoca klasy PHP-Mailer */

    function MySend($rcptTo = '', $replyTo = '') {
        if (!empty($rcptTo))
            $this->getMailer()->AddAddress($rcptTo);
        if (!empty($replyTo)) {
            $this->getMailer()->From = ConfigController::getOptionExtra('auth_email')['auth_email'];
            $this->getMailer()->FromName = $replyTo;
            $this->getMailer()->AddReplyTo($replyTo);
        } else {
            $this->getMailer()->From = ConfigController::getOptionExtra('auth_email')['auth_email'];
            $this->getMailer()->FromName = FIRM_NAME;
        }

        //wylaczenie wysylania maili
//        return true;

        if ($this->getMailer()->send()) {
            return true;
        } else {
            $this->setMailerError($this->getError());

            return false;
        }
    }

    /* funkcja ustawia nadawce listu */

    function setFrom($from = '', $fromName = '') {
        if (!empty($from)) {
            $this->getMailer()->From = $from;
            if (!empty($fromName)) {
                $this->getMailer()->FromName = $fromName;
            } else {
                $this->getMailer()->FromName = $this->getMailer()->From;
            }
        }
        return true;
    }

    /* funkcja ustawia temat listu */
   public function setSubject(&$subject) {
        $this->getMailer()->Subject = &$subject;

        return true;
    }

    /* funkcja ustawia tresc listu (HTML lub plain/text) */

    function setBody(&$body) {
        $this->getMailer()->Body = '<style>' . PHP_EOL;
        $this->getMailer()->Body .= '    table, p, a {' . PHP_EOL;
        $this->getMailer()->Body .= '        margin: 0px;' . PHP_EOL;
        $this->getMailer()->Body .= '        font-family: Verdana;' . PHP_EOL;
        $this->getMailer()->Body .= '        color: #1b1b1b;' . PHP_EOL;
        $this->getMailer()->Body .= '    }' . PHP_EOL;
        $this->getMailer()->Body .= '' . PHP_EOL;
        $this->getMailer()->Body .= '    table {' . PHP_EOL;
        $this->getMailer()->Body .= '        border-collapse: collapse;' . PHP_EOL;
        $this->getMailer()->Body .= '    }' . PHP_EOL;
        $this->getMailer()->Body .= '' . PHP_EOL;
        $this->getMailer()->Body .= '    hr {' . PHP_EOL;
        $this->getMailer()->Body .= '        border: 0px;' . PHP_EOL;
        $this->getMailer()->Body .= '        border-bottom: 1px solid #e5e5e5;' . PHP_EOL;
        $this->getMailer()->Body .= '    }' . PHP_EOL;
        $this->getMailer()->Body .= '' . PHP_EOL;
        $this->getMailer()->Body .= '    a {' . PHP_EOL;
        $this->getMailer()->Body .= '        color: #ff402d;' . PHP_EOL;
        $this->getMailer()->Body .= '    }' . PHP_EOL;
        $this->getMailer()->Body .= '</style>' . PHP_EOL;
        $this->getMailer()->Body .= '' . PHP_EOL;
        $this->getMailer()->Body .= '<br>' . PHP_EOL;
        $this->getMailer()->Body .= '<table border="0" width="100%" align="left" style=" padding: 30px; border-collapse: collapse; ">' . PHP_EOL;
        $this->getMailer()->Body .= '    <tbody>' . PHP_EOL;
        $this->getMailer()->Body .= '        <tr>' . PHP_EOL;
        $this->getMailer()->Body .= '            <td style="font-family: Verdana; font-size: 14px; font-weight: 700; line-height: 16px; padding: 30px;"><img src="'.$_SERVER['HTTP_ORIGIN'].'/mail-header.png" style="height: 70px" alt="Argonium profesjonalne strony WWW"></td>' . PHP_EOL;
        $this->getMailer()->Body .= '        </tr>' . PHP_EOL;
        $this->getMailer()->Body .= '        <tr>' . PHP_EOL;
        $this->getMailer()->Body .= '            <td style="color: #1b1b1b; font-family: Verdana; font-size: 12px; font-weight: 400; line-height: 14px; padding: 30px;">' . $body . '</td>' . PHP_EOL;
        $this->getMailer()->Body .= '        </tr>' . PHP_EOL;
        $this->getMailer()->Body .= '    </tbody>' . PHP_EOL;
        $this->getMailer()->Body .= '</table>' . PHP_EOL;

        return true;
    }


    public function setContentAsBody($content){
        $this->getMailer()->Body = $content;
    }

    function getMailerError() {
        return $this->getMailer()->ErrorInfo;
    }

    public function clearAddr(){
        return $this->getMailer()->clearAddresses();
    }

    function htmlWrap($s, $chars = 65) {
        $aChunks = explode('<', $s);

        $sLine = '';
        $aLines = array();

        foreach ($aChunks as $i => $sChunk) {
            $sLine .= ($i == 0 ? '' : '<') . $sChunk;

// Check if line length too long
            if (strlen($sLine) + strlen(@$aChunks[$i + 1]) + 1 >= $chars) {
                $aLines[] = $sLine;
                $sLine = '';
            }
        }
        if ($sLine)
            $aLines[] = $sLine;

        return implode("\n", $aLines);
    }

    public function addAttachment($data){
        $this->getMailer()->addAttachment($data['path'], $data['name']);
    }

    public function setMailerError($error){
        $this->mailerError = $error;
    }

    public function getTemplatesModel(){
        if(!$this->templatesModel)
            $this->templatesModel = new MailTemplate();
        return $this->templatesModel;
    }
}
