<?php

include_once "functions.php";

// производим добавление/удаление лайков если пользователь авторизован
if(checkLogin()) {
  // проверяем, что в запросе данные пришли
  if(isset($_POST['idPost']) && !empty($_POST['idPost'])) {
    // берём id поста
    $postId = $_POST['idPost'];
    // если пользователь лайкнул уже пост с данным id - лайк убираем
    if(postLikedLoginUser($postId)) {   
      deleteLike($postId); //
    } else {
      // если лайк не ещё оставлял то добавляем
      addLike($postId);
    }

    $countLikes = getLikes($postId); // количество лайков поста после запроса

    echo $countLikes; // отправлем в ответе итоговое количество лайков
  } 
} else {
  // если пользователь не авторизован - устанавливаем статус 404
  http_response_code(401);
}

