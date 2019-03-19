<?php 
require_once dirname(__FILE__) . '/Base.php';

/**
 * Yahooのアカウント関係を処理するクラス
 */
class Models_Account_Yahoo extends Models_Account_Base {

    const SERVICE_KEY = 'Yahoo';
    public $accounts;

    function __construct() {
        $this->accounts = $this->getAccounts(self::SERVICE_KEY);
    }

    /**
     * 指定したサービスのアカウント情報を取得
     * 
     * @param mixed $service_key 
     * @return stdClass
     */
    private function getAccounts($service_key) {
        $json = file_get_contents(PATH_ACCOUNT_JSON);
        $account = json_decode($json);
        return $account->$service_key;
    }
}
