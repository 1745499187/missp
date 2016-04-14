function dotest() {
	alert("this is test");
}

function do_ajax(obj, href) {
//	alert(href);
	$.get(
			href,
			function(data) {
				$("div#test_area").html(data);
			}
	);
}