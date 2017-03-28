$(function () {
	$.nette.init();

	$('a[data-confirm]').on('click', function() {
		return window.confirm('Opravdu?');
	});

});
