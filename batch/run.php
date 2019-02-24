<?php 
/**
 * 第一引数にheadlessで起動するかを指定する
 * 
 * ex.) php batch/run.php 1の場合、headless起動
 * ex.) php batch/run.phpの場合、画面起動
 */

require_once dirname(__FILE__) . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/../app/controllers/MainController.php';
require_once dirname(__FILE__) . '/../lib/Define.php';

$is_headless = false;
if(isset($argv[1]) && $argv[1] === '1') $is_headless = true;
$main = new MainController($is_headless);
$main->main();
