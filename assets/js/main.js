$(function () {
	$.nette.init();

	$('a[data-confirm]').on('click', function() {
		return window.confirm('Opravdu?');
	});

	var anchor = document.location.hash;
	if (anchor) {
		$('.nav-tabs a[href="#' + anchor.split('#')[1] + '"]').tab('show');
	}

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		if (history.replaceState) {
			history.replaceState(null, null, e.target.href);
		} else {
			document.location.hash = e.target.href.substring(e.target.href.indexOf("#") + 1);
		}
	})

});
