<?php
$au = new auth_ssh();
checkAuIsAdmin($au);
?>

<html>

<body>

	<?php
	require("auth_ssh.class.php");

	$au = new auth_ssh();
	if ($au->isAdmin($_SESSION['hash'])) {

		if (isset($_POST['update'])) {
			exec("sudo git reset --hard", $out, $ret);
			echo "reset" . implode("<br>", $out) . "<br>ret code = " . $ret . "<br>";
			unset($out);
			exec("sudo git fetch", $out, $ret);
			echo "fetch" . implode("<br>", $out) . "<br>ret code = " . $ret . "<br>";
			unset($out);
			exec("sudo git pull", $out, $ret);
			echo "pull" . implode("<br>", $out) . "<br>ret code = " . $ret . "<br>";

			exec("sudo git submodule foreach git pull origin main", $out, $ret);
			echo "submodule foreach git pulling" . implode("<br>", $out) . "<br>ret code = " . $ret . "<br>";
		}
	}
	?>

</body>

</html>