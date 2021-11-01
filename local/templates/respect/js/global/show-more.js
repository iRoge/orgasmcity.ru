$(document).on('click', '[data-show-more]', function(){
    var btn = $(this);
    var page = btn.attr('data-next-page');
    var id = btn.attr('data-show-more');
    var bx_ajax_id = btn.attr('data-ajax-id');

    var block_id = "#comp_"+bx_ajax_id;
    var button_id="#btn_"+bx_ajax_id;


	var container_class=".cnt_"+bx_ajax_id;	
    var append= (btn.data('append')==true)?true:false;
    var data = {};
    data['bxajaxid'] = bx_ajax_id;
    data['PAGEN_'+id] = page;

    //show_wait($(block_id));

    $.ajax({
        type: "GET",
        url: window.location.href,
        data: data,
        timeout: 5000,
        success: function(data) {
			//hide_wait();
			
        	if (append) {
        		var html_content=$('<div>'+data+'</div>').find(container_class).html();
        		var html_button=$('<div>'+data+'</div>').find(button_id).html();
                var html_paging=$('<div>'+data+'</div>').find("#paging_"+bx_ajax_id).html();
        		if (html_button==undefined)
					html_button='';

				$(container_class).append(html_content);	
				
				
				$(button_id).html(html_button);
                $("#paging_"+bx_ajax_id).html('');
                $("#paging_"+bx_ajax_id).html(html_paging);
			} else {
				$(button_id).remove();
				$("#paging_"+bx_ajax_id).remove();
				$(block_id).append(data);
			}
			$(window).trigger('ajaxLoad');
        }
    });
});