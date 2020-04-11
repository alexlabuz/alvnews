<?php

class User{
	private $db;
	private $insert;
	private $connect;
	private $selectById;

	public function __construct($db){
		$this->db = $db;


		$this->insert = $this->db->prepare(
			"INSERT INTO utilisateur(email, nom, mdp,  image, dateInscription)
			VALUE (:email , :nom , :mdp , :image , NOW())");

		$this->connect = $this->db->prepare("SELECT * FROM utilisateur WHERE email = :email");

		$this->selectById = $this->db->prepare("SELECT * FROM utilisateur WHERE id = :id");
	}

	public function insert($email, $nom, $mdp, $image){
		$r = true;

		$this->insert->execute(array(":email"=>$email,":nom"=>$nom,":mdp"=>$mdp,":image"=>$image));

		if($this->insert->errorCode()!=0){
			print_r($this->insert->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function connect($email){
		$this->connect->execute(array(":email" => $email));
		
		if($this->connect->errorCode()!=0){
			print_r($this->connect->errorInfo());
		}

		return $this->connect->fetch();
	}

	public function selectById($id){
		$this->selectById->execute(array(":id" => $id));
		
		if($this->selectById->errorCode()!=0){
			print_r($this->selectById->errorInfo());
		}

		return $this->selectById->fetch();
	}
}