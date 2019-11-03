<?php require("../engine.php");
	/*
	/
	/		D U R A S O F T W A R E
	/			BAYIR #00000001
	/
	/	URUN/cek.php
	/	Description:
	/	
	/	Fetches all products from the database
	/	and returns a JSON element with header
	/		
	/
	*/

	$db = createDB("USERNAME", "PASSWORD", "DB NAME");
	$products = []; //products array

	if (isset($_SESSION['bayi_num']) && $_SESSION['bayi_num'] != "") {
		$statement = $db->query("SELECT * FROM items");
		$statement->setFetchMode(PDO::FETCH_ASSOC);

		$products = $statement->fetchAll();

		setDocumentAs("JSON");
		echo JSONout($products);
	}
	else {
		setDocumentAs("unauthorized");
	}

?>