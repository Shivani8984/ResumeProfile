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
            header("Location: add.php");
            return;

      }elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
              $_SESSION["error"] = "Email must have an at-sign (@)";
              header("Location: add.php");
              return;

      }else{
        //print_r($_POST);exit;
        //validate position entries if present
        $msg = validatePos();
        if(is_string($msg)){
          $_SESSION["error"] = $msg;
          header("Location: add.php");
          return;
        }

            $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
                                  VALUES ( :uid, :fn, :ln, :em, :he, :su)');
            $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'])
            );
            $profile_id = $pdo->lastInsertId();
            //INSERT the POSITION entries
            $rank=1;
            for($i=1;$i<=9;$i++){
              if(!isset($_POST['year'.$i])) continue;
              if(!isset($_POST['desc'.$i])) continue;
              $year = $_POST['year'.$i];
              $desc = $_POST['desc'.$i];
              $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
                                      VALUES ( :pid, :rank, :year, :desc)');
              $stmt->execute(array(
                  ':pid' => $profile_id,
                  ':rank' => $rank,
                  ':year' => $year,
                  ':desc' => $desc)
              );
                $rank++;
            }
               $_SESSION["success"] = "Profile added.";
               header('Location: index.php');
               return;
        }
   }
?>
 <!DOCTYPE html>
 <html>
 <head>
 <title>Shivaniben Patel's Profile Add</title>
    <?php require_once "bootstrap.php"; ?>
 </head>
 <body>
 <div class="container">
   <h1>Adding Profile for <?= htmlentities($_SESSION['name']); ?></h1>
 <?php flashmessages(); ?>
 <form method="post">
 <p>First Name:
 <input type="text" name="first_name" size="60"/></p>
 <p>Last Name:
 <input type="text" name="last_name" size="60"/></p>
 <p>Email:
 <input type="text" name="email" size="30"/></p>
 <p>Headline:<br/>
 <input type="text" name="headline" size="80"/></p>
 <p>Summary:<br/>
 <textarea name="summary" rows="8" cols="80"></textarea></p>
 <p>Position: <input type="submit" id="addPos" value="+">
   <div id="position_fields"></div>
 </p>
 <p>
 <input type="submit" value="Add">
 <input type="submit" name="cancel" value="Cancel">
 </p>
 </form>
       <script>
            countPos = 0;
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
