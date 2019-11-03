<?php require("../engine.php");

if (LOGGED && $_SESSION['privilage'] == 1) {

	header("Content-disposition: attachment; filename=EBAYI_LOGS");
	header("Content-type: text/plain");
	readfile("../EBAYI_LOGS");
}
else setDocumentAs("unauthorized");
?>