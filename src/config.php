<?php
	/**
	* 	Classe responsavel por configurar o usuario que vai usar o mail2easy
	*
	*	@author Charley Oliveira <charleycesar@gmail.com>
	*	@link https://mail2easypro.com/apidoc/#api-_
	*	@version 1.0
	*/
	class Configuration
	{
		/**
		* @var string
		*/
		protected $login;

		/**
		* @var string
		*/
		protected $password;

		private function __construct()
		{
			$this->setLogin("login_aqui");
			$this->setPassword("senha_aqui");			
		}

		/**
		*	Atribui o valor na variavel login
		*	@param string $login
		*	@return Configuration
		*/
		protected function setLogin($login)
		{
			$this->login = $login;
			return $this;
		}

		/**
		*	Atribui o valor na variavel login
		*	@param string $password
		*	@return Configuration
		*/
		protected function setPassword($password)
		{
			$this->password = $password;
			return $this;
		}

		/**
	    *	Get Login
	    *	@return string
	    */
	    public function getLogin()
	    {
	    	return $this->login;
	    }

	    /**
	    *	Get Password
	    *	@return string
	    */
	    public function getPassword()
	    {
	    	return $this->password;
	    }
	}
?>