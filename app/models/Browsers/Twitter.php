<?php 
require_once dirname(__FILE__) . '/../Selenium/Base.php';
require_once dirname(__FILE__) . '/../../../lib/Account.php';
require_once dirname(__FILE__) . '/../Accounts/Gmail.php';
require_once dirname(__FILE__) . '/Gmail.php';

class Models_Twitter extends Models_Selenium_Base {
    const BASE = 'https://twitter.com/i'; 
    const SIGNUP = '/flow/signup';
    const MAX_RETRY_CNT = 5;
    const TEXT_CHANGE_TO_EMAIL = 'かわりにメールアドレスを登録する';
    const TEXT_SUBMIT = '登録する';
    const TEXT_SKIP = '今はしない';
    const TEXT_NEXT = '次へ';
    const TEXT_NO = 'いいえ';
    const TEXT_FETAIL_INPUT_AUTH_CODE = 'コードが間違っています。やりなおしてください。';

    // ログ書き込み用
    private $username;
    private $email;
    private $password;

    public function signup() {
        $this->regist();
        $this->createAndMoveTab();
        if(!$this->auth()) {
            echo ERROR_FETAIL_GET_AUTH_CODE . "\n";
            exit;
        }
        $this->setPassword();
        $result = [
            'username' => $this->username,
            'email'    => $this->email,
            'password' => $this->password,
        ];
        return $result;
    }

    /**
     * 新規登録完了後の諸々の初期設定
     * 
     * @return void
     */
    public function setting() {
        $this->setIcon();
        $this->setProfile();
        $this->setContact();
        $this->setTopic();
        $this->setInicialFollow();
        $this->setNotification();
    }

    /**
     * ユーザー名、メールアドレスを登録
     * 
     * @return void
     */
    private function regist() {
        $this->driver->get(self::BASE. self::SIGNUP);
        $username = Account::createUsername();
        $email = Account::createEmail('Gmail');
        $this->username = $username;
        $this->email = $email;
        $this->findElementByName('name')->sendKeys($username);
        $this->findElementByXpathText(self::TEXT_CHANGE_TO_EMAIL)->click();
        $this->findElementByName('email')->sendKeys($email);
        $this->clickNext();
        $this->findElementByXpathText(self::TEXT_SUBMIT)->click();
    }

    /**
     * 認証コードを入力する
     * 
     * @return boolean 
     */
    private function auth() {
        // Gmailから認証コードを取得
        $retry_cnt = 0;
        $is_retry = false;

        while($retry_cnt < self::MAX_RETRY_CNT) {
            try {
                $retry_cnt++;
                $auth_code = "";
                $auth_code = $this->getAuthCodeFromGmail($is_retry);
                if(is_null($auth_code)) throw new Exception(RETRY_AUTH);
                // 操作中のタブをtwitterに戻す
                $tabs = $this->driver->getWindowHandles();
                $this->driver->switchTo()->window(array_shift($tabs));

                // 認証コードを入力する
                $this->findElementByName('verfication_code')->sendKeys($auth_code);
                $this->clickNext();
                // if(!$this->isValidAuth()) throw new Exception("認証に失敗しました。リトライします。\n");
                break;
            } catch (Exception $e) {
                echo $e->getMessage();
                $is_retry = true;
                if($retry_cnt === self::MAX_RETRY_CNT) return false;
            }
        }
        return true;

    }

    /**
     * 認証に成功したかを判定
     * 
     * 間違った認証コードの場合、アラートが表示されるのでその有無で判定する
     * 認証に失敗した場合、タブ操作をGmailに戻す
     * 
     * @return boolean true: 認証成功, false: 認証失敗
     */
    private function isValidAuth() {
        $alert = $this->findElementByXpathText(self::TEXT_FETAIL_INPUT_AUTH_CODE);
        if(count($alert) > 0) {
            $tabs = $this->driver->getWindowHandles();
            $this->driver->switchTo()->window(end($tabs));
            return false;
        } 
        return true;
    }

    /**
     * GmailからTwitterの認証コードを取得する
     * 
     * @return string
     */
    private function getAuthCodeFromGmail($is_retry) {
        // リトライ処理の場合、既にログイン済みなので認証コードの取得のみ行う
        $gmail = new Models_Browser_Gmail($this->driver);
        if($is_retry) return $gmail->getAuthCode(); 
        $gmail->login();
        return $gmail->getAuthCode();
    }

    /**
     * YahooからTwitterの認証コードを取得する
     * 
     * @return string
     */
    private function getAuthCodeFromYahoo($is_retry) {
        // リトライ処理の場合、既にログイン済みなので認証コードの取得のみ行う
        $yahoo = new Models_Browser_Yahoo($this->driver);
        if($is_retry) return $yahoo->getAuthCode(); 
        $yahoo->login();
        return $yahoo->getAuthCode();
    }

    /**
     * パスワードを設定
     * 
     * @return void
     */
    private function setPassword() {
        $password = Account::createPassword();
        $this->password = $password;
        $this->findElementByName('password')->sendKeys($password);
        $this->clickNext();
    }

    /**
     * プロフィール画像設定
     * 
     * @return void
     */
    private function setIcon() {
        // スキップする
        $this->findElementByXpathText(self::TEXT_SKIP)->click();
    }

    /**
     * 自己紹介設定
     * 
     * @return void
     */
    private function setProfile() {
        // スキップする
        $this->findElementByXpathText(self::TEXT_SKIP)->click();
    }

    /**
     * 連絡先同期設定
     * 
     * @return void
     */
    private function setContact() {
        // スキップする
        $this->findElementByXpathText(self::TEXT_SKIP)->click();
    }

    /**
     * 興味のあるトピック設定
     * 
     * @return void
     */
    private function setTopic() {
        // スキップする
        $this->findElementByXpathText(self::TEXT_SKIP)->click();
    }

    /**
     * おすすめアカウントの設定
     * 初期フォロー
     * 
     * @return void
     */
    private function setInicialFollow() {
        // スキップする
        $this->findElementByXpathText(self::TEXT_NEXT)->click();
    }

    /**
     * 通知設定
     * 
     * @return void
     */
    private function setNotification() {
        // スキップする
        $this->findElementByXpathText(self::TEXT_SKIP)->click();
    }

    /**
     * アプリをダウンロード設定
     * 
     * @return void
     */
    private function setAppDownload() {
        // スキップする
        $this->findElementByXpathText(self::TEXT_NO)->click();
    }

    /**
     * 「次へ」ボタンをクリックする
     * 
     * @return void
     */
    private function clickNext() {
        $next = $this->findElementByXpathText(self::TEXT_NEXT);
        sleep(2);
        $next->click();
    }
}
