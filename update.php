<?php
$au = new auth_ssh();
checkAuIsAdmin($au);
?>

<html>

<body>

  <?php
  require("auth_ssh.class.php");

  $au = new auth_ssh();
  if ($au->isAdmin()) {
    echo '<form action="update_action.php"><input name="main" type="submit" value="main"/></form>';
    echo '<form action="update_action.php"><input name="dev" type="submit" value="dev"/></form>';
    echo '<form action="update_action.php"><input name="update" type="submit" value="update"/></form>';
  }
  ?>

</body>

</html>