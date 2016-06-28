<?php 
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
use Mail2Easy\Mail2Easy;
use Mail2Easy\Configuration;

class Mail2EasyTest extends PHPUnit_Framework_TestCase {
 
  public function testLoading()
  {
    $Mail2Easy = new Mail2Easy;
	$this->assertTrue($Mail2Easy->test());
  }
  public function testInitialize()
  {
    $Mail2Easy = new Mail2Easy;
	$this->assertTrue($Mail2Easy->initialize());
  }
  // public function test()
  // {

  // 	$Configuration = new Configuration;
  // 	echo $Configuration::test();
  // }
 
}
?>