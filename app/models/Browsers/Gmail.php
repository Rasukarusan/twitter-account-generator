<?php 
require_once dirname(__FILE__) . '/../Selenium/Base.php';
require_once dirname(__FILE__) . '/../Accounts/Gmail.php';

/**
 * ブラウザのGmailを扱うクラス
 */
class Models_Browser_Gmail extends Models_Selenium_Base {
    const URL_LOGIN = 'https://mail.google.com/';

    /**
     * ログインして受信トレイを表示
     * 
     * @return void
     */
    public function login() {
        $this->driver->get(self::URL_LOGIN);
        $gmail = Models_Account_Gmail::create();
        $this->findElementById('identifierId')->sendKeys($gmail->user_id);
        $this->clickNext();
        $this->findElementByName('password')->sendKeys($gmail->password);
        $this->clickNext();
        $this->waitTitleContains('受信トレイ');
    }

    /**
     * 認証コードを取得する
     * 
     * 1番上のメールのみを対象に取得したかったが、本文の取得が不安定だったため一旦全てのメール本文を取得し、
     * 直近の認証メールを取得するという形にしている。
     * また、Gmailでの要素の取得はウィンドウサイズに強く関連しており、ウィンドウサイズが小さいと要素が取得できないことがある。
     * ウィンドウサイズが小さいとメール本文が途中で見切れてしまい、要素が生成されない。そのため'y2'で取得しても空文字になってしまう場合がある。
     *
     * @return string 認証コード
     */
    public function getAuthCode() {
        $mail_elements = $this->findElementsByClass('y2');
        foreach ($mail_elements as $mail_element) {
            $mail_text = $mail_element->getText();
            if(empty($mail_text)) continue;
            preg_match('/Twitterを使い始めるには、以下の認証コードを入力/', $mail_text, $matches);
            if(count($matches) > 0) {
                $latest_mail_text = $mail_text;
                break;
            }
        }
        preg_match('/[0-9]{6}/', $latest_mail_text, $auth_code);
        return array_shift($auth_code);
    }


    /**
     * アカウントの認証処理をする
     *
     * 端末で一度もログインしていない場合に必要になる
     * 
     * @return void
     */
    private function auth() {
        sleep(2);
        $gmail = Models_Account_Gmail::create();
        $re_setting_email = $gmail->getReSettingEmail();
        $this->findElementByXpathText('再設定用のメールアドレスを確認してください')->click();
        $this->findElementById('identifierId')->sendKeys($re_setting_email);
        $this->clickNext();
    }

    /**
     * 「次へ」ボタンをクリック
     * 
     * @return void
     */
    private function clickNext() {
        // FIXME: seleniumのwait形式が不安定だったためsleep()を使用しているが、原因分かり次第wait形式に変更してください
        sleep(1);
        $this->findElementByXpathText('次へ')->click();
    }
}
