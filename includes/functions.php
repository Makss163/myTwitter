<?php

include_once "config.php";

// функция, возвращающая адрес страницы страницы
function get_url($page = '') {
  return HOST . "/$page";
}

function redirect($link = HOST) {
  header("Location: " . $link);
}

function debug($value, $stop = false) {
  echo "<pre>";
  print_r($value);
  echo "</pre>";
  if($stop) {
    die;
  }
}

//функция, возвращающая заголовок страницы
function get_title_page($title = '') {
  if(empty($title)) {
    return SITE_NAME;
  } else {
    return SITE_NAME . " - $title";
  }
}

// функция подключения к базе данных
function db() {

  //$dsn = "mysql:host=DB_HOST; dbname= DB_NAME; charset=utf8;";
  $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
  $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];

  try {
    return new PDO($dsn, DB_USER, DB_PASS, $options);
  } catch(PDOException $e) {
    die($e->getMessage());
  }

}

// функция отправки запроса к БД
function db_query($sql, $exec = false) {

  // fslse если не передан никакой запрос
  if (empty($sql)) {
    return false;
  }

  if($exec) {
    /* если параметр $exec true, производится выполнение запроса
    в БД с добавлением содержимого */
    return db()->exec($sql);
  } else {
    /* если $exec false, производится запрос в БД
    с получением содержимого */
    return db()->query($sql);
  }

}

function searchId() {
  $id = db_query("SELECT `id` FROM `users` ORDER BY `id`")->fetchAll();
  return $id;
}

// функция, возвращающая посты в виде массива строк из таблицы
function get_posts($user_id = 0, $checkSort = false) {

  if($checkSort) {
    $sorted = 'ASC'; //сортируем посты от старых к новым
  } else {
    $sorted = 'DESC'; //сортируем посты от новых к старым
  }

  if ($user_id > 0) {
    return db_query("SELECT posts.*, users.name, users.login, users.avatar FROM `posts` JOIN `users` ON posts.user_id = users.id WHERE posts.user_id = $user_id ORDER BY `posts`.`date` $sorted")
    ->fetchAll();
  }
  return db_query("SELECT posts.*, users.name, users.login, users.avatar FROM `posts` JOIN `users` ON posts.user_id = users.id ORDER BY `posts`.`date` $sorted")->fetchAll(); // преобразуем к массиву
}

function get_liked_posts() {
  $userId = $_SESSION['userId'];
  return db_query("SELECT posts.*, users.name, users.login, users.avatar FROM `posts` JOIN `users` JOIN `likes` ON posts.id = likes.post_id AND users.id = posts.user_id WHERE likes.user_id = $userId ORDER BY `posts`.`date` DESC")->fetchAll();
}


//регистрация новго пользователя ----

function get_user_info($login) {
  return db_query("SELECT * FROM `users` WHERE `login` = '$login'")->fetch();
}

function add_user($login, $pass) {
  $login = trim($login); // если есть пробелы вначале или конце строки - обрезаем их
  $name = ucfirst($login); // вызываем метод перевода в верхни регистр первого символа
  $password = password_hash($pass, PASSWORD_DEFAULT); // хешируем пароль
  $idArr = searchId();
  $lastId = count($idArr) + 1;
  // запрос в БД, который ничего не возвращает, а производит добавление новой информации
  return db_query("INSERT INTO `users` (`id`, `login`, `pass`, `name`) VALUES ('$lastId', '$login', '$password', '$name');", true);
}

function register_user($auth_data) {

  if(empty($auth_data['login']) || !isset($auth_data['login']) || empty($auth_data['password']) || !isset($auth_data['password']) || empty($auth_data['repeatPassword']) || !isset($auth_data['repeatPassword'])) {
    header("Location: " . get_url('register.php'));
  } else {
    // проиводим запрос в БД в таблицу пользователей с полученным логином
    $user = get_user_info($auth_data['login']);

    // если пользователь с данным логином найден
    if(!empty($user)) {
      $_SESSION['error'] = 'Пользователь с логином ' . $auth_data['login'] . ' уже существует.';
      // перенаправляем обратно на страницу регистрации с установленным $_SESSION['error']
      redirect(get_url('register.php'));
      //header("Location: " . get_url('register.php'));
      die;
    } elseif($auth_data['password'] !== $auth_data['repeatPassword']) {
      $_SESSION['error'] = 'Пароли не совпадают';
      redirect(get_url('register.php'));
      //header("Location: " . get_url('register.php')); 
      die;
    } elseif(strlen($auth_data['password']) < 8) {
      $_SESSION['error'] = 'Пароль слишком простой';
      redirect(get_url('register.php'));
      //header("Location: " . get_url('register.php')); 
      die;
    } elseif(strlen($auth_data['login']) < 3) {
      $_SESSION['error'] = 'Слишком короткий логин';
      redirect(get_url('register.php'));
      //header("Location: " . get_url('register.php')); 
      die;
    } else {
      // если ошибок при регистрации нет
      add_user($auth_data['login'], $auth_data['password']);
      $login = $auth_data['login'];
      $user = db_query("SELECT * FROM `users` WHERE `login` = '$login'")->fetchAll();
      $_SESSION['userId'] = $user[0]['id'];
      $_SESSION['userAvatar'] = $user[0]['avatar'];
      unset($_SESSION['error']);
      redirect(get_url('successSignUp.php'));
      die;
      //header("Location: " . get_url('successSignUp.php'));
    }

    

  }
  
}


