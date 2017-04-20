/*global $*/
/*global img*/

$(document).ready(function() {
	$("#loginButton").on("click", function() {
  	var $userName = $("#loginUserName");
  	var $password = $("#loginPassword");
    var verifyItesm = $userName.val().substring($userName.val().length-9, $userName.val().length);
    if (verifyItesm != "@itesm.mx") {
      alert("Email is not valid!");
    } else {
    	var jsonToSend = {
  	    "action" : "LOGIN",
        "username" : $userName.val(),
        "userPassword" : $password.val()
      };
    
      $.ajax({
          url : "data/applicationLayer.php",
          type : "POST",
          data : jsonToSend,
          dataType : "json",
          contentType : "application/x-www-form-urlencoded",
          success : function(jsonResponse){
              window.location.replace("home.html");
          },
          error : function(errorMessage){
              alert(errorMessage.status);
          }
      });
    }
	});

	$("#registerButton").on("click", function() {
		var $registerUserName = $("#registerUserName");
		var $registerPassword = $("#registerPassword");
		var $passwordConfirm = $("#registerConfirmPassword");
    var verifyItesm = $registerUserName.val().substring($registerUserName.val().length-9, $registerUserName.val().length);
		if ($registerPassword.val() != $passwordConfirm.val() || verifyItesm != "@itesm.mx") {
	    alert("Passwords do not match or email is not valid!");
		} else {
			var jsonObject = {
  	    "action" : "REGISTER",
        "username" : $registerUserName.val(),
        "userPassword" : $registerPassword.val()
      };
      
      $.ajax({
          url: "data/applicationLayer.php",
          type: "POST",
          data : jsonObject,
          dataType : "json",
          contentType : "application/x-www-form-urlencoded",
          success: function(jsonData) {
              window.location.replace("home.html"); 
          },
          error: function(errorMsg){
            alert(errorMsg.status);
          }
      });
		}
	});
});