<?php
namespace uBlog\Providers;

class Users {
	private $app;

	public function __construct($app) {
		$this->app = $app;
	}

	public function __destruct() {}

	public function authenticate($email,$password) {
		$auth_statement = $this->app['db']->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
		$auth_statement->execute(array(
			":email" => $email,
			":password" => md5($password),
		));
		return $auth_statement->rowCount()>0;
	}
}

