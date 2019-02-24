<?php
require_once dirname(__FILE__) .'/../models/Selenium/Webdriver.php';
require_once dirname(__FILE__) .'/../models/Browsers/Twitter.php';

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
        // twitterで新規アカウント作成
        $twitter = new Models_Twitter($driver);
        for ($i = 1; $i < self::GENERATE_USER_CNT; $i++) {
            echo "\n{$i}つ目のユーザー作成中...\n";
            echo "=======================================================\n";
            $result = $twitter->signup();
            $this->writeLog($result);
            $twitter->setting();
            echo "=======================================================\n";
        }
        // $driver->quit();
    }

    private function writeLog($result) {
        $log = "\nusername:".$result['username']."\nemail:".$result['email']."\npassword:".$result['password']."\n\n";
        file_put_contents(PATH_GENERATED_ACCOUNT, $log, FILE_APPEND | LOCK_EX);
    }
}
