function addComment(postId) {
    var content = document.getElementById("commentContent").value;
    
    // Client-side validation
    if(content.trim() == "") {
        alert("Comment cannot be empty!");
        return;
    }
    
    if(content.length > 500) {
        alert("Comment is too long (max 500 characters)!");
        return;
    }
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            
            if(response.success) {
                var comment = response.comment;
                
                var commentDiv = document.createElement("div");
                commentDiv.className = "comment";
                commentDiv.id = "comment-" + comment.id;
                
                commentDiv.innerHTML = 
                    "<p class='comment-header'>" +
                    "<strong>" + comment.name + "</strong> " +
                    "<span class='comment-date'>- " + comment.created_at + "</span>" +
                    "</p>" +
                    "<p class='comment-content'>" + comment.content + "</p>" +
                    "<button class='delete-btn' onclick='deleteComment(" + comment.id + ")'>Delete</button>";
                
                var commentsList = document.getElementById("commentsList");
                
                if(commentsList.firstChild) {
                    commentsList.insertBefore(commentDiv, commentsList.firstChild);
                } else {
                    commentsList.appendChild(commentDiv);
                }
                
                document.getElementById("commentContent").value = "";
                alert("Comment added successfully!");
            } else {
                alert("Error: " + response.error);
            }
        }
    };
    
    xhttp.open("POST", "../Controller/post_detail_process.php", true);  // ← Fixed
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("action=add_comment&post_id=" + postId + "&content=" + encodeURIComponent(content));
}

function deleteComment(commentId) {
    if(!confirm("Are you sure you want to delete this comment?")) {
        return;
    }
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            
            if(response.success) {
                var commentDiv = document.getElementById("comment-" + commentId);
                if(commentDiv) {
                    commentDiv.remove();
                }
                alert("Comment deleted successfully!");
            } else {
                alert("Error: " + response.error);
            }
        }
    };
    
    xhttp.open("POST", "../Controller/post_detail_process.php", true);  // ← Fixed
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("action=delete_comment&comment_id=" + commentId);
}