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

	newLog("User with ID: " . $_SESSION['bayi_num'] . " has logged out succefully.");

	session_unset();
	session_destroy();

	location("../../");
?>