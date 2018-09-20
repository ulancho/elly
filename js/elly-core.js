
// TODO: ajaxStack is array of pending ajax requests, make function, which send next ajaxRequest when previous request ends if ajaxStack is not empty.

    
function EllyModel(list, identity_name) {

	this.list = list;
	this.identity_name = identity_name;
	this.indexes = [];

	this.get = function(code_element) {
		return this.list[this.indexes[code_element]];
	}
    
    this.homeUrl =  function() 
    {
        var path = '';
        $('script').each(function()
        {
            if(this.src && /elly-core.js/i.test(this.src))
            {
               var a = this.src.split(/\/{1}/); 
               path = a.slice(0, a.length-3).join('/')+'/';              
               return false; // прерывание цикла
            }
        });
        return path;
    }

	this.indexlist = function() {
		var self = this;
		var temp_list = [];

		self.list.forEach(function(element, index){
			temp_list.push(element);
		});
		self.list = temp_list;
		self.list.forEach(function(element, index){
			self.indexes[element[identity_name]] = index;
		});
	}

	this.exist = function(codeid) {
		return ( this.indexes[codeid]!==undefined ) ? true : false ;
	}

	// функция добавляет модель, или массив моделей в начало списка
	this.unshift = function(element) {
		var self = this;

		if ( Object.prototype.toString.call(element) == '[object Array]' ) {

			$.each(element, function(index, model) {
				if ( self.exist(element[self.identity_name]) ) {
					self.list[self.indexes[element[self.identity_name]]] = element;
				} else {
					self.list.unshift(element);
				}
			});
			this.indexlist();

		} else {

			if ( this.exist(element[this.identity_name]) ) {
				this.list[this.indexes[element[this.identity_name]]] = element;
			} else {
				this.list.unshift(element);
				this.indexlist();
			}

		}
	}

    this.filter = function(searchArr) {

        return this.list.filter(function(item){
            var flag = true;
            for(var key in searchArr) {
                var search = searchArr[key];
                if(typeof search == 'object') {
                    var flag2 = false;
                    for(var k in search) {
                        if(search[k] == item[key]) flag2 = true;
                    }
                    flag = flag2;
                }
                else if(search != item[key]) flag = false;
            }
            return flag;
        })
    }
    
	// функция добавляет модель, или массив моделей в конец списка
	this.push = function(element) {
		var self = this;

			// если element - массив, значит это массив моделей, в foreach вызываем функцию this.push для каждой модели
		if ( Object.prototype.toString.call(element) == '[object Array]' ) {

			$.each(element, function(index, model) {
				self.push(model);
			});

		} else {

			// если element - объект, то проверяем есть ли уже такая модель в индексе, если нет, то добавляем, если есть, то заменяем на новую
			if ( this.exist(element[this.identity_name]) ) {
				this.list[this.indexes[element[this.identity_name]]] = element;
			} else {
				this.list.push(element);
				this.indexes[element[this.identity_name]] = this.list.length-1;
			}

		}
	}

		// функция очищает список моделей
	this.clear = function(element) {
		this.indexes = {};
		this.list = [];
	}

	this.delete = function(codeid) {
		if ( this.exist(codeid) ) {
			delete this.list[this.indexes[codeid]];
			delete this.indexes[codeid];
			this.indexlist();
		}
	}

	this.indexlist();
}

