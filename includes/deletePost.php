<?php
include_once "functions.php";

// если неавторизованный пользователь каким-то образом добрался до данной страницы то отправляем его на главную
if(!checkLogin()) redirect();

//отправляем содержимое POST в функцию
if(isset($_GET['id']) && !empty($_GET['id'])) {
  
  if(!deletePost($_GET)) {
    $_SESSION['error'] = 'При удалении поста что-то пошло не так';
    redirect();
  }

  redirect(get_url('userPosts.php'));

}