<?php

class Article{
	private $db;
	private $insert;
	private $update;
	private $delete;
	private $select;
	private $selectByUser;
	private $selectById;

	public function __construct($db){
		$this->db = $db;

		$this->insert = $this->db->prepare(
			"INSERT INTO article(titre, description, image, contenu, dateCreation, dateModif, visible, idTheme, idUtilisateur)
			VALUE (:titre, :description, :image, :contenu, NOW(), NOW(), :visible, :idTheme, :idUtilisateur)");
		
		$this->update = $this->db->prepare(
			"UPDATE article
			SET titre= :titre, description= :description, image= :image, contenu= :contenu, dateModif=NOW(), visible= :visible, idTheme= :idTheme 
			WHERE id = :id");

		$this->delete = $this->db->prepare("DELETE FROM article WHERE id= :id");

		$this->select = $this->db->prepare(
			"SELECT a.id AS id, titre, a.image, description, dateCreation , visible, t.libelle AS theme, t.couleur AS couleur, u.nom AS redacteur
			FROM article a, theme t , utilisateur u
			WHERE a.idTheme = t.id
			AND a.idUtilisateur = u.id
			ORDER BY dateCreation DESC
			LIMIT :min, :max");

		$this->selectByUser = $this->db->prepare(
			"SELECT titre, description, dateCreation, dateModif, a.id AS id
			FROM article a, utilisateur u
			WHERE a.idUtilisateur = u.id
			AND u.id = :id
			ORDER BY dateCreation DESC");

		$this->selectById = $this->db->prepare(
			"SELECT a.id AS id, titre, description, a.image AS image, contenu, dateCreation, dateModif, idTheme, idUtilisateur, t.libelle AS theme, u.nom AS redacteur
			FROM article a, theme t, utilisateur u
			WHERE a.idTheme = t.id 
			AND a.idUtilisateur = u.id
			AND a.id = :id");
	}

	public function insert($titre, $description, $image, $contenu, $visible, $idTheme, $idUtilisateur){
		$r = true;

		$this->insert->execute(array(
			":titre" => $titre,
			":description" => $description,
			":image" => $image,
			":contenu" => $contenu,
			":visible" => $visible,
			":idTheme" => $idTheme,
			":idUtilisateur" => $idUtilisateur
		));

		if($this->insert->errorCode() != 0){
			print_r($this->insert->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function update($titre, $description, $image, $contenu, $visible, $idTheme, $idArticle){
		$r = true;

		$this->update->execute(array(
			":titre" => $titre,
			":description" => $description,
			":image" => $image,
			":contenu" => $contenu,
			":visible" => $visible,
			":idTheme" => $idTheme,
			":id" => $idArticle
		));

		if($this->update->errorCode() != 0){
			print_r($this->insert->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function delete($id){
		$r = true;

		$this->delete->execute(array(":id" => $id));

		if($this->delete->errorCode() != 0){
			print_r($this->insert->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function select($min, $max){
		$this->select->bindParaM(":min", $min, PDO::PARAM_INT);
		$this->select->bindParaM(":max", $max, PDO::PARAM_INT);

		$this->select->execute();

		if($this->select->errorCode() != 0){
			print_r($this->select->errorInfo());
		}

		return $this->select->fetchAll();
	}

	public function selectByUser($idUser){
		$this->selectByUser->execute(array(":id" => $idUser));

		if($this->selectByUser->errorCode() != 0){
			print_r($this->selectByUser->errorInfo());
		}

		return $this->selectByUser->fetchAll();
	}

	public function selectById($idArticle){
		$this->selectById->execute(array(":id" => $idArticle));

		if($this->selectById->errorCode() != 0){
			print_r($this->selectById->errorInfo());
		}

		return $this->selectById->fetch();
	}

}