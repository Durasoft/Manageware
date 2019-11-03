<?php require("../engine.php");

if (LOGGED && $_SESSION['privilage'] == 1) {
	$db = createDB("USERNAME", "PASSWORD", "DB NAME");

	$id = $_POST['id'];

	if ($_SESSION['bayi_gercek_id'] == $id) setDocumentAs("unauthorized"); //if tried to delete theirselves
	else {
		$statement = $db->prepare("DELETE FROM users WHERE id = :id");
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$statement->execute(array(':id' => $id));

		$response = $statement->execute(array(':un' => $id, ':pw' => $password));
		if ($response) setDocumentAs("success");
		else setDocumentAs("no-content");
	}
}
else setDocumentAs("unauthorized");
?>