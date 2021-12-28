    <section class="wrapper">
			<h2 class="tweet-form__title"><?php echo $title; ?></h2>
			<?php if($err) { ?>
				<div class="tweet-form__error"><?php echo $err; ?></div>
			<?php } ?>
			<form class="tweet-form" action="<?php echo get_url('index.php'); ?>" method="post">
				<!-- <div class="tweet-form__wrapper_inputs">
					<input type="text" class="tweet-form__input" placeholder="Логин" name="login" >
					<input type="password" class="tweet-form__input" placeholder="Пароль" name="password" >
					<input type="password" class="tweet-form__input" placeholder="Пароль повторно" name="repeatPassword" >
				</div> -->
				<div class="tweet-form__btns_center">
					<button class="tweet-form__btn_center" type="submit">OK</button>
				</div>
			</form>
		</section>