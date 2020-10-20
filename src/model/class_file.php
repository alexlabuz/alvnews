<?php
class File{
	private $link;

	public function __construct($link){
		$this->link = $link;
	}

	public function save($data, $name, $sizeMax, $extentionList){
		if($sizeMax == null){
			$sizeMax = 8000000;
		}
		if($extentionList == null){
			$extentionList = ["jpg", "JPG", "png", "PNG", "jpeg", "JPEG", "webm"];
		}
		$file = array();
		$file["name"] = null;
		$file["errorMessage"] = "Erreur lors de l'envoie du fichier";

		// Vérifie si le fichier éxiste
		if(isset($_FILES[$data])){

			// Vérifie si le fichier a un nom/extension
			if(!empty($_FILES[$data]["name"])){

				// Récupère l'extension du fichier
				$explode = explode(".", $_FILES[$data]["name"]);
				$ext = $explode[count($explode)-1];
				$name = $name.".".$ext;

				// Vérifie si l'extension du fichier est autorisé
				if(in_array($ext, $extentionList)){

					// Vérifie la taille du fichier
					if(filesize($_FILES[$data]["tmp_name"]) <= $sizeMax){

						// Envoie du fichier
						$success = move_uploaded_file($_FILES[$data]["tmp_name"], $this->link . "/". $name);
						
						// Vérifie si l'envoie à réussi
						if($success){
							$file["name"] = $name;
							$file["errorMessage"] = null;
						}

					}else{
						$file["errorMessage"] = "Le fichier doit faire moins de " . ($sizeMax / 1000000) . " Mo";
					}

				}else{
					$file["errorMessage"] = "L'extention du fichier (" . $ext . ") est incorrect";
				}

			}

		}

		return $file;

	}

	/**
	 * Supprime un fichier
	 * @param name Nom de fichier à supprimer
	 * @return bool réussite de supression
	 */
	public function remove($name){
		if(file_exists($this->link . "/" . $name)){
			return unlink($this->link . "/" . $name); 
		}
	}

}