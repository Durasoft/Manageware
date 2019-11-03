<div style="margin: 10px;">
	<h2 style="font-weight: 200;"><i class="fas fa-lg fa-address-book"></i> Bayi İşlemleri</h3>

		<div>
			<label for="siralama">Sırala:</label>
			<select id="sortUsersBySelect" onchange="refreshUsers(this.value)">
			  <option value="tarih-eskiden">Tarih: Eskiden Yeniye</option>
			  <option value="tarih-yeniden">Tarih: Yeniden Eskiye</option>
			  <option value="yetki-yuksek">Yetki: Yüksekten Düşüğe</option>
			  <option value="yetki-dusuk">Yetki: Düşükten Yükseğe</option>
			</select>

			<button onclick="refreshUsers($('#sortUsersBySelect').val())"><i class="fas fa-sync"></i> Yenile</button>
		</div>
		<table id="users" style="background: white; max-height: 80%;">
			<thead>
			  <tr>
			  	<th>Bayi Adı<br><input type="text" onkeyup="printUsersToTable(last_user_data.filter(s => s.bayi_adi.toLowerCase().includes(this.value.toLowerCase())));" placeholder="ile ara"></th>
			    <th>Bayi Numarası<br><input type="text" onkeyup="printUsersToTable(last_user_data.filter(s => s.bayi_num.toString().includes(this.value)));" placeholder="ile ara"></th>
			    <th>Yetki</th>
			    <th>E-Posta<br><input type="text" onkeyup="printUsersToTable(last_user_data.filter(s => s.email.toLowerCase().includes(this.value.toLowerCase())));" placeholder="ile ara"></th> 
			    <th>Telefon<br><input type="text" onkeyup="printUsersToTable(last_user_data.filter(s => s.telephone.toLowerCase().includes(this.value.toLowerCase())));" placeholder="ile ara"></th>
			    <th>Adres<br><input type="text" onkeyup="printUsersToTable(last_user_data.filter(s => s.address.toLowerCase().includes(this.value.toLowerCase())));" placeholder="ile ara"></th>
			  </tr>
			</thead>
			<tbody>

			</tbody>
		</table>
		
		<div class="shadow panel">
		<div class="margin-box">
			<h3>
				Yeni Bayi Kaydı
			</h3>
			<?php
				if(isset($_SESSION['new_bayi_error'])) {
					if ($_SESSION['new_bayi_error'] == true) echo "<p style='text-align: center; color: green;'>Başarıyla yeni bayi yaratıldı!</p>";
					else echo "<p style='text-align: center; color: red;'>Bayi yaratılırken bir sorun oluştu.</p>";
					unset($_SESSION['new_bayi_error']);
				}
			?>
			<form action="./API/USER/new.php" method="POST" enctype="multipart/form-data">
				<div class="inline-dynamic" style="text-align: center;">
					<img id="new_img" height="120px" src="./IMG/default_user.png">
					<br>
					<input type='file' onchange="readImg(this)" name="img" />
				</div>
				<div class="inline-dynamic">
					<input type="text" name="bayi_name" placeholder="Bayi Adı" required>
					<br>
					<input type="email" name="bayi_email" onkeydown="if (this.style.background != 'white') this.style.background = 'white'; $('#login-err').text('&nbsp');" placeholder="E-Posta Adresi" required>
					<br>
					<input type="text" name="bayi_telephone" onkeydown="if (this.style.background != 'white') this.style.background = 'white'; $('#login-err').text('&nbsp');" placeholder="Telefon Numarası" required>
					<br>
					<select name="bayi_privilage" style="width: 100%;">
						<option value="3">Bayi</option>
						<option value="2">Görevli</option>
						<option value="1">Yönetici</option>
					</select>
				</div>
				<div class="inline-dynamic">
					<textarea name="bayi_address" placeholder="Adresi" style="height: 120px;"></textarea>
				</div>
				<br>
				<div class="inline-dynamic" style="width: 100%; text-align: right;">
					<span id="login-err">&nbsp;</span><input type="submit" value="Bayi Oluştur" onclick="createBayi()">
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	//Search strings: https://stackoverflow.com/questions/5424488/how-to-search-for-a-string-inside-an-array-of-strings
	//modified to accept key
	function searchStringInArray (str, strArray, key = null) {
	    for (var j=0; j<strArray.length; j++) {
	    	if (typeof(key) != null) {
	    		if (strArray[j][key].match(str)) return j;
	    	}
	        else if (strArray[j].match(str)) return j;
	    }
	    return -1;
	}

	var last_user_data; //keeps last retrieved users array.

	function printUsersToTable(userArr) {
		$("#users > tbody > tr").remove(); //removes old entries
		for (var i = 0; i < userArr.length; i++) {
			$("#users").append(	"<tr class='cursor-pointer' onclick='UserPanel(" + userArr[i].id + ");'>" +
				"<td>" + userArr[i].bayi_adi + "</td>" +
				"<td>" + userArr[i].bayi_num + "</td>" +
				"<td>" + userArr[i].privilage + "</td>" +
				"<td>" + userArr[i].email + "</td>" +
				"<td>" + userArr[i].telephone + "</td>" +
				"<td>" + userArr[i].address + "</td></tr>"											
			);
		}
	}
	function refreshUsers(sort = "tarih-eskiden") {
		NProgress.start(); //starts the top progress bar.
		$.getJSON( "API/USER/list.php", function( data ) {

			switch (sort) {
				case "tarih-eskiden":
					break;
				case "tarih-yeniden":
					data = data.reverse();
					break;
				case "yetki-yuksek":
					data = data.sort();
					break;
				case "yetki-dusuk":
					data = data.sort().reverse();
					break;
				default:
					break;
			}
			last_user_data = data;
			printUsersToTable(data);
			NProgress.done();
		}).fail(function(jqXHR) {
			NProgress.done();
			if (jqXHR.status == 401) {
				if (!reLoginStatus) reLogin(); //if not displayed
			}
			else {
				location.reload();
			}
		});
	}
	refreshUsers();

	function init () {
		refreshUsers();
	}

	function readImg(input) {
		if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#new_img').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
	}

	function UserPanel(userID) {
		var bayi_obj = last_user_data.find(o => o.id === userID);
		var img_src;

		if (bayi_obj.pp == false) img_src = "./IMG/default_user.png";
		else img_src = "./IMG/BAYI_PICS/" + bayi_obj.pp;

		var user_popup = new PopDown();
		var bayi_html = document.createElement('div');

		$(bayi_html).loadTemplate("templates/popups/bayi.html", {
			creation_date: bayi_obj.creation_date,
	        bayi_adi: bayi_obj.bayi_adi,
	        img_src: img_src,
	        email: bayi_obj.email,
	        telephone: bayi_obj.telephone,
	        address: bayi_obj.address,
	        userID: userID
	    }).appendTo(user_popup.elem); //one liner async to sync... I really hate JS promises. They are very helpfull, however I fail to understand every single time I try reading about them.

		user_popup.toggle();
	}
	function delete_warn(userID) {
		var del_popup = new PopDown();

		var del_html = document.createElement('div');
		$(del_html).loadTemplate("templates/popups/delete_warning.html", {
			userID: userID
		}).appendTo(del_popup.elem);

		del_popup.toggle();
	}
	function delete_user(userID) {
		console.log(userID);

		$.ajax({
			url: "API/USER/delete.php",
			type: "POST",
			data: { id: userID },
			dataType:"text xml",
			complete: function(xhr, textStatus) {
			    	DestroyPopDowns();
			    	init();
			}
		});
	}
</script>