<?php 
/**
* Mail2EasyAPI Test - Gerenciar a Mail2Easy
*
* Esta API serviá para manipular os eventos que a mail2easy oferece 
* tais como enviar email, agendar, criar usuario e etc...
*
* PHP versions 5
* @link          https://mail2easypro.com/apidoc/
* @author        Charley Oliveira <charleycesar@gmail.com>
*/
use PHPUnit_Framework_TestCase as PHPUnit;
use Mail2Easy\Mail2Easy;
use Mail2Easy\Configuration;

class Mail2EasyTest extends PHPUnit {
    protected $Mail2Easy;
    protected $Configuration;
    
    public function setUp(){
        $this->Mail2Easy = new Mail2Easy();
        $this->Configuration = new Configuration();
        $this->mock = array(
            "new_user" => array(
                "email"=>"teste@teste.com",
                "name"=>"teste",
                "password"=>"@teste@01@",
                "language"=>"pt_BR",              #Optional
                "timezone"=>"America/Sao_Paulo"   #Optional
            )
        );
    }

    public function testGetLogin(){
        $login = (Bool) $this->Configuration->getLogin();
        $this->assertTrue($login);
    }

    public function testGetPassword(){
        $password = (Bool) $this->Configuration->getPassword();
        $this->assertTrue($password);
    }

    // Test Case Classe Mail2Easy
    public function testAuth()
    {
        $return = $this->Mail2Easy->auth($this->Configuration);
        $this->assertEquals(480001,$return['code']);
    }
    //Test Autenticar
    public function testGetAuthToken(){
        $token  = $this->Mail2Easy->getAuthToken();
        $this->assertEquals(63,strlen($token));
    }

    /**
    *   TESTE DE USUARIOS
    *   uri -> /user/add
    *   uri -> /user/update
    *   uri -> /user/get 
    *   OBS: Para deletar esse usuario de Teste voce vai precisar acessar o painel e deletar com uma conta de adminsitrador pois a API não permite deletar um usuario
    */
    
    //Test de Adicionar Usuario
    public function testAddUser(){
        $new_user = $this->mock['new_user'];
        $response = $this->Mail2Easy->api('/user/add',json_encode($new_user));
        if($response->code == 480001){
            $cod_usuario = $response->body->code;
        }
        $this->assertEquals(480001,$response->code);
    }

    //Test para atualizar um usuario
    public function testUpdateUser(){
        $new_user = $this->mock['new_user'];
        $response = $this->Mail2Easy->api('/user/add',json_encode($new_user));
        if($response->code == 480001){
            $cod_usuario = $response->body->code;
        }
        $this->assertEquals(480001,$response->code);
    }

    //Test de Obter o usuario logado
    public function testGetUser(){
        $response = $this->Mail2Easy->api('/user/get');
        $this->assertEquals(480001,$response->code);
    }
    
    public function tearDown(){
        unset($this->Mail2Easy);
        unset($this->Configuration);
    } 
}
?>