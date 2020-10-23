<?php

class ForumReponse{
	
	private $db;
	private $add;
	private $selectBySujet;

	public function __construct($db){
		$this->db = $db;

		$this->add = $this->db->prepare("INSERT INTO forum_reponse (contenu, date_creation, idUser, idSujet) VALUES (:contenu, NOW(), :idUser, :idSujet)");

		$this->selectBySujet = $this->db->prepare(
			"SELECT r.id AS id, contenu, r.date_creation AS date, idUser, u.nom AS userName, u.image AS userImage
			FROM forum_reponse r, utilisateur u
			WHERE r.idUser = u.id
			AND r.idSujet = :idSujet
			ORDER BY r.date_creation"
		);
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

	public function selectBySujet($idSujet){
		$this->selectBySujet->execute(array(":idSujet" => $idSujet));

		if($this->selectBySujet->errorCode() != 0){
			print_r($this->selectBySujet->errorInfo());
		}

		return $this->selectBySujet->fetchAll();
	}
}