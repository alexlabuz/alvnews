<?php

class User{
	private $db;
	private $insert;
	private $connect;
	private $selectById;
	private $update;
	private $delete;

	public function __construct($db){
		$this->db = $db;


		$this->insert = $this->db->prepare(
			"INSERT INTO utilisateur(email, nom, mdp,  image, dateInscription)
			VALUE (:email , :nom , :mdp , :image , NOW())");

		$this->connect = $this->db->prepare("SELECT * FROM utilisateur WHERE email = :email");

		$this->selectById = $this->db->prepare("SELECT * FROM utilisateur WHERE id = :id");

		$this->update = $this->db->prepare(
			"UPDATE utilisateur 
			SET email=:email, nom=:nom, image=:image
			WHERE id = :id");

		$this->delete = $this->db->prepare("DELETE FROM utilisateur WHERE id = :id");
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

	public function update($email, $nom, $image, $id){
		$r = true;

		$this->update->execute(array(":email"=>$email,":nom"=>$nom,":image"=>$image,":id"=>$id));

		if($this->update->errorCode()!=0){
			print_r($this->update->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function delete($id){
		$r = true;

		$this->delete->execute(array(":id"=>$id));

		if($this->delete->errorCode()!=0){
			print_r($this->delete->errorInfo());
			$r = false;
		}

		return $r;
	}
}