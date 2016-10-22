<?php

define("SK", 0);
define("ENG", 1);

function __autoload($class_name) {
    include(dirname(__FILE__)."/../classes/$class_name.php");
}

function page_head($title)
{
    ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));
    session_start();
?>
<!DOCTYPE html>
<html lang="sk-SK">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="fll, lego, letna liga">
        <meta name="author" content="Chaos">
        <meta data-trans-title="<?php echo $title ?>">
        <title><?php echo $title ?></title>
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <link type="text/css" href="css/styles.css" rel="stylesheet"> 
        <link type="text/css" href="css/dropdownmenu.css" rel="stylesheet">
	<link type="text/css" href="css/stylesMin.css" rel="stylesheet" media="(max-width:530px)"> 

	<script type="text/javascript" src="js/iframeresize.js" ></script>
        <script type="text/javascript" src="js/dropdownmenu.js" ></script>
        <script type="text/javascript" src="js/translator.js" ></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    </head>

    <script>
        $(document).ready(function(){
            dict.translateElement();
        });
	
	window.onload =  function() {
            sendResizeRequest();
        };
    </script>

    <body>
    <div id="wrapper">
    <a href="."><h1 data-trans-key="main-header"></h1></a>
<?php
Image::setIcon("images/image.png");
Video::setIcon("images/video.png");
Program::setIcon("images/file.png");
}

function get_topright_form()
{
    if (!isset($_SESSION['loggedUser']))
        get_login_form();
    else
        get_logout_button();
}

function get_login_form(){
?>
    <form id="login-form" method="post" accept-charset="utf-8">
        <table>
            <tr>
                <td><p style="margin-bottom: 0; margin-top: 0; font-weight: bold; color: #3399ff;" data-trans-key="login-form"></p></td>
            </tr>
            <tr>
                <td><label for="mail" data-trans-key="login-form"></label></td>
                <td><input id="mail" type="text" placeholder="email@email.com"></td>
            </tr>
            <tr>
                <td><label for="password" data-trans-key="login-form"></label></td>
                <td><input id="password" type="password"  data-trans-key="login-form"></td>
            </tr>
            <tr>
                <td><input type="submit" name="submit" data-trans-key="login-form"></td>
                <td style="text-align: right;"><a href="registracia.php" data-trans-key="login-form"></a></td>
            </tr>
        </table>
    </form>
    <script>
        $("#login-form")[0].addEventListener("submit", function(event) {
            event.preventDefault();
            var login = $("#mail").val();
            var password = $("#password").val();
            $.ajax({cache : false,
                    async : true,
                    type: "POST",
                    data : {mail : login, password : password},
                    url : "includes/login.php"}).done(function(error) {
                if (error) {
                    dict.echoError(error, '');
                }
                else{
                    location.reload();
                }
            });
        });
    </script>

<?php
}

function zobrazHodnotenie($ries){
    if ($link = db_connect()) {
        $sql="SELECT * FROM `comments` WHERE `solution_id` = '".$ries."' and `user_id` = 1"; // definuj dopyt
    $result = mysqli_query($link, $sql); // vykonaj dopyt
    if ($result) {
        $result_pole = mysqli_fetch_array($result);
        if ($result_pole['text'] == null or $result_pole['text'] == '') {
            return false;
        }else{
            return true;
        }
            
    } else {
            // NEpodarilo sa vykonať dopyt!
        echoError('err-db-query-fail');
    }
    mysqli_close($link);
    } else {
        // NEpodarilo sa spojiť s databázovým serverom alebo vybrať databázu!
        echoError('err-db-connection-fail');
    }

}

function get_logout_button(){
    ?>
    <form id="logout-form" action="includes/logout.php">
        <span><span data-trans-key="logged-in"></span> <?php echo $_SESSION['loggedUser']->mail;?></span>
        <input type="submit" name="submit" data-trans-key="logout">
    </form>
    <?php
}

