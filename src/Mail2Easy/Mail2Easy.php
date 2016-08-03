<?php
	include 'Configuration.php';


	/**
	* 	Classe responsavel por permitir que o CakePHP 1.3
	*	trabalhe com a API da Dinamize realizando a integração com o servico Mail2Easy
	*
	*	@author Charley Oliveira <charleycesar@gmail.com>
	*	@link https://mail2easypro.com/apidoc/#api-_
	*	@version 1.0
	*/
	class Mail2Easy
	{
		/**
		*	URL utilizada para autenticação da API do Mail2Easy
		*/
		const SERVICE_AUTH_URL = 'https://api.dinamize.com/auth';

		/**
		*	URL utilizada para consumo da API do Mail2Easy
		*/
		const BASE_API_URL = 'https://api.dinamize.com';

		/**
		*	@var string
		*/
		public $authToken;

		/**
		*	Initialize Mail2Easy
		*	@param Controller $controller
		*	@throws Exception
		*/
		//called before Controller::beforeFilter()
	    public function __construct()
	    {
	        $user = new Configuration;
	        $this->auth($user);

	    }

	    /**
	    *	Autenticated
	    *	Função responsavel por autenticar
	    *	@author Charley Oliveira <charleycesar@gmail.com>
	    *	@param $user Objeto da Classe de Configuração
	    *	@return boolean
	    */
	    public function auth($user){
	    	// Cria um array de configuracoes de conexao
	        $data = array('user'=>$user->getLogin(),'password'=>$user->getPassword(),'client_code'=>$user->getClientCode());
	        // Converte pra json
	        $data_string = json_encode($data);

	        // Prepara as opções para o processo de autenticação
	    	$serviceHandler = curl_init();
	        curl_setopt($serviceHandler, CURLOPT_URL, self::SERVICE_AUTH_URL);
	        curl_setopt($serviceHandler, CURLOPT_HTTPHEADER,  array('Content-Type: application/json; charset=utf-8'));
	        curl_setopt($serviceHandler, CURLOPT_POSTFIELDS, $data_string);
	        curl_setopt($serviceHandler, CURLOPT_SSLVERSION, 1);
	        curl_setopt($serviceHandler, CURLOPT_POST, TRUE);
	        curl_setopt($serviceHandler, CURLOPT_RETURNTRANSFER, TRUE);

	        $response = curl_exec($serviceHandler);

	        // Status HTTP da resposta
	        $code = curl_getinfo($serviceHandler, CURLINFO_HTTP_CODE);
	        // Se a requisição HTTP foi bem sucedida (código 200)
	        if( $code != 200 )
	        {
	            throw new Exception('Ocorreu um erro ao realizar a autenticação com o serviço de email "Mail2Easy".');
            }

        	// Aplica a função da linguagem que converte uma string JSON para um array
	        $response = json_decode($response,true);

	        // Armazena o Token e a URL que serão usados nas requisições subsequentes
        	if ( $response['code'] == '480001' )  // CODIGO DE SUCESSO
        	{
		        // Armazena o Token e a URL que serão usados nas requisições subsequentes
		        $authToken = $response['body']['auth-token'];
        		$this->setAuthToken($authToken);
        		return $response;
        	}
		    else
		    {
		    	// Erros retornados pela API
		        throw new \Exception($response['code_detail']);
		    }
	    }

	    /**
	    *	Get AuthToken
	    *
	    *	@return string
	    */
	    public function getAuthToken()
	    {
	    	return $this->authToken;
	    }

	    /**
		*	Set AuthToken
		*
		*	@param string $authToken
		*	@return Mail2Easy
	    */
	    protected function setAuthToken($authToken)
	    {
	    	$this->authToken = $authToken;
	    	return $this;
	    }

	    /**
	    *	Este método é fornecido na API do Mail2Easy::runCommand
	    *
	    *	@param  string $uri      o recurso que será solicitado
	    *	@param  array|null $data os dados enviados via requisição POST e PUT ou nulo para GET
	    *	@param  string $method   o método de envio
	    *	@return array
	    */
	    public function api($uri, $data = null, $method = 'POST')
	    {
			$method = strtoupper($method);
	        $token = $this->getAuthToken();
			$data = ($data) ? $data : '{}';
	        // Inicializa a biblioteca cURL
	        $serviceHandler = curl_init();
	        if ( $method == 'POST' )
	        {
	        	curl_setopt_array($serviceHandler,
	        		array(
						CURLOPT_URL => self::BASE_API_URL . $uri,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS => $data,
						CURLOPT_HTTPHEADER => array(
							"auth-token: $token",
							"cache-control: no-cache",
							"content-type: application/json; charset=utf-8"
						)
					)
	        	);
	        }
	        // Retorna a resposta, já decodificada como objeto PHP.
	        return json_decode(curl_exec($serviceHandler));
		}
	}
?>
