$(document).ready(function(){
	$('.refresh').click(function(){
		$.ajax({
		  type: "POST",
		  url: "/local/ajax/ibcajax.php",
		  data: "",
		  success: function(msg){
			alert( "Прибыли данные: " + msg );
		  }
		});
	});
});