<?php

include_once "includes/functions.php";

if(!checkLogin()) redirect();

$title = 'Понравившиеся посты';
$err = get_error_message();

$posts = get_liked_posts(); // получаем посты и кладём в переменную

include_once "includes/header.php";
include_once "includes/posts.php";
include_once "includes/footer.php";
?>
