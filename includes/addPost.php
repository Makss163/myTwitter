<?php
include_once "functions.php";

// если неавторизованный пользователь каким-то образом добрался до данной страницы то отправляем его на главную
if(!checkLogin()) redirect();

//отправляем содержимое POST в функцию
if(isset($_POST['text']) && !empty($_POST['text'])) {
  
  if(!addPost($_POST)) {
    $_SESSION['error'] = 'При добавлении поста что-то пошло нетак';
    redirect();
  }
}
unset($_SESSION['error']);
redirect(get_url('userPosts.php'));