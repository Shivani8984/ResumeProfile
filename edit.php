<?php
session_start();
//print_r($_POST);
require_once "pdo.php";
require_once "util.php";
  if ( ! isset($_SESSION['user_id'])) {
      die("ACCESS DENIED");
      return;
  }
  if ( isset($_POST['cancel']) ) {
      header('Location: index.php');
      return;
  }

 if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']))
 {
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1)
    {
            $_SESSION["error"] = "All fields are required";
            header("Location: edit.php?profile_id=".$_POST['profile_id']);
            return;

      }elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
              $_SESSION["error"] = "Email must have an at-sign (@)";
              header("Location: edit.php?profile_id=".$_POST['profile_id']);
              return;

      }else{

        $msg = validatePos();
          if(is_string($msg)){
            $_SESSION["error"] = $msg;
            header("Location: edit.php?profile_id=".$_POST['profile_id']);
            return;
          }
          $sql = "UPDATE profile SET user_id = :uid,
                  first_name = :fn, last_name = :ln,email = :em,headline = :he,summary = :su
                  WHERE profile_id = :profile_id AND user_id = :uid";
         //echo("<pre>\n".$sql."\n</pre>\n");
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':profile_id' => $_POST['profile_id'],
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'])
            );
            // CLEAR OLD POSITION entries
            $stmt = $pdo->prepare('DELETE FROM Position where profile_id = :pid');
            $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

      //TO INSERT THE POSITION ENTRY
                  $rank=1;
                  for($i=1;$i<=9;$i++){
                    if(!isset($_POST['year'.$i])) continue;
                    if(!isset($_POST['desc'.$i])) continue;
                    $year = $_POST['year'.$i];
                    $desc = $_POST['desc'.$i];
                    $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
                                            VALUES ( :pid, :rank, :year, :desc)');
                    $stmt->execute(array(
                        ':pid' => $_REQUEST['profile_id'],
                        ':rank' => $rank,
                        ':year' => $year,
                        ':desc' => $desc)
                    );
                      $rank++;
                  }
               $_SESSION["success"] = "Profile updated.";
               header('Location: index.php');
               return;

        }
   }

   if ( ! isset($_GET['profile_id']) ) {
     $_SESSION['error'] = "Missing profile_id";
     header('Location: index.php');
     return;
   }
//TO LOAD THE PROFILE DATA
   $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :profile_id AND user_id = :uid");
   $stmt->execute(array(":profile_id" => $_REQUEST['profile_id'],":uid" => $_SESSION['user_id']));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   if ( $row === false ) {
       $_SESSION['error'] = 'Could not load profile & Bad value for profile_id';
       header( 'Location: index.php' ) ;
       return;
   }
       $fn = htmlentities($row['first_name']);
       $ln = htmlentities($row['last_name']);
       $em = htmlentities($row['email']);
       $he = htmlentities($row['headline']);
       $su = htmlentities($row['summary']);
       $profile_id = $row['profile_id'];
// TO LOAD THE POSITION rows
$positions = loadpos($pdo,$_REQUEST['profile_id']);

?>
 <!DOCTYPE html>
 <html>
 <head>
 <title>Shivaniben Patel's Profile Edit</title>
    <?php require_once "bootstrap.php"; ?>
 </head>
 <body>
 <div class="container">
   <h1>Editing Profile for <?= $fn ?></h1>
 <?php flashmessages();?>
 <form method="post" action="edit.php">
 <p>First Name:
 <input type="text" name="first_name" size="60" value="<?= $fn ?>"/></p>
 <p>Last Name:
 <input type="text" name="last_name" size="60" value="<?= $ln ?>"/></p>
 <p>Email:
 <input type="text" name="email" size="30" value="<?= $em ?>"/></p>
 <p>Headline:<br/>
 <input type="text" name="headline" size="80" value="<?= $he ?>"/></p>
 <p>Summary:<br/>
 <textarea name="summary" rows="8" cols="80"><?= $su ?></textarea></p>
<?php
  $pos=0;
  echo('<p>Position:<input type="submit" id="addPos" value="+">'."\n");
  echo('<div id="position_fields">'."\n");
  foreach ($positions as $position) {
    $pos++;
    echo('<div id="position'.$pos.'">'."\n");
    echo('<p>Year: <input type="text" name="year'.$pos.'" value="'.$position['year'].'" />'."\n");
    echo(' <input type="button" value="-" onclick="$(\'#position'.$pos.'\').remove();return false;"></p>'."\n");
    echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'.htmlentities($position['description']).'</textarea>'."\n");
    echo "</div>\n";
  }
  echo "</div></p>\n";

 ?>

 <p>
 <input type="hidden" name="profile_id" value="<?= $profile_id ?>"/>
 <input type="submit" value="Save">
 <input type="submit" name="cancel" value="Cancel">
 </p>
 </form>
 <script>
      countPos = <?=$pos?>;
      // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
      $(document).ready(function(){
      window.console && console.log('Document ready called');
      $('#addPos').click(function(event){
            event.preventDefault();
         if ( countPos >= 9 ) {
             alert("Maximum of nine position entries exceeded");
             return;
         }
         countPos++;
         window.console && console.log("Adding position "+countPos);
         $('#position_fields').append(
             '<div id="position'+countPos+'"> \
             <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
             <input type="button" value="-" \
                 onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
             <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
             </div>');
      });
      });
</script>
 </div>
 </body>
 </html>
