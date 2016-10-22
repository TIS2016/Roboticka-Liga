<?php
require_once(dirname(__FILE__)."/../includes/functions.php");

class Assignment extends Context {
    private $name_sk;
    private $name_eng;
	private $text_sk;
	private $text_eng;
	private $timeOfPublishing;
	private $deadline;
	private $solutions;
	private $text_id_name;
	private $text_id_desc;

    public function __construct($conn, $id) {
		$sql_get_assignment = "SELECT * FROM assignments a, contexts c WHERE c.context_id = a.context_id AND c.context_id = ".$id;
		$assignment = mysqli_query($conn,$sql_get_assignment);
		if ($assignment != false && mysqli_num_rows($assignment) != 0) {
			$assignment_pole = mysqli_fetch_array($assignment);
			parent::__construct($conn, $assignment_pole['context_id'], Organisator::getFromDatabaseByID($conn, $assignment_pole['user_id']));
			
			$this->timeOfPublishing = $assignment_pole['begin'];
			$this->deadline 		= $assignment_pole['end'];
			
			$this->text_id_name = $assignment_pole['text_id_name'];
			$this->text_id_desc = $assignment_pole['text_id_description'];
			
			$sql_get_text = "SELECT * FROM texts WHERE text_id = ".$this->text_id_name;
			$text = mysqli_query($conn,$sql_get_text);
			if ($text != false) {
				$text_pole = mysqli_fetch_array($text);
				$this->name_sk 	= $text_pole['sk'];
				$this->name_eng = $text_pole['eng'];
			}
			
			$sql_get_text = "SELECT * FROM texts WHERE text_id = ".$this->text_id_desc;
			$text = mysqli_query($conn,$sql_get_text);
			if ($text != false) {
				$text_pole = mysqli_fetch_array($text);				
				$this->text_sk 	= $text_pole['sk'];
				$this->text_eng = $text_pole['eng'];
			}
			$this->setSolutions($conn);
		}
		else {
			$this->name_sk 	= "Nové zadanie";
			$this->name_eng = "New assignment";
		}
    }
	
	public function setSolutions($conn) {
		$this->solutions = array(); // TODO
		$sql_get_solutions = "SELECT c.user_id as 'user_id', c.context_id as 'context_id', s.best as 'best_id'  FROM solutions s, contexts c WHERE c.context_id = s.context_id AND s.assignment_id = ".$this->id;  
		$solutions = mysqli_query($conn,$sql_get_solutions);
		if ($solutions != false) {
		    while ($solutions_row = mysqli_fetch_assoc($solutions)) {	      
				 array_push($this->solutions,new Solution($conn, $solutions_row['context_id'], Team::getFromDatabaseByID($conn, $solutions_row['user_id']), $this));
      } 
		
		}
		
	}
	
	public function uploadFiles($conn, $subory) {
		$this->uploadFiles1($conn, $subory, dirname(__FILE__)."/../attachments/assignments/".$this->id."/");
	}
	
	public function deleteAttachments($conn, $prilohy) {
		$this->deleteAttachments1($conn, $prilohy, dirname(__FILE__)."/../attachments/assignments/".$this->id."/");
	}
	
	public function getEditingHtml($new = false){
	if ($new) {
		$link = "newAssignment.php?";
	}
	else {
		$link = "addAssignment.php?id=".$this->id."&";
	}
	?>
	<div id="content">
		
		<form name="form1" accept-charset="utf-8" enctype="multipart/form-data" method="POST" action=<?php echo $link;?> >					
			<h2><span data-trans-key="assignment-name"></span> (SK) </h2>
			<input type="text" name="skName" value="<?php echo $this->getSkName() ?>">
			<h2><span data-trans-key="assignment-name"></span> (ENG) </h2>
			<input type="text" name="engName" value="<?php echo $this->getEngName() ?>">
			<h2><span data-trans-key="assignment-description"></span> (SK)</h2>
			<textarea name="skTextPopis" cols="60" rows="10" ><?php echo $this->getSkTxt() ?></textarea>
			<h2><span data-trans-key="assignment-description"></span> (ENG)</h2>
			<textarea name="engTextPopis" cols="60" rows="10" ><?php echo $this->getEngTxt() ?></textarea>
	
			<br>			
			<?php
			$this->getAttachmentsTableHtml('assignments');
			?>
			
			<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
			
			<h2 data-trans-key="solution-edit-page"></h2>
			<textarea name="textVideo" cols="60" rows="3" ></textarea>
			
			<h2 data-trans-key="solution-edit-page"></h2>
			<input type="file" name="uploadedFiles[]" multiple />
			<br>
			<br>
			<button type="submit" formaction="<?php echo $link; ?>" data-trans-key="save-changes" id="upload" />
			<button type="submit" formaction="<?php echo $link; ?>action=1" data-trans-key="save-changes-view" id="uploadAndView" />
			<button type="submit" formaction="<?php echo $link; ?>action=2" data-trans-key="save-changes-end" id="uploadAndEnd" />
			
		</form>

	</div>
	<?php
	}
	
