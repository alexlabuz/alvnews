<?php

class ForumSujet {

	private $db;
	private $add;
	private $select;
	private $selectByUser;
	private $selectById;
	private $selectCount;
	private $selectByReponseUser;
	private $delete;
	private $updateOuvert;

	public function __construct($db){
		$this->db = $db;

		$this->add = $this->db->prepare("INSERT INTO sujet(titre, contenu, date_creation, ouvert, idUser) VALUES (:titre, :contenu, NOW(), 1, :idUser)");

		$this->select = $db->prepare(
			"SELECT s.id AS id, titre, s.date_creation AS date, u.nom AS userName, idUser, 
				(SELECT COUNT(id)
				FROM reponse_sujet
				WHERE idSujet = s.id) AS nbReponse
			FROM sujet s, utilisateur u
			WHERE s.idUser = u.id
			AND s.ouvert = :ouvert
			GROUP BY s.id
			ORDER BY s.date_creation DESC
			LIMIT :min, :max"
		);

		$this->selectByUser = $this->db->prepare("SELECT id, titre, date_creation, ouvert, idUser FROM sujet fs WHERE idUser = :idUser ORDER BY date_creation DESC");

		$this->selectById = $this->db->prepare(
			"SELECT s.id AS id, titre, contenu, s.date_creation AS date, ouvert, idUser, u.nom AS userName, u.image AS userImage
			FROM sujet s, utilisateur u
			WHERE s.idUser = u.id AND s.id = :id");

		$this->selectCount = $this->db->prepare("SELECT COUNT(id) AS nombre FROM sujet WHERE ouvert = :ouvert");
	
		$this->selectByReponseUser = $this->db->prepare(
			"SELECT s.id AS id, titre, s.date_creation AS date, u.nom AS nameUser, (
				SELECT MAX(date_creation)
				FROM reponse_sujet
				WHERE idSujet = s.id) AS dateLastReponse
			FROM sujet s, reponse_sujet r, utilisateur u
			WHERE r.idSujet = s.id
			AND r.idUser = u.id
			AND u.id = :idUser
			AND s.ouvert = 1
			GROUP BY s.id
			ORDER BY s.date_creation DESC"
		);

		$this->delete = $this->db->prepare("DELETE FROM sujet WHERE id = :id");
		
		$this->updateOuvert = $this->db->prepare("UPDATE sujet SET ouvert = :ouvert WHERE id = :id");
	}

	public function add($titre, $contenu, $idUser){
		$r = true;
		$this->add->execute(array(":titre" => $titre, ":contenu" => $contenu, ":idUser" => $idUser));

		if($this->add->errorCode() != 0){
			print_r($this->add->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function select($ouvert, $min, $max){
		$this->select->bindParaM(":ouvert", $ouvert, PDO::PARAM_INT);
		$this->select->bindParaM(":min", $min, PDO::PARAM_INT);
		$this->select->bindParaM(":max", $max, PDO::PARAM_INT);

		$this->select->execute();

		if($this->select->errorCode() != 0){
			print_r($this->select->errorInfo());
		}

		return $this->select->fetchAll();
	}
	
	public function selectByUser($idUser){
		$this->selectByUser->execute(array(":idUser" => $idUser));

		if($this->selectByUser->errorCode() != 0){
			print_r($this->selectByUser->errorInfo());
		}

		return $this->selectByUser->fetchAll();
	}
	
	public function selectById($idUser){
		$this->selectById->execute(array(":id" => $idUser));

		if($this->selectById->errorCode() != 0){
			print_r($this->selectById->errorInfo());
		}

		return $this->selectById->fetch();
	}
	
	public function selectByReponseUser($idUser){
		$this->selectByReponseUser->execute(array(":idUser" => $idUser));

		if($this->selectByReponseUser->errorCode() != 0){
			print_r($this->selectByReponseUser->errorInfo());
		}

		return $this->selectByReponseUser->fetchAll();
	}

	public function selectCount($ouvert){
		$this->selectCount->execute(array(":ouvert" => $ouvert));

		if($this->selectCount->errorCode() != 0){
			print_r($this->selectCount->errorInfo());
		}

		return $this->selectCount->fetch();
	}
	
	public function delete($id){
		$r = true;
		$this->delete->execute(array(":id"=>$id));

		if($this->delete->errorCode() != 0){
			print_r($this->delete->errorInfo());
			$r = false;
		}

		return $r;
	}
	
	public function updateOuvert($ouvert, $id){
		$r = true;
		$this->updateOuvert->execute(array(":ouvert" => $ouvert,":id"=>$id));

		if($this->updateOuvert->errorCode() != 0){
			print_r($this->updateOuvert->errorInfo());
			$r = false;
		}

		return $r;
	}
}
