<div style="margin: 10px;">
	<h2 style="font-weight: 200;"><i class="fas fa-lg fa-people-carry"></i> Siparişler</h2>

		<div>
			<label for="siralama">Sırala:</label>
			<select id="sortOrdersBySelect" onchange="refreshOrders(this.value)">
			  <option value="tarih-eskiden">Tarih: Eskiden Yeniye</option>
			  <option value="tarih-yeniden">Tarih: Yeniden Eskiye</option>
			</select>
			<select id="fetchOrdersBySelect" onchange="refreshOrders($('#sortOrdersBySelect').val())">
				<option value="0">Hepsini Listele</option>
				<option value="4">Durum: Cevapsız</option>
				<option value="3">Durum: Sipariş Hazırlanıyor</option>
				<option value="2">Durum: Kurye ile Gönderildi</option>
				<option value="1">Durum: Tamamlandı</option>
			</select>

			<button onclick="refreshOrders($('#sortOrdersBySelect').val())"><i class="fas fa-sync"></i> Yenile</button>
		</div>

		<table id="orders" style="background: white; max-height: 80%;">
			<thead>
			  <tr>
			  	<th>Sipariş Numarası</th>
			    <th>Tarih</th>
			    <th>Bayi Adı</th>
			    <th>Toplam Tutar</th> 
			    <th>Durum</th>
			  </tr>
			</thead>
			<tbody>

			</tbody>
		</table>
</div>

<script>
	var last_order_data; //keeps last retrieved orders array.

	function printOrdersToTable(orderArr) {
		$("#orders > tbody > tr").remove(); //removes old entries
		for (var i = 0; i < orderArr.length; i++) {
			$("#orders").append("<tr class='cursor-pointer' onclick='OrderPanel(" + orderArr[i].id + ");'>" +
				"<td>" + orderArr[i].id + "</td>" +
				"<td>" + orderArr[i].tarih + "</td>" +
				"<td>" + orderArr[i].bayi_adi + "</td>" +
				"<td>" + orderArr[i].toplam_tutar + "</td>" +
				"<td>" + orderArr[i].status + "</td></tr>"										
			);
		}
	}
	function refreshOrders(sort = "tarih-eskiden") {
		NProgress.start(); //starts the top progress bar.

		$.getJSON( "API/ORDER/list.php?method=" + $("#fetchOrdersBySelect").val(), function( data ) {

			switch (sort) {
				case "tarih-eskiden":
					break;
				case "tarih-yeniden":
					data = data.reverse();
					break;
				default:
					break;
			}
			last_order_data = data;
			printOrdersToTable(data);
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
	refreshOrders();

	function init () {
		refreshOrders();
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

	function OrderPanel(orderID) {
		var siparis_obj = last_order_data.find(o => o.id === orderID);
		if (typeof(siparis_obj) != "object") return false;

		$.getJSON( "API/USER/list.php?id=" + siparis_obj.bayi_num, function( user_data ) {
			user_data = user_data[0];
			var img_src;

			if (user_data.pp == false) img_src = "./IMG/default_user.png";
			else img_src = "./IMG/BAYI_PICS/" + user_data.pp;

		 	var order_popup = new PopDown();
			var bayi_html = document.createElement('div');

			$(bayi_html).loadTemplate("templates/popups/siparis.html", {
				status: siparis_obj.status + 1,
		        bayi_adi: user_data.bayi_adi,
		        img_src: img_src,
		        email: user_data.email,
		        telephone: user_data.telephone,
		        address: user_data.address,
		        orderID: orderID
		    }, {
		    	complete: function () {
		    		$(bayi_html).find("#SiparisPanelUrunTable").loadTemplate($(bayi_html).find("#tableContent"), siparis_obj.list, {
		    			complete: function () {
		    				$(bayi_html).appendTo(order_popup.elem);
		    			}
		    		});
		    	}
			});

			order_popup.toggle();
		});
	}
	function delete_warn(orderID) {
		var del_popup = new PopDown();

		var del_html = document.createElement('div');
		$(del_html).loadTemplate("templates/popups/delete_warning.html", {
			orderID: orderID
		}).appendTo(del_popup.elem);

		del_popup.toggle();
	}
	function delete_order(orderID) {
		console.log(orderID);

		$.ajax({
			url: "API/ORDER/delete.php",
			type: "POST",
			data: { id: orderID },
			dataType:"text xml",
			complete: function(xhr, textStatus) {
			    	DestroyPopDowns();
			    	init();
			}
		});
	}

	$( document ).ready(function() {
		var url = window.location.href;
		var url_arr = url.split("#"); //split from #
		if (url_arr.length === 2) { //We have a possible ID coming in.
			var possible_id = parseInt(url_arr[1]);

			if (isNaN(possible_id) || possible_id < 0) return false;
			else { //our id is a number for sure.
				var order_print_interval = setInterval(function(){ if (typeof(last_order_data) === "object") { OrderPanel(possible_id); clearInterval(order_print_interval); } }, 250);
			}
		}
		else return false;
	});
</script>