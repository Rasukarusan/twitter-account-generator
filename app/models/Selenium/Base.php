<?php
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverKeys;
// use Facebook\WebDriver\WebDriverAlert;

class Models_Selenium_Base {

    protected $driver;

    public function __construct($driver) {
        $this->driver = $driver;
    }

  /**
   * ID値から要素を取得
   *
   * @param  string           $id 要素のID値
   * @return WebDriverElement
   */
    protected function findElementById($id) {
        return $this->driver->findElement(WebDriverBy::id($id));
    }

  /**
   * name値から要素を取得
   *
   * @param  string           $name 要素のname値
   * @return WebDriverElement $element
   */
    protected function findElementByName($name) {
        return $this->driver->findElement(WebDriverBy::name($name));
    }

  /**
   * クラス名から要素を取得
   *
   * @param  string $class クラス名
   * @return array [WebDriverElement]
   */
    protected function findElementsByClass($class) {
        return $this->driver->findElements(WebDriverBy::className($class));
    }

  /**
   * タグから要素を取得
   *
   * @param  string $tag タグ名
   * @return array [WebDriverElement]
   */
    protected function findElementsByTag($tag) {
        return $this->driver->findElements(WebDriverBy::tagName($tag));
    }

  /**
   * CSSセレクタから要素を取得
   *
   * @param  string $selector CSSセレクタ
   *         ex.) <p class="content">の場合、$selector = 'p.content'
   * @return WebDriverElement
   */
    protected function findElementByCssSelector($selector) {
        return $this->driver->findElement(WebDriverBy::cssSelector($selector));
    }


   /**
    * 完全一致のtext検索で要素を取得する
    *
    * @param  string $text 取得したい要素の単語
    * @return WebDriverElement $element
    */
    protected function findElementByLinkText($link_text) {
        return $this->driver->findElement(WebDriverBy::linkText($link_text));
    }

   /**
    * 部分一致のtext検索で要素を取得する
    *
    * @param  string $text 取得したい要素の単語
    * @return array [WebDriverElement]
    */
    protected function findElementsByLinkTextMatch($link_text) {
        return $this->driver->findElements(WebDriverBy::partialLinkText($link_text));
    }

    /**
     * XPathから要素を取得
     *
     * @param string $xpath
     * @return WebDriverElement $element
     */
    protected function findElementByXpath($xpath) {
        return $this->driver->findElement(WebDriverBy::xpath($xpath));
    }

   /**
    * text検索で要素を取得する
    *
    * linkTextでも取得できない場合の最終手段となる
    * <a>検索ワード</a>のような要素を取得できる
    *
    * @param  string $text 取得したい要素の単語
    * @return WebDriverElement $element
    */
    protected function findElementByXpathText($text) {
        return $this->driver->findElement(WebDriverBy::xpath('//*[text()="'.$text.'"]'));
    }

    /**
     * タイトルが完全一致で指定したものになるまで待つ
     *
     * @param string $title
     * @return void
     */
    protected function waitTitleIs($title) {
        $this->driver->wait(30)->until(WebDriverExpectedCondition::titleIs($title));
    }

    /**
     * タイトルが部分一致で指定したものになるまで待つ
     *
     * @param string $title
     * @return void
     */
    protected function waitTitleContains($title) {
        $this->driver->wait(30)->until(WebDriverExpectedCondition::titleContains($title));
    }

    /**
     * 要素がクリックできるようになるまで待つ
     *
     * @param WebDriverElement $element
     * @return void
     */
    protected function waitClickable($element) {
        $this->driver->wait(30)->until(WebDriverExpectedCondition::elementToBeClickable($element));
    }

    /**
     * <select>タグの要素を扱うインスタンスを生成
     *
     * findElementByIdなどで取得した<select>要素の選択・非選択など、
     * <select>タグの要素を扱うためのメソッドが使用できるようになります。
     *
     * @param  WebDriverElement $element <select>タグの要素
     * @return WebDriverSelectオブジェクト
     */
    protected function createWebDriverSelect($element) {
        $_element = new WebDriverSelect($element);
        return $_element;
    }

    /**
     * 対象の<tbody>の行を全て取得
     *
     * @param  WebDriverElement $tbody 対象の<tbody>のID
     * @return array <tr>要素がWebDriberBy型で格納された配列
     */
    protected function getTableRows($tbody) {
        $rows  = $tbody->findElements(WebDriverBy::tagName('tr'));
        return array_values($rows);
    }

    /**
     * 新規タブを作成し、以後の$driverの処理を遷移後のタブで行う
     *
     * @return void
     */
    protected function createAndMoveTab() {
        $this->driver->executeScript('window.open()');
        $tabs = $this->driver->getWindowHandles();
        $this->driver->switchTo()->window(end($tabs));
    }

    /**
     * 対象の要素までスクロールする
     *
     * @param WebDriverElement $element
     * @return void
     */
    protected function scrollToElement($element) {
        $element->getLocationOnScreenOnceScrolledIntoView();
    }
}

