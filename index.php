<?php
session_start();
require_once "pdo.php";
require_once "util.php";
$stmt = $pdo->query("SELECT profile_id,first_name, last_name, headline FROM profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Shivaniben Patel's Resume Registry</title>
    <?php require_once "bootstrap.php"; ?>
<style>
    table, th, td {
      border: 1px solid black;
    }
    th, td {
  padding: 15px;
  text-align: left;
}
tr:nth-child(even) {background-color: #f2f2f2;}
th {
  background-color: #4CAF50;
  color: white;
}
</style>
  </head>
<body>
  <div class="container">
    <h1>Shivaniben Patel's Resume Registry</h1>
<?php
flashmessages();
if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1  ) {

    echo '<p><a href="login.php">Please log in</a></p>';
}else{

  if(empty($rows)){
    echo '<p>No rows found</p>';
  }else{

    echo('<table>'."\n");
    echo('<thead><tr><th>Name</th><th>Headline</th><th>Action</th></tr></thead>');
    $stmt = $pdo->query("SELECT profile_id,first_name, last_name, headline FROM profile");
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        echo "<tr><td>";
        echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
        echo("</td><td>");
        echo(htmlentities($row['headline']));
        echo("</td><td>");
        echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
        echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
        echo("</td></tr>\n");
  }
  echo('</table>');
  echo("<p></P>\n");
}

echo '<p><a href="add.php">Add New Entry</a></p>
<p><a href="logout.php">Logout</a></p>';
}
?>

  </div>
</body>
</html>
