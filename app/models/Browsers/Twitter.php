<?php 
require_once dirname(__FILE__) . '/../Selenium/Base.php';
require_once dirname(__FILE__) . '/../../../lib/Account.php';
require_once dirname(__FILE__) . '/../Accounts/Gmail.php';
require_once dirname(__FILE__) . '/Gmail.php';
require_once dirname(__FILE__) . '/Factory.php';

class Models_Twitter extends Models_Selenium_Base {
    const BASE = 'https://twitter.com/i'; 
    const SIGNUP = '/flow/signup';
    const MAX_RETRY_CNT = 5;
    const TEXT_CHANGE_TO_EMAIL = 'かわりにメールアドレスを登録する';
    const TEXT_SUBMIT = '登録する';
    const TEXT_SKIP = '今はしない';
    const TEXT_SKIP_FOR_NOW = 'Skip for now';
    const TEXT_NEXT = '次へ';
    const TEXT_NO = 'いいえ';
    const TEXT_FETAIL_INPUT_AUTH_CODE = 'コードが間違っています。やりなおしてください。';

    // ログ書き込み用
    private $username;
    private $email;
    private $password;

    public function signup($mail_address, $mail_password) {
        $this->regist($mail_address);
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
        // $this->setTopic();
        $this->setInicialFollow();
        $this->setNotification();
    }

    /**
     * ユーザー名、メールアドレスを登録
     * 
     * @return void
     */
    private function regist($email) {
        $this->driver->get(self::BASE. self::SIGNUP);
        $username = Account::createUsername();
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
        $retry_cnt = 0;
        $is_retry = false;

        while($retry_cnt < self::MAX_RETRY_CNT) {
            try {
                $retry_cnt++;
                $auth_code = $this->getAuthCode('Yahoo');
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

    private function getEmail($service_key) {
        $factory = new Models_Account_Factory();
        $account_model = $factory->getInstance($service_key);
        if(is_null($account_model)) return null;
        $email = $account_model->getEmail();
        return $auth_code;

    }

    /**
     * 認証コードを取得する
     * 
     * @param string $service_key 
     * @return mixed 認証コード。認証コードの取得に失敗、またはモデルの生成に失敗した場合、NULLを返す
     */
    private function getAuthCode($service_key) {
        $factory = new Models_Browser_Factory($this->driver);
        $browser_model = $factory->getInstance($service_key);
        if(is_null($browser_model)) return null;
        // FIXME:  Yahoo独自の処理。このままではFactoryクラスを使っている意味がないため修正必須。
        $main_addr = $browser_model->getMainAddrFromSafetyAddr($this->email);
        $password = $browser_model->getPasswordFromMainAddr($main_addr);
        $browser_model->login($main_addr, $password);
        $auth_code = $browser_model->getAuthCode();
        return $auth_code;
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
        $this->findElementByXpathText(self::TEXT_SKIP_FOR_NOW)->click();
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
