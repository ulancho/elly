// инициализация slidepanels
$(function(){
	$('#panels-container').slidepanels({
		'orientation': 'horizontal',
		'leftPanel': $('#left'),
		'rightPanel': $('#right'),
		'switchPlaceholder': $('#sp-switch-place')
	});
	
	$('#ss60').click(function(){
		$('mcontainer').slidepanels('resize', {'orientation': 'vertical', 'leftPanelSize': 0.6});
		return false;
	});
	var or = ['vertical','horizontal'], i = 0
	$('#switch').click(function(){
		i++;
		$('mcontainer').slidepanels('resize', {'orientation': or[(i % 2)]});
		$(this).text('set '+or[(i + 1) % 2]);
		return false;
	});
});

// инициализация списка сообщений
$(function(){
	$('#list-container').tablist({
		'minsize': 400,
		'fields': [
			{'name': 'time', 'clName': 'tl-field-left tl-field-time', 'important': true},
			{'name': 'icon', 'clName': 'tl-field-left tl-field-icon', 'important': true},
			{'name': 'type', 'clName': 'tl-field-left tl-field-type', 'important': true},
			{'name': 'counter', 'clName': 'tl-field-left tl-field-counter', 'important': true},
			{'name': 'author', 'clName': 'tl-field-right tl-field-author tl-hidable', 'important': false},
			{'name': 'title', 'clName': 'tl-field-title', 'important': true}
		],
		'selected': '2',
		'content': {
			'group': [
				{
					'id': '1',
					'title': 'Сегодня, 20 мая',
					'items': {
						'1': {
							'id': 1,
							'type': 'Задача',
							'title': '<span class="sticker-title" id="task-1"><a href="#">Подготовить подотчетную записку о резерве мощности</a> <span class="red">до&nbsp;20&nbsp;сентября</span></span>',
							'time': '10:50',
							'author': '<a href="#">Алексей Крынко</a>'
						},
						'2': {
							'id': 2,
							'title': '<span class="sticker-title" id="order-2"><a href="#">Провести опрос сотрудников об их отношении к руководству</a> должна быть завершена <span class="red">до&nbsp;завтра</span></span>',
							'type': 'Поручение',
							'time': '11:30',
							'counter': '2',
							'icon': '<div class="icon icon-flag"></div>',
							'author': '<a href="#">Павел Борисов</a>',
							'subitems': {
								'11': {
									'id': 11,
									'type': '<b>Поручение</b>',
									'title': '<span class="sticker-title" id="order-11"><a href="#">Расстрелять невиновных</a></span>',
									'time': '10:55',
									'author': '<a href="#">Павел Борисов</a>'
								},
								'12': {
									'id': 12,
									'type': '<b>Поручение</b>',
									'title': '<span class="sticker-title" id="order-12"><a href="#">Упрочить благосостояние конформистов и адептов</a></span>',
									'time': '10:57',
									'author': '<a href="#">Автандил Перзаде</a>'
								}
							}
						},
						'3': {
							'id': 3,
							'title': '<span class="sticker-title" id="task-3"><a href="#">А то если нам махнуть рукой на задержки поставок, попробовать самостоятельно освоить производство гуталина</a></span>',
							'type': 'Задача',
							'time': '15:00',
							'icon': '<div class="icon icon-excl"></div>',
							'author': '<a href="#">Иван Горемыка</a>'
						}
					}
				},
				{
					'id': '2',
					'title': 'Завтра, 21 мая',
					'items': {
						'4': {
							'id': 4,
							'type': 'Согласование',
							'title': '<span class="sticker-title" id="coordination-4"><a href="#">Отпуск за свой счет</a> согласовано</span>',
							'time': '11:00',
							'icon': '<div class="icon icon-ok"></div>',
							'counter': '2',
							'author': '<a href="#">Сергей Жемчужный</a>',
							'subitems': {
								'13': {
									'id': 13,
									'type': '<b>Поручение</b>',
									'title': '<span class="sticker-title" id="order-13"><a href="#">Согласовать отпуск</a></span>',
									'time': '10:55',
									'author': '<a href="#">Кирилл Коралл</a>'
								},
								'14': {
									'id': 14,
									'type': '<b>Поручение</b>',
									'title': '<span class="sticker-title" id="order-14"><a href="#">Купить билеты на поезд Москва—Кремль</a></span>',
									'time': '10:57',
									'author': '<a href="#">Анастасия Сия</a>'
								}
							}
						},
						'5': {
							'id': 5,
							'type': 'Поручение',
							'title': '<span class="sticker-title" id="order-5"><a href="#">Подготовить служебную записку о чем-нибудь</a></span>',
							'time': '14:30',
							'author': '<a href="#">Ольга Шмольга</a>'
						},
						'6': {
							'id': 6,
							'type': 'Задача',
							'title': '<span class="sticker-title" id="task-6"><a href="#">Сделать что-нибудь хорошее</a> в работе</span>',
							'time': '18:00',
							'icon': '<div class="icon icon-talk"></div>',
							'author': '<a href="#">Петр Шморт</a>'
						}
					}
				},
				{
					'id': '3',
					'title': 'Понедельник, 23 мая',
					'items': {
						'24': {
							'id': 24,
							'type': 'Согласование',
							'title': '<span class="sticker-title" id="coordination-24"><a href="#">Установление личности убитого в перестрелке между злыми полицейскими и невинными гражданами</a> необходимо согласовать до <span class="red">15&nbsp;сентября</span></span>',
							'time': '11:00',
							'icon': '<div class="icon icon-ok"></div>',
							'counter': '4',
							'author': 'Петров Дмитрий',
							'subitems': {
								'213': {
									'id': 213,
									'type': '<b>Поручение</b>',
									'title': '<span class="sticker-title" id="order-213"><a href="#">Запомнить все хорошее, что было в жизни</a></span>',
									'time': '10:55',
									'author': '<a href="#">Кристофер Робин</a>'
								},
								'214': {
									'id': 214,
									'type': '<b>Поручение</b>',
									'title': '<span class="sticker-title" id="order-214"><a href="#">Подготовить примеры политической травли</a></span>',
									'time': '10:57',
									'author': '<a href="#">Иван Богохульник</a>'
								},
								'215': {
									'id': 215,
									'type': '<b>Поручение</b>',
									'title': '<span class="sticker-title" id="order-215"><a href="#">Подзаголовок номер 23</a></span>',
									'time': '10:57',
									'author': '<a href="#">Роберт Капучино</a>'
								},
								'216': {
									'id': 216,
									'type': '<b>Поручение</b>',
									'title': '<span class="sticker-title" id="order-216"><a href="#">Оформление страховых полисов</a> в работе</span>',
									'time': '10:57',
									'author': '<a href="#">Семен Магнит</a>'
								}
							}
						},
						'25': {
							'id': 25,
							'type': 'Поручение',
							'title': '<span class="sticker-title" id="order-25"><a href="#">Поручение незначительное в своей сути</a></span>',
							'time': '14:30',
							'author': '<a href="#">Татар Крымский</a>'
						},
						'26': {
							'id': 26,
							'type': 'Задача',
							'title': '<a href="#">Заголовок номер 26</a>',
							'time': '18:00',
							'icon': '<div class="icon icon-talk"></div>',
							'author': '<a href="#">Евстигней Пертуров</a>'
						}
					}
				},
				{
					'id': '4',
					'title': 'Вторник, 24 мая',
					'items': {
						'34': {
							'id': 34,
							'type': 'Согласование',
							'title': '<a href="#">Заголовок номер 34</a>',
							'time': '11:00',
							'icon': '<div class="icon icon-ok"></div>',
							'counter': '3',
							'author': 'Петров Дмитрий',
							'subitems': {
								'313': {
									'id': 313,
									'type': '<b>Поручение</b>',
									'title': '<a href="#">Подзаголовок номер 31</a>',
									'time': '10:55',
									'author': 'Иванов Иван'
								},
								'314': {
									'id': 314,
									'type': '<b>Поручение</b>',
									'title': '<a href="#">Подзаголовок номер 32</a>',
									'time': '10:57',
									'author': 'Иванов Иван'
								},
								'315': {
									'id': 315,
									'type': '<b>Поручение</b>',
									'title': '<a href="#">Подзаголовок номер 33</a>',
									'time': '10:57',
									'author': 'Иванов Иван'
								}
							}
						},
						'35': {
							'id': 35,
							'type': 'Поручение',
							'title': '<a href="#">Заголовок номер 35</a>',
							'time': '14:30',
							'author': 'Иванов Иван'
						},
						'36': {
							'id': 26,
							'type': 'Задача',
							'title': '<a href="#">Заголовок номер 36</a>',
							'time': '18:00',
							'icon': '<div class="icon icon-talk"></div>',
							'author': 'Петров Дмитрий'
						},
						'37': {
							'id': 27,
							'type': 'Задача',
							'title': '<a href="#">Заголовок номер 37</a>',
							'time': '18:10',
							'icon': '<div class="icon icon-flag"></div>',
							'author': 'Петров Дмитрий'
						},
						'38': {
							'id': 28,
							'type': 'Задача',
							'title': '<a href="#">Заголовок номер 38</a>',
							'time': '18:20',
							'author': 'Иванов Иван'
						}
					}
				}
			]
		}
	});

});