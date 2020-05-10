<?php

class Comment{
	private $db;
	private $insert;
	private $selectByArticle;
	private $selectById;
	private $delete;

	public function __construct($db){
		$this->db = $db;

		$this->insert = $this->db->prepare("INSERT INTO commentaire(text, dateCreation, idArticle, idUtilisateur) VALUE(:text, NOW(), :idArticle, :idUtilisateur)");

		$this->selectByArticle = $this->db->prepare(
			"SELECT text, c.dateCreation AS dateCreation, nom, u.image AS imageUtilisateur, u.id AS idUtilisateur, c.id AS id, u.image AS imageUser
			FROM commentaire c, article a, utilisateur u
			WHERE c.idArticle = a.id
			AND c.idUtilisateur = u.id
			AND a.id = :id
			ORDER BY dateCreation DESC");
		
		$this->selectById = $this->db->prepare("SELECT * FROM commentaire WHERE id = :id");

		$this->delete = $this->db->prepare("DELETE FROM commentaire WHERE id=:id");
	}

	public function insert($text, $idArticle, $idUtilisateur){
		$r = true;
		$this->insert->execute(array(":text" => $text, ":idArticle" => $idArticle, ":idUtilisateur" => $idUtilisateur));

		if($this->insert->errorCode() != 0){
			print_r($this->insert->errorInfo());
			$r = false;
		}

		return $r;
	}
	
	public function selectByArticle($idArticle){
		$this->selectByArticle->execute(array(":id" => $idArticle));

		if($this->selectByArticle->errorCode() != 0){
			print_r($this->selectByArticle->errorInfo());
		}

		return $this->selectByArticle->fetchAll();
	}

	public function selectById($id){
		$this->selectById->execute(array(":id" => $id));

		if($this->selectById->errorCode() != 0){
			print_r($this->selectById->errorInfo());
		}

		return $this->selectById->fetch();
	}

	public function delete($idCommentaire){
		$r = true;
		$this->delete->execute(array(":id" => $idCommentaire));

		if($this->delete->errorCode() != 0){
			print_r($this->delete->errorInfo());
			$r = false;
		}

		return $r;
	}
}