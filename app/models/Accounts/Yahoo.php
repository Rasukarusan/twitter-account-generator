<?php 
require_once dirname(__FILE__) . '/Base.php';

/**
 * Yahooのアカウント関係を処理するクラス
 */
class Models_Account_Yahoo extends Models_Account_Base {

    private $re_setting_email = '';

    function __construct() {
        $account = $this->getAccount('Yahoo');
        $this->re_setting_email = $account['re_setting_email'];
        parent::__construct($account['user_id'], $account['password']);
    }

    /**
     * 確認用の再設定メールアドレスを取得する
     * 
     * @return string
     */
    public function getReSettingEmail() {
        return $this->re_setting_email;
    }
}