function page_nav()
{
      ?>
    	<div class="nav">
			<ul id="menu" class="menu">
				<li><span data-trans-key="assignments"></span>
					<ul>

					<?php
					if ($link = db_connect()) {
            $sql =  "SELECT a.context_id AS id FROM assignments a WHERE a.year = (SELECT max(year) FROM assignments) AND a.begin <= NOW() ORDER BY begin ASC;";
            $result = mysqli_query($link,$sql);
            $i=1;
            while ($row = mysqli_fetch_assoc($result)) {
              ?>
                <li><a href="assignment.php?id=<?php echo $row["id"] ?>"> <?php echo $i ?>. <span data-trans-key="assignment"></span></a></li>
                <?php
              $i++;
            }
          }
          ?>
						<li><a href="prehladZadani.php" <span data-trans-key="assignments-overview"></span></a></li>
					</ul>
				</li>
				<li><a href="results.php" <span data-trans-key="results"></span></a></li>
				
				
				<li><span data-trans-key="archive"></span>
					      <ul>
					   <?php
					     if($link = db_connect()){
                $sql = "SELECT a.context_id, a.year FROM assignments a WHERE a.begin <= NOW() ORDER BY a.begin ASC";
                $result = mysqli_query($link,$sql);
                $rok = 0;
                $poc = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    if($rok != $row["year"]){
                      if($rok != 0){
                        ?> 
                        </ul>
                        <?php
                      }
                      $poc=1;
                      $rok=$row["year"];
                      ?>
                      <li class="submenu">
                        <span><?php echo $row["year"] ?></span> <ul>
                        <li class="noborder"><a href="results.php?year=<?php echo $row["year"] ?>""><span data-trans-key="results"></span></a></li>
                    <?php
                    }
                   ?>
                        <li><a href="assignment.php?id=<?php echo $row["context_id"] ?>"> <?php echo $poc ?>. <span data-trans-key="assignment"></span></a></li>
                    <?php
                    $poc++;
                }
                    if ($poc != 1) {
                    ?>
                    </ul>
                    <?php
                    }
               }
					   ?>

					</ul>
				</li>
                <?php 
                if (isset($_SESSION['loggedUser'])){
                if ($_SESSION['loggedUser'] instanceof Administrator) { ?>
				            <li><a href="#" data-trans-key="users"></a>
                    <ul>
                        <li><a href="spravaUctov.php?id=0" data-trans-key="teams"></a></li>
                        <li><a href="spravaUctov.php?id=1" data-trans-key="jury-pl"></a></li>
                    </ul>
                </li>
                <?php } }?>
				<li><a href="#" data-trans-key="language"></a>
					<ul>
						<li><a href="#" onclick="dict.translateElement(dict.SK)"><img src="images/sk.png" width=33 height=22></a></li>
						<li><a href="#" onclick="dict.translateElement(dict.ENG)"><img src="images/eng.png" width=33 height=22></a></li>
					</ul>
				</li>
			</ul>
		</div>
		<script type="text/javascript">
		var dropdown=new TINY.dropdown.init("dropdown", {id:'menu', active:'menuhover'});
		</script>
        <p id="success-message"></p>
        <p id="error-message"></p>
    <?php
}

function page_footer()
{
    ?>
    </div>
    </body>
	</html>
    <?php
}

function checkUploadFile($vel)
{
	if ($vel < 10000000)
	{
		return True;
	}
	return False;
}

function isSupportedImageFormat($ext) {
	$ext = strtolower($ext);
	if ($ext == 'jpg' || $ext == 'jpeg' ||$ext == 'png' || $ext == 'gif') {
		return true;
	} 
	return false;
}

function dieWithError($key){
    echoError($key);
    die();
}

function echoError($key, $info = null){
    ?><script>dict.echoError('<?php echo $key;?>', '<?php echo $info;?>');</script>
    <?php
}

function echoMessage($key, $info = null){
    ?><script>dict.echoSuccess('<?php echo $key;?>', '<?php echo $info;?>');</script>
    <?php
}

