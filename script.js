$(document).ready(function(){

	var valueSelected0 = $('select[name="user_region"]').val();
	if (undefined !== valueSelected0) {
		$.post( "/ajax.php", { getRegionChild: valueSelected0})
			.done(function( data ) {
				$('select[name="user_city"]').html(data);
				$('select[name="user_city"]').trigger("chosen:updated");
			});
	}
	
    $('select[name="user_region"]').on('change', function (e) {
		var optionSelected = $("option:selected", this);
		var valueSelected = this.value;
		$.post( "/ajax.php", { getRegionChild: valueSelected })
		  .done(function( data ) {
			$('select[name="user_city"]').html(data);
			$('select[name="user_city"]').trigger("chosen:updated");
		});
	});
	
	if (navigator.userAgent.indexOf("MSIE 10") > -1) {document.body.classList.add("ie10");}
	
	$('.slickSelect').chosen({no_results_text: "Nerasta"}); 
	
	var wind = $(window).height();
	var docm = $('body').height();
	if(wind > docm) {
		$('#push').height(wind-docm);
	}

	if ($(document.getElementById("clamp1")).length) {
		$clamp(document.getElementById("clamp1"), {clamp: 3});
		$clamp(document.getElementById("clamp2"), {clamp: 3});
		$clamp(document.getElementById("clamp3"), {clamp: 3});
	}
});