<?php
	if (isset($_POST['saveConf'])) { //if save button is pressed
		$conf_obj['title'] = $_POST['title'];
		$conf_obj['welcoming_txt'] = $_POST['welcoming_txt'];
		$conf_obj['ver'] = $_POST['ver'];

		saveNewConf($conf_obj);
	}
?>
<div class="row">
		<div class="shadow panel" id="general-settings">
			<div class="margin-box">
				<div class="row">
					<h3>Genel Ayarlar</h3>

					<form method="POST">
						<label for="title">Site Başlığı</label>
						<br>
						<input class="shadow" type="text" name="title" placeholder="<?=$conf_obj['title']?>" value="<?=$conf_obj['title']?>">

						<br>

						<label for="welcoming_txt">Karşılama Mesajı</label>
						<br>
						<input class="shadow" type="text" name="welcoming_txt" placeholder="<?=$conf_obj['welcoming_txt']?>" value="<?=$conf_obj['welcoming_txt']?>">

						<br>

						<label for="ver">Versiyon</label>
						<br>
						<input class="shadow" type="text" name="ver" placeholder="<?=$conf_obj['ver']?>" value="<?=$conf_obj['ver']?>">
						<br>
						<input type="submit" name="saveConf" value="Kaydet">
					</form>
				</div>
				<div class="row">
					<h3>E-Bayi Kayıtları</h3>
					<p>
						E-Bayi kayıtlarını indirip sistemde oluşan hata kodlarını, hatalı giriş denemelerini ve bunun gibi bilgileri bulabilirsiniz. Durasoft'a destek talebinde bulunurken bu dosyayı eklemeyi unutmayınız.
					</p>
					<div style="text-align: center;">
						<button style="text-align: center;" onclick="window.open('API/USER/download_logs.php', '_blank');"><i class="fas fa-lg fa-indent"></i>Kayıtları İndir</button>
					</div>
				</div>
			</div>
		</div>

		<div class="shadow panel" id="variable-list">
			<div class="margin-box">
				<h3>Kullanabilen Değişkenler</h3>
				<table class="siparisler">
					<thead>
					  <tr>
					  	<th>Değişken</th>
					    <th>Değer</th>
					  </tr>
					</thead>
					<tbody>
					  <?php
					  	foreach ($varset as $var) {
							echo "<tr>";
								echo "<td>%" . $var->key . "%</td>";
								echo "<td>" . $var->val . "</td>";
							echo "</tr>";
						}
					  ?>
					</tbody>
				</table>
				<p class="text-center">
					<i class="fas fa-exclamation"></i> Bu değişkenleri modifiye etmek/yenisini eklemek için <a href="mailto:arda.ntourali@Durasoftware.com">Durasoft Yazılım</a>'a detaylı bir şekilde e-posta atınız.
				</p>
			</div>
		</div>
</div>