var EllyCore = ({

	ajaxActive: false,
	ajaxStack:[],
	filesStack:[],
	filesTotalSize:0,
	filesLoadedSize:0,
	filesCurrentLoadingSize:0,
	profilerTime:0,

	ajax: function(options){
		options.origSuccess = options.success;
		options.origError = options.error;
		var options = $.extend({}, EllyCore.defaults.ajax, options);
		options.success = this.defaults.ajax.success;
		options.error = this.defaults.ajax.error;
		$.ajax(options);
	},

	ajaxForm: function(options){
		element = options.element;
		options.element = null;
		options.origSuccess = options.success;
		options.origError = options.error;
		var options = $.extend({}, EllyCore.defaults.ajaxForm, options);
		options.success = this.defaults.ajaxForm.success;
		options.error = this.defaults.ajaxForm.error;
		element.ajaxForm(options);
	},

	showAjaxForm: function(options){

		fillFormParams = {
			url: ( options.url ) ? options.url : null,
			values: ( options.values ) ? options.values : null,
			element: options.element,
		}

		modalParams = {
			element: options.element,
			title: ( options.title ) ? options.title : null,
			width: ( options.width ) ? options.width : null,
			callback: ( options.callback ) ? options.callback : null,
		}

		this.fillForm(fillFormParams);
		this.modal(modalParams);

	},


/*
EllyCore: fillForm({
	element: $('ID_ФОРМЫ'),
	url: EllyCore.url('Модуль', 'Экшен'),
	values: {},
});
*/
	fillForm: function(options){
		$form = (options.element.is('form')) ? options.element : options.element.find('form');
		$form.resetForm();
		if ( options.url ) {
			$form.prop('action', options.url);
		}

        if ( options.values ) {
            $.each(options.values, function(index, value) {
                $form_element = $form.find('[name="' + index + '"]');
                form_element_prop = $form_element.prop('type');
                if ( form_element_prop=='file' ) return;

                isDateField = form_element_prop=='datetime' || form_element_prop=='datetime-local' || form_element_prop=='date' || form_element_prop=='time' || $form_element.hasClass('hasDatepicker');
                isCheckbox = form_element_prop=='checkbox' || form_element_prop=='radio';

                if ( isDateField ) {
                    if ( typeof value == 'object' ) {

                        if ( form_element_prop=='time' ) {
                            $form_element.val(moment(value).utcOffset('+06:00').format('HH:mm'));
                        } else {
                            $form_element.val(moment(value).utcOffset('+06:00').format('DD.MM.YYYY HH:mm'));
                        }
                    } else {
                        if ( $form_element.hasClass('browser_date') ) {
                            $form_element.val(value);
                        } else if ( form_element_prop=='time' ) {
                            $form_element.val(moment(new Date(value * 1000)).format('HH:mm'));
                        } else if ( form_element_prop=='date' ) {
                            var today = new Date(value * 1000);
                            var tomorrow = new Date(today);
                            tomorrow.setDate(today.getDate()+1);
                            var date = tomorrow.toJSON().slice(0,10);
                            $form_element.val(date);
                        } else {
                            $form_element.val(moment(new Date(value * 1000)).format('DD.MM.YYYY HH:mm'));
                        }
                    }
                } else if ( isCheckbox ) {
                    $form_element.prop('checked', value);
                } else {
                    $form_element.val(value);
                }
            });
        }

		if ( options.callback ) {
			options.callback();
		}

	},

	modal: function(options){
		var options = $.extend({}, EllyCore.defaults.modal, options);

		if ( !options.container ) {
			options.container = $('#elly-modal-container');
		}

		$modalBody = options.container.find('.modal-body');

		if ( options.title ) {
			options.container.find('.modal-title').html( options.title );
		}

		$modalDialog = options.container.find('.modal-dialog');
		if ( options.width ) {
			$modalDialog.css('width', options.width);
		}

		if ( options.url ) {

			var data = $.extend({}, options.data, {context:'ajax'});
			$modalBody.html('<div id="ellyAjaxLoader"></div>');
			options.container.modal();
			var currentRequest = $.ajax({
				url: options.url,
				data: data,
				type: 'POST',
				success: function(data) {
					$modalBody.html(data);
					if ( options.callback ) {
						options.callback();
					}
				},
			});
			options.container.on('hidden.bs.modal', function (e) {
				$(this).off('hidden.bs.modal');
                console.log('123');
				$modalBody.html('');
				currentRequest.abort();
			});

		} else if ( options.element ) {

			var $originalContainer = options.element.parent();
			options.element.appendTo($modalBody);
			if ( options.callback ) {
				options.container.on('shown.bs.modal', function (e) {
					$(this).off('shown.bs.modal');
					options.callback();
				});
			}
			options.container.on('hidden.bs.modal', function (e) {
				$(this).off('hidden.bs.modal');
				$modalBody.children().appendTo($originalContainer);
				$modalBody.html('');
			});
			options.container.modal();

		} else {
			alert('не задан обязательный параметр `element` или `url` для функции EllyCore.modal()');
		}

	},
    
    modalClose: function(options){
		var options = $.extend({}, EllyCore.defaults.modalClose, options);
		if (options.container) {
			options.container.modal('hide');
		} else {
			$('#elly-modal-container').modal('hide');
		}
	},
	
	modalForm: function(obj, data)
    {
        EllyCore.modalObj = $(obj).modal();        
        if(data) EllyCore.form(obj, data);
        
        return EllyCore.modalObj;
    },
	
	form: function(form, data)
    {
        var last_name  = '';
        var last_index = {};
        
        if(typeof(data) != 'object')
        {
            console.log('РћС€РёР±РєР°: РІ С„СѓРЅРєС†РёСЋ РЅСѓР¶РЅРѕ РїРµСЂРµРґР°РІР°С‚СЊ РІС‚РѕСЂС‹Рј РїР°СЂР°РјРµС‚СЂРѕРј РћР±СЉРµРєС‚!');
            return false;
        }
        
        $(form).find('input, select, textarea').each(function(i,v){
            
            var obj  = $(v);
            var type = obj.attr('type');
            var name = obj.attr('name');
            var val  = null;
            var name2= '';
            
            if(type == 'submit' || type == 'button') return;
            
            // РѕР±СЂР°Р±РѕС‚РєР° РјР°СЃСЃРёРІРЅС‹С… РїРµСЂРµРјРµРЅРЅС‹С…
            if(name.substr(-2) == '[]')
            {                
                name2 = name.slice(0, -2);
                last_index[name2] = typeof(last_index[name2]) == 'undefined' ? 0 : last_index[name2]+1;
                val = $.isArray(data[name2]) ? data[name2][last_index[name2]] : null;
            }
            else val = typeof(data[name]) == 'undefined' ? null : data[name];
                        
            if(type == 'checkbox') obj.prop('checked', val);
            else if(type == 'radio') 
            {
                if(name == last_name) return;
                if(val == null) 
                     $(form).find('[name="'+ name +'"]').prop('checked', false);
                else $(form).find('[name="'+ name +'"][value="'+ val +'"]').prop('checked', true);
                last_name = name;
            }
            else
            {
                // С‡С‚РѕР±С‹ РјРѕР¶РЅРѕ Р±С‹Р»Рѕ РѕС‚РґРµР»РёС‚СЊ input:text, select Рё textarea РґСЂСѓРі РѕС‚ РґСЂСѓРіР°
                if(!type) type = obj.get(0).tagName.toLowerCase();
                obj.val(val);
            }
        });
        return this;
    },
	
	UploadFile: function(base)
    {        
        //var base = this;
        var self;
        
        base = $.extend({
                            drop    : '',                       // элемент выступающий в роли кнопки загрузки
                            progress: '',                       // элемент, где отображать процесс загрузки
                            back    : '#_upload_',              // элемент, где будут создаваться скрытые поля с именами файлов
                            maxMBite: 2,                        // мах размер файла
                            sendData: '',                       // данные отправляемые на сервер
                            callback: null,                     // событие происходящее после загрузоки всех файлов
                            onstart : null,                     // событие происходящее перед каждой загрузкой файла
                            onfinish: null,                     // событие происходящее после каждой загрузкой файла
                            php     : '/system/mod/upload.php'  // отрабатывающий скрипт на сервере
                        }, base);
        /*
        if(typeof(drop) == 'object') {        
            base = {
                drop    : drop.drop || '',
                progress: drop.progress || '',
                back    : drop.back || '#_upload_',
                maxMBite: drop.maxMBite || 2,
                sendData: drop.sendData || '',
                callback: drop.callback,
                onstart : drop.onstart,
                onfinish: drop.onfinish,
                php     : drop.php || '/system/mod/upload.php'
            };
        }
        else base = {
                drop    : drop,
                progress: progress,
                back    : back || '#_upload_',
                maxMBite: maxMBite || 2,
                sendData: sendData || '',
                php     : '/system/mod/upload.php'
            };
        */
        
        $('body').append('<div id="_upload_"></div>');            
        $('#_upload_').html('<input type="file" multiple="multiple" />').css('display', 'none');
        
        $('#_upload_ input').bind('change', function() { base.upload(this.files) });
                
        
        $(base.drop).bind('dragover', function(){return false})
               .bind('dragenter', function(){return false})
               .bind('drop', function(e){
                        
                        // Отмена реакции браузера по-умолчанию
                        e.preventDefault();
                        e.stopPropagation();
                        
                        if(e.originalEvent.dataTransfer.files.length) {
                            self = this;
                            base.upload(e.originalEvent.dataTransfer.files);
                        }
                        
                  })
               .bind('click', function(){  //эмуляция клика выбора файлов
                        self = this;
                        $('#_upload_ input:file').trigger('click');
                  });
                  
        
        base.upload = function(files)
                        {
                            var sendDataTemp = $.extend({ajax:1}, base.sendData);
                            var result = null;
                            if(base.onstart) result = base.onstart(self);
                            if(result != null) {if(typeof(sendDataTemp) == 'object') $.extend(sendDataTemp, result); else sendDataTemp = result;}
                                                    
                            var maxBite = base.maxMBite *1024 *1024;  //2Mb
                            $(base.progress).html('');
                            
                            elly.wait = true;
                            
                            $.each(files, function(i, file)
                            {    
                                var formData = new FormData;
                                formData.append('file', file); //formData.append('files[]', file);
                                if(typeof(sendDataTemp) == 'object') 
                                {
                                    for(var key in sendDataTemp) formData.append(key, sendDataTemp[key]);
                                }
                                
                                var obj = base.create(i, file, obj).find('.fu_line'); 
                                
                                if(file.size > maxBite)
                                {    
                                    obj.css({width:'100%', background:'#f55', textAlign:'center'})
                                       .html('Файл больше ' + base.maxMBite + ' Mb');
                                    if(base.onfinish) result = base.onfinish(obj, self); 
                                    return;
                                } 
                                /* console.log(formData);*/
                                
                                $.ajax({
                                    url: base.php,
                                    type: 'post',
                                    data: formData,
                                    dataType: 'json',
                                    contentType: false,
                                    processData: false,
                                    success: function (data){
                                        base.success(data.data.fileName, data.data.json, obj.parents('.fu_progres'));
                                        //console.log(data.json);
                                        //if(data.script) window.execScript ? execScript(data.script) : window.eval(data.script);
                                    },
                                    xhr: function()
                                    {
                                        var xhr = new $.ajaxSettings.xhr();
                                        if(xhr.upload)  // проверка что осуществляется upload
                                            xhr.upload.addEventListener("progress",
                                            function(e)
                                            {
                                                if (e.lengthComputable) {
                                                    var procent = Math.round(100 * e.loaded / e.total) + '%';
                                                    obj.text(procent).css('width', procent);
                                                }
                                            }
                                            , false);
                                        return xhr;
                                    },
                                    error: function( data, status, textMsg ) {
                                        console.group('Ошибка AJAX-запроса в функции elly.ajax()!');
                                        console.warn(textMsg);
                                        //console.log(data.responseText);
                                        console.log(status); 
                                        console.groupEnd();
                                        
                                        elly.dialog(data.responseText,'Ошибка AJAX-загрузки файла');
                                    },
                                    complete: function() {if(base.onfinish) result = base.onfinish(obj, self);}
                                });
                            }); 
                            elly.wait = false;  
                        }                   
        base.create = function(i, file) 
                        {
                            $(base.progress).append('<div id="info' + i + '" class="fu_progres"><div class="fu_name"></div><div class="progress"><div class="fu_line progress-bar"></div></div></div>');
                            var obj = $(base.progress + ' #info' +i);
                            obj.find('.fu_name').html( file.name + ' (' + (file.size/1048576).toFixed(2) + ' Mb)' );
                            return obj;
                        }
        base.success = function(file, newName, obj)
                        {
                            if(base.callback) base.callback(file, newName, obj);
                            else {
                                //obj.parent().remove();                                
                                setTimeout(function(){
                                    obj.remove();
                                    $(base.back).append('<input type="hidden" name="files[]" value="' + newName + '" />');
                                }, 1500);                                
                            }
                        }
    },

	template:function(options){
		var options = $.extend({}, EllyCore.defaults.template, options);

		var source   = options.source.html();
		var template = Handlebars.compile(source);
		var context  = options.values;
		var html     = template(context);
			// Если не задан параметр element, то просто возращаем html
		if ( !options.element ) {
			options.callback();
			return html;
		} else if ( options.method=='replace' ) {

			options.element.replaceWith(html);

		} else if ( options.method=='append' ) {

			options.element.append(html);

		} else if ( options.method=='after' ) {

			options.element.after(html);

		} else if ( options.method=='prepend' ) {

			options.element.prepend(html);

		} else if ( options.method=='replaceContent' ) {

			options.element.html(html);

		}

		options.callback();
	},

	confirm: function(label) {
		return confirm(label);
	},

	url: function(controller, action, params) {
		if ( params ) {
			var tmp_params = '';
			$.each(params, function(index, val) {
				tmp_params += '&' + index + '=' + val;
			});
			params = tmp_params;
		} else {
			params = '';
		}

		//return '?module=' + controller + '&action=' + action + params;
		return '?' + controller + '@' + action + params;
	},

	profiler:function(label){
		if ( this.profilerTime==0 ) {
			this.profilerTime = new Date();
		} else {
			console.log('(' + label + ') time: ' + (new Date() - this.profilerTime) + " milliseconds");
			this.profilerTime = new Date();
		}
	},

/*
EllyCore.keyFilter({
	element: $('ID_элемента_фильтр'),
	type: 'nums',
});
*/
	keyFilter: function(options){
		if ( options.type=='nums' ) {
				// Только цифры в поле Цена
			options.element.keydown(function(event) {
				// Разрешаем нажатие клавиш backspace, del, tab, enter и esc
				if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 13 || event.keyCode == 27 ||
					 // Разрешаем выделение: Ctrl+A
					(event.keyCode == 65 && event.ctrlKey === true) ||
					 // Разрешаем клавиши навигации: home, end, left, right
					(event.keyCode >= 35 && event.keyCode <= 39) ||
					 // Разрешаем символ точки и минус
					(event.keyCode == 173 || event.keyCode == 190)
				)
					 {
						 return;
				}
				else {
					// Запрещаем всё, кроме клавиш цифр на основной клавиатуре, а также Num-клавиатуре
					if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
						event.preventDefault();
					}
				}
			});
		} else {
			alert('Wrong type passed in EllyCore.keyFilter "' + options.type + '" for "' + options.element + '" element!');
		}
	},

	fixedSidebar: function(options){
		if ( typeof(options.topOffset)=='undefined' || options.topOffset==null ) {
			options.topOffset = 0;
		}
		if ( typeof(options.bottomOffset)=='undefined' || options.bottomOffset==null ) {
			options.bottomOffset = 0;
		}

		$window = $(window);

		$window.resize(function() {
			if ($window.scrollTop() > options.topOffset) {
				$(options.element).css({
					'top': 0,
					'height': $window.height() - options.bottomOffset,
					'position': 'fixed'
				});
			} else {
				$(options.element).css({
					'top': options.topOffset + 'px',
					'height': $window.height() - options.topOffset - options.bottomOffset + window.pageYOffset,
					'position': 'absolute'
				});
			}
		});
		$window.scroll(function() {
			if ($window.scrollTop() > options.topOffset) {
				$(options.element).css({
					'top': 0,
					'height': $window.height() - options.bottomOffset,
					'position': 'fixed'
				});
			} else {
				$(options.element).css({
					'top': options.topOffset + 'px',
					'height': $window.height() - options.topOffset - options.bottomOffset + window.pageYOffset,
					'position': 'absolute'
				});
			}
		});
		$window.resize();
	},


		/* Фильтр и сортировка
EllyCore.filter({
	element: $('ID_элемента_фильтр'),
	filter_elements: ['codeid', 'name'],
});
		*/
	filter: function(options){
		if (options.element==null) {
			EllyCore.error({
				message: 'Ошибка JS: Для функции EllyCore.filter не задан обязательный параметр "element"'
			});
		}
		if (options.filter_elements==null) {
			EllyCore.error({
				message: 'Ошибка JS: Для функции EllyCore.filter не задан обязательный параметр "filter_elements"'
			});
		}

		window.client_filter_order = new List(options.element.attr('id'), {valueNames: options.filter_elements, page: 10000});

		options.element.find('.sort').click(function(){
			options.element.find('.sort').removeClass('btn-success');
			$(this).addClass('btn-success');
		});

		$('#search').keyup(function(event){
			if ( event.keyCode==27 ) {
				$('#search_clear').click();
			}
			if ( $(this).val()!='' ) {
				$('#search_clear').css('display', 'block');
			} else {
				$('#search_clear').css('display', 'none');
			}
		});

		$('#search_clear').click(function(){
			$('#search').val('');
			$('#search_clear').css('display', 'none');
			window.client_filter_order.search('');
		});
	},

		// Вывод ошибок. Пока что просто алерт.
	error: function(options) {
		alert(options.message);
	},

		/* Контекстное меню при щелчке правой кнопкой.
EllyCore.contextMenu({
	element: $('ID_элемента_при_нажатии_на_который_вызовется_меню'),
	menu: $('ID_меню'),
	delete: function(element){
		alert('Эта функция вызовется когда нажмется кнопка delete в контекстном меню.');
	},
});
		*/
	contextMenu: function(options){
			// Определяем bindings, нужны как экшены при выборе пункта из контекстного меню
		bindings = options;
		$element = options.element;
		$menu = options.menu;

			// Проверяем задан ли element и удаляем element из bindings
		if ( $element==null ) {
			EllyCore.error({
				message: 'Ошибка JS: Для функции contextMenu не задан обязательный параметр "element"'
			});
			return false;
		}
		delete bindings.element;

			// Проверяем задано ли menu и удаляем menu из bindings
		if ( $menu==null ) {
			EllyCore.error({
				message: 'Ошибка JS: Для функции contextMenu не задан обязательный параметр "menu"'
			});
			return false;
		}
		delete bindings.menu;

			// вызываем плагин контекстого меню для заданного элемента
		$element.contextMenu($menu, {
			bindings: bindings,
			callback: function(key, options) { alert(1);
				$(this).parent('tr').css('background','#D3EBFF');
			},
		});
	},


	defaults:{
		'ajax':{
			type:'GET',
			dataType:'json',
			success:function(response) {
                $('#elly-ajax-loading').css('display', 'none');
				// $(document).unbind('mousemove');
				if (response.result==1) {
					if (typeof(this.origSuccess)!='undefined') {
						this.origSuccess(response.data);
					}
				} else {
					if(typeof(response.message)=='undefined') {
						alert('Во время запроса произошла ошибка');
					} else {
						alert(response.message);
					}
				}
                if(response.script) window.execScript ? execScript(response.script) : window.eval(response.script);
			},
			error:function(XMLHttpRequest, textStatus, errorThrown) {
				$('#elly-ajax-loading').css('display', 'none');
				//$(document).unbind('mousemove');
				if (typeof(this.origError)!='undefined') {
					this.origError();
				} else {
					alert('Ошибка соединения с сервером.');
				}
			},
			beforeSend:function(qXHR, settings){
					// bind mousemove and show ajax loading if event object proveded
				$('#elly-ajax-loading').css('display', 'block');
				if ( typeof(this.event)!='undefined' ) {
					$('#elly-ajax-loading').css({
						position: 'absolute',
						left: this.event.pageX+10,
						top: this.event.pageY+16,
					});
					$(document).bind('mousemove', function(e){
						$('#elly-ajax-loading').css({
							left: e.pageX+10,
							top: e.pageY+16,
						});
					});
				} else {
					$('#elly-ajax-loading').css({
						position: 'fixed',
						left: $(window).width()/2+10,
						top: $(window).height()/2+16,
					});
				}
			},
		},

		'ajaxForm': {
			success:function(response) {
				$('#elly-ajax-loading').css('display', 'none');
				//$(document).unbind('mousemove');
				if (response.result==1) {
					if (typeof(this.origSuccess)!='undefined') {
						this.origSuccess(response.data);
					}
				} else {
					if(typeof(response.message)=='undefined') {
						alert('Во время запроса произошла ошибка');
					} else {
						alert(response.message);
					}
				}
			},
			error:function(XMLHttpRequest, textStatus, errorThrown) {
				$('#elly-ajax-loading').css('display', 'none');
				//$(document).unbind('mousemove');
				if (typeof(this.origError)!='undefined') {
					this.origError();
				} else {
					alert('Ошибка соединения с сервером.');
				}
			},
			beforeSend:function(qXHR, settings){
					// bind mousemove and show ajax loading if event object proveded
				$('#elly-ajax-loading').css('display', 'block');
				if ( typeof(this.event)!='undefined' ) {
					$('#elly-ajax-loading').css({
						position: 'absolute',
						left: this.event.pageX+10,
						top: this.event.pageY+16,
					});
					$(document).bind('mousemove', function(e){
						$('#elly-ajax-loading').css({
							left: e.pageX+10,
							top: e.pageY+16,
						});
					});
				} else {
					$('#elly-ajax-loading').css({
						position: 'fixed',
						left: $(window).width()/2+10,
						top: $(window).height()/2+16,
					});
				}
			},
		},

		'showAjaxForm':{
			element:null,
			values:null,
			url:null,
			title:null,
			callback:function(data){},
		},

		'modal':{
			element:null,
			container:null,
			data:null,
			url:null,
			title:null,
			callback:function(){},
		},
        
        'modalClose':{
			container: null,
		},

		'template':{
			element:null,
			source:null,
			values:null,
			method:'replaceContent',
			callback:function(){},
		},
	}
});

