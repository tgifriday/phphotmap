var phpHotMapExamples = {
    client: new phpHotMapClient("/example/server.php"),
	
    serverTime: function(){
        var serverTime = this.client.getServerTime();
        $("#block-example-result").append("<p>The server time is " + serverTime + "</p>");
		document.getElementById("block-example-result").scrollTop = 99999;
    },
	
    myIP: function(){
        var myIP = this.client.getClientIP();
		$("#block-example-result").append("<p>Your IP is " + myIP);
		document.getElementById("block-example-result").scrollTop = 99999;
    },
	
	md5: function(){
		var v = $("#edtExampleValue").val();
		var md5 = this.client.Encryption.getMd5(v);
		$("#block-example-result").append("<p>The MD5 of '" + v + "' is <br> " + md5+ "</p>");
		document.getElementById("block-example-result").scrollTop = 99999;
	},
	sha1: function(){
		var v = $("#edtExampleValue").val();
		var sha1 = this.client.Encryption.getSha1(v);
		$("#block-example-result").append("<p>The SHA1 of '" + v + "' is <br> " + sha1+ "</p>");
		document.getElementById("block-example-result").scrollTop = 99999;
	},
	login: function(){
		var u = $("#edtExampleUser").val();
		var p = $("#edtExamplePass").val();
		var r = this.client.__login(u,p);
		
		if (r == PHP_HOT_MAP_LOGIN_FAILED){
			alert("Access denied [" + PHP_HOT_MAP_LOGIN_FAILED + "]");
		} else {
			$("#exampleLoginBox").fadeOut("medium");
			$("#exampleSecurityBox").fadeIn("medium");
		}
		document.getElementById("block-example-result").scrollTop = 99999;
	},
	logout: function(){
		this.client.__logout();
		$("#exampleLoginBox").fadeIn("medium");
		$("#exampleSecurityBox").fadeOut("medium");
	},
	privateData: function(){
		var v = this.client.getPrivateData();
		if (v == PHP_HOT_MAP_ACCESS_DENIED_USER) {
			alert("Access denied [" + PHP_HOT_MAP_ACCESS_DENIED_USER + "]");
		} else {
			$("#block-example-result").append("<p>" + v + "</p>");
		} 
		document.getElementById("block-example-result").scrollTop = 99999;
	}
}
