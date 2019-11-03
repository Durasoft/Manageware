<?php require("../engine.php");
	/*
	/
	/		D U R A S O F T W A R E
	/			BAYIR #00000001
	/	
	/	list.php
	/	Description:
	/	
	/	Fetches all users from the database
	/	and returns a JSON element with header
	/		
	/
	*/

	/*
		User Privilages:
			1: Administrator
			2: Moderator
			3: User
	*/

	setDocumentAs("JSON");
	$db = createDB("USERNAME", "PASSWORD", "DB NAME");

	if (isset($_SESSION['bayi_num']) && $_SESSION['bayi_num'] != "") {
		if ($_SESSION['privilage'] == 1) {
			$users = [];

			//if ID is given through GET:
			if (isset($_GET['id']) && $_GET['id'] != "") {
				$statement = $db->prepare("SELECT bayi_num, bayi_adi, email, telephone, address FROM users WHERE bayi_num= ?");
				$statement->setFetchMode(PDO::FETCH_ASSOC);
				$statement->execute(array($_GET['id']));

				while ($row = $statement->fetch()) {
					if (file_exists("../../IMG/BAYI_PICS/" . $row['id'] . ".jpg")) $row["pp"] = $row['id'] . ".jpg";
					else $row['pp'] = false;
					array_push($users, $row);
				}
			}
			else {
				$statement = $db->query("SELECT id, bayi_num, bayi_adi, email, privilage, creation_date, telephone, address FROM users;");
				$statement->setFetchMode(PDO::FETCH_ASSOC);

				if ( $statement->rowCount() ){
				    foreach( $statement as $row ){
				        switch ($row['privilage']) {
				        	case 1:
					       		$row['privilage'] = "Yönetici";
					       		break;
					       	case 2:
					       		$row['privilage'] = "Görevli";
					       		break;
					       	case 3:
					       		$row['privilage'] = "Bayi";
					       		break;
					       	default:
					       		$row['privilage'] = "<small>kullanıcının yetkisi hatalı</small>";
					       		break;
					    }

					    if (file_exists("../../IMG/BAYI_PICS/" . $row['id'] . ".jpg")) $row["pp"] = $row['id'] . ".jpg";
						else $row['pp'] = false;

					    array_push($users, $row);
					}
				}
			}
			echo JSONout($users);
		}
		else {
			setDocumentAs("unauthorized");
		}
	}
	else {
		setDocumentAs("unauthorized");
	}

?>