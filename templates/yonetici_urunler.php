<div style="margin: 10px;">
	<h3 style="font-weight: 200;"><i class="fas fa-lg fa-boxes"></i> Ürün İşlemleri</h3>

	<div>
			<label for="siralama">Sırala:</label>
			<select id="sortProductsBySelect" onchange="refreshProducts(this.value)">
			  <option value="tarih-eskiden">Kategori: Hepsi</option>
			  <option value="tarih-yeniden">Kategori: Tatlı</option>
			  <option value="tarih-yeniden">Kategori: Tuzlu</option>
			  <option value="tarih-yeniden">Kategori: Süt Ürünleri</option>
			  <option value="tarih-yeniden">Kategori: Dondurma</option>
			</select>
			<select id="fetchProductsBySelect" onchange="refreshProducts($('#sortProductsBySelect').val())">
				<option value="0">Hepsini Listele</option>
				<option value="1">Durum: Stokta</option>
				<option value="2">Durum: Stokta Yok</option>
			</select>

			<button onclick="refreshProducts($('#sortProductsBySelect').val())"><i class="fas fa-sync"></i> Yenile</button>
		</div>

		<table id="products" style="background: white; max-height: 80%;">
			<thead>
			  <tr>
			  	<th>Ürün Kodu</th>
			    <th>Adı</th>
			    <th>Stok Durumu</th>
			    <th>Mevcut Fiyatı</th> 
			    <th>Vergi Oranı</th>
			  </tr>
			</thead>
			<tbody>

			</tbody>
		</table>


		<div class="shadow panel">
		<div class="margin-box">
			<h3>
				Yeni Ürün Kaydı
			</h3>
			<?php
				if(isset($_SESSION['new_urun_error'])) {
					if ($_SESSION['urun'] == true) echo "<p style='text-align: center; color: green;'>Başarıyla yeni ürün yaratıldı!</p>";
					else echo "<p style='text-align: center; color: red;'>Ürün yaratılırken bir sorun oluştu.</p>";
					unset($_SESSION['new_urun_error']);
				}
			?>
			<form action="./API/URUN/new.php" method="POST" enctype="multipart/form-data">
				<div class="inline-dynamic" style="text-align: center;">
					<img id="new_img" height="120px" src="./IMG/default_product.png">
					<br>
					<input type='file' onchange="readImg(this)" name="img" />
				</div>
				<div class="inline-dynamic">
					<input type="text" name="urun_name" placeholder="Ürün Adı" required>
					<br>
					<input type="number" min="0" step="any" name="urun_fiyat" onkeydown="if (this.style.background != 'white') this.style.background = 'white'; $('#login-err').text('&nbsp');" placeholder="Ürün Fiyatı" required>
					<br>
					<input type="number" min="0" step="1" name="urun_stok" onkeydown="if (this.style.background != 'white') this.style.background = 'white'; $('#login-err').text('&nbsp');" placeholder="Stok Miktarı" required>
					<br>
					<select name="bayi_privilage" style="width: 100%;">
						<option value="3">Tatlı</option>
						<option value="2">Tuzlu</option>
						<option value="1">Süt Ürünleri</option>
						<option value="1">Dondurma</option>
					</select>
				</div>
				<div class="inline-dynamic">
					<textarea name="urun_aciklama" placeholder="Ürün Açıklaması" style="height: 120px;"></textarea>
				</div>
				<br>
				<div class="inline-dynamic" style="width: 100%; text-align: right;">
					<span id="login-err">&nbsp;</span><input disabled type="submit" value="Ürün Oluştur (versiyon 1,4)" onclick="createBayi()">
				</div>
			</form>
		</div>
	</div>


</div>
	<script>
		var last_product_data; //keeps last retrieved orders array.

	function printProductsToTable(productArr) {
		$("#products > tbody > tr").remove(); //removes old entries
		for (var i = 0; i < productArr.length; i++) {
			var stok;
			if (productArr[i].stock > 0) stok = "<span style='color: green;'>Var (<i>" + productArr[i].stock + " adet</i>)</span>";
			else stok = "<span style='color: red;'>Yok</span>";
			$("#products").append("<tr class='cursor-pointer' onclick='ProductPanel(" + productArr[i].id + ");'>" +
				"<td>" + productArr[i].id + "</td>" +
				"<td>" + productArr[i].name + "</td>" +
				"<td>" + stok + "</td>" +
				"<td>10₺ <i>(+KDV 10,8₺)</i></td>" +
				"<td>8%</td></tr>"										
			);
		}
	}
	function refreshProducts(sort = "tarih-eskiden") {
		NProgress.start(); //starts the top progress bar.

		$.getJSON("API/URUN/cek.php", function( data ) {

			switch (sort) {
				case "tarih-eskiden":
					break;
				case "tarih-yeniden":
					data = data.reverse();
					break;
				default:
					break;
			}
			last_product_data = data;
			printProductsToTable(data);
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
	refreshProducts();

	function init () {
		refreshProducts();
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

	function ProductPanel(userID) {
		var urun_popup = new PopDown();
		var urun_html = document.createElement('div');

		$(urun_html).loadTemplate("templates/popups/urun.html", {}).appendTo(urun_popup.elem); //one liner async to sync... I really hate JS promises. They are very helpfull, however I fail to understand every single time I try reading about them.
		urun_popup.toggle();
	}
	</script>