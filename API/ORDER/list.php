<?php require("../engine.php");
	/*
	/
	/		D U R A S O F T W A R E
	/			BAYIR #00000001
	/
	/	URUN/siparisler.php
	/	Description:
	/	
	/	Fetches all products from the database
	/	and returns a JSON element with header
	/		
	/
	*/

	/*
		Order statuses:
			1: Completed
			2: Delivery send out
			3: Preparing Order
			4: Not-responed to the Order
	*/

	setDocumentAs("JSON");
	$db = createDB("USERNAME", "PASSWORD", "DB NAME");
	
	$products = []; //products array

	if (isset($_SESSION['bayi_num']) && $_SESSION['bayi_num'] != "") {
		if ($_SESSION['privilage'] == 1 || $_SESSION['privilage'] == 2) {
			
			if (isset($_GET['method'])) {
				switch($_GET['method']) {
					case 4:
						$statement = $db->query("SELECT * FROM orders WHERE status = 4;");
						break;
					case 3:
						$statement = $db->query("SELECT * FROM orders WHERE status = 3;");
						break;
					case 2:
						$statement = $db->query("SELECT * FROM orders WHERE status = 2;");
						break;
					case 1:
						$statement = $db->query("SELECT * FROM orders WHERE status = 1;");
						break;
					default:
						$statement = $db->query("SELECT * FROM orders;");
						break;
				}
			}
			else $statement = $db->query("SELECT * FROM orders;");
			$statement->setFetchMode(PDO::FETCH_ASSOC);

			$statement = $statement->fetchAll();
			foreach($statement as $order) {
				$id = $_GET['id']; 
				$order['list'] = unserialize($order['list']);
				array_push($products, $order);
			}

			echo JSONout($products);
		}	
		else {
			setDocumentAs("unauthorized");
		}
	}
	else {
		setDocumentAs("unauthorized");
	}

?>