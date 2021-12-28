/*const getResourse = async url => {
	const res = await fetch(url);

	if (!res.ok) {
		throw new Error('Произошла ошибка: ' + res.status)
	}

	return res.json();
};*/

const openModal = function () {
	const overlay = document.getElementById('login-modal').closest('.overlay');
	overlay.style.display = 'block';
};

const closeModal = function (event) {
	const target = event.target,
				overlay = document.getElementById('login-modal').closest('.overlay'),
				closeButton = overlay.querySelector('.modal-close__btn');

	if (target === overlay || target === closeButton)	overlay.style.display = 'none';
};

const loginModalShowButton = document.querySelector('.header__link_profile_fill'),
			loginModal = document.getElementById('login-modal'),
			imgButton = document.querySelector('.tweet-img__btn');
			likesBtn = document.querySelectorAll('.tweet__like');

if (loginModalShowButton) {
	loginModalShowButton.addEventListener('click', openModal);
}

if (loginModal) {
	const loginModalOverlay = loginModal.closest('.overlay');
	loginModalOverlay.addEventListener('click', closeModal);
}

if (imgButton) {
	imgButton.addEventListener('click', () => {
		const imgInput = document.getElementById('image-path'),
					imgSpan = document.getElementById('image-span');

		imgUrl = prompt('Введите адрес картинки');
		imgInput.value = imgUrl;
		if(imgUrl.length > 50) {
			imgUrl = `${imgUrl.slice(0, 50)}...`;
		}
		imgSpan.textContent = imgUrl;
	});
}


if(likesBtn) {
	// для каждой кнопки лайка вешаем обработчик клика
	for(let like of likesBtn) {
		like.addEventListener('click', (event) => {
			idPost = like.dataset.postid; // поулчаем id поста

			// формируем данные для ajax запроса
			const request = new XMLHttpRequest();
			const url = 'includes/addLike.php'; // обработка запроса в файле addLike.php
			const params = `idPost=${idPost}`; // строка с данными, отправляемыми в запросе
			// метод open, задаём способ отправки, адрес файла обработки, true - запрос асинхронный
			request.open('POST', url, true);
			// задаём заголовки, вторым параметром говорим, что отправляемые в запросе анные зашифрованы
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			// когда ответ от сервера придёт, сработает событие readystatechange
			request.addEventListener('readystatechange', () => {
				// если ответ от сервера с нормальным статусом
				if(request.readyState === 4 && request.status === 200) {
					// проверяем, вернулось ли число (количество лайков) в ответе от сервера
					if(typeof(+request.responseText) === 'number') {
						// в кнопке отображаем количество лайков которое придёт в ответе после запроса
						like.innerHTML = request.responseText;
						// убираем/добавляем цвет индикатора лайкнутого авторизованным пользователем поста
						if(like.classList.contains('tweet__like_active')) {
							like.classList.remove('tweet__like_active');
						} else {
							like.classList.add('tweet__like_active');
						}	
					}
						
				}
			});
			request.send(params);
			
		});
	}
}
