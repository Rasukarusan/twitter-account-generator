<?php
require_once 'vendor/autoload.php';

class Example extends PHPUnit_Extensions_SeleniumTestCase
{
    protected function setUp()
    {
        $this->setBrowserUrl('http://www.google.com');
        $this->setBrowser('*firefox');
    }

    public function myTest()
    {
        $this->open("/");
        $this->waitForPageToLoad("60000");
        try {
            $this->assertTrue($this->isTextPresent("google"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
    }
}
