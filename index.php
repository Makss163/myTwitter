<?php

use function PHPSTORM_META\type;

include_once "includes/functions.php";

if($_GET['sort'] == 1) {
  $posts = get_posts(0, true); // сортируем посты в обратном порядке
} else {
  $posts = get_posts(); // получаем посты по умолчанию от новых до старых
}

$title = 'Главная страница';
$err = get_error_message();

include_once "includes/header.php";

if(checkLogin()) {
  $avatarPath = $_SESSION['userAvatar'];
  include_once "includes/tweetForm.php";
}

include_once "includes/posts.php";
include_once "includes/footer.php";
?>
