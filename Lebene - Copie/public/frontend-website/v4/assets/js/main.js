$(document).ready(function(){	
	$('.hamburger').on('click', function(e){
		$('nav').toggleClass('active');
		$('.hamburger').toggleClass('active');
	});

	$('.features-toggle').on('click', function(e){
		e.preventDefault();
	});
	
	$('.features-dropdown').on('click', function(e){
		$('.features-toggle').toggleClass('active');
		$('.features_nav').toggleClass('active');
	});

	if ($(window).width() > 1024) {
		$('.features-dropdown').hover(
			function() {
				$('.features-toggle').attr("aria-expanded", "true");
				$('.features-toggle').addClass('active');
				$('.features_nav').addClass('active');				
			},
			function() {
				$('.features-toggle').attr("aria-expanded", "false");
				$('.features-toggle').removeClass('active');
				$('.features_nav').removeClass('active');			
			}
		);	
	}

	window.onscroll = function () { stickyMenu() };
	var header = document.getElementById("header");
	var sticky = 50;
	function stickyMenu() {
		if (window.pageYOffset >= sticky) {
			header.classList.add("sticky");
		} else {
			header.classList.remove("sticky");
		}
	}
});

function showLanguageSelect() {
	$("#languageModal").slideDown();
}

function closeLanguageSelect() {
	$("#languageModal").slideUp();
}


