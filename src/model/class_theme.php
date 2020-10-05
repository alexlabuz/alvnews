<?php

class Theme{
	private $db;
	private $insert;
	private $select;
	private $selectGroupBy;
	private $selectByName;
	private $selectById;
	private $update;
	private $delete;

	public function __construct($db){
		$this->db = $db;

		$this->insert = $this->db->prepare("INSERT INTO theme(libelle, couleur)VALUE( :libelle, :couleur)");

		$this->select = $this->db->prepare("SELECT * FROM theme ORDER BY LOWER(libelle)");

		$this->selectGroupBy = $this->db->prepare("SELECT t.id, t.libelle, t.couleur, COUNT(a.titre) AS nbArticle FROM article a, theme t WHERE a.idTheme = t.id GROUP BY t.id ORDER BY t.libelle");

		$this->selectByName = $this->db->prepare("SELECT * FROM theme WHERE libelle = :libelle");

		$this->selectById = $this->db->prepare("SELECT * FROM theme WHERE id = :id");

		$this->update = $this->db->prepare("UPDATE theme SET libelle = :libelle, couleur= :couleur WHERE id = :id");

		$this->delete = $this->db->prepare("DELETE FROM theme WHERE id= :id");
	}

	public function insert($nameType, $colorTheme){
		$r = true;

		$this->insert->execute(array(":libelle" => $nameType, ":couleur" => $colorTheme));

		if($this->insert->errorCode()!=0){
			print_r($this->insert->errorInfo());
			$r = false;
		}

		return $r;
	}
	
	public function select(){
		$this->select->execute();

		if($this->select->errorCode()!=0){
			print_r($this->select->errorInfo());
		}

		return $this->select->fetchAll();
	}

	public function selectGroupBy(){
		$this->selectGroupBy->execute();

		if($this->selectGroupBy->errorCode()!=0){
			print_r($this->selectGroupBy->errorInfo());
		}

		return $this->selectGroupBy->fetchAll();
	}

	public function selectByName($nameType){
		$this->selectByName->execute(array(":libelle" => $nameType));

		if($this->selectByName->errorCode()!=0){
			print_r($this->selectByName->errorInfo());
		}

		return $this->selectByName->fetch();
	}

	public function selectById($id){
		$this->selectById->execute(array(":id" => $id));

		if($this->selectById->errorCode()!=0){
			print_r($this->selectById->errorInfo());
		}

		return $this->selectById->fetch();
	}
	
	public function update($libelle, $couleur, $id){
		$r = true;

		$this->update->execute(array(":libelle" => $libelle, ":couleur" => $couleur, ":id" => $id));

		if($this->update->errorCode()!=0){
			print_r($this->update->errorInfo());
			$r = false;
		}

		return $r;
	}

	public function delete($id){
		$r = true;

		$this->delete->execute(array(":id" => $id));

		if($this->delete->errorCode()!=0){
			print_r($this->delete->errorInfo());
			$r = false;
		}

		return $r;
	}
	
}