	public function getSkName() {
		return $this->name_sk;
	}
	
	public function setSkName($conn, $text) {
		$this->name_sk = $text;
		updateData($conn, "texts", "sk", $text, "text_id", $this->text_id_name);
	}
	
	public function getEngName() {
		return $this->name_eng;
	}
	
	public function setEngName($conn, $text) {
		$this->name_eng = $text;
		updateData($conn, "texts", "eng", $text, "text_id", $this->text_id_name);
	}
	
	public function getSkTxt() {
		return $this->text_sk;
	}
	
	public function setSkTxt($conn, $text) {
		$this->text_sk = $text;
		updateData($conn, "texts", "sk", $text, "text_id", $this->text_id_desc);
	}
	
	public function getEngTxt() {
		return $this->text_eng;
	}
	
	public function setEngTxt($conn, $text) {
		$this->text_eng = $text;
		updateData($conn, "texts", "eng", $text, "text_id", $this->text_id_desc);
	}
	
	public function getPreviewHtml(){
		?>
		<?php
		$name_sk = $this->name_sk;
		$name_eng = is_null($this->name_eng) ? $name_sk : $this->name_eng;
		$text_sk = $this->text_sk;
		$text_eng = is_null($this->text_eng) ? $text_sk : $this->text_eng;
		?>
		<h2 data-trans-lang="<?php echo SK?>"> <?php echo $name_sk?> </h2>
		<h2 data-trans-lang="<?php echo ENG?>"> <?php echo $name_eng?> </h2>
		<h3><span data-trans-key="assignment-page"></span> <?php  echo $this->deadline;?></h3>
		<div data-trans-lang="<?php echo SK?>"> <?php echo $text_sk?> </div>
		<div data-trans-lang="<?php echo ENG?>"> <?php echo $text_eng?> </div>
		<br>
		<br>
		<?php
		if(Date("Y-m-d H:i:s") < $this->deadline && isset($_SESSION['loggedUser']) && is_a($_SESSION['loggedUser'], 'Team')){
			$idecko = getSolutionId($_SESSION['loggedUser']->getId(), $this->id);
			if ($idecko == 0) {
				?> <a href="addSolution.php" data-trans-key="add-solution"></a> <?php           
			}
			else {
				?>
				<a href="addSolution.php" data-trans-key="edit-solution"></a>
				<br>
				<br>
				<a href="solution.php?id=<?php echo $idecko; ?>" data-trans-key="view-solution"></a>
				<?php           
			}			
		}
		else if (isset($_SESSION['loggedUser']) && is_a($_SESSION['loggedUser'], 'Administrator')){
			?> <table> <?php
			if($link = db_connect()){
				?>
				<tr>
				<th></th>
				<?php
				$sql = "SELECT * FROM users as s INNER JOIN organisators as o on (o.user_id=s.user_id) WHERE o.admin=0 AND o.validated = 1 ORDER BY s.user_id";
				$result = mysqli_query($link,$sql);
				?>
				<br>
				<a href="bestSolution.php?id=<?php echo $this->id ?>" data-trans-key="select-best-solution"></a>
        <br><br> 
				<?php
				if($result!=false){
					$pocet=1;
					$rozhodcovia = array();
					while ($row = mysqli_fetch_assoc($result)) { 
						?>            
						<th><span data-trans-key="jury"></span> 
						<?php
						echo " " . $pocet . "<br>" . substr($row['mail'],0,10) . "</th>";
						array_push($rozhodcovia, $row['user_id']);
						$pocet++;
					} 
				}
				
				?> </tr> <?php
				for($i=0;$i<count($this->solutions);$i++){
				?>
				<tr>
					<th><a href="solution.php?id=<?php echo $this->solutions[$i]->getId(); ?>"> <?php echo $this->solutions[$i]->getTeam()->getName(); ?> </a></th>
					<?php
					for($j=0;$j<count($rozhodcovia);$j++){
						$sql = "SELECT * FROM comments c WHERE c.solution_id=".$this->solutions[$i]->getId()." AND c.user_id=".$rozhodcovia[$j];
						$result = mysqli_query($link,$sql);
						if($result!=false){
							$arrayResult = mysqli_fetch_array($result);
						  if($arrayResult!=null && $arrayResult['text']!=null && $arrayResult['points']!=null ){
  							?> <td data-trans-key="finished"></td> <?php
  						}
  						else {
  							?> <td data-trans-key="not-rated"></td> <?php
  						}
  					}
					}
			
				?> </tr> <?php              
				}
			}
			?> </table> <?php    
		}
		else if (Date("Y-m-d H:i:s") > $this->deadline) {
			?>
			<h3><span data-trans-key="solutions"></span>:</h3>
			<ul>
			<?php
			for($i=0;$i<count($this->solutions);$i++){ 
			
				$team = $this->solutions[$i];
				$team2= $team->getTeam();
				$team3 = $team2->getName();
				?>
				<li><a href="solution.php?id=<?php echo $team->getId(); ?>"> <?php echo $team3; ?> </a> </li> 
				<?php                  
			}
			?> </ul> <?php
		}  
	}


