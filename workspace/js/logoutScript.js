/*global $*/
/*global img*/

$(document).ready(function() {
	$("#logoutButton").on("click", function() {
		var jsonToSend = {
			"action" : "LOGOUT"
		};

		var $category = $(".categoryName");
		console.log($category.html());

	    var finalurl = "data/applicationLayer.php";
	    if ($category.html() != "all" && $category.html() != "popular"
	     && $category.html() != "unpopular" && $category.html() != "controversial"
	     && $category.html() != "oldest") {
	        finalurl = "../" + finalurl;
	    }

		$.ajax({
		    url : finalurl,
		    type : "POST",
		    data : jsonToSend,
		    dataType : "json",
		    contentType : "application/x-www-form-urlencoded",
		    success : function(jsonResponse) {
		    	var $category = $(".categoryName");
	            var replaceLocation = "";
	            if ($category.html() == "all" || $category.html() == "popular"
	            || $category.html() == "unpopular" || $category.html() == "controversial"
	            || $category.html() == "oldest") {
	                replaceLocation = "index.html";
	            } else {
	                replaceLocation = "../index.html";
	            }
	            window.location.replace(replaceLocation);
		    },
		    error : function(errorMessage) {
		        alert(errorMessage.status);
		        var $category = $(".categoryName");

	            var replaceLocation = "";
	            if ($category.html() == "all" || $category.html() == "popular"
	            || $category.html() == "unpopular" || $category.html() == "controversial"
	            || $category.html() == "oldest") {
	                replaceLocation = "index.html";
	            } else {
	                replaceLocation = "../index.html";
	            }
	            window.location.replace(replaceLocation);
		    }
		});
	});
});