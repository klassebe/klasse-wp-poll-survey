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
					  	"post_status": "publish",
					  	"post_title": "Entry"
				  	}

				  	$.ajax({
						    type: "POST",
						    url: "wp-admin/admin-ajax.php?action=kwps_save_entry",
						    // The key needs to match your method's input parameter (case-sensitive).
						    data: JSON.stringify({ Entries: entry }),
						    contentType: "application/json; charset=utf-8",
						    dataType: "json",
						    success: function(data){alert(data);},
						    failure: function(errMsg) {
						        alert(errMsg);
						    }
						});
				  // Send the data using post
				  // var posting = $.post( url, {  
				  	
				  	
				  // } );
				 
				  // // Put the results in a div
				  // posting.done(function( data ) {
				  //   elem.find('.kwps-content').hide();
						// elem.find('.kwps-outro-inside').html(data);
						// elem.find('.kwps-outro').show();

						// console.log('verzonden data');
						// console.log(data);
				  // });

				// $('.kwps-intro').hide();
				// $('.kwps-outro').show();
				// $('.kwps-content').hide();
			});
			



		});

	};

	$('.kwps_poll').pollPlugin();
});