	public function getBestSolutionSlovak(){
		if (Date("Y-m-d H:i:s") > $this->deadline) {
			?>
			<h2 data-trans-lang="<?php echo SK?>"> <?php echo $name_sk?> </h2>
		  <h2 data-trans-lang="<?php echo ENG?>"> <?php echo $name_eng?> </h2>
			<h3><span data-trans-key="solutions"></span>:</h3>
			<form id="form1" name="form1" method="post" action="">
			<table>
			<?php
			if($link = db_connect()){
			$sql = "SELECT c.user_id as 'user_id', c.context_id as 'context_id', s.best as 'best',  t.sk_league AS  'liga'  FROM solutions s, contexts c,  teams t WHERE c.context_id = s.context_id AND c.user_id = t.user_id  AND  s.assignment_id = ".$this->id;
			$result = mysqli_query($link,$sql);
			}
			?><h3><span data-trans-key="sk-league"></span></h3><?php
			for($i=0;$i<count($this->solutions);$i++){ 
				$best = mysqli_fetch_assoc($result);
				$team = $this->solutions[$i];
				$team2= $team->getTeam();
				$team3 = $team2->getName();
				$liga = "{$best['liga']}";
				$best = "{$best['best']}";
				
				if($liga =='1'){
				?>
				<tr>
				<td><a href="solution.php?id=<?php echo $this->solutions[$i]->getId(); ?>"> <?php echo $team3; ?> </a> </td> 
				<?php
				
				if ($best == '1'){
					?><td><input type='radio' name='bestSlovak' value="<?php echo $this->solutions[$i]->getTeam()->getId(); ?>" checked></td></tr><?php
				}else{
					
				?>	
				<td><input type='radio' name='bestSlovak' value='<?php echo $this->solutions[$i]->getTeam()->getId(); ?>'></td>
				</tr>
				<?php 
			}
			}                 
		}
			?></table>  <?php
		}
		?>
		
		<input type="submit" name="saveSlovak" id="save" value="Save" data-trans-key="save">
		</form>
		<?php

	
	}