function db_connect() {
    if ($link = @mysqli_connect('localhost', 'lltest', '******')) {
        if (@mysqli_select_db($link, 'lltest')) {
            @mysqli_query($link, "SET CHARACTER SET 'utf8'");
            return $link;
        } else {
            echoError('err-db-choice-fail');
            return false;
        }
    } else {
        echoError('err-db-connection-fail');
        return false;
    }
}

function new_solution($conn, $uid, $aid) {
	mysqli_query($conn,"INSERT INTO contexts (user_id) VALUES (".$uid.")");
	$cid = mysqli_insert_id($conn);
	mysqli_query($conn,"INSERT INTO solutions (context_id,assignment_id) VALUES (".$cid.",".$aid.")");
	return $cid;
}

function new_assignment($conn, $uid) {
	mysqli_query($conn,"INSERT INTO contexts (user_id) VALUES (".$uid.")");
	$cid = mysqli_insert_id($conn);
	mysqli_query($conn,"INSERT INTO texts () VALUES ()");
	$id1 = mysqli_insert_id($conn);
	mysqli_query($conn,"INSERT INTO texts () VALUES ()");
	$id2 = mysqli_insert_id($conn);
	mysqli_query($conn,"INSERT INTO assignments (context_id, text_id_name, text_id_description, year) VALUES (".$cid.", ".$id1.", ".$id2.", ".date("Y").")");
	return $cid;
}

function new_comment($conn, $cid, $uid) {
	mysqli_query($conn,"INSERT INTO comments (solution_id, user_id) VALUES (".$cid.",".$uid.")");
	return mysqli_insert_id($conn);
}

function getSolutionId($uid, $aid) {
	if ($conn = db_connect()) {
		if ($result = mysqli_query($conn,"SELECT s.context_id AS 'context_id' FROM teams t, solutions s, contexts c WHERE t.user_id = ".$uid." AND c.user_id = t.user_id AND c.context_id = s.context_id AND s.assignment_id = ".$aid)) {
			if (mysqli_num_rows($result) != 0) {
				return mysqli_fetch_array($result)['context_id'];
			}
		}
	}
	return 0;
}

function pridaj_hodnotenie($comments, $id) {
	?>
	<form accept-charset="utf-8" name="form1" enctype="multipart/form-data" method="POST" action="<?php echo "solution.php?id=".$comments[$id]->getSolution()."&comment=".$id; ?>" >
		<table>
			<?php
			if (is_a($_SESSION['loggedUser'], 'Administrator')) {
				for ($i = 0 ; $i < count($comments) ; $i++) {
					if ($i != $id) $comments[$i]->getTableHtml();
				}
			}
			$comments[$id]->getEditingHtml();
			?>
		</table>
		<br>
		<input type="submit" data-trans-key="save-changes" id="upload" />
	</form>
	<?php
}

function updateData($conn, $kde, $co, $zaco, $idName, $id) {
	$sql_update = "UPDATE ".$kde." SET ".$co." = '".addslashes($zaco)."' WHERE ".$idName." = ".$id;
	if (mysqli_query($conn,$sql_update)) {
		echoMessage("m-text-saved");
	}
	else {
		echoError("text-saving", mysqli_error($conn));
	}
}

function get_max_year(){
    if ($link = db_connect()){
        $sql = "SELECT max(year) AS year FROM assignments;";
        if ($result = mysqli_query($link, $sql))
            if ($row = mysqli_fetch_array($result))
                return $row['year'];

    }
    return Date("Y");
}

