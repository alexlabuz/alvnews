<?php

class User{
	private $db;
	private $insert;
	private $connect;
	private $selectById;
	private $update;
	private $updateMdp;
	private $delete;
	private $select;
	private $selectCount;
	private $activeProfil;
	private $updateIdGenere;

	public function __construct($db){
		$this->db = $db;

		$this->insert = $this->db->prepare(
			"INSERT INTO utilisateur(email, nom, mdp, dateInscription, idGenere)
			VALUE (:email , :nom , :mdp , NOW(), :idGenere)");

		$this->connect = $this->db->prepare("SELECT * FROM utilisateur WHERE email = :email");

		$this->selectById = $this->db->prepare("SELECT * FROM utilisateur WHERE id = :id");

		$this->update = $this->db->prepare(
			"UPDATE utilisateur 
			SET email=:email, nom=:nom, image=:image, role=:role
			WHERE id = :id");

			
		$this->updateMdp = $this->db->prepare("UPDATE utilisateur SET mdp = :mdp WHERE id = :id");

		$this->delete = $this->db->prepare("DELETE FROM utilisateur WHERE id = :id");

		$this->select = $this->db->prepare("SELECT * FROM utilisateur ORDER BY nom LIMIT :min, :max");

		$this->selectCount = $this->db->prepare("SELECT count(*) AS nombre FROM utilisateur");

		$this->activeProfil = $this->db->prepare("UPDATE utilisateur SET valide = 1 WHERE id = :id");

		$this->updateIdGenere = $this->db->prepare("UPDATE utilisateur SET idGenere = :idGenere WHERE id = :id");
	}

	public function insert($email, $nom, $mdp, $idGenere){
		$r = true;

		$this->insert->execute(array(":email"=>$email,":nom"=>$nom,":mdp"=>$mdp, ":idGenere"=>$idGenere));

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

	public function update($email, $nom, $image, $role, $id){
		$r = true;

		$this->update->execute(array(":email"=>$email,":nom"=>$nom,":role"=>$role,":image"=>$image,":id"=>$id));

		if($this->update->errorCode()!=0){
			print_r($this->update->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function updateMdp($mdp, $id){
		$r = true;

		$this->updateMdp->execute(array(":mdp"=>$mdp,":id"=>$id));

		if($this->updateMdp->errorCode()!=0){
			print_r($this->updateMdp->errorInfo());
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

	public function select($min, $max){
		$this->select->bindParaM(":min", $min, PDO::PARAM_INT);
		$this->select->bindParaM(":max", $max, PDO::PARAM_INT);

		$this->select->execute();
		
		if($this->select->errorCode()!=0){
			print_r($this->select->errorInfo());
		}

		return $this->select->fetchAll();
	}
	
	public function selectCount(){
		$this->selectCount->execute();
		
		if($this->selectCount->errorCode()!=0){
			print_r($this->selectCount->errorInfo());
		}

		return $this->selectCount->fetch();
	}
	
	public function activeProfil($id){
		$r = true;

		$this->activeProfil->execute(array(":id"=>$id));

		if($this->activeProfil->errorCode()!=0){
			print_r($this->activeProfil->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function updateIdGenere($idGenere, $idUser){
		$r = true;

		$this->updateIdGenere->execute(array(":idGenere" => $idGenere, ":id"=>$idUser));

		if($this->updateIdGenere->errorCode()!=0){
			print_r($this->updateIdGenere->errorInfo());
			$r = false;
		}

		return $r;
	}
}