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
    echo '<form action="update_action.php"><input type="submit" value="update"/></form>';
  }
  ?>

</body>

</html>