	public function getBestSolutionOpen(){
		if (Date("Y-m-d H:i:s") > $this->deadline) {
			?>
			<h2 data-trans-lang="<?php echo SK?>"> <?php echo $name_sk?> </h2>
		  <h2 data-trans-lang="<?php echo ENG?>"> <?php echo $name_eng?> </h2>
			<h3><span data-trans-key="solutions"></span>:</h3>
			<form id="form1" name="form1" method="post" action="">
			<table>
			<?php
			if($link = db_connect()){
			$sql = "SELECT c.user_id as 'user_id', c.context_id as 'context_id', s.best as 'best',  t.sk_league AS  'liga'  FROM solutions s, contexts c,  teams t WHERE c.context_id = s.context_id AND c.user_id = t.user_id  AND s.assignment_id = ".$this->id;
			$result = mysqli_query($link,$sql);
			}
			?><h3><span data-trans-key="open-league"></h3><?php
			for($i=0;$i<count($this->solutions);$i++){ 
				$best = mysqli_fetch_assoc($result);
				$team = $this->solutions[$i];
				$team2= $team->getTeam();
				$team3 = $team2->getName();
				$liga = "{$best['liga']}";
				$best = "{$best['best']}";
				if($liga =='0'){
				?>
				<tr>
				<td><a href="solution.php?id=<?php echo $this->solutions[$i]->getId(); ?>"> <?php echo $team3; ?> </a> </td> 
				<?php
				
				if ($best == '1'){
					?><td><input type='radio' name='bestOpen' value="<?php echo $this->solutions[$i]->getTeam()->getId(); ?>" checked></td></tr><?php
				}else{
					
				?>	
				<td><input type='radio' name='bestOpen' value='<?php echo $this->solutions[$i]->getTeam()->getId(); ?>'></td>
				</tr>
				<?php 
				}
			}                 
		}
			?></table>  <?php
		}
		?>
		
		<input type="submit" name="saveOpen" id="save" value="Save" data-trans-key="save">
		</form>
		<?php

	
	}

	public function addBestSolutionSlovak($pom){
		if($link = db_connect()){
			$sql = "UPDATE solutions as s , contexts c, teams t SET best = 0 WHERE c.context_id = s.context_id AND t.sk_league =1
AND t.user_id = c.user_id AND s.assignment_id = ".$this->id;
			$result = mysqli_query($link,$sql);
			$sql = "UPDATE solutions as s , contexts c, teams t SET best = 1 WHERE c.context_id = s.context_id AND s.assignment_id = '".$this->id."' AND t.user_id =c.user_id AND c.user_id =".$pom; 
			$result = mysqli_query($link,$sql);
			if($result){
				echoMessage('m-best-solution-selected');
				?>
				<meta http-equiv="refresh" content="1;url=bestSolution.php?id=<?php echo $this->id ?>">
				<?php 
			}
		}

	}

	public function addBestSolutionOpen($pom){
		if($link = db_connect()){
			$sql = "UPDATE solutions as s , contexts c, teams t SET best = 0 WHERE c.context_id = s.context_id AND t.sk_league =0
AND t.user_id = c.user_id AND s.assignment_id = ".$this->id;
			$result = mysqli_query($link,$sql);
			$sql = "UPDATE solutions as s , contexts c, teams t SET best = 1 WHERE c.context_id = s.context_id AND s.assignment_id = '".$this->id."' AND t.user_id =c.user_id AND c.user_id =".$pom; 
			$result = mysqli_query($link,$sql);
			if($result){
				echoMessage('m-best-solution-selected');
				?>
				<meta http-equiv="refresh" content="1;url=bestSolution.php?id=<?php echo $this->id ?>">
				<?php 
			}
		}

	}
	
	public function getSolutions(){
		return $this->solutions;
	}
	
	public function getSolution($id){
    for($i=0;$i<count($this->solutions);$i++){
      if($this->solutions[$i]->getId()==$id){
        return $this->solutions[$i]; 
      }
    }
  }
	
	public function isPublished(){
		return $this->timeOfPublishing != null;
	}
	
	public function isAfterDeadline(){
		$deadline = strtotime($this->deadline);
		$cur_time = strtotime(date("c"));
		return $deadline < $cur_time;
	}
}
?>
