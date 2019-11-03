<div id="left-bar">
	<div class="user-picture">
		<img src="<?php
			//Show user picture
			$files = glob("./IMG/BAYI_PICS/" . $_SESSION['bayi_gercek_id'] . ".*");
			if (count($files) > 0) {
				echo $files[0];
			}
			else {
				echo "./IMG/default_user.png";
			}
		?>">
	</div>

	<h4 class="bayi-adi">
		<?php
			echo $_SESSION['bayi_adi'];
			if ($_SESSION['privilage'] == 1) echo "<br><small>(Yönetici)</small>";
			else if ($_SESSION['privilage'] == 2) echo "<br><small>(Yetkili)</small>";
		?>
	</h4>

	<ul>
		<?php
			if ($_SESSION['privilage'] == 1) {//if admin
				echo '<li><a href="./anasayfa"><i class="fas fa-lg fa-home"></i><span>Anasayfa</span></a></li>';
				echo '<li><a href="./siparis"><i class="fas fa-lg fa-people-carry"></i><span>Siparişler</span></a></li>';
				echo '<li><a href="./urunler"><i class="fas fa-lg fa-boxes"></i><span>Ürünleri Düzenle</span></a></li>';
				echo '<li><a href="./bayi"><i class="fas fa-lg fa-address-book"></i><span>Bayi İşlemleri</span></a></li>';
				echo '<li><a href="./kayitlar"><i class="fas fa-lg fa-indent"></i><span>İşlem Kayıtları</span></a></li>';
				echo '<li><a href="./ayarlar"><i class="fas fa-lg fa-cog"></i><span>E-Bayi Ayarları</span></a></li>';
				echo '<li><a href="./API/GIRIS/cikis.php"><i class="fas fa-lg fa-sign-out-alt"></i><span>Çıkış</span></a></li>';
			}
			else if ($_SESSION['privilage'] == 2) {//if mod
				echo '<li><a href="./anasayfa"><i class="fas fa-lg fa-home"></i><span>Anasayfa</span></a></li>';
				echo '<li><a href="./siparis"><i class="fas fa-lg fa-people-carry"></i><span>Siparişlerim</span></a></li>';
				echo '<li><a href="./urunler"><i class="fas fa-lg fa-boxes"></i><span>Ürünlerimiz</span></a></li>';
				echo '<li><a href="./bayi"><i class="fas fa-lg fa-address-card"></i><span>Bayi Profili</span></a></li>';
				echo '<li><a href="./API/GIRIS/cikis.php"><i class="fas fa-lg fa-sign-out-alt"></i><span>Çıkış</span></a></li>';
			}
			else {
				echo '<li><a href="./anasayfa"><i class="fas fa-lg fa-home"></i><span>Anasayfa</span></a></li>';
				echo '<li><a href="./siparis"><i class="fas fa-lg fa-people-carry"></i><span>Siparişlerim</span></a></li>';
				echo '<li><a href="./urunler"><i class="fas fa-lg fa-boxes"></i><span>Ürünlerimiz</span></a></li>';
				echo '<li><a href="./bayi"><i class="fas fa-lg fa-address-card"></i><span>Bayi Profili</span></a></li>';
				echo '<li><a href="./API/GIRIS/cikis.php"><i class="fas fa-lg fa-sign-out-alt"></i><span>Çıkış</span></a></li>';
			}
		?>
	</ul>

	<footer>
		<a href="https://durasoftware.com/hizmetler/e-bayi">E-Bayi&copy;</a> <a href="https://durasoftware.com"><b>durasoft</b> Yazılım</a> 2018-2019	
	</footer>
</div>