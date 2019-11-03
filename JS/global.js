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


	//index.php
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

	//end index.php

	//yonetici-siparis.php
		const table_header = $("#orders > tbody > tr")[0]; //The first tr, consisting of table headers (th blocks).
	var last_order_data; //keeps last retrieved orders array.

	function printOrdersToTable(orderArr) {
		$("#orders > tbody > tr").not(':first').remove(); //removes old entries
		for (var i = 0; i < orderArr.length; i++) {
			$("#orders").append(	"<tr class='cursor-pointer' onclick='OrderPanel(" + orderArr[i].id + ");'>" +
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
		$.getJSON( "API/ORDER/list.php", function( data ) {

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
		var bayi_obj = last_order_data.find(o => o.id === orderID);
		var img_src;

		if (bayi_obj.pp == false) img_src = "./IMG/default_order.png";
		else img_src = "./IMG/BAYI_PICS/" + bayi_obj.pp;

		var order_popup = new PopDown();
		var bayi_html = document.createElement('div');

		$(bayi_html).loadTemplate("templates/popups/bayi.html", {
			creation_date: bayi_obj.creation_date,
	        bayi_adi: bayi_obj.bayi_adi,
	        img_src: img_src,
	        email: bayi_obj.email,
	        telephone: bayi_obj.telephone,
	        address: bayi_obj.address,
	        orderID: orderID
	    }).appendTo(order_popup.elem); //one liner async to sync... I really hate JS promises. They are very helpfull, however I fail to understand every single time I try reading about them.

		order_popup.toggle();
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
			url: "API/USER/delete.php",
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
				console.log("We have our ID as: " + possible_id);
			}
		}
		else return false;
	});
	//end yonetici-siparis.php

	//yonetici-urunler.php
	(function () { //anon function to fetch products
		$.getJSON( "API/URUN/cek.php", function( data ) {
			for (var i = 0; i < data.length; i++) {
				$("#bloklar").append("<div class='blok blok-5' id='urun-" + i + "'><img src='./IMG/URUN/" + data[i].image_loc + "'><h4>" + data[i].name + "<br><small>Stokta " + data[i].stock + " adet</small></h4><a href='duzenle' style='display: block; margin-top: 10px; color: white; text-decoration: none;'><i class='fas fa-lg fa-edit'></i> Düzenle</a></div>");
			}
			$("#bloklar").append("<div class='blok blok-5' id='urun-ekle'><i style='margin-top: 10px;' class='fas fa-10x fa-plus-circle'></i><br><br>Ürün Ekle</div>");
		});
	})();
	//end yonetici-urunler.php

	//giris.php
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
	//end giris.php

	//yonetici-bayi.php

	const table_header = $("#users > tbody > tr")[0]; //The first tr, consisting of table headers (th blocks).
	var last_user_data; //keeps last retrieved users array.

	function printUsersToTable(userArr) {
		$("#users > tbody > tr").not(':first').remove(); //removes old entries
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
	//end yonetici-bayi.php