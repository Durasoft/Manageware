<?php require("../engine.php");
	/*
	/
	/		D U R A S O F T W A R E
	/			BAYIR #00000001
	/
	/	GIRIS/cikis.php
	/	Description:
	/	
	/	Destroys the session data and adds
	/	this to logs at the database
	/		
	/
	*/

	$db = createDB("USERNAME", "PASSWORD", "DB NAME");

	$id = $_POST['id'];
	$password = hash('sha256', $_POST['password']);

	$statement = $db->prepare("SELECT id, bayi_num, bayi_adi, privilage, COUNT(*) as count FROM users WHERE bayi_num = :un AND password = :pw");
	$statement->setFetchMode(PDO::FETCH_ASSOC);
	$statement->execute(array(':un' => $id, ':pw' => $password));

	while ($row = $statement->fetch()) {
	  	$username_count = $row["count"];
	  	$bayi_id = $row['id'];
	  	$bayi_num = $row['bayi_num'];
	  	$bayi_adi = $row['bayi_adi'];
	  	$privilage = $row['privilage'];
	}
	//End fetching data


	//Start evaluating data
	$status = false;

	if ($username_count > 0) {
		$_SESSION['bayi_num'] = $bayi_num;
		$_SESSION['bayi_adi'] = $bayi_adi;
		$_SESSION['bayi_gercek_id'] = $bayi_id;
		$_SESSION['privilage'] = $privilage;
		$status = true;
		setDocumentAs("success");
		newLog("User with ID: " . $id . " has logged in succefully.");
	}
	else {
		newLog("Unsuccesfull login attempt:\nIP Addr: " . getRealIpAddr() . "\nUser ID: " . $id);
		setDocumentAs("unauthorized");
	}
?>