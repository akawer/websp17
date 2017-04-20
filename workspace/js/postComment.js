/*global $*/
/*global img*/

$(document).ready(function() {
    // Post a comment
    $("#submitButton").on("click", function() {
        var $comment = $("#commentText");
        var $category = $("#commentCategory");

        if ($comment.val() != "" && $category.val() != "") {
            var jsonObject = {
                "action" : "POST_COMMENT",
                "commentText" : $comment.val(),
                "commentCategory" : $category.val()
            };
            
            $.ajax({
                url : "data/applicationLayer.php",
                type : "POST",
                data : jsonObject,
                dataType : "json",
                contentType : "application/x-www-form-urlencoded",
                success : function(data) {
                    console.log($category.val() + ": " + $comment.val());
                    // $("#commentSection").append("<li>" +
                    // data.category + ": " + data.comment + "</li>");
                    window.location.replace("home.html");
                },
                error : function(errorMessage) {
                    alert(errorMessage.status);
                    window.location.replace("index.html");
                }
            });
        }
    });
});