function login($auth_data) {
  if(!empty($auth_data) || !empty($auth_data['login']) || isset($auth_data['login'])
  || !empty($auth_data['password']) || isset($auth_data['password'])) {

    $login = $auth_data['login'];
    $user = db_query("SELECT * FROM `users` WHERE `login` = '$login'")->fetchAll();

    if(empty($user)) {
      $_SESSION['error'] = 'Неправильный логин или проль';
      header("Location: " . get_url('index.php'));
      die;
    }

    if(password_verify($auth_data['password'], $user[0]['pass'])) {
      $_SESSION['userId'] = $user[0]['id'];
      $_SESSION['userAvatar'] = $user[0]['avatar'];
      header("Location: " . get_url('userPosts.php'));
      unset($_SESSION['error']);
      die;

    } else {
      $_SESSION['error'] = 'Неправильный логин или проль';
      header("Location: " . get_url('index.php'));
      die;
    }

  } else {
    return false;
  }

}

// функция проверки авторизован ли пользователь
function checkLogin() {
  if (isset($_SESSION['userId']) && !empty($_SESSION['userId'])) {
    return true;
  } else return false;
}

function addPost($data) {
  // обрезаем крайние пробелы
  $text = trim($data['text']);
  // удаляем лишние пробелы между слов
  //$text = preg_replace('/\s+/', ' ', $data['text']);
  /* 
  // массив допустимых типов файлов
  $types = ['image/gif', 'image/png', 'image/jpeg'];
  if(!in_array($dataFiles['image']['type'], $types)) {
    redirect();
  }

  // допустимый размер изображения
  $size = 1024000;
  if($dataFiles['image']['size'] > $size) {
    redirect();
  }

  if(!@copy($dataFiles['image']['tmp_name'], $path . $dataFiles['image']['name'])) {
    
  }
  */

  // проверяем количество слов
  if(str_word_count($text) > 50) {
    $text = mb_substr($text, 0, 50);
  }

  $image = NULL;
  if(isset($data['image']) && !empty($data['image'])) {
    $image = $data['image'];
  }

  $userId = $_SESSION['userId'];

  $sql = "INSERT INTO `posts` (`id`, `user_id`, `text`, `image`) VALUES (NULL, $userId, '$text', '$image');";
  return db_query($sql, true);
}

function deletePost($data) {
  if(!is_numeric($data['id'])) {
    redirect();
  }
  $id = $data['id'];
  $userId = $_SESSION['userId'];
  $sql = "DELETE FROM `posts` WHERE `posts`.`id` = $id AND `posts`.`user_id` = $userId";
  db_query($sql, true);
  
}

function getLikes($postId) {
  if(!empty($postId)) {
    return db_query("SELECT COUNT(*) FROM `likes` WHERE `post_id` = $postId")->fetchColumn();
  }
}

function postLikedLoginUser($postId) {

  if(empty($postId)) return false;
  
  if(checkLogin()) {
    $loginUserId = $_SESSION['userId'];
    $rows = db_query("SELECT * FROM `likes` WHERE `post_id` = $postId AND `user_id` = $loginUserId")->rowCount();

    if($rows) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
  

  
}

function addLike($postId) {
  if(empty($postId)) return false;
  $loginUserId = $_SESSION['userId'];
  if(checkLogin()) {
    $sql = "INSERT INTO `likes` (`post_id`, `user_id`) VALUES ($postId, $loginUserId);";
    db_query($sql, true);
  } else {
    return false;
  }
}

function deleteLike($postId) {
  if(empty($postId)) return false;
  $loginUserId = $_SESSION['userId'];
  if(checkLogin()) {
    $sql = "DELETE FROM `likes` WHERE `user_id` = $loginUserId AND `post_id` = $postId";
    db_query($sql, true);
  } else {
    return false;
  }
}

/* //функция возвращает сообщение об ошибке,
 если в $_SESSION['error'] что-то установлено */
function get_error_message() {
  $error = '';
  if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
    //$_SESSION['error'] = '';
    return $error;
  } else return $error;

}

// ----