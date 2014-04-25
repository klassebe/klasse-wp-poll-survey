'use strict';


jQuery(function($) {
	
	$('.kwps-outro').hide();
	$('.kwps-content').hide();

	$.fn.pollPlugin = function ( options ) {
		return this.each( function ( ) {
			var elem = $( this );

			elem.find('.kwps-next').on('click', function () {
				elem.find('.kwps-intro').hide();
				elem.find('.kwps-outro').hide();
				elem.find('.kwps-content').show();
				console.log('next was clicked');
			});

			//  Search for the class with the ID in it
			elem.find('input:radio').click( function () {

				var selected = $(this).val();
			    var url = "wp-admin/admin-ajax.php?action=kwps_save_entry";

			    var entry = {
				  		"post_parent": selected,
				  		"_kwps_sort_order": 0
				  	}

				  	$.ajax({
						    type: "POST",
						    url: "wp-admin/admin-ajax.php?action=kwps_save_entry",
						    data: JSON.stringify(entry),
						    contentType: "application/json; charset=utf-8",
						    dataType: "json",
						    success: function(data){
						    	// alert(data);
						    	// console.log(entry);
						    	console.log(data);
						    	$.each(data.entries, function(index, value) {
						    		// console.log(value);

						    		elem.find('.kwps-chart').append(value.answer_option_content + '<br><progress value="' + value.entry_count + '" max="' + data[0].total_entries + '"></progress><br>');
						    	});
						    },
						    failure: function(errMsg) {
						        alert(errMsg);
						    }
						});

				$('.kwps-intro').hide();
				$('.kwps-outro').show();
				$('.kwps-content').hide();
			});
			
		});

	};

	// TODO: Has to be attached to the id of every class 'kwps_poll'
	$('.kwps-poll').pollPlugin();
});