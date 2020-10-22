<?php

class ForumSujet {

	private $db;
	private $ajout;
	private $selectAll;

	public function __construct($db){
		$this->db = $db;

		$this->ajout = $this->db->prepare("INSERT INTO forum_sujet(titre, contenu, date_creation, ouvert, idUser) VALUES (:titre, :contenu, NOW(), 1, :idUser)");

		$this->selectByUser = $this->db->prepare("SELECT titre, date_creation, ouvert, idUser FROM forum_sujet fs WHERE idUser = :idUser");

	}

	public function ajout($titre, $contenu, $idUser){
		$r = true;
		$this->ajout->execute(array(":titre" => $titre, ":contenu" => $contenu, ":idUser" => $idUser));

		if($this->ajout->errorCode() != 0){
			print_r($this->ajout->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function selectByUser($idUser){
		$this->selectByUser->execute(array(":idUser" => $idUser));

		if($this->selectByUser->errorCode() != 0){
			print_r($this->selectByUser->errorInfo());
		}

		return $this->selectByUser->fetchAll();
	}
	
}
