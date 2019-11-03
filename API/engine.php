<?php
	/*
	/
	/		D U R A S O F T W A R E
	/			BAYIR #00000001
	/	
	/	D u r a s o f t  P H P  E n g i n e
	/
	/	Description:
	/	
	/	Contains all general usage functions
	/	and datasets.
	/
	/	This is a very prototyping unit and should used cautiously.
	/	There are known security flaws within the SQL operations and possibly file operations.
	*/

	session_start(); //enable session for all PHP-Enabled pages

	/* - - Function Sets - - */
		//function::addOrUpdateUrlParam -> function name describes it all. takes url from current url
		function addOrUpdateUrlParam($name, $value) {
			$params = $_GET;
			unset($params[$name]);
			$params[$name] = $value;

			return basename($_SERVER['PHP_SELF']).'?'.http_build_query($params);
		}
		//function::location -> relocates location
		function location($url) { header("Location: " . $url); }
		//function::getRealIpAddr -> Returns IP address of client
		function getRealIpAddr() {
		    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		    {
		      $ip=$_SERVER['HTTP_CLIENT_IP'];
		    }
		    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		    {
		      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		    }
		    else
		    {
		      $ip=$_SERVER['REMOTE_ADDR'];
		    }
		    return $ip;
		}
		//function::craeteDB -> Creates PDO object, returns PDO object. eg.: $db = createDB("username", "Password", "Database");
		function createDB($username, $password, $dbname) {
			try {
				$db = new PDO("mysql:host=localhost;dbname=" . $dbname . ";charset=utf8", $username, $password);
			}
			catch ( PDOException $e ){
				newLog("Unable to create PDO object.\nBegin PDOException ->\n" + $e->getMessage() + "\n<- EOL");
			}
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			return $db;
		}
		function setDocumentAs($type) {
			switch (strtolower($type)) {
				case "json":
					header('Content-type:application/json;');
					header('Charset: utf8');
					header('Access-Control-Allow-Origin: *');
					header('Access-Control-Allow-Methods: POST, GET'); 
					header('Access-Control-Allow-Headers: X-Requested-With, content-type, X-Token, x-token');
					break;
				case "unauthorized":
					header('X-PHP-Response-Code: 401', true, 401);
					break;
				case "success":
					header('X-PHP-Response-Code: 200', true, 200);
					break;
				case "text":
					header("Content-type: text/plain");
				case "no-content":
					header('X-PHP-Response-Code: 204', true, 204);
					break;
				default:
					break;
			}
		}
		//function::JSONout -> Json_encode function that always returns Pretty Printed JSON strings.
		function JSONout($arr) {
			return json_encode($arr, JSON_PRETTY_PRINT);
		}
		//function::ParseDoubleP -> Applies rulesets from a $varlist to a string. Returns parsed string.
			function ParseDoubleP ($str, $varlist) {
				$generated_str = $str;

				foreach ($varlist as $var) {
					$generated_str = str_replace("%" . $var->key . "%", $var->val, $generated_str);
				}
				//$parsed_generated_str = $generated_str;
				//$generated_str = preg_replace('#(%)(.*)(%)#si', "", $generated_str);

				//if ($parsed_generated_str != $generated_str) newLog("Warning: Unknown variables also used in ParseDoubleP function. Omitting...");
				return $generated_str;
			}
	/* - End Function Sets - */

	/* - - Define Logged Status - - */
		if (isset($_SESSION['bayi_num']) && $_SESSION['bayi_num'] != "")
			define("LOGGED", true);
		else
			define("LOGGED", false);
	/* - End Define Logged Status - */

	/* - - Parse CONF.JSON - - */
		$varset = []; //Stores key's and values
		$conf_str = file_get_contents(dirname( __FILE__ ) . "/conf.json"); //use dirname to get rid of requiring outside

		//JSON does not allow comments. Commet replaced with array: '_comment'
		//$conf_str = preg_replace('~"(?:[^\\\"]+|\\\.)*+"(*SKIP)(*FAIL)|/\*(?:[^*]+|\*+(?!/))*+\*/~s', '',$conf_str); //remove comments from encoded json string

		//create conf object
		$conf_obj = json_decode($conf_str, true);	
		//parse conf object elements
			foreach(array_keys($conf_obj['variables']) as $key) {
			    $obj = new stdClass();
				$obj->key = $key;
				$obj->val = $conf_obj['variables'][$key];

			   	array_push($varset, $obj);
			}
		//end parsing file

			define("TITLE", ParseDoubleP($conf_obj['title'], $varset));
			define("VER", ParseDoubleP($conf_obj['ver'], $varset));
			newLog("Configuration file processed. Current E-Bayi Version: '" . $conf_obj['ver'] . "'");
			define("WELCOMING_TXT", ParseDoubleP($conf_obj['welcoming_txt'], $varset));
		//End definitions
	/* - End Parse CONF.JSON - */

	/* - - Save CONF.JSON - - */
		//warning: this function overwrites current config.
		//function::saveNewConf -> Encodes object into JSON and overwrites conf.json file.
		function saveNewConf($varlist) {
			$generated_json = json_encode($varlist, JSON_PRETTY_PRINT);
			return file_put_contents(dirname( __FILE__ ) . '/conf.json', $generated_json);
		}
	/* - End Save CONF.JSON - */

	/* - - Create Log Arr and Append File - - */
		$log_arr = []; //All logs should be pushed into this
		function newLog($text) {
			global $log_arr;
			if (sizeof($log_arr) == 0) $log_arr = ["[" . date("Y/m/d h:i:sa") . "] -> " . $text];
			else array_push($log_arr, "[" . date("Y/m/d h:i:sa") . "] -> " . $text);
			return true;
		}
		function appendLogs() {
			global $log_arr;
			if (sizeof($log_arr) > 0) {
				$txt = "";
				foreach($log_arr as $log) {
					$txt = $txt . $log . PHP_EOL;
				}
				$myfile = file_put_contents(dirname( __FILE__ ) . '/EBAYI_LOGS', $txt, FILE_APPEND | LOCK_EX);
			}
		}
		register_shutdown_function('appendLogs'); //call function before shutdown 'termination'
	/* - End Create Log Arr and Append File - */

	/* - - Evaluate Routes - - */
		//function::RouteTo -> Handles routing. Manually add URL's.
		function RouteTo() {
			$page = "404";

			if (!isset($_GET['r']) || $_GET['r'] == "") { //if $_GET['r'] is not present, just route to anasayfa to re-evaluate
				location("./anasayfa"); //Not set route is an error. Don't throw out an error but route back to anasayfa.
				define("PAGE", "anasayfa-redirect");
			}
			else {
				switch (strtolower($_GET['r'])) {
				    case "anasayfa":
				    	if (LOGGED && $_SESSION['privilage'] == 1) {
				    		define("PAGE", "yonetici_anasayfa");
				    	}
				        else define("PAGE", "anasayfa");
				        break;
				    case "urunler": //A list of products
				        if (LOGGED && $_SESSION['privilage'] == 1) {
				    		define("PAGE", "yonetici_urunler");
				    	}
				        else define("PAGE", "urunler");
				        break;
				    case "siparis": //A list of products
				        if (LOGGED && $_SESSION['privilage'] == 1) {
				    		define("PAGE", "yonetici_siparis");
				    	}
				        else define("PAGE", "siparis");
				        break;
				    case "bayi": //A list of products
				        if (LOGGED && $_SESSION['privilage'] == 1) {
				    		define("PAGE", "yonetici_bayi");
				    	}
				        else define("PAGE", "bayi");
				        break;
				    case "ayarlar":
				    	if (LOGGED && $_SESSION['privilage'] == 1) {
				    		define("PAGE", "ayarlar");
				    	}
				    	else define("PAGE", "401");
				    	break;
				    case "404": //Page not found
				    	define("PAGE", "404");
				    	break;
				    case "kayitlar":
				    	if (LOGGED && $_SESSION['privilage'] == 1) {
				    		define("PAGE", "kayitlar");
				    	}
				        else define("PAGE", "401");
				        break;
				    case "401": //Not-authorized request (privilage not enough)
				    	define("PAGE", "401");
				    	break;
				    default:
				    	define("PAGE", "404");
				    	break;
				}
			}
			return addOrUpdateUrlParam("r", $page);
		}
	/* - End Evaluate Routes - */

	/* - - E-Mail - - */
		require_once(dirname( __FILE__ ) . '/PHPMailer/class.phpmailer.php');

		define("MAIL_SERVER", "localhost");
		define("SMTP_DEBUG", 0);
		define("SMTP_PORT", 587);
		define("MAIL_USER_NAME", "ENTER USER NAME HERE");
		define("MAIL_USER_PASS", "ENTER PASSWORD HERE");

		define("MAIL_FROM_USER", "iletisim@bayirsut.com");
		define("MAIL_FROM_USER_FAMILIAR", "Bayır Entegre Süt Ürünleri");


		function sendMail($mailobj) {
			// check incoming object whether is valid
				if (gettype($mailobj) == "object") {
					if (!isset($mailobj->address)) {
						newLog("Error: sendMail function expects an address to send mail to.\n\tmailObject->address missing");
						return false;
					}
					if (!isset($mailobj->familiar_name)) {
						$mailobj->familiar_name = $mailobj->address;
					}
					if (!isset($mailobj->subject)) {
						newLog("Warning: sendMail function expects an optional but suggested subject to the mail. Leaving blank.");
						$mailobj->subject = "";
					}
					if (!isset($mailobj->body)) {
						newLog("Error: sendMail function expects the mail body.\n\tmailObject->body missing");
						return false;
					}
				}
				else {
					newLog("Error: sendMail function expects a valid stdClass object as parameter.");
					return false;
				}
			//end check and continue if nothing gone wrong

			$mail = new PHPMailer(true); //exceptions on
			try {
			    $mail->SMTPDebug = SMTP_DEBUG;
			    $mail->isSMTP(); //Use SMTP rather than PHP mail()
			    $mail->CharSet = "UTF-8";
			    $mail->Host = MAIL_SERVER; //ssl://mail.durasoftware.com:465
			    $mail->SMTPAuth = true;
			    $mail->Username = MAIL_USER_NAME;
			    $mail->Password = MAIL_USER_PASS; //correct temporary password
			    // TO::DO -> Change password.

			    $mail->Port = SMTP_PORT;

			    $mail->setFrom(MAIL_FROM_USER, MAIL_FROM_USER_FAMILIAR);
				$mail->addAddress($mailobj->address, $mailobj->familiar_name);

			    $mail->addReplyTo(MAIL_FROM_USER, MAIL_FROM_USER_FAMILIAR);

			    $mail->isHTML(true);
			    $mail->Subject = $mailobj->subject;
			    $mail->Body    = $mailobj->body;

			    $mail->AltBody = 'Kullanmakta olduğunuz E-Posta görüntüleyicisi HTML görüntülemeyi desteklememektedir. Lütfen E-Postayı görüntülemek için modern bir E-Posta sağlayıcısına geçin.\nBayır Entegre Süt Ürünleri';

			    $mail->send();
			    newLog("E-Mail Succesfuly sent with subject: '" . $mailobj->subject . "' to '" . $mailobj->address . "'.");

			    return true;
			}
			catch (Exception $e) {
			    newLog("E-Mail could not be sent. Mailer Error:\nbegin error->\n" . $mail->ErrorInfo . "\n<-end error");
			    return false;
			}
		}
	/* - End E-Mail - */