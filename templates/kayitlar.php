<div style="margin: 10px;">
	<h2 style="font-weight: 200;"><i class="fas fa-lg fa-indent"></i> Kayıtlar <small style="font-size: 0.8em;">son 100 işlem</small></h3>

		<table style="background: white;">
		  <tr>
		  	<th>Tarih</th>
		  	<th>Kullanıcı</th>
		    <th>IP Adresi</th>
		    <th>İşlem</th> 
		    <th>Açıklama</th>
		  </tr>
		  <?php
		  		$db = createDB("USERNAME", "PASSWORD", "DB NAME");
			  	$statement = $db->query("SELECT * FROM logs ORDER BY id DESC LIMIT 100;");
				$statement->setFetchMode(PDO::FETCH_ASSOC);

				if ( $statement->rowCount() ){
				     foreach( $statement as $row ){
				        if ($row['user_id'] == "") $row['user_id'] = "-";
						echo "<tr>";
					  	echo "<td>" . $row['tarih'] . "</td>";
					  	echo "<td>" . $row['user_id'] . "</td>";
					  	echo "<td>" . $row['ip'] . "</td>";
					  	echo "<td>" . $row['process'] . "</td>";
					  	echo "<td>" . $row['description'] . "</td>";
					  	echo "</tr>";
				    }
				}
		  ?>
		</table>
</div>