<?php
	include_once('src/config.php');
	/**
	* 	Classe responsavel por permitir que o CakePHP 1.3
	*	trabalhe com a API da Dinamize realizando a integração com o servico Mail2Easy
	*
	*	@author Charley Oliveira <charleycesar@gmail.com>
	*	@link https://mail2easypro.com/apidoc/#api-_
	*	@version 1.0
	*/
	class Mail2Easy extends Object
	{
		/**
		*	URL utilizada para autenticação da API do Mail2Easy
		*/
		const SERVICE_AUTH_URL = 'https://api.dinamize.com/auth';
		
		/**
		*	URL utilizada para consumo da API do Mail2Easy
		*/
		const BASE_API_URL = 'https://api.dinamize.com/';

		/**
		*	@var string
		*/
		protected $authToken;

		/**
		*	Initialize Mail2Easy 
		*	@param Controller $controller
		*	@throws Exception
		*/
		//called before Controller::beforeFilter()
	    function initialize(&$controller, $settings = array())
	    {
	        // salva a controller para utilizar em outros methods internos
	        $this->controller =& $controller;
	        $user = new Configuration();

	        $serviceHandler = curl_init();
	        // Cria um array de configuracoes de conexao
	        $data = array("user"=>$user->getLogin(),"password"=>$user->getPassword());
	        // Converte pra json
	        $data_string = json_encode($data); 

	        // Prepara as opções para o processo de autenticação
	        curl_setopt($serviceHandler, CURLOPT_URL, self::SERVICE_AUTH_URL);
	        curl_setopt($serviceHandler, CURLOPT_HEADER,  array('Content-Type: application/json; charset=utf-8'));
	        curl_setopt($serviceHandler, CURLOPT_POSTFIELDS, $data_string);
	        curl_setopt($serviceHandler, CURLOPT_SSLVERSION, 1);
	        curl_setopt($serviceHandler, CURLOPT_POST, TRUE);
	        curl_setopt($serviceHandler, CURLOPT_RETURNTRANSFER, TRUE);

	        $response = curl_exec($serviceHandler);
	        
	        // Status HTTP da resposta
	        $code = curl_getinfo($serviceHandler, CURLINFO_HTTP_CODE);

	        // Se a requisição HTTP fio bem sucedida (código 200) 
	        if( $code != 200 )
	        {
	            throw new \Exception('Ocorreu um erro ao realizar a autenticação com o serviço de email "Mail2Easy".');
            }

        	// Aplica a função da linguagem que converte uma string JSON para um array
        	$response = json_decode($response,true);

	        // Armazena o Token e a URL que serão usados nas requisições subsequentes
        	if ( $response['code'] == '480001' )  // CODIGO DE SUCESSO
        	{
		        // Armazena o Token e a URL que serão usados nas requisições subsequentes
		        $authToken = $response['body']['auth-token'];
        		$this->setAuthToken($authToken);
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
	        // Inicializa a biblioteca cURL
	        $serviceHandler = curl_init();
	        curl_setopt($serviceHandler, CURLOPT_URL, $this->getServiceBaseUrl() . '/' . $uri);
	        curl_setopt($serviceHandler, CURLOPT_HEADER, false);
	        // Inicializa o(s) cabeçalho(s) HTTP. O cabeçalho "Dinamize-Auth" deve estar sempre presente
	        $headers = array('Dinamize-Auth: ' . $this->getAuthToken());
	        curl_setopt($serviceHandler, CURLOPT_RETURNTRANSFER, true);
	        if ( $method == 'POST' )
	        {
	            curl_setopt($serviceHandler, CURLOPT_POST, TRUE);
	            curl_setopt($serviceHandler, CURLOPT_POSTFIELDS, $data);
	        }
	        else if ($method == 'GET')
	        {
	            curl_setopt($serviceHandler, CURLOPT_HTTPGET, TRUE);
	        }
	        else
	        {
	        	curl_setopt($serviceHandler, CURLOPT_CUSTOMREQUEST, $method);
	        	if (strtoupper($method) == 'PUT')
	            {
	                // No caso de uma requisição PUT, um cabeçalho HTTP adicional é necessário
	                $data = http_build_query($data);
	                $headers[] = 'Content-Length: ' . strlen($data);
	                curl_setopt($serviceHandler, CURLOPT_POSTFIELDS, $data);
	            }
	        }
	        // Seta os cabeçalhos HTTP
	        curl_setopt($serviceHandler, CURLOPT_HTTPHEADER, $headers);
	        // Retorna a resposta, já decodificada como objeto PHP.
	        return json_decode(curl_exec($serviceHandler));
		}
	}
?>