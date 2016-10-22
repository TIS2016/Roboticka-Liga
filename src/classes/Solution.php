<?php
require_once(dirname(__FILE__)."/../includes/functions.php");

class Solution extends Context {
    private $text;
    private $best;
	private $points;
	private $comments;
	private $assignment;	
	public function __construct($conn, $id, $author, $assignment) {
		parent::__construct($conn, $id, $author);
		$sql_get_solution = "SELECT * FROM solutions WHERE context_id = ".$id;
		$solution = mysqli_query($conn,$sql_get_solution);
		if ($solution != false) {
			$solution_pole = mysqli_fetch_array($solution);

			$this->text = $solution_pole['text'];
			$this->best = $solution_pole['best'];
			$this->assignment = $assignment;
			$this->id=$id;
			$this->author=$author;

			if (is_null($this->assignment)){
        		$selectAssignmentId = "SELECT assignment_id FROM solutions WHERE context_id = {$this->id}";
				if ($result = mysqli_query($conn, $selectAssignmentId))
					if ($row = mysqli_fetch_array($result))
						$this->assignment = new Assignment($conn, $row['assignment_id']);
			}
		
			$sql_get_comment = "SELECT * FROM comments WHERE solution_id = ".$id;
			$comment = mysqli_query($conn,$sql_get_comment);
			if ($comment != false) {
				$comments = array();
				while($comments_pole = mysqli_fetch_array($comment)) {
					$comments[] = new Comment($conn,
												 $comments_pole['comment_id'],
												 $this->id,
												 $comments_pole['user_id'],
												 $comments_pole['text'],
												 $comments_pole['points']
												);
				}
				$this->setComments($comments);
			}

		}
    }
	
	public static function getFromDatabaseByID($conn, $id){
		$sql_get_solution = "SELECT c.user_id AS 'user_id', s.assignment_id AS 'assignment_id' FROM contexts c, solutions s WHERE c.context_id = s.context_id AND c.context_id = ".$id;
		$solution = mysqli_query($conn,$sql_get_solution);
		if ($solution != false) {
			$solution_pole = mysqli_fetch_array($solution);
			return new self($conn, $id, Team::getFromDatabaseByID($conn, $solution_pole['user_id']), new Assignment($conn, $solution_pole['assignment_id']));
		}
		return null;
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
	
	public function getTxt() {
		return $this->text;
	}
	
	public function setTxt($conn, $text) {
		$this->text = $text;
		updateData($conn, "solutions", "text", $text, "context_id", $this->getId());
	}
	
	public function setComments($comments){
		$this->comments = $comments;
		if (count($comments) == 1) {
			$this->points = $comments[0]->getPoints();
			return;
		}
		$points = 0.0;
		for ($i = 0 ; $i < count($comments) ; $i++) {
			if ($comments[$i]->getAuthor() instanceof Jury) {
				$points += $comments[$i]->getPoints();
			}
		}
		if (count($comments) != 1) {
			$this->points = $points / (count($comments) - 1);
		}
	}		
	
	public function getEditingHtml(){
	?>
	<div id="content">
		
		<form name="form1" accept-charset="utf-8" enctype="multipart/form-data" method="POST">
			<h2 data-trans-key="solution-description"></h2>
			<textarea name="textPopis" cols="60" rows="10" ><?php echo $this->getTxt() ?></textarea>
	
			<br>			
			<?php
			$this->getAttachmentsTableHtml('solutions');
			?>
			
			<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
			
			<h2 data-trans-key="solution-edit-page"></h2>
			<textarea name="textVideo" cols="60" rows="3" ></textarea>
			
			<h2 data-trans-key="solution-edit-page"></h2>
			<input type="file" name="uploadedFiles[]" multiple />
			<br>
			<br>
			<button type="submit" formaction="addSolution.php" data-trans-key="save-changes" id="upload" />
			<button type="submit" formaction="addSolution.php?action=1" data-trans-key="save-changes-view" id="uploadAndView" />
			<button type="submit" formaction="addSolution.php?action=2" data-trans-key="save-changes-end" id="uploadAndEnd" />
			
		</form>

	</div>
	<?php
	}
	
	public function uploadFiles($conn, $subory) {
		$this->uploadFiles1($conn, $subory, dirname(__FILE__)."/../attachments/solutions/".$this->id."/");
	}
	
	public function deleteAttachments($conn, $prilohy) {
		$this->deleteAttachments1($conn, $prilohy, dirname(__FILE__)."/../attachments/solutions/".$this->id."/");
	}
	
	public function getPreviewHtml(){
	?>
    <h3><span data-trans-key="team-name"></span>: <?php echo $this->author->getName(); ?></h3>
    <p><?php echo $this->author->description; ?></p>
    <h3 data-trans-lang="<?php echo SK?>">Riešenie úlohy: <?php echo $this->assignment->getSkName();?></h3>
    <h3 data-trans-lang="<?php echo ENG?>">Solution of assignment:
		<?php
		echo is_null($this->assignment->getEngName()) ? $this->assignment->getSkName() : $this->assignment->getEngName();
		?>
    </h3>
    <p><?php echo $this->text; ?></p>
	
	
<?php
	}
	
	public function getCommentEditingHtml() {
		$conn = db_connect();
		if ($conn == false) return;
		$user = $_SESSION['loggedUser'];
		for ($i = 0 ; $i < count($this->comments) ; $i++) {
			if ($this->comments[$i]->getAuthor()->getId() == $user->getId()) {
				if (is_a($_SESSION['loggedUser'], 'Administrator')) $this->comments[$i]->setPoints($conn, $this->points);
				pridaj_hodnotenie($this->comments, $i);
				return;
			}
		}
		$comment_id = new_comment($conn, $this->id, $user->getId());
		
		$this->comments[] = Comment::getFromDatabaseByID($conn, $comment_id);
		if (is_a($_SESSION['loggedUser'], 'Administrator')) $this->comments[count($this->comments) - 1]->setPoints($conn, $this->points);
		pridaj_hodnotenie($this->comments, count($this->comments) - 1);
	}
	
	public function getComments(){
		return $this->comments;
	}

	public function getTeam(){
   return $this->author;
  }
  
  public function getPoints(){
    return $this->points;
  }
  
  public function getId(){
    return $this->id;
  }

  public function getAssignment(){
  	return $this->assignment;
  }
}
?>
