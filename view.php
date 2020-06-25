<?php
session_start();
require_once "pdo.php";
require_once "util.php";
$stmt = $pdo->prepare("SELECT profile_id,first_name, last_name, email, headline, summary FROM profile WHERE profile_id = :profile_id");
$stmt->execute(array(':profile_id' => $_GET['profile_id']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
//print_r($row);exit;

$positions = loadpos($pdo,$_REQUEST['profile_id']);

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Shivaniben Patel's Profile View</title>
      <?php require_once "bootstrap.php"; ?>
    </head>
<body>
  <div class="container">
    <h1>Profile information</h1>
     <?php foreach ( $rows as $row ) { ?>
          <p>First Name: <?echo(htmlentities($row['first_name']));?></p>
          <p>Last Name: <?echo(htmlentities($row['last_name']));?></p>
          <p>Email: <?echo(htmlentities($row['email']));?></p>
          <p>Headline:<br/> <?echo(htmlentities($row['headline']));?></p>
          <p>Summary:<br/><?echo(htmlentities($row['summary']));?></p>
      <?}?>
          <p>Position</p><ul>
            <?php foreach ( $positions as $position ) { ?>
                            <li><?echo(htmlentities($position['year']));?>: <?echo(htmlentities($position['description']));?></li>
            <?}?>
                         </ul>

      <p>      </p>
      <a href="index.php">Done</a>
  </div>
</body>
</html>