// Хелперы для js шаблонизатора для вывода даты и ссылки

/*
	{{link module=user action=view id=codeid}}
*/
Handlebars.registerHelper('link', function(params) {
	var module = params.hash.module || 'index';
	var action = params.hash.action || 'index';

	str_params = '';
	if ( params.hash!={} ) {
		$.each(params.hash, function(index, value){
			if ( index=='module' || index=='action' ) return;
			str_params += '&'+index+'='+value;
		});
	}
	return 'index.php?module='+module+'&action='+action+str_params;
});

Handlebars.registerHelper('dateFormat', function(context, block) {
    var f = block.hash.format || "LL";
    if ( context===false || context===undefined ) return '';

    if(f=="DD.MM.YYYY") {
        var today = new Date(context * 1000);
        /*
        var tomorrow = new Date(today);
        tomorrow.setDate(today.getDate()+1);
        var date = tomorrow.toJSON().slice(0,10);
        return moment(date,'YYYY-MM-DD').format('DD.MM.YYYY');
        */
        return moment(today,'YYYY-MM-DD').format('DD.MM.YYYY');
    }

    return moment(new Date(context * 1000)).utcOffset('+06:00').format(f);
});

Handlebars.registerHelper("debug", function(optionalValue) {
	console.log("Current Context");
	console.log("====================");
	console.log(this);

	if (optionalValue) {
		console.log("Value");
		console.log("====================");
		console.log(optionalValue);
	}
});

