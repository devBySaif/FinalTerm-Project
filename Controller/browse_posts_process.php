<?php
include '../Model/mydb.php';  
session_start();

// CREATE FAKE SESSION
$_SESSION["user_id"] = 1;
$_SESSION["name"] = "Test User";
$_SESSION["role"] = "user";
$_SESSION["is_verified"] = 1;

$mydb = new MyDB();
$conn = $mydb->createConn();

// Handle AJAX Search
if(isset($_GET['action']) && $_GET['action'] == 'search'){
    header('Content-Type: application/json');
    
    $query = isset($_GET['q']) ? $_GET['q'] : '';
    
    if(empty($query)){
        echo json_encode([]);
        $mydb->closeConn($conn);
        exit();
    }
    
    $result = $mydb->searchPosts($query, $conn);
    $posts = [];
    
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $posts[] = $row;
        }
    }
    
    echo json_encode($posts);
    $mydb->closeConn($conn);
    exit();
}

// Handle AJAX Filter
if(isset($_GET['action']) && $_GET['action'] == 'filter'){
    header('Content-Type: application/json');
    
    $country = isset($_GET['country']) ? $_GET['country'] : '';
    $genre = isset($_GET['genre']) ? $_GET['genre'] : '';
    $cost = isset($_GET['cost']) ? $_GET['cost'] : '';
    
    $result = $mydb->filterPosts($country, $genre, $cost, $conn);
    $posts = [];
    
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $posts[] = $row;
        }
    }
    
    echo json_encode($posts);
    $mydb->closeConn($conn);
    exit();
}

// Regular page load
$posts = $mydb->getApprovedPosts($conn);

// Get unique countries and genres
$countries = [];
$genres = [];

if($posts->num_rows > 0){
    foreach($posts as $post){
        if(!in_array($post['country'], $countries)){
            $countries[] = $post['country'];
        }
        if(!in_array($post['genre'], $genres)){
            $genres[] = $post['genre'];
        }
    }
}

// Reset pointer
$posts = $mydb->getApprovedPosts($conn);
?>