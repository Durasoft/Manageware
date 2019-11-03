/*
	author: Arda Ntourali
	github: @rdadrl
	date: it's a pretty old project
	

  Popdown.js
    A simple popup creator with shadow.
    
    simply, create a new variable and set it as a new PopDown() object, Eg.
    
    var my_popup = new PopDown();
		my_popup.toggle(); //displays on/off
		my_popup.destroy(); //destroys the parent object,
		my_popup.append(); re-append the div and also the shadow.
		my_popup.zIndex(); Returns current zIndex.
		my_popup.zIndex(2); Sets current zIndex as 2
		
		Popups Shadow always have -1 zIndex of parent div.
		To destroy shadow, simply call my_popup.shadow.destroy();
		You also can destroy/append/zIndex shadow just like parent popup
*/

const PopDowns = []; //array which holds all popdowns created.
function DestroyPopDowns() {
	for (var i = PopDowns.length - 1; i >= 0; i--) {
		PopDowns[i].destroy();
	}
}

function PopDown() {
	var self = this; //this was fairly easy... added this var in order to reach parent obj from shadow

	this.elem = document.createElement('div');
	this.elem.className = "popup";

	//set default attributes
	this.elem.style.display = "none";
	this.elem.style.zIndex = 2; //default nominal z value. this gets bigger and bigger everytime a new popup is created.
	if (PopDowns.length > 0) this.elem.style.zIndex = PopDowns[PopDowns.length - 1].zIndex() + 2; //add 2 per new PopDowns
	//end setting default attributes

	this.toggle = function () {
		if (this.elem.style.display == "none") {
			$(this.elem).fadeIn("fast");
			return true;
		}
		else {
			$(this.elem).fadeOut("fast", function() {
				this.elem.style.dsplay = "none";
			});
			return false;
		}
	};

	this.destroy = function () {
		this.shadow.destroy();
		$(this.elem).fadeOut("fast", function() {
			this.remove();

			var index = PopDowns.indexOf(self);
			PopDowns.splice(index, 1);
		});
		return true;
	};
	this.append = function () {
		if(document.body.contains(this.elem)) return false;
		else {
			this.shadow.init();
			document.body.appendChild(this.elem);
			return this.elem;
		}
	};
	//if no parameter is passed, returns current zIndex. else, sets it.
	this.zIndex = function () {
		if (arguments.length == 0) return parseInt(this.elem.style.zIndex);
		else {
			this.elem.style.zIndex = arguments[0];
			this.shadow.zIndex(arguments[0] - 1);
			return true;
		}
	};
	//actual styling:
	this.shadow = {
		elem: document.createElement('div'),
		zIndex: function() {
			if (arguments.length == 0) return parseInt(this.elem.style.zIndex.split(" ")[0]);
			else {
				this.elem.style.cssText += 'z-index: ' + arguments[0] + ' !important;';
				return true;
			}
		},
		destroy: function () {
			$(this.elem).fadeOut("fast", function() {
				this.remove();
			});
			return true;
		},
		append: function () {
			if(document.body.contains(this.elem)) return false;
			else {
				document.body.appendChild(this.elem);
				return this.elem;
			}
		},
		init: function() {
			this.elem.onclick = function() { self.destroy(); return true; } //destroy the parent container, which also destroys the whole shadow
			this.zIndex(self.zIndex() - 1);
			this.elem.style.backgroundColor = "rgba(30,30,30, 0.5)";
			this.elem.style.cssText += "position: fixed !important;";
			this.elem.style.width = "100%";
			this.elem.style.height = "100%";
			this.append();
		}
	};
	this.append(); //calling append function by default
	PopDowns.push(this); //add to master arr
	return this;
}

document.addEventListener("keyup", function(e) {
	if (e.keyCode == 27 && PopDowns.length > 0) {
		for (var i = PopDowns.length - 1; i >= 0; i--) {
			//remove only the top-most PopDown
			if (PopDowns[i].elem.style.display != "none") {
				PopDowns[i].destroy();
				break;
			}
		}
	}
});