function get_result_table($sk_league, $year) {
    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
    if ($link = db_connect()) {
        if (!isset($year) || ($year == NULL)){
            $maxYearQuery = "SELECT MAX(year) AS year FROM assignments";
            if ($result = mysqli_query($link, $maxYearQuery))
                if ($row = mysqli_fetch_array($result))
                    $year = $row['year'];
        }
        if ($year <= 2015 && !$sk_league) {
            return "";
        }

        $sql = "SELECT q.name, q.solution_id, q.best, a.context_id assignment_id, q.points, q.sk_league AS league
                FROM assignments a
                INNER JOIN (
                    SELECT ANY_VALUE(t.name) as name, ANY_VALUE(t.sk_league) as sk_league, s.context_id solution_id, s.best, s.assignment_id, ANY_VALUE(comm.points) as points
                    FROM solutions s
                    LEFT OUTER JOIN contexts c ON (c.context_id = s.context_id)
                    LEFT OUTER JOIN users u ON (u.user_id = c.user_id)
                    LEFT OUTER JOIN teams t ON (t.user_id = u.user_id)
                    INNER JOIN comments comm ON (comm.solution_id = c.context_id AND comm.user_id = 1 AND comm.text IS NOT null)
                        WHERE sk_league IN (1, $sk_league)
                        GROUP BY t.user_id, s.context_id) q
                ON (q.assignment_id = a.context_id)
                WHERE a.year = $year AND a.begin <= NOW()
                ORDER BY a.begin ASC, a.context_id ASC;";

/*
"SELECT q.name, q.solution_id, q.best, a.context_id assignment_id, q.points, q.sk_league AS league
                FROM assignments a
                INNER JOIN (
                    SELECT t.name, t.sk_league, s.context_id solution_id, s.best, s.assignment_id, comm.points
                    FROM solutions s
                    LEFT OUTER JOIN contexts c ON (c.context_id = s.context_id)
                    LEFT OUTER JOIN users u ON (u.user_id = c.user_id)
                    LEFT OUTER JOIN teams t ON (t.user_id = u.user_id)
                    INNER JOIN comments comm ON (comm.solution_id = c.context_id AND comm.user_id = 1 AND comm.text IS NOT null)
                   	WHERE t.sk_league IN (1, $sk_league)
                	GROUP BY t.user_id, s.context_id) q
                ON (q.assignment_id = a.context_id)
                WHERE a.year = $year AND a.begin <= NOW()
                ORDER BY a.begin ASC, a.context_id ASC;
                ";

*/

        if (!$result = mysqli_query($link, $sql))
            return "";

        $teamPointsMap = array();
        $teamLeagueMap = array();
        $aid_array = array();
        while ($row = mysqli_fetch_array($result)) {
            $end_array = array_values($aid_array);
            if (!sizeof($aid_array) || $row['assignment_id'] != end($end_array)){
                array_push($aid_array, $row['assignment_id']);
                foreach ($teamPointsMap as $user => $array) {
                    array_push($array, null);
                }
            }

            if (!isset($teamPointsMap[$row['name']]) && $row['name'] != null){
                $teamPointsMap[$row['name']] = array();
                for ($i = 0; $i < sizeof($aid_array); $i++)
                {
                    array_push($teamPointsMap[$row['name']], null);
                }
            }

            if ($row['name'] != null){
                $teamPointsMap[$row['name']][sizeof($aid_array)-1] = array((float)$row['points'], $row['solution_id'], $row['best']);
                $teamLeagueMap[$row['name']] = $row['league'];
            }
        }

        if (empty($teamPointsMap)){
            return "";
        }

        $sum_array = array();
        foreach ($teamPointsMap as $user => $array){
            $sum = 0;
            for ($i = 0; $i < sizeof($aid_array); $i++){
                if (!is_null($array[$i])){
                    $sum += $array[$i][0];
                }
            }
            $sum_array[$user] = $sum;
        }

        arsort($sum_array);

        $league = $sk_league ? "sk-league" : "open-league";

        $result_table = '<p class="center" data-trans-key="'.$league.'"></p>';
        $result_table .= '<table class="result-table">
                         <tr style="font-weight: bold; background-color: #ff6600; border-bottom: 1px solid black;">
                         <th><span data-trans-key="team-name"></span></th>';

        for ($i = 1; $i < sizeof($aid_array)+1; $i++){
            $href = 'assignment.php?id='.$aid_array[$i-1];
            $result_table .= '<th><a href="'.$href.'">'.$i.'</a></th>';
        }
        $result_table .= '<th><span data-trans-key="sum-points"></span></th></tr>';

        foreach ($sum_array as $user => $sum){
            $best_color = array("#53DFF5", "#73FF57")[$teamLeagueMap[$user]];
            $result_table .= "<tr style='border-top: 1px solid black;'><td style='border-right: 1px solid black; font-weight: bold;'><strong>$user</strong></td>";
            for ($i = 0; $i < sizeof($aid_array); $i++){
                if (is_null($teamPointsMap[$user][$i])){
                    $result_table .= "<td style=' font-weight: bold;'>-</td>";
                }
                else {
                    $result_table .= '<td style="font-weight: bold; '.($teamPointsMap[$user][$i][2]?"background-color: $best_color;":"").'"><a
                    href="solution.php?id='.$teamPointsMap[$user][$i][1].'">'.round($teamPointsMap[$user][$i][0],2).'</a></td>';
                };
            }
            $result_table .= '<td style="border-left: 1px solid black;"><strong>'.round($sum_array[$user],2).'</strong></td>';
            $result_table .= "</tr>";
        }
        $result_table .= "</table>";

        return $result_table;
    }


} 



