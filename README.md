# php-selenium-base
php selenium baby.

# Usage

```sh
# headless
$ php batch/run.php 1

# non-headless
$ php batch/run.php
```

# Example

Edit account.json
```sh
$ vim account.json
{
    "Gmail" : {
        "user_id"          : "hoge@gmail.com",
        "password"         : "foofoo",
        "re_setting_email" : "foo@yahoo.co.jp"
    }
}
```

Edit MainController.php
```sh
$ vim app/controllers/MainController.php
```

```php
<?php
require_once dirname(__FILE__) .'/../models/Selenium/Webdriver.php';
require_once dirname(__FILE__) .'/../models/Browsers/Gmail.php';

class MainController {

    private $is_headless;

    function __construct($is_headless) {
        $this->is_headless = $is_headless;
    }

    public function main() {
        $driver = Models_Webdriver::create($this->is_headless);
        // Gmail login. Edit account.json before you run.
        $gmail = new Models_Browser_Gmail($driver);
        $gmail->login();
        $driver->quit();
    }
}
```

