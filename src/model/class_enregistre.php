<?php
class Enregistre{
	private $db;
	private $insert;
	private $delete;
	private $selectByUserArticle;
	private $selectByUser;

	public function __construct($db){
		$this->db = $db;

		$this->insert = $this->db->prepare("INSERT INTO enregistre(dateEnregistre, idArticle, idUtilisateur) VALUE(NOW(), :idArticle, :idUtilisateur)");

		$this->delete = $this->db->prepare("DELETE FROM enregistre WHERE id= :id");
		
		$this->selectByUserArticle = $this->db->prepare(
			"SELECT e.id AS id
			FROM enregistre e, article a, utilisateur u
			WHERE e.idArticle = a.id
			AND e.idUtilisateur = u.id
			AND u.id = :idUser
			AND a.id = :idArticle");

		$this->selectByUser = $this->db->prepare(
			"SELECT e.id AS id, dateEnregistre, titre, a.image AS image, description, a.id AS idArticle
			FROM enregistre e, article a, utilisateur u
			WHERE e.idArticle = a.id
			AND e.idUtilisateur = u.id
			AND u.id = :idUser
			ORDER BY dateEnregistre DESC");
		}

	public function insert($idArticle, $idUtilisateur){
		$r = true;

		$this->insert->execute(array(":idArticle"=>$idArticle, ":idUtilisateur"=>$idUtilisateur));

		if($this->insert->errorCode()!=0){
			print_r($this->insert->errorInfo());
		}

		return $r;
	}

	public function delete($id){
		$r = true;

		$this->delete->execute(array(":id"=>$id));

		if($this->delete->errorCode()!=0){
			print_r($this->delete->errorInfo());
		}

		return $r;
	}

	public function selectByUserArticle($idUser, $idArticle){
		$this->selectByUserArticle->execute(array(":idUser"=>$idUser,"idArticle"=>$idArticle));

		if($this->selectByUserArticle->errorCode()!=0){
			print_r($this->selectByUserArticle->errorInfo());
		}

		return $this->selectByUserArticle->fetch();
	}

	public function selectByUser($idUser){
		$this->selectByUser->execute(array(":idUser"=>$idUser));

		if($this->selectByUser->errorCode()!=0){
			print_r($this->selectByUser->errorInfo());
		}

		return $this->selectByUser->fetchAll();
	}
}