function sprava_uctov() {

    if ($link = db_connect()) {
        $sql="SELECT * FROM teams WHERE user_id>0 ORDER BY name ASC"; // definuj dopyt
    $result = mysqli_query($link, $sql); // vykonaj dopyt
    if ($result) {
            // dopyt sa podarilo vykonať
            echo '<p>';
        ?>
            <form method="post">
            <?php
            echo "<table text-align = 'center' border = '0'>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td><a href='editAcc.php?id={$row['user_id']}'>{$row['name']}</a></td>";
                echo "<td><button type='submit' name='zrus' onclick='return confirm(dict.get(\"delete-acc-prompt\") + \" \" + \"{$row['name']}\"  + \"?\");' value='{$row['user_id']}'><span
                data-trans-key='delete'></span></button><br></td>\n";
                echo "</tr>";
            }
            echo "</table>";
            ?>
</form>
<?php
            echo '</p>';
            mysqli_free_result($result);
    } else {
            // NEpodarilo sa vykonať dopyt!
        echoError('err-db-query-fail');
    }
    mysqli_close($link);
    } else {
        // NEpodarilo sa spojiť s databázovým serverom alebo vybrať databázu!
        echoError('err-db-connection-fail');
    }
}

function sprava_uctov_jury() {

    if ($link = db_connect()) {
        $sql="SELECT * FROM organisators o INNER JOIN users u ON o.user_id = u.user_id WHERE o.user_id>0 ORDER BY u.mail ASC"; // definuj dopyt
    $result = mysqli_query($link, $sql); // vykonaj dopyt
    if ($result) {
            // dopyt sa podarilo vykonať
            echo '<p>';
        ?>
            <form method="post">
            <?php
            echo "<table text-align = 'center' border = '0'>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td><a href='editAccJury.php?id={$row['user_id']}'>{$row['mail']}</a></td>";
                if ($row['validated']==0) {
                    echo "<td><button type='submit' name='active' value='{$row['user_id']}'><span data-trans-key='validate'></span></button><br></td>";
                }else{
                    echo "<td><br></td>";
                }

                if ($row['user_id'] != $_SESSION['loggedUser']->id) {
                    echo "<td><button type='submit' name='zrus' onclick='return confirm(dict.get(\"delete-acc-prompt\") + \" \" + \"{$row['mail']}\"  + \"?\");'
                    value='{$row['user_id']}'><span data-trans-key='delete'></span></button><br></td>\n";
                }else{
                    echo "<td><br></td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            ?>
</form>
<?php
            echo '</p>';
            mysqli_free_result($result);
    } else {
            // NEpodarilo sa vykonať dopyt!
        echoError('err-db-query-fail');
    }
    mysqli_close($link);
    } else {
        // NEpodarilo sa spojiť s databázovým serverom alebo vybrať databázu!
        echoError('err-db-connection-fail');
    }
}

function isUserTypeLogged($type) {
	if (isset($_SESSION["loggedUser"]) && get_class($_SESSION["loggedUser"]) == $type) {
		return true;
	}
	return false;
}

function prehlad_zadani($zverejnene) {
    if ($link = db_connect()) {
		if ($zverejnene) {
			$sql="SELECT * FROM assignments a INNER JOIN texts t ON a.text_id_name = t.text_id WHERE begin <= NOW() AND end > NOW()";
			$nadpis = "published-assignments";
		}
		else {
			$sql="SELECT * FROM assignments a INNER JOIN texts t ON a.text_id_name = t.text_id WHERE begin > NOW() OR begin is NULL";
			$nadpis = "unpublished-assignments";
		}
        $result = mysqli_query($link, $sql);
		if ($result) {
			?>
			<div id="prehlad_zadani">
			<p>
			<form method="post">
				<h2 data-trans-key="<?php echo $nadpis; ?>"></h2>
				<table>
				<?php
				while ($row = mysqli_fetch_assoc($result)) {
					$eng = is_null($row['eng']) ? $row['sk'] : $row['eng'];
					echo "<tr>";
						if (isUserTypeLogged("Administrator") || (isUserTypeLogged("Jury") && $zverejnene == false)) {
							echo "<td><input type='radio' name='id' value='{$row['context_id']}'><br></td>\n";
						}
						echo "<td data-trans-lang='".SK."'><a href='assignment.php?id={$row['context_id']}'>{$row['sk']}</a></td>";
						echo "<td data-trans-lang='".ENG."'><a href='assignment.php?id={$row['context_id']}'>{$eng}</a></td>";
						?>
						<td> <?php
							if (isUserTypeLogged("Administrator") || (isUserTypeLogged("Jury") && $zverejnene == false)) 
							{
								if ($row['begin'] == "") {
									echo "---";
								}
								else {
									echo $row['begin'];
								}
							}
							else echo '<span data-trans-key="upload-by"></span>';
							?>
						</td>
						<td> <?php
							if ($row['end'] == "") {
								echo "---";
							}
							else {
								echo $row['end'];
							}
							?>
						</td>
						<?php
					echo "</tr>";
				}
				if (isUserTypeLogged("Administrator")){
					?>
					<tr>
						<td> </td>
						<td data-trans-key="publish-date"> </td>
						<td> <input type='datetime-local' name='start' value="<?php echo Date("Y-m-d")."T".Date("H:i"); ?>"> </td>
					</tr>
					<tr>
						<td> </td>
						<td data-trans-key="deadline-date"> </td>
						<td> <input type='datetime-local' name='stop' value="<?php echo date('Y-m-d', strtotime(Date("Y-m-d"). ' + 14 days'))."T23:59"; ?>"> </td>
						<td> <button type="submit" style="width:200px" formaction="prehladZadani.php?action=1" data-trans-key="publish-selected-assignment" id="publishAssignment" /> </td>
						
					 </tr>
					 <?php
					 if (!$zverejnene) {
						 ?>
						 <tr>
							<td> </td><td> </td><td> </td>
							<td> <button type="submit" style="width:200px" formaction="prehladZadani.php?action=2" data-trans-key="delete-selected-assignment" id="deleteAssignment" /> </td>
						 </tr>
						 <?php
					 }
				}
				if (isUserTypeLogged("Administrator") || (isUserTypeLogged("Jury") && $zverejnene == false)) {
					?>
					<tr>
						<td> </td><td> </td><td> </td>
						<td> <button type="submit" style="width:200px" formaction="prehladZadani.php?action=3" data-trans-key="edit-selected-assignment" id="editAssignment" /> </td>
					 </tr>
					<?php
				}
				?>
				</table>
			</form>
			</p>
			</div>
			<?php
			mysqli_free_result($result);
		}
		else {
			echoError('err-db-query-fail');
		}
		mysqli_close($link);
	}
	else {
		echoError('err-db-connection-fail');
	}
}

function createThumbnail($image_name,$new_width,$new_height,$uploadDir,$moveToDir)
{
    $path = $uploadDir . '/' . $image_name;

    $mime = getimagesize($path);

	if($mime['mime']=='image/gif'){ $src_img = imagecreatefromgif($path); }
    if($mime['mime']=='image/png'){ $src_img = imagecreatefrompng($path); }
    if($mime['mime']=='image/jpg'){ $src_img = imagecreatefromjpeg($path); }
    if($mime['mime']=='image/jpeg'){ $src_img = imagecreatefromjpeg($path); }
    if($mime['mime']=='image/pjpeg'){ $src_img = imagecreatefromjpeg($path); }

    $old_x          =   imageSX($src_img);
    $old_y          =   imageSY($src_img);

    if($old_x > $old_y) 
    {
        $thumb_w    =   $new_width;
        $thumb_h    =   $old_y*($new_height/$old_x);
    }

    if($old_x < $old_y) 
    {
        $thumb_w    =   $old_x*($new_width/$old_y);
        $thumb_h    =   $new_height;
    }

    if($old_x == $old_y) 
    {
        $thumb_w    =   $new_width;
        $thumb_h    =   $new_height;
    }

    $dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);

    imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 

    $new_thumb_loc = $moveToDir . $image_name;
	
	if($mime['mime']=='image/gif'){ $result = imagegif($dst_img,$new_thumb_loc,8); }
    if($mime['mime']=='image/png'){ $result = imagepng($dst_img,$new_thumb_loc,8); }
    if($mime['mime']=='image/jpg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }
    if($mime['mime']=='image/jpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }
    if($mime['mime']=='image/pjpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }

    imagedestroy($dst_img); 
    imagedestroy($src_img);

    return $result;
}

function delete_assignment($id) {
	if ($link = db_connect()) {
		$sql = "SELECT context_id, text_id_name, text_id_description FROM assignments WHERE context_id = ".$id;
		$result = mysqli_query($link, $sql);
		if ($result && mysqli_num_rows($result) != 0) {
			$assignment = mysqli_fetch_array($result);
			$sql = "DELETE FROM texts WHERE text_id = ".$assignment['text_id_name']." OR text_id = ".$assignment['text_id_description'];
			if (!mysqli_query($link, $sql)) {
				echoError('err-assignment-deleting');
				return;
			}
			$sql = "DELETE FROM videos WHERE context_id = ".$assignment['context_id'];
			if (!mysqli_query($link, $sql)) {
				echoError('err-assignment-deleting');
				return;
			}
			$sql = "DELETE FROM images WHERE context_id = ".$assignment['context_id'];
			if (!mysqli_query($link, $sql)) {
				echoError('err-assignment-deleting');
				return;
			}
			$sql = "DELETE FROM programs WHERE context_id = ".$assignment['context_id'];
			if (!mysqli_query($link, $sql)) {
				echoError('err-assignment-deleting');
				return;
			}
			$sql = "DELETE FROM assignments WHERE context_id = ".$assignment['context_id'];
			if (!mysqli_query($link, $sql)) {
				echoError('err-assignment-deleting');
				return;
			}
			if (!deleteDir(dirname(__FILE__)."/../attachments/assignments/".$assignment['context_id'])) {
				echoError('err-assignment-deleting');
				return;
			}
			echoMessage('m-assignment-deleted');
		}
		else {
			echoError('err-assignment-deleting');
		}
	}
	else {
		echoError('err-db-connection-fail');
	}
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
		return false;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
	return true;
}


?>
