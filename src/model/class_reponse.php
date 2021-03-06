<?php

class ForumReponse{
	
	private $db;
	private $add;
	private $delete;
	private $update;
	private $selectBySujet;
	private $selectById;

	public function __construct($db){
		$this->db = $db;

		$this->add = $this->db->prepare("INSERT INTO reponse_sujet (contenu, date_creation, idUser, idSujet) VALUES (:contenu, NOW(), :idUser, :idSujet)");

		$this->delete = $this->db->prepare("DELETE FROM reponse_sujet WHERE id = :id");

		$this->update = $this->db->prepare("UPDATE reponse_sujet SET contenu = :contenu WHERE id = :id");

		$this->selectBySujet = $this->db->prepare(
			"SELECT r.id AS id, contenu, r.date_creation AS date, idUser, u.nom AS userName, u.image AS userImage
			FROM reponse_sujet r, utilisateur u
			WHERE r.idUser = u.id
			AND r.idSujet = :idSujet
			ORDER BY r.date_creation"
		);

		$this->selectById = $this->db->prepare("SELECT idUser, idSujet FROM reponse_sujet WHERE id = :id");
	}

	public function add($content, $idUser, $idSujet){
		$r = true;
		$this->add->execute(array(":contenu" => $content, ":idUser" => $idUser, ":idSujet" => $idSujet));

		if($this->add->errorCode() != 0){
			print_r($this->add->errorInfo());
			$r = false;
		}

		return $r;
	}
	
	public function delete($id){
		$r = true;
		$this->delete->execute(array(":id" => $id));

		if($this->delete->errorCode() != 0){
			print_r($this->delete->errorInfo());
			$r = false;
		}

		return $r;
	}
	
	public function update($contenu, $id){
		$r = true;
		$this->update->execute(array(":contenu" => $contenu,":id" => $id));

		if($this->update->errorCode() != 0){
			print_r($this->update->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function selectBySujet($idSujet){
		$this->selectBySujet->execute(array(":idSujet" => $idSujet));

		if($this->selectBySujet->errorCode() != 0){
			print_r($this->selectBySujet->errorInfo());
		}

		return $this->selectBySujet->fetchAll();
	}
	
	public function selectById($id){
		$this->selectById->execute(array(":id" => $id));

		if($this->selectById->errorCode() != 0){
			print_r($this->selectById->errorInfo());
		}

		return $this->selectById->fetch();
	}
}