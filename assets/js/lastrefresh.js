var $lastrefresh = $('.js-lastRefresh');
var lastRefresh = new Date($lastrefresh.data('lastrefreshNow'));

function lastrefreshReload() {
	var now = new Date();
	var difference = Math.floor((now.getTime() - lastRefresh.getTime()) / 1000);
	$lastrefresh.text("-" + difference);
}

setInterval(lastrefreshReload, 1000);
