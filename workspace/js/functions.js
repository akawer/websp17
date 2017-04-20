/*global $*/
/*global img*/

function submitToggle() {
	if ($('#submit').css('display') == 'none') {
		$('#submit').slideDown();
	} else {
		$('#submit').slideUp();
	}
}

function plusToggle(id) {
	if ($('#plus'+id).css('display') == 'none') {
		$('#plus'+id).slideDown();
	} else {
		$('#plus'+id).slideUp();
	}
}