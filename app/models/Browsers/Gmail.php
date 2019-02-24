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
