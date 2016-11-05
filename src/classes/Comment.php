<?php
require_once(dirname(__FILE__)."/../includes/functions.php");

class Comment {
    private $id;
    private $author;
	private $text;
	private $solution;
	private $points;

    public function __construct($conn, $id, $solution, $author, $text, $points){
		$this->id = $id;
		$user = mysqli_query($conn, "SELECT u.user_id AS 'id', u.mail AS 'mail', o.admin AS 'admin', o.validated AS 'valid' FROM organisators o, users u WHERE u.user_id = ".$author." AND u.user_id = o.user_id");
		if ($user != false) {
			$author_pole = mysqli_fetch_array($user);
			if ($author_pole['admin'] == 1) {
				$this->author = new Administrator($author, $author_pole['mail']);
			}
			else {
				$this->author = new Jury($author, $author_pole['mail'], $author_pole['valid']);
			}				
			$this->text = $text;
			$this->solution = $solution;
			$this->points = $points;
		}
    }
	
	public static function getFromDatabaseByID($conn, $id){
		$sql_get_comment = "SELECT * FROM comments WHERE comment_id = ".$id;
		$comment = mysqli_query($conn,$sql_get_comment);
		if ($comment != false) {
			$comment_pole = mysqli_fetch_array($comment);
			return new self($conn, $id, $comment_pole['solution_id'],$comment_pole['user_id'],$comment_pole['text'],$comment_pole['points']);
		}
		return null;
    }
	
	public function getAuthor() {
		return $this->author;
	}
	
	public function getTxt() {
		return $this->text;
	}
	
	public function getPoints() {
		return $this->points;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getSolution() {
		return $this->solution;
	}
	
	public function setPoints($conn, $points) {
		if (mysqli_query($conn, "UPDATE comments SET points = ".$points." WHERE comment_id = ".$this->id)) {
			$this->points = $points;
		}		
	}
	
	public function setTxt($conn, $text) {
		if (mysqli_query($conn, "UPDATE comments SET text = \"".$text."\" WHERE comment_id = ".$this->id)) {
			$this->text = $text;
		}		
	}
	
	public function getEditingHtml() {
		?>
		<tr>
			<td> <textarea name="commentText" cols="40" rows="4" ><?php echo $this->text ?> </textarea> </td>
			<?php
			if (is_a($_SESSION['loggedUser'], 'Administrator')) {
				?> <td> <?php echo $this->points; ?> </td> <?php
			}
			else {
				?> <td> <input type="number" name="commentPoints" min="0" max="3" step="0.1" value="<?php echo $this->points ?>"> </td> <?php
			}
			?>
		</tr>
		<?php
	}

	public function getTableHtml() {
		?>
		<tr>
			<td> <?php echo $this->text; ?> </td>
			<td> <?php echo $this->points . '&nbsp;(' . $this->author->id . ')'; ?> </td>
		</tr>
		<?php
	}
  
  // Nova funkcia ktora rata priemer bodov 
	/*public function ResultPoints()
	{
		$point = 0;
		$pom = 0;
		if ($link = db_connect()) {
        	$sql="SELECT * FROM comments WHERE solution_id = ".$_GET['id']; // definuj dopyt
    		$result = mysqli_query($link, $sql); // vykonaj dopyt
    		if ($result) 
    		{
    			while ($row = mysqli_fetch_assoc($result)) 
    			{
    				if($row['user_id'] != 1 && $row['points'] != null)
    				{
    					$point += $row['points'];
    					$pom++;    					
    				}
    			}
    		}
    	}
    	return round ($point/$pom, 3);
	}   */
	/* Koniec funkcie. problem je ale pri tejto funkcii ten ze musi hodnotit kazdy rozhodca pretoze ak rohodca nehodnoti neberie sa v uvahu null a tym padom sa rata priemer len z tych bodov co maju cize ak hodnoti jeden rozhodca tak je to aj finalne hodnotenie.*/

	public function getPreviewHtml() {
		?>
		<?php echo $this->text; ?><br><br>
		<span data-trans-key="points"></span>: <strong><?php echo $this->points; ?></strong>
		<?php
	}
  // zmena oproti originalu   $this->points   som nahradil    $this->ResultPoints()
}
?>
