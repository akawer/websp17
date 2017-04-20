/*global $*/
/*global img*/

$(document).ready(function() {

    var $category = $(".categoryName");

    // Load comments
    var loadCommentsJson = {
        "action" : "LOAD_COMMENTS",
        "category" : $category.html()
    };

    var finalurl = "data/applicationLayer.php";
    if ($category.html() != "all" && $category.html() != "popular"
     && $category.html() != "unpopular" && $category.html() != "controversial"
     && $category.html() != "oldest") {
        finalurl = "../" + finalurl;
    }

    $.ajax({
        url : finalurl,
        type : "POST",
        data: loadCommentsJson,
        dataType : "json",
        contentType : "application/x-www-form-urlencoded",
        success : function(data) {
            var newHtml = "";
            $.each(data.comments, function(i, item) {
                // console.log(item.comment + ", " + item.category + ", " + item.votes + ",id: " + item.id);
                if ($category.html() == "oldest") {
                    newHtml += "<div class='col-sm-9'><div class='well'><p>" + item.comment
                    + "</p><a id='"
                    + item.id + "up' href='javascript:UPVOTE("+item.id
                    +");' ><i class='fa fa-thumbs-o-up fa-fw'></i></a>\tVotes: <span id='votes"+item.id+"'>"
                    +item.votes+"</span>\t<a id='"
                    + item.id + "down' href='javascript:DOWNVOTE("+item.id
                    +");'><i class='fa fa-thumbs-o-down fa-fw'></i></a>"
                    + "<p>" + item.category + "</p></div></div>"
                } else {
                    newHtml = "<div class='col-sm-9'><div class='well'><p>" + item.comment
                    + "</p><a id='"
                    + item.id + "up' href='javascript:UPVOTE("+item.id
                    +");' ><i class='fa fa-thumbs-o-up fa-fw'></i></a>\tVotes: <span id='votes"+item.id+"'>"
                    +item.votes+"</span>\t<a id='"
                    + item.id + "down' href='javascript:DOWNVOTE("+item.id
                    +");'><i class='fa fa-thumbs-o-down fa-fw'></i></a>"
                    + "<p>" + item.category + "</p></div></div>"
                    + newHtml;
                }
            });
            $(".col-sm-7").html(newHtml);
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