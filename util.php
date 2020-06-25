<?php
function flashmessages(){
  if ( isset($_SESSION['error']) ) {
      echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
      unset($_SESSION['error']);
  }
  if ( isset($_SESSION['success']) ) {
      echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
      unset($_SESSION['success']);
  }

}

function validatePos() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
        }

        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}

function loadpos($pdo,$profile_id){

  $stmt = $pdo->prepare("SELECT * FROM position where profile_id = :profile_id order by rank");
  $stmt->execute(array(":profile_id" => $profile_id));
  $positions = array();
    while(  $row = $stmt->fetch(PDO::FETCH_ASSOC)){
      $positions[] = $row;
    }
return $positions;
}


?>
