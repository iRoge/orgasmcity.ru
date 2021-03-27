$(document).ready(function(){
	$('select[name=filter_cities]').change(function(){
		$.cookie('FILTER_CITY', $(this).val().split('city_id=')[1]);
		location.reload($(this).val());
	});
});