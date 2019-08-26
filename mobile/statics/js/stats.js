window.onload=function(){
	var b = new Base64(),
		u = encodeURI(b.encode(window.location.href)),
		r = encodeURI(b.encode(document.referrer));
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp=new XMLHttpRequest();
	} else {
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("GET","/stats/v.php?u="+u+"&r="+r,true);
	xmlhttp.send();
};