Handlebars.registerHelper('dateFormat2', function(date) {

	return moment(date,'YYYY-MM-DD hh:mm:ss').utcOffset('+06:00').format('DD.MM.YYYY');
});

Handlebars.registerHelper('dateFormatD', function(date) {
	return moment(date,'YYYY-MM-DD').format('DD.MM.YYYY');
});

Handlebars.registerHelper('dateSQL', function(date, type='date') {
    if(date=='')
        return '';
    else {
        if (type=='date'){
            return elly.date(date, 'd.m.Y')
        }
        else {
           return elly.date(date, 'd.m.Y H:i');
        }
    }
});

Handlebars.registerHelper('dateSQLT', function(date) {
    if(date=='')
        return '';
    else
	return elly.date(date, 'd.m.Y');
});

Handlebars.registerHelper('dateFormat3', function(date) {
    if(date=='')
        return '';
    else
	   return moment(date,'MMM DD YYYY hh:mm:ss:sss').utcOffset('+06:00').format('DD.MM.YYYY HH:mm');
});

Handlebars.registerHelper('dateFormatUni', function(date, block) {
    if(date=='')
        return '';
    else
	   return moment(date,'MMM DD YYYY hh:mm:ss:sss').utcOffset('+06:00').format(block);
});

Handlebars.registerHelper("getTitle", function(Id, table, field) {
    try {
        return eval(table + '.get(' + Id + ').' + field);
    } catch (err) {
        return Id;
    }
});

