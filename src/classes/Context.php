<?php
require_once(dirname(__FILE__)."/../includes/functions.php");

abstract class Context{
    protected $id;
    protected $author;
    protected $attachments;

    public function __construct($conn, $id, $author){
        $this->id = $id;
        $this->author = $author;
		$this->setAttachments($conn);
    }

	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}

		return null;
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
		return $this;
	}

	public function getAttachments() {
		return $this->attachments;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getAuthor() {
		return $this->author;
	}
	
	public function getAttachmentsTableHtml($att_folder) {
		?>
		<table cellpadding="3">
		  <caption><h2 data-trans-key="context-edit-page"></h2></caption>
			  <tr>
				<th width="5%" data-trans-key="context-edit-page"></th>
				<th width="30%" data-trans-key="context-edit-page"></th>
				<th width="60%" data-trans-key="context-edit-page"></th>
				<th width="5%" data-trans-key="context-edit-page"></th>
		  </tr>
		  <?php
		  if (is_array($this->attachments)) {
			foreach ($this->attachments as $attachment) {
				$odkaz = "";
				$ikona = "";
				$checkbox = "";
				if ($attachment instanceof Image) {
					$odkaz = "attachments/$att_folder/".$attachment->getContext_id()."/images/big/".$attachment->getId().".".pathinfo($attachment->getName(), PATHINFO_EXTENSION);
					$ikona = Image::getIcon();
					$checkbox = "image;".$attachment->getId();
				}				
				else if ($attachment instanceof Program) {
					$odkaz = "attachments/$att_folder/".$attachment->getContext_id()."/programs/".$attachment->getId().".".pathinfo($attachment->getName(), PATHINFO_EXTENSION);
					$ikona = Program::getIcon();
					$checkbox = "program;".$attachment->getId();
				}
				else {
					$odkaz = "http://www.youtube.com/embed/".$attachment->getName();
					$ikona = Video::getIcon();
					$checkbox = "video;".$attachment->getId();
				}
				$odkaz = "<a href=".$odkaz.' target="_blank">'.$odkaz;
				echo "<tr>";
				echo "<td width=\"5%\" align=\"center\"> <img src=".$ikona."></td>";
				echo "<td width=\"30%\">".$attachment->getName()."</td>";
				echo "<td width=\"60%\"> ".$odkaz." </td>";				
				echo '<td width=\"5%\" align="center"><input name="checkbox[]" type="checkbox" id="checkbox[]" value="'.$checkbox.'"></td>';
				echo "</tr>";
			}
		  }
		  
		  ?>
		</table>
		<?php		
	}
	
	public function setAttachments($conn) {
		$this->attachments = array();
		$sql_get_images = "SELECT * FROM images WHERE context_id = ".$this->id;
		$images = mysqli_query($conn,$sql_get_images);	
		if ($images != false) {
			while($images_pole = mysqli_fetch_array($images)) {
				$this->attachments[] = new Image($images_pole['image_id'],
												   $this->id,
												   $images_pole['original_name']
												  );
			}
		}
		$sql_get_programs = "SELECT * FROM programs WHERE context_id = ".$this->id;
		$programs = mysqli_query($conn,$sql_get_programs);	
		if ($programs != false) {			
			while($programs_pole = mysqli_fetch_array($programs)) {
				$this->attachments[] = new Program($programs_pole['program_id'],
													  $this->id,
													  $programs_pole['original_name']
													 );
			}
		}
		$sql_get_videos = "SELECT * FROM videos WHERE context_id = ".$this->id;
		$videos = mysqli_query($conn,$sql_get_videos);	
		if ($videos != false) {
			while($videos_pole = mysqli_fetch_array($videos)) {
				$this->attachments[] = new Video($videos_pole['video_id'],
												   $this->id,
												   $videos_pole['link']
												  );
			}
		}
	}
	
	public function uploadFiles1($conn, $files, $kde) {
		$fileCount = count($files["name"]);
		if ($fileCount + count($this->attachments) > 100) {
			echoError("err-too-many-attachments");
			return;
		}
		for ($i = 0; $i < $fileCount; $i++) {
			$subor = $files["name"][$i];
			$ext = pathinfo($subor, PATHINFO_EXTENSION);
			if (checkUploadFile($ext,$files["size"][$i]))
			{
				$typ = "program";
				if (isSupportedImageFormat($ext)) {
					$typ = "image";
				}
				if (mysqli_query($conn,"INSERT INTO ".$typ."s (context_id, original_name) VALUES (".$this->id.",\"".$subor."\")")) {
					$new_name = mysqli_insert_id($conn).".".$ext;
					if ($typ == "image") {
						$target_file = $kde.$typ."s/big/".$new_name;
						$target_file2 = $kde.$typ."s/small/".$new_name;
						if (!file_exists($kde.$typ."s/big")) {
							mkdir($kde.$typ."s/big", 0777, true);
						}
						if (!file_exists($kde.$typ."s/small")) {
							mkdir($kde.$typ."s/small", 0777, true);
						}
					}
					else {
						$target_file = $kde.$typ."s/".$new_name;
						if (!file_exists($kde.$typ."s")) {
							mkdir($kde.$typ."s", 0777, true);
						}
					}					
					if (move_uploaded_file($files["tmp_name"][$i], $target_file)) {
						if ($typ == "image") {
							createThumbnail($new_name, 250, 250, $kde.$typ."s/big", $kde.$typ."s/small/");
						}
						echoMessage("m-file-uploaded", $subor);
					} else {
						mysqli_query($conn,"DELETE FROM ".$typ."s WHERE ".$typ."_id = ".mysqli_insert_id($conn));
						echoError("err-file-upload", $subor);
					}				
				} else {
					echoError("err-file-upload-db", $subor.": ".mysqli_error($conn));
				}		
			} else {
				echoError("err-file-too-big", $subor);
			}
		}
	}
	
	public function uploadVideo($conn, $videa) {
		mysqli_query($conn,"DELETE FROM videos WHERE location_id = ".$this->id);
		$pattern = '/[;," "\n]/';
		$pole = preg_split($pattern, $videa);
		for ($i = 0; $i < count($pole); $i++) {
			if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $pole[$i], $match)) {
				$video = $match[1];
				if (mysqli_query($conn,"INSERT INTO videos (context_id,link) VALUES (".$this->id.",\"".$video."\")")) {
					echoMessage("m-video-uploaded", $video);
				} else {
					echoError("err-video-upload", $video.": ".mysqli_error($conn));
				}
			}		
		}
	}
	
	public function deleteAttachments1($conn, $prilohy, $kde) {
		foreach($prilohy as $value) {
			$pole = explode(";", $value);
			$sql = "SELECT * FROM ".$pole[0]."s WHERE ".$pole[0]."_id='".$pole[1]."'";
			$result = mysqli_query($conn, $sql);
			if ($result) {
				if ($pole[0] != "video") {
					$attachment = mysqli_fetch_array($result);
					$ext = pathinfo($attachment['original_name'], PATHINFO_EXTENSION);
				}
				$sql = "DELETE FROM ".$pole[0]."s WHERE ".$pole[0]."_id='".$pole[1]."'";
				$result = mysqli_query($conn, $sql);
				if ($result) {
					if ($pole[0] != "video") {
						if ($pole[0] == "image") {
							if (unlink($kde.$pole[0]."s/big/".$pole[1].".".$ext) && unlink($kde.$pole[0]."s/small/".$pole[1].".".$ext)) {
								echoMessage("m-attachment-deleted");
							}
							else {
								echoError("err-attachment-deletion");
							}
						}
						else {
							if (unlink($kde.$pole[0]."s/".$pole[1].".".$ext)) {
								echoMessage("m-attachment-deleted");
							}
							else {
								echoError("err-attachment-deletion");
							}
						}
					}					
				}
				else {
					echoMessage("m-attachment-deleted");
				}
			}
			else {
				echoError("err-attachment-db-deletion", mysqli_error($conn));
			}
		}
		/*else {
			echoError("err-attachment-not-in-db");
			}
		}*/
		
	}
}
?>
