<div class="margin-box">
	<?php 
		if (strlen(WELCOMING_TXT) != 0) echo '<h2 class="text-weight-2">' . WELCOMING_TXT . '<small id="zaman">00/00/0000 00.00</small></h3>';
	?>

	<div class="row">
		<div class="shadow panel">
			<div class="margin-box">
				<h3>
					Cevapsız Siparişler
				</h3>
				<table class="siparisler">
					<thead>
					  <tr>
					  	<th>Tarih</th>
					    <th>Sipariş Veren</th>
					    <th>Toplam Tutar</th>
					    <th>Sipariş Durumu</th>
					    <th>İşlemler</th>
					  </tr>
					</thead>

					<tbody>

					</tbody>
				</table>
			</div>
		</div>

		<div class="shadow panel">
			<div class="margin-box">
				<h3>
					Son Hareketler
					<br>
					<small>
						günlük
					</small>
				</h3>

				<div class='blok blok-8 text-center'>
					<h1>550₺</h1>satış
				</div>

			</div>
		</div>
	</div>

</div>
<script>
	function update_time() {
		var date = new Date();
		date.getHours() + "." + date.getMinutes()
		var tarih = (date.getMonth() + 1) + '.' + date.getDate() + '.' + date.getFullYear();

		if (date.getHours() < 10) var saat = "0" + date.getHours() + "." + date.getMinutes();
		else var saat = date.getHours() + "." + date.getMinutes();
		$("#zaman").text(tarih + " " + saat);

		return tarih + " " + saat;
	}
	update_time();
	setInterval(update_time, 10000);
	function checkSiparis() {
		NProgress.start();
		$.getJSON( "API/ORDER/list.php?method=4", function( data ) {
			$(".siparisler > tbody > tr").remove();
			for (var i = 0; i < data.length; i++) {

				switch(data[i].status) {
					case 4:
						data[i].status = "Cevapsız";
						break;
					case 3:
						data[i].status = "Sipariş Hazırlanıyor";
						break;
					case 2:
						data[i].status = "Kurye ile Gönderildi";
						break;
					case 1:
						data[i].status = "Tamamlandı";
						break;
				}

				$(".siparisler > tbody").append("<tr><td>" + data[i].tarih + "</td><td>" + data[i].bayi_num + "</td><td>" + data[i].toplam_tutar + "₺</td><td>" + data[i].status + "</td><td><a style='color: green; text-align: center;' href='siparis#" + data[i].id + "'><i class='fas fa-arrow-alt-circle-right fa-2x'></i></a></td></tr>");
			}
			if (data.length == 0) {
				$(".siparisler > tbody").append("<h3 style='position: absolute; text-align: center; left: 0; right: 0;'>Tebrikler!<br><small>bütün siparişler ile ilgilenmişsiniz.</small></h3>");
			}
			NProgress.done();
		}).fail(function(jqXHR) {
			if (jqXHR.status == 401) {
				if (!reLoginStatus) reLogin(); //if not displayed
			}
			else {
				location.reload();
			}
			NProgress.done();
		});
	}
	checkSiparis();
	setInterval(checkSiparis, 10000); //check new orders every 10secs
	function init() {
		checkSiparis();
	}
</script>