Handlebars.registerHelper("selectedS", function(codeid, code) {
    if(codeid==code)
		return 'selected';
});

Handlebars.registerHelper("selectedExcess", function(code) {
if(!isNaN(parseFloat(code)) && isFinite(code)){
    if('0'==code){
       return '<option value="0" selected>0</option><option value="0.5">0.5</option><option value="1">1</option>';
    }
    else if('0.5'==code){
       return '<option value="0">0</option><option value="0.5" selected>0.5</option><option value="1">1</option>';
    }
    else if('1'==code){
        return '<option value="0">0</option><option value="0.5">0.5</option><option value="1" selected>1</option>';
    }
    else {
        return '<option value="0">0</option><option value="0.5">0.5</option><option value="1">1</option><option value="'+code+'" selected>'+code+'</option>';
    }
}
else{
    return '<option value="0">0</option><option value="0.5">0.5</option><option value="1">1</option>';
}

});

Handlebars.registerHelper("checkedS", function(codeid, code) {
    if(codeid==code)
		return 'checked';
});

Handlebars.registerHelper("checkS", function(codeid) {
    if(codeid==1)
		return 'checked';
});

Handlebars.registerHelper("viewHideS", function(code) {
    if(code==1)
		return "display: block;";
    else
        return "display: none;";
});

Handlebars.registerHelper("viewShowS", function(code) {
    if(code==0)
		return "display: block;";
    else
        return "display:none;";
});