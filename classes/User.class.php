<?php
//User.class.php

require_once 'DB.class.php';


class User {

	public $user_id;
	public $name;
	public $surname;
	public $login;
	public $hashedPassword;
	public $email;
	public $isadmin;
	
	//Constructor is called whenever a new object is created.
	//Takes an associative array with the DB row as an argument.
	function __construct($data) {
		$this->user_id = (isset($data['user_id'])) ? $data['user_id'] : "";
		$this->name = (isset($data['name'])) ? $data['name'] : "";
		$this->surname = (isset($data['surname'])) ? $data['surname'] : "";
		$this->login = (isset($data['login'])) ? $data['login'] : "";
		$this->hashedPassword = (isset($data['password'])) ? $data['password'] : "";
		$this->email = (isset($data['email'])) ? $data['email'] : "";
		$this->isadmin = (isset($data['isadmin'])) ? $data['isadmin'] : "";
	}

	public function save($isNewUser = false) {
		//create a new database object.
		$db = new DB();
		
		//if the user is already registered and we're
		//just updating their info.
		if(!$isNewUser) {
			//set the data array
			$data = array(
				"name" => "'$this->name'",
				"surname" => "'$this->surname'",
				"login" => "'$this->login'",
				"password" => "'$this->hashedPassword'",
				"email"=> "'$this->email'",
				"isadmin"=> "'$this->isadmin'");
			
			//update the row in the database
			$db->update($data, 'users', 'user_id = '.$this->user_id);
		}else {
			//if the user is being registered for the first time.
			$data = array(
				"name" => "'$this->name'",
				"surname" => "'$this->surname'",
				"login" => "'$this->login'",
				"password" => "'$this->hashedPassword'",
				"email"=> "'$this->email'",
				"isadmin"=> "'$this->isadmin'");
			
			$this->user_id = $db->insert($data, 'users');
		}
		return true;
	}
	
}

?>