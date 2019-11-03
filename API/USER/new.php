<?php require("../engine.php"); //Include Master Library

if (LOGGED && $_SESSION['privilage'] == 1) {
	if (isset($_POST['bayi_name']) && !empty($_POST['bayi_name']) ||
		isset($_POST['bayi_email']) && !empty($_POST['bayi_email']) ||
		isset($_POST['bayi_privilage']) && !empty($_POST['bayi_privilage'])
	) {
		newLog("Starting new user creation process...");
		$db = createDB("USERNAME", "PASSWORD", "DB NAME");

		$generated_password = substr(str_shuffle("abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890"), 0, 10);
		$query = $db->prepare("INSERT INTO users SET
		bayi_num = FLOOR(rand() * 90000000 + 10000000),
		password = ?,
		bayi_adi = ?,
		email = ?,
		privilage = ?,
		telephone = ?,
		address = ?");

		$insert = 0;

		while ($insert == 0) {
			$insert = $query->execute(array(
			     hash('sha256', $generated_password),
			     $_POST['bayi_name'],
			     $_POST['bayi_email'],
			     $_POST['bayi_privilage'],
			     $_POST['bayi_telephone'],
			     $_POST['bayi_address']
			));
		}

		if ($insert == 1 && sizeof($_FILES) == 1 && pathinfo($_FILES['img']['name'])['extension'] != "") { //if picture is present, start processing it
			$ext = pathinfo($_FILES['img']['name'])['extension']; // get the extension of the file
			$img_name = $db->lastInsertId() . "." . $ext; 
			$target = '../../IMG/BAYI_PICS/'.$img_name;
			if (move_uploaded_file( $_FILES['img']['tmp_name'], $target)) {
				newLog("Succesfully move new users picture as at '" . $target . "'");
			}
			else {
				newLog("Unable to move users picture to '" . $target . "'");
			}
		}
		else {
			newLog("This new user has no profile picture uploaded.");
		}

		$statement = $db->prepare("SELECT bayi_num FROM users WHERE id=" . $db->lastInsertId());
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$statement->execute();

		while ($row = $statement->fetch()) {
		  	$bayi_num = $row['bayi_num'];
		}

		newLog("User creation process ended:\n\tBayi adi : '" . $_POST['bayi_name'] . "'\n\tBayi ID  : " . $bayi_num . "'");

		$obj = new stdClass();
		$obj->key = "yeni_bayi_adi";
		$obj->val = $_POST['bayi_name'];
		array_push($varset, $obj);

		$obj = new stdClass();
		$obj->key = "bayi_num";
		$obj->val = $bayi_num;
		array_push($varset, $obj);

		$obj = new stdClass();
		$obj->key = "bayi_pass";
		$obj->val = $generated_password;
		array_push($varset, $obj);


		$mail = new StdClass;
		$mail->address = $_POST['bayi_email'];
		$mail->familiar_name = $_POST['bayi_name'];
		$mail->subject = "Bayır Süt E-Bayi Kimlik Bilgileriniz";

		$mail_template = file_get_contents("/home/bayirsut/bayi/templates/mail/new_user.html");
		$mail->body = ParseDoubleP($mail_template, $varset);

		sendMail($mail);

		$_SESSION['new_bayi_error'] = true;
		location("../../bayi");
	}
	else {
		newLog("Failed to create new user.\n\tMissing POST data.");
		$_SESSION['new_bayi_error'] = false;
		location("../../bayi");
	}
}
else {
	newLog("Client with IP '" . getRealIpAddr() . "' has unsuccesfully tried to create new user.\n\tMissing privilage.");
	$_SESSION['new_bayi_error'] = false;
	location("../../bayi");
}
?>