<?php 
require_once dirname(__FILE__) . '/../Selenium/Base.php';
require_once dirname(__FILE__) . '/../Accounts/Yahoo.php';

/**
 * ブラウザのYahooを扱うクラス
 */
class Models_Browser_Yahoo extends Models_Selenium_Base {

    const URL_LOGIN = 'https://login.yahoo.co.jp/config/login';
    const URL_LOGOUT = 'https://login.yahoo.co.jp/config/login?logout=1&.intl=jp&.done=https://mail.yahoo.co.jp&.src=ym';
    const URL_MAIL_BOX = 'https://jp.mg5.mail.yahoo.co.jp/neo/launch';
    const PATTERN_TWITTER_AUTH = '/Twitterを使い始めるにはメールアドレスを確認してください/';

    /**
     * ログイン
     * 
     * @return void
     */
    public function login($mail_addr, $password) {
        $this->driver->get(self::URL_LOGIN);
        $this->findElementById('username')->sendKeys($mail_addr);
        $this->findElementByName('btnNext')->click();
        $this->findElementById('passwd')->sendKeys($password);
        $this->findElementById('btnSubmit')->click();
        $this->waitTitleContains('Yahoo! JAPAN');
    }

    /**
     * ログアウト
     * 
     * @return void
     */
    public function logout() {
        $this->driver->get(self::URL_LOGOUT);
    }

    /**
     * 認証コードを取得する
     * 
     * @return string
     */
    public function getAuthCode() {
        $this->driver->get(self::URL_MAIL_BOX);
        $mail_elements = $this->findElementsByClass('list-view-item');
        // 先頭はテーブルのヘッダ行なので削除する
        array_shift($mail_elements);
        // 認証メールを取得しクリックする(プレビューする)
        foreach ($mail_elements as $mail_element) {
            $mail_text = $mail_element->getText();
            if(empty($mail_text)) continue;
            preg_match(self::PATTERN_TWITTER_AUTH , $mail_text, $matches);
            if(count($matches) > 0) {
                $mail_element->click();
                sleep(2);
                break;
            }
        }
        // メール本文から認証コードを取得
        $preview = $this->findElementById('msg-preview')->getText();
        preg_match('/[0-9]{6}/', $preview, $auth_code);
        return array_shift($auth_code);
    }

    /**
     * セーフティアドレスの親アドレスを取得する
     * 
     * @param string $safety_addr セーフティアドレス
     * @return string 親アドレス
     */
    public function getMainAddrFromSafetyAddr($safety_addr) {
        $model_yahoo_account = new Models_Account_Yahoo();
        foreach($model_yahoo_account->accounts as $account) {
            $mail_addresses = $account->safety_mail_address;
            foreach ($mail_addresses as $mail_address) {
                if($mail_address === $safety_addr) return $account->user_id;
            }
        }
        return '';
    }

    /**
     * パスワードを取得する 
     * 
     * @param string $main_addr 
     * @return string
     */
    public function getPasswordFromMainAddr($main_addr) {
        $model_yahoo_account = new Models_Account_Yahoo();
        foreach($model_yahoo_account->accounts as $account) {
            if($account->user_id === $main_addr) return $account->password;
        }
        return '';
    }
}
