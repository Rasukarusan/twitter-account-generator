<?php
require_once dirname(__FILE__) .'/../models/Selenium/Webdriver.php';
require_once dirname(__FILE__) .'/../models/Browsers/Twitter.php';
require_once dirname(__FILE__) .'/../models/Browsers/Yahoo.php';
require_once dirname(__FILE__) .'/../models/Accounts/Yahoo.php';

class MainController {

    // 作成するユーザー数  
    // 1つのメールアドレスで登録できるのは3つまで。 
    const GENERATE_USER_CNT = 3;

    private $is_headless;

    function __construct($is_headless) {
        $this->is_headless = $is_headless;
    }

    public function main() {
        // ブラウザ起動
        $driver = Models_Webdriver::create($this->is_headless);
        $yahoo = new Models_Browser_Yahoo($driver);
        $model_yahoo_account = new Models_Account_Yahoo();
        $twitter = new Models_Twitter($driver);

        foreach($model_yahoo_account->accounts as $account) {
            $mail_addresses = $account->safety_mail_address;
            foreach ($mail_addresses as $mail_address) {
                $result = $twitter->signup($mail_address, $account->password);
                $this->writeLog($result);
                $twitter->setting();
            }
        }
        $driver->quit();
    }

    private function writeLog($result) {
        $log = "\nusername:".$result['username']."\nemail:".$result['email']."\npassword:".$result['password']."\n\n";
        file_put_contents(PATH_GENERATED_ACCOUNT, $log, FILE_APPEND | LOCK_EX);
    }
}
