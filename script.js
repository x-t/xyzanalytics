function filterSite() {
	var tablem = document.getElementsByTagName('table')[0];
	var filter = document.getElementById('site');
	var inps = tablem.getElementsByTagName('tr');
	var filtinit = document.getElementById('filtDom');
	var filt = document.getElementById('filtDomA');
	for (var i = 1; i < inps.length; i++) {
		var x = inps[i].getElementsByTagName('td');
		for (var ii = 0; ii < x.length; ii++) {
			var y = x[ii];
			if (y.getAttribute('data-domain') == "1") {
				if (y.innerHTML != filter.value)
					inps[i].style.display = 'none';
			}
		}
	}
	filtinit.style.display = 'none';
	filt.innerHTML = "Active filter: domain:" + filter.value + " <button onclick='remFilter()'>Remove</button>";
}

function remFilter() {
	var tablem = document.getElementsByTagName('table')[0];
	var inps = tablem.getElementsByTagName('tr');
	var filtinit = document.getElementById('filtDom');
	var filter = document.getElementById('site');
	var filt = document.getElementById('filtDomA');
	filt.innerHTML = "";
	filtinit.style.display = "block";
	filter.value = "";
	for (var i = 1; i < inps.length; i++) {
		inps[i].style.display = '';
	}
}

function hideBots() {
	var filtbot = document.getElementById('filtBot');
	if (filtbot.checked == true) {
		var tablem = document.getElementsByTagName('table')[0];
		var filter = document.getElementById('site');
		var inps = tablem.getElementsByTagName('tr');
		for (var i = 1; i < inps.length; i++) {
			var x = inps[i].getElementsByTagName('td');
			for (var ii = 0; ii < x.length; ii++) {
				var y = x[ii];
				if (y.getAttribute('data-isp') == "1") {
					if (y.innerHTML == "AS15169 Google LLC")
						inps[i].style.display = 'none';
                    else if (y.innerHTML == "AS30060 VeriSign Infrastructure & Operations") 
						inps[i].style.display = 'none';
                    else if (y.innerHTML == "AS13238 YANDEX LLC") 
						inps[i].style.display = 'none';
                    else if (y.innerHTML == "AS16276 OVH SAS") 
						inps[i].style.display = 'none';
                    else if (y.innerHTML == "AS8075 Microsoft Corporation")
						inps[i].style.display = 'none';
				}
			}
		}
	} else {
		var tablem = document.getElementsByTagName('table')[0];
		var inps = tablem.getElementsByTagName('tr');
		for (var i = 1; i < inps.length; i++) {
			inps[i].style.display = '';
		}
	}
}
