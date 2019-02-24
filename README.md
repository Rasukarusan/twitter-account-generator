# twitter-account-generator
Twitterアカウント自動作成
Twitter accounts generate automation by selenium.

# Usage

```sh
$ git clone https://github.com/Rasukarusan/twitter-account-generator.git
$ cd twitter-account-generator
$ php batch/run.php
```

# Setting

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

# Warning 

We can create **3** accounts by one mail_address. You need phone number auth over 3 accounts. 
So if you need many mail_addresses.
