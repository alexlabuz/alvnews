<?php
class Upload{
	private $nom;
	private $extension;
	private $chemin;
	private $taille;

	public function __construct($nom, $chemin, $taille, $extensions){
		$this->nom = $nom;
		$this->chemin = $chemin;
		$this->taille = 8000000;
		$this->extension = ["jpg", "JPG", "png", "PNG", "jpeg", "JPEG"];
		if($taille != null){
			$this->taille = $taille;
		}
		if($extensions != null){
			$this->extension = $extensions;
		}
	}

	public function enregistrer($data){
		$fichier = array();
		$fichier["code"] = null;
		$fichier["error"] = true;

		// Vérifie si le fichier éxiste
		if(isset($_FILES[$data])){

			// Vérifie si le fichier a un nom/extension
			if(!empty($_FILES[$data]["name"])){

				// Récupère l'extension du fichier
				$explode = explode(".", $_FILES[$data]["name"]);
				$ext = $explode[count($explode)-1];
				$this->nom = $this->nom.".".$ext;

				// Vérifie si l'extension du fichier est autorisé
				if(in_array($ext, $this->extension)){

					// Vérifie la taille du fichier
					if(filesize($_FILES[$data]["tmp_name"]) <= $this->taille){
						// Envoie du fichier
						$success = move_uploaded_file($_FILES[$data]["tmp_name"], $this->chemin . "/". $this->nom);
						
						// Vérifie si l'envoie à réussi
						if($success){
							$fichier["nom"] = $this->nom;
							$fichier["error"] = false;
						}
					}

				}

			}

		}

		return $fichier;
	}

}