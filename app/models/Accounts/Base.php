<?php 

class Models_Account_Base {

    public $user_id;
    public $password;

    function __construct($user_id = '', $password = '') {
        $this->user_id = $user_id;
        $this->password = $password;
    }

    /**
     * インスタンスを生成
     * 
     * @return Models_Account_Base
     */
    public static function create() {
        $class = get_called_class();
        return new $class;
    }


    /**
     * 指定したサービスのアカウント情報を取得
     * 
     * @param mixed $service_key 
     * @return stdClass
     */
    protected function getAccount($service_key) {
        $json = file_get_contents(PATH_ACCOUNT_JSON);
        $account = json_decode($json);
        return $account[$service_key];
    }
}
