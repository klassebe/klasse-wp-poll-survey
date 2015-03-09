jQuery(document).ready(function($) {
	// send id before unload to unlock editing for other users
	window.onbeforeunload = function(event) {
		var url = kwpsInfo.adminurl + "admin-ajax.php?action=kwps_unlock_test";
		var entry = {
			ID : parseInt(kwpsInfo.id)
		};
		console.log(entry);
		$.ajax({
			type: "POST",
			url: url,
			data: JSON.stringify(entry),
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			success: function(data) {
				console.log(data);
				console.info(data.data.message);
			},
			failure: function (err) {
				console.error(err);
			}
		});
	};
});
