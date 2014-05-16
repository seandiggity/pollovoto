function toggleLogo() { //shows or hides the top logo when the toggle button is clicked (i.e. opens the responsive dropdown menu)
	var ele = document.getElementById("topLogo");
	var img = document.getElementById("topLogoImg");
	var lnk = document.getElementById("toggleButton");
	if(ele.style.display == "none") {
		ele.style.display = "block";
		/*lnk.innerHTML = "show";*/
		img.className="";
  	}
	else {
    		ele.style.display = "none";
		/*lnk.innerHTML = "hide";*/
		img.className="hid";
	}
} 

function showLogo() { //simple function to show the top logo
	var ele = document.getElementById("topLogo");
	ele.style.display = "block";
} 

function hideLogo() { //simple function to hide the top logo
	var ele = document.getElementById("topLogo");
	ele.style.display = "none";
} 

$( window ).resize(function() { //hides the top logo if the viewspace is less wide than 768px *if the toggle button has been clicked* (i.e. the responsive dropdown menu is open)
	var width = $( window ).width();
	var logo = document.getElementById("topLogo");
	var logoimg = document.getElementById("topLogoImg");
	if (width >= 768) {
		logo.style.display = "block";
	}
	if (width < 768) {
		if (logoimg.className == "hid") {
			logo.style.display = "none";
		}
	}
});
