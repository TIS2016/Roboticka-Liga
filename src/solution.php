<?php
require_once(dirname(__FILE__)."/includes/functions.php");

page_head("Letná liga FLL");
page_nav();
if (!isset($_SESSION['loggedUser']))
            get_login_form();
        else
            get_logout_button();
$id = (integer)$_GET["id"] ;

if($link = db_connect()){
  $_SESSION['solution'] = Solution::getFromDatabaseByID($link, $id);
}
if(isset($_SESSION['solution'])){
  if (isset($_GET['comment'])) {
    $comment = $_SESSION['solution']->getComments()[$_GET['comment']];
    if ($_SESSION['loggedUser']->getId() == $comment->getAuthor()->getId()) {
      if (isset($_POST['commentText']) && $_POST['commentText'] != $comment->getTxt()) {
        $comment->setTxt($link, $_POST['commentText']); 
      }
      if (isset($_POST['commentPoints']) && $_POST['commentPoints'] != $comment->getPoints()) {
        $comment->setPoints($link, $_POST['commentPoints']);  
      }
    }
  } 
  
?>
  <div id="content">
<?php
  $_SESSION['solution']->getPreviewHtml();
  ?>
    <table>
    <tr>
    <?php
    if($link= db_connect()){
    
      $sql_get_images = "SELECT * FROM images i WHERE i.context_id = ".$id;
      $images = mysqli_query($link,$sql_get_images);
      if ($images != false) {  
          $pocet=0;

          while ($images_row = mysqli_fetch_assoc($images)) {  
            $pripona = explode(".",$images_row['original_name']);
            $suborB = "attachments/solutions/".$_SESSION['solution']->getId()."/images/big/".$images_row['image_id'].".".$pripona[count($pripona)-1]; 
            $suborS = "attachments/solutions/".$_SESSION['solution']->getId()."/images/small/".$images_row['image_id'].".".$pripona[count($pripona)-1]; 
           ?>
              <td><a class="fancybox" data-url="kempelen.ii.fmph.uniba.sk/ll/<?php echo $suborB; ?>" rel="group" href="<?php echo $suborB; ?>"><img src="<?php echo $suborS ?>" width="250", width="250") ?> </a></td>
            <?php

            $pocet++;
            if($pocet%2==0){
              ?>
              </tr>
              <tr>
              <?php
            }
        }
      }
      
      $sql_get_video = "SELECT * FROM videos v WHERE v.context_id = ".$id;
      $videos = mysqli_query($link,$sql_get_video);
      if ($videos != false) { 
?>
      </tr>
      </table>
      <?php
        while ($videos_row = mysqli_fetch_assoc($videos)) { 
          $linka = "http://www.youtube.com/embed/".$videos_row['link'] . "?rel=0&loop=1";
          
        ?>
          <iframe width="500" height="375" src="<?php echo $linka; ?>" frameborder="0" allowfullscreen></iframe> <br>
        <?php
        
        }
      }
      $sql_get_prilohy = "SELECT * FROM programs p WHERE p.context_id = ".$id;
      $prilohy = mysqli_query($link,$sql_get_prilohy);
      if ($prilohy != false) {
        ?>
        <h3><span data-trans-key='attachments'></span>:</h3>
        <?php
        while ($prilohy_row = mysqli_fetch_assoc($prilohy)) { 
          $pripona = explode(".",$prilohy_row['original_name']);
	  if (count($pripona) == 1) $priponoviny = '';
	  else $priponoviny = ".".$pripona[count($pripona)-1]; 
          $subor = "attachments/solutions/".$_SESSION['solution']->getId()."/programs/".$prilohy_row['program_id'].$priponoviny;
          ?>
          <a href="<?php echo $subor; ?>" target="_blank"><?php echo $prilohy_row['original_name']; ?></a><br>
          <?php
        }
      }
  }
  ?>
  <h3><span data-trans-key="rating"></span>:</h3>
  <p>
  <?php
  if ((isset($_SESSION['loggedUser']) && (is_a($_SESSION['loggedUser'], 'Jury') || is_a($_SESSION['loggedUser'], 'Administrator')))) {
    $_SESSION['solution']->getCommentEditingHtml();
  }
  else {
    for ($i = 0 ; $i < count($_SESSION['solution']->comments) ; $i++) {
      if ($_SESSION['solution']->comments[$i]->getAuthor() instanceof Administrator) {
        if(zobrazHodnotenie($_GET["id"])){
        $_SESSION['solution']->comments[$i]->getPreviewHtml();
        break;}
        
      }
    }
  }
  ?>
  </p>
    
</div>
<?php
}
?>
<!-- Add jQuery library -->
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<!-- Add fancyBox -->
<link rel="stylesheet" href="js/source/jquery.fancybox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/jquery.fancybox.custom.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript">
$(".fancybox").fancybox({
    afterShow: function () {
        var url = "http://" + $(this.element).data("url");
        $(".fancybox-image").wrap("<a href='"+url+"' target='_blank' />");
    }
});

</script>
<?php
page_footer()
?>
