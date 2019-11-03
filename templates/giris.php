<div id="giris-panel">
	<h2 style="text-align: center; margin-top: 20px;">Bayır<small>e-bayi</small></h2>
	<img src="./IMG/default_user.png">

	<div class="login-form">
		<input id="bayi_num" type="text" onkeydown="if (this.style.background != 'white') this.style.background = 'white'; $('#login-err').text('&nbsp');" name="id" placeholder="Bayi Numarası" required>
		<br>
		<input id="bayi_pass" type="password" onkeydown="if (this.style.background != 'white') this.style.background = 'white'; $('#login-err').text('&nbsp');" name="password" placeholder="************" required>
		<br>
		<input type="submit" onclick="bayi_login_refresh($('#bayi_num'), $('#bayi_pass'))" value="Giriş Yap">
		<br>
		<span id="login-err">&nbsp;</span>
	</div>

	<p>
		Bayilik kaydı veya yeni şıfre talebi için ofisimizi<br>
		<span class="telephone">0 (286) 123 456</span>
		<br>numaralı telefondan arayınız.
	</p>
</div>
<p style="text-align: right; margin-right: 25px;">
		<a href="https://durasoftware.com/hizmetler/e-bayi">E-Bayi&copy;</a> <a href="https://durasoftware.com"><b>durasoft</b> Yazılım</a> 2018-2019	
	</p>
<script>
	function bayi_login_refresh(numElem, passElem) {
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
					    	console.log(xhr.status);
					        if (xhr.status == 200) {
					        	$("#login-err").css("color", "green");
								$("#login-err").text("Giriş başarılı!");
								location.reload();
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

	$(function() { //add enter key function to the login form
        $("body").find('input').keypress(function(e) {
            // Enter pressed?
            if(e.which == 10 || e.which == 13) {
                bayi_login_refresh($('#bayi_num'), $('#bayi_pass'));
            }
        });
        $("#main-body").css("left", "0px");
	});
</script>
