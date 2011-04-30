function getQuery(param) {
	var url = window.location.toString();
	url.match(/\?(.+)$/);
	var params = RegExp.$1;
	var params = params.split("&");
	for (i = 0; i < params.length; i++) {
		var tmp = params[i].split("=");
		if (tmp[0] == param) return unescape(tmp[1]);
	}
}