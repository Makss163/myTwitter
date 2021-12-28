    <!-- проверяем, если в $posts есть что-то,
		то возвращаем вёрстку с постами -->
		<?php if(!empty($posts)) { ?>
		<section class="wrapper">
			<ul class="tweet-list">
				<?php foreach($posts as $post) {?> 
				<li>
					<article class="tweet">
						<div class="row">
							<img class="avatar" src="<?php echo $post['avatar']; ?>" alt="Аватар пользователя Мария">
							<div class="tweet__wrapper">
								<header class="tweet__header">
									<h3 class="tweet-author"><?php echo $post['name']; ?>
										<a href="<?php echo get_url('userPosts.php?id=' . $post['user_id']);// формируем ссылку, в глобальной переменной $_GET будет id пользователя ?>"
											class="tweet-author__add tweet-author__nickname">@<?php echo $post['login']; ?></a>
										<time class="tweet-author__add tweet__date"><?php echo date('d.m.y в H:i', strtotime($post['date'])); ?></time>
									</h3>
									<?php if(checkLogin() && $post['user_id'] === $_SESSION['userId']) { ?>
										<a href="<?php echo 'includes/deletePost.php?id=' . $post['id'] ?>" class="tweet__delete-button chest-icon"></a>
									<?php } ?>
								</header>
								<div class="tweet-post">
									<p class="tweet-post__text"><?php echo $post['text']; ?></p>
									<?php if($post['image']) { ?>
									<figure class="tweet-post__image">
										<img src="<?php echo $post['image']; ?>" alt="twitter image">
									</figure>
									<?php } ?>
								</div>
							</div>
						</div>
						<footer>
							<button class="tweet__like <?php if(checkLogin() && postLikedLoginUser($post['id'])) echo ' tweet__like_active'; ?>" data-postid="<?php echo $post['id']; ?>" ><?php echo getLikes($post['id']); ?></button>
						</footer>
					</article>
				</li>
				<?php } ?>

			</ul>
		</section>
		<?php } else {
			echo "<h1 style='margin:auto' >Постов пока нет</h1>";
		} ?>