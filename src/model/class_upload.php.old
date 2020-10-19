<?php
class Upload{
	private $extension;
	private $chemin;
	private $taille;

	public function __construct($extensions, $chemin, $taille){
		$this->extension = $extensions;
		$this->chemin = $chemin;
		$this->taille = $taille;
	}

	public function enregistrer($data){
		$fichier = array();
		$fichier["nom"] = null;
		$fichier["message"] = null;

		if(isset($_FILES[$data])){
			if(!empty($_FILES[$data]["name"])){
				if(!in_array(substr(strrchr($_FILES[$data]["name"], "."), 1), $this->extension)){
					$fichier["message"] = "Veuillez choisir un autre type de fichier";
				}else{
					if(file_exists($_FILES[$data]["tmp_name"]) && filesize($_FILES[$data]["tmp_name"]) > $this->taille){
						$fichier["message"] = "Votre fichier doit faire moins de ".($this->taille / 1000000)." Mo";
					}else{
						$fichier["nom"] = basename($_FILES[$data]["name"]);
						// Enlève les accents
						$fichier["nom"] = strtr($fichier["nom"], "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ", 
						"AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy");
						// Remplace les caractères autres que des lettres ou chiffres et point par _
						$fichier["nom"] = preg_replace('/([^.a-z0-9]+)/i', "_", $fichier["nom"]);
						// Copie du fichier
						move_uploaded_file($_FILES[$data]["tmp_name"], $this->chemin . "/" . $fichier["nom"]);
					}
				}
			}
			return $fichier;
		}
	}
}