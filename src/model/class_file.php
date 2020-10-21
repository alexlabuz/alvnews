<?php
class File{
	private $link;

	public function __construct($link){
		$this->link = $link;
	}

	public function save($data, $name, $sizeMax, $extensionList){
		if($sizeMax == null){
			$sizeMax = 8000000;
		}
		if($extensionList == null){
			$extensionList = ["jpg", "JPG", "png", "PNG", "jpeg", "JPEG", "webm"];
		}
		$file = array();
		$file["name"] = null;
		$file["errorMessage"] = null;

		// Vérifie si le fichier éxiste
		if(isset($_FILES[$data]) && !empty($_FILES[$data]["name"])){
				// Récupère l'extension du fichier
				$explode = explode(".", $_FILES[$data]["name"]);
				$ext = $explode[count($explode)-1];
				$name = $name.".".$ext;

				if(!in_array($ext, $extensionList)){
					// Si l'extension du fichier est autorisé
					$file["errorMessage"] = "L'extention du fichier (" . $ext . ") est incorrect";
				}elseif(filesize($_FILES[$data]["tmp_name"]) > $sizeMax){
					// Si la taille du fichier est correct
					$file["errorMessage"] = "Le fichier doit faire moins de " . ($sizeMax / 1000000) . " Mo";
				}else{
					// Envoie du fichier
					$success = move_uploaded_file($_FILES[$data]["tmp_name"], $this->link . "/". $name);
					
					// Vérifie si l'envoie à réussi
					if($success){
						$file["name"] = $name;
					}else{
						$file["errorMessage"] = "Erreur lors de l'envoie du fichier";
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