<?php 

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverDimension;

class Models_Webdriver {

    const SELENIUM_TIMEOUT_SEC = 100; // seleniumのタイムアウト時間(秒)

    /**
     * WebDriverを生成
     *
     * @return ReomoteWebDriver
     */
    public static function create($is_headless = true) {
        $options = new ChromeOptions();
        $option_args = $is_headless ? ['--headless', '--no-sandbox', '--disable-gpu'] : [];
        $options->addArguments($option_args);
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $host = 'http://localhost:4444/wd/hub';
        $driver = RemoteWebDriver::create($host, $capabilities);
        // $driver->manage()->window()->maximize();
        $size = new WebDriverDimension(1600, 800);
        $driver->manage()->window()->setSize($size);
        $driver->manage()->timeouts()->implicitlyWait(self::SELENIUM_TIMEOUT_SEC);
        $driver->manage()->timeouts()->pageLoadTimeout(self::SELENIUM_TIMEOUT_SEC);
        $driver->manage()->timeouts()->setScriptTimeout(self::SELENIUM_TIMEOUT_SEC);

        return $driver;
    }
}


