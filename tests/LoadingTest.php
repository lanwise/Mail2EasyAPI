<?php 
use PHPUnit_Framework_TestCase as PHPUnit;
use Mail2Easy\Mail2Easy;
use Mail2Easy\Configuration;

class Mail2EasyTest extends PHPUnit {
    protected $Mail2Easy;
    protected $Configuration;
    
    public function setUp(){
        $this->Mail2Easy = new Mail2Easy();
        $this->Configuration = new Configuration();
    }

    public function testGetLogin(){
        $login = $this->Configuration->getLogin();
        $this->assertEquals('login_aqui',$login);
    }

    public function testGetPassword(){
        $password = $this->Configuration->getPassword();
        $this->assertEquals('senha_aqui',$password);
    }

    //Test Case Classe Mail2Easy
    public function testAuth()
    {
        try {
            if( $this->Mail2Easy->auth($this->Configuration) ){
                $erro = false;
            }
        } catch (Exception $e) {
            $erro = true;
        }
        $this->assertFalse($erro);
    }
    public function tearDown(){
        unset($this->Mail2Easy);
        unset($this->Configuration);
    } 
}
?>