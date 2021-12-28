<?php
include_once "includes/functions.php";


// если в $_GET содержится нужный ключ, и он не пуст, то перезаписываем $id
if(isset($_GET['id']) && !empty($_GET['id'])) {
  $id = $_GET['id'];
} elseif(checkLogin()) { 
  //если пользователь авторизован, в $_SESSION['id'] есть значение
  $id = $_SESSION['userId'];
} else $id = 0;// функция get_posts адекватно воспринимает 0, возвращает посты всех пользоваталей

$posts = get_posts($id); // получаем посты и кладём в переменную

if($id > 0) {
  $title = "Твиты пользователя @" . $posts[0]["login"]; 
} else $title = "Твиты пользователей";

include_once "includes/header.php";
include_once "includes/posts.php";
include_once "includes/footer.php";
?>