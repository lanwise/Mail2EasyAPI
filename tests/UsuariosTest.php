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

class UsuariosTest extends PHPUnit {
    protected $Mail2Easy;
    protected $Configuration;
    
    public function setUp(){
        $this->Mail2Easy = new Mail2Easy();
        $this->Configuration = new Configuration();
        $this->mock = array(
            "new_user" => array(
                "email"=>"novo@teste.com",
                "name"=>"teste",
                "password"=>"@teste@01@",
                "language"=>"pt_BR",              #Optional
                "timezone"=>"America/Sao_Paulo"   #Optional
            ),
            "user_update" => array(
                "email"=>"user_atual_update@gmail.com",
                "name"=>"Teste Update",
                "password"=>"@mudar123@",
                "language"=>"pt_BR",              #Optional
                "timezone"=>"America/Sao_Paulo"   #Optional
            ),
            "search_user" => array(
                // "page_number" => 1, #Optional
                // "page_size" => 10,  #Optional
                // "order" => array(   #Optional ASC || DESC
                //     0 => array(
                //         "field"=>"email",
                //         "type"=>"ASC"
                //     )
                // )
                "search" => array(
                    0 => array(
                        "field"=>"email",
                        "operator"=>"=",
                        "value"=>"novo@teste.com"
                    )
                )
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
    public function getLogin(){
        return $this->login;
    }
    public function getPassword(){
        return $this->password;
    }
    public function getClientCode(){
        return $this->Configuration->getClientCode();
    }
    public function setLogin($login){
        $this->login = $login;
        return $this;
    }
    public function setPassword($password){
        $this->password = $password;
        return $this;
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
    *   uri -> /user/search 
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
    /**
    * Atualiza um usuario logado

    public function testUpdateUser(){
        $user = $this->mock['user_update'];
        $response = $this->Mail2Easy->api('/user/update',json_encode($user));
        if($response->code == 480001){
            $updated = $response->body->affected_rows;
        }
        $this->setLogin($this->mock['user_update']['email']);
        $this->setPassword($this->mock['user_update']['password']);
        $logar_with_update = $this->Mail2Easy->auth($this);
        var_dump($logar_with_update);        
        //Testa se atualizou
        $this->assertEquals(1,$updated);
        $this->assertEquals(480001,$response->code);
        //Testa se consegue logar apos atualizar
        $this->assertEquals(480001,$logar_with_update['code']);
    }
    */

    //Test de Obter o usuario logado
    public function testGetUser(){
        $response = $this->Mail2Easy->api('/user/get');
        $this->assertEquals(480001,$response->code);
    }

    //Test de Consulta de usuarios
    public function testSearchUser(){
        $data_search = json_encode($this->mock['search_user']);
        $response = $this->Mail2Easy->api('/user/search',$data_search);
        $this->assertEquals(480001,$response->code);
        $this->assertEquals("novo@teste.com",$response->body->items[0]->email);
    }
    
    public function tearDown(){
        unset($this->Mail2Easy);
        unset($this->Configuration);
    } 
}
?>