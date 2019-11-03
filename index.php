<?php require ("./API/engine.php"); 
	$currentPath = RouteTo();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta charset="utf-8" />
	    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

	    <title><?=TITLE?></title>

	    <meta name="description" content="Bayır Entegre Süt Mamulleri bayilerine özel geliştirilmiş E-Bayi sistemi.">
	    <meta name="keywords" content="Bayır, Bayır Süt Mamulleri, Süt, Süt Ürünleri, Yoğurt">
	    <meta name="author" content="Durasoft Yazılım">

	    <meta name="robots" content="index, follow">
	    <meta name="mobile-web-app-capable" content="yes">
	    <meta name="apple-mobile-web-app-capable" content="yes">
	    <meta name="apple-mobile-web-app-status-bar-style" content="default">

		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"   integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="   crossorigin="anonymous"></script>
		<script src='./JS/nprogress.js'></script>
	    <link rel="stylesheet" href="./CSS/main.css">
	    <link rel='stylesheet' href='./CSS/nprogress.css'/>
	    <meta property="fb:page_id" content="">
	    <meta property="og:title" content="E-Bayi - Bayır Entegre Süt Mamulleri">
	    <meta property="og:image" content="">
	    <meta property="og:description" content="Bayır Entegre Süt Mamulleri E-Bayi Sistemi">
	    <meta property="og:url" content="https://bayirsut.com/bayi/">
	    <meta property="og:site_name" content="E-Bayi - Bayır Entegre Süt Mamulleri">
	    <meta property="og:type" content="website">
	</head>
	<body>
		<div id="container">
			<?php
				if (LOGGED) include("templates/left-bar.php");
			?>
			<div id="main-body" class="text-weight-2">
				<?php
					if (!LOGGED) include("templates/giris.php");
					else include ("templates/" . PAGE . ".php");
				?>
				<p style="text-align: center;">Yapım aşamasındadır.<br>Durasoft E-Bayi - Versiyon: <?=VER?></p>
			</div>
		</div>
		<script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>
		<script>
			console.log('%cDurasoft F.I.S. - E-Bayi, tüm hakları saklıdır. Bilgi için https://fis.durasoftware.com adresini ziyaret edebilirsiniz', 'color: rgb(26,26,26); font-size: 16px;');
			console.log('%cDUR!', 'color: red; font-size: 52px; font-weight: bold;');
			console.log('%cBu ekran sadece yazılım geliştiricilerine ayrılmış bir araçtır. Lütfen internetten kopyaladığınız kod parçalarını yapıştırmayınız.', 'color: red; font-size: 28px;');

			/* - - load sounds on user interaction - - */
				var errSnd,
					newSnd;
				$(document).click(function(event) {
					errSnd = new Audio("./SOUNDS/err.mp3"); 
				    errSnd.load(); // load the audio data
				    errSnd.volume=1;

				    newSnd = new Audio("./SOUNDS/new.mp3"); 
				    newSnd.load(); // load the audio data
				    newSnd.volume=1;

				    $(document).off('click');
				});
			/* - End load sounds on user interaction - */

			/* - - Login Handler - - */
				var reLoginStatus = false;
				function reLogin() {
					if (reLoginStatus) {
						$("#reLogin").fadeOut( "slow", function() {
							$("#reLogin").remove();
						});
					}
					else {
						if(typeof(errSnd) === "object") errSnd.play(); //only play if load complete.

						var relogin_popup = new PopDown();

						var relogin_html = document.createElement('div');
						$(relogin_html).load("templates/popups/relogin.html").appendTo(relogin_popup.elem); //one liner async to sync... I really hate JS promises. They are very helpfull, however I fail to understand every single time I try reading about them.

						relogin_popup.toggle();
					}
					reLoginStatus = !reLoginStatus;

					if (typeof init === "function") init(); //if page has init function, call it.
				}

				function bayi_login(numElem, passElem) {
					if (numElem.val() != "" && parseInt(numElem.val()) == numElem.val()) {
						if (passElem.val() != "") {
							//Valid (!) inputs
								$("#login-err").css("color", "#ccc");
								$("#login-err").text("Giriş yapılıyor");
								$.ajax({
								    url: "API/GIRIS/yap.php",
								    type: "POST",
								    data: {
								        id: numElem.val(),
								        password: passElem.val()
								    },
								    dataType:"text xml",
								    complete: function(xhr, textStatus) {
								        if (xhr.status == 200) {
								        	$("#login-err").css("color", "green");
											$("#login-err").text("Giriş başarılı!");
											setTimeout(reLogin, 1000);
								        }
								        else if (xhr.status == 401) {
								        	$("#login-err").css("color", "rgb(240,80,80)");
											$("#login-err").text("Yanlış Bayi Numarası/Şifre.");
								        }
								        else {
								        	$("#login-err").css("color", "rgb(240,80,80)");
											$("#login-err").text("Sunucu ile bir sorun yaşandı.");
								        }
								    } 
								});
							//End API check
						}
						else {
							passElem.css("background", "rgba(220,120,120, 0.75)");
							$("#login-err").text("Şifre kısmı boş bırakılamaz!");
						}
					}
					else {
						numElem.css("background", "rgba(220,120,120, 0.75)");
						if (numElem.val() == "") $('#login-err').text('Bayi numarası boş bırakılamaz!');
						else $('#login-err').text('Bayi numarası geçersiz. Sadece sayılardan oluştuğuna emin olun.');
					}
				}
			/* - End Login Handler - */
		</script>
		<script src="JS/jquery.loadTemplate-1.4.4.min.js"></script>
		<script src="JS/popdown.js"></script>

	</body>
</html>