function searchPosts() {
    var query = document.getElementById("search").value;
    
    if(query.length == 0) {
        location.reload();
        return;
    }
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            var posts = JSON.parse(this.responseText);
            displayPosts(posts);
        }
    };
    xhttp.open("GET", "../Controller/browse_posts_process.php?action=search&q=" + query, true);  
    xhttp.send();
}

function applyFilters() {
    var country = document.getElementById("countryFilter").value;
    var genre = document.getElementById("genreFilter").value;
    var costRadios = document.getElementsByName("costFilter");
    var cost = "";
    
    for(var i = 0; i < costRadios.length; i++) {
        if(costRadios[i].checked) {
            cost = costRadios[i].value;
            break;
        }
    }
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            var posts = JSON.parse(this.responseText);
            displayPosts(posts);
        }
    };
    
    var url = "../Controller/browse_posts_process.php?action=filter&country=" + country +   // ← Fixed
              "&genre=" + genre + "&cost=" + cost;
    xhttp.open("GET", url, true);
    xhttp.send();
}

function displayPosts(posts) {
    var grid = document.getElementById("postsGrid");
    grid.innerHTML = "";
    
    if(posts.length == 0) {
        grid.innerHTML = "<p>No posts found.</p>";
        return;
    }
    
    for(var i = 0; i < posts.length; i++) {
        var post = posts[i];
        var shortHistory = post.short_history.substring(0, 150) + "...";
        
        var postCard = "<div class='post-card'>" +
            "<h3>" + post.title + "</h3>" +
            "<p><strong>Country:</strong> " + post.country + "</p>" +
            "<p><strong>Genre:</strong> " + post.genre + "</p>" +
            "<p><strong>Cost Level:</strong> " + post.cost_level.charAt(0).toUpperCase() + post.cost_level.slice(1) + "</p>" +
            "<p>" + shortHistory + "</p>" +
            "<a href='../View/post_detail.php?id=" + post.id + "' class='read-more'>Read more</a>" +  // ← Fixed
            "</div>";
        
        grid.innerHTML += postCard;
    }
}