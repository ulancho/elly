
var TITLE_CLASS = 'sticker-title';
var TITLE_MOUSE_DOWN_CLASS = 'sticker-title-mouse-down';
var DRAGGED_CLASS = 'sticker-dragged';
var IMAGE_CLASS = 'sticker-image';
var IMAGE_ID_PREFIX = 'sticker-image';
var IMAGE_TYPE_PREFIX = 'sticker-image';
var IMAGE_MOUSEDOWN_CLASS = 'sticker-image-mousedown';
var SEPARATOR = '-';
var STORAGE_ID = 'stickers';
var STORAGE_TRIGGER_ID = 'stickers-trigger';

(function($){
	// static constructs
	$.sticker = $.sticker || {version: '1.0'};
	
	$.sticker.conf = {
		
	}
	
	function Sticker (root, conf) {		
		// sticker properties
		var self = this;
		var image = $();
		var imageAPI = null;
		var icon = $();
		var storage = $();
		var storageProps = {x: 0, y: 0, w: 0, h: 0};
		
		var save = false; // to save or not to save that is the question
		
		$.extend(self, {			
			init: function() {
				storage = $('#' + STORAGE_ID);
				
				root.mousedown(function(){
					root.addClass(TITLE_MOUSE_DOWN_CLASS);
				});
				
				$(document).mousemove(function(e) {					
					if (root.hasClass(TITLE_MOUSE_DOWN_CLASS)) {
						if (!root.hasClass(DRAGGED_CLASS)) {
							root.addClass(DRAGGED_CLASS);
							self.createImage({x: root.offset().left, y: root.offset().top}, e);
							
							if (storage.is(':visible')) {
								storageProps = {
									x: storage.offset().left,
									y: storage.offset().top,
									w: storage.width(),
									h: storage.height()
								};
							}
						}
					}
				}).mouseup(function() {
					root.removeClass(TITLE_MOUSE_DOWN_CLASS);
					root.removeClass(DRAGGED_CLASS);
				});
				
				root.find('a').mousedown(function(e){
					e.preventDefault();
				});
			},
			
			createImage: function(pos, e) {
				var imageId = IMAGE_ID_PREFIX + SEPARATOR + root.attr('id');
				
				if ($('#' + imageId).size() == 0) {
					image = $('<div />').addClass(IMAGE_CLASS);
					image.html(root.html());
					image.appendTo('body');
					
					var type = root.attr('id').split(SEPARATOR)[0];
					image.addClass(IMAGE_TYPE_PREFIX + SEPARATOR + type);
					image.attr('id', imageId).addClass(IMAGE_MOUSEDOWN_CLASS);
					
					icon = $('<span class="icon remove" />');
					icon.appendTo(image);
					
					// это дикая конструкция нужна, чтобы можно было увести курсор
					// с иконки удаления стикера уже после нажатия, чтобы сохранить стикер
					icon.mousedown(function() {
						e.stopPropagation();
						return false;
					}).mousemove(function(e) {
						e.stopPropagation();
						return false;
					}).mouseup(function() {
						image.remove();
					});
					
					image.css({
						left: pos.x,
						top: pos.y
					});
					
					image.draggable1();
					
					imageAPI = image.data('draggable');
					//console.dir(imageAPI);
					//if(imageAPI.imitateMousedown){
					imageAPI.imitateMousedown({
						x: e.pageX,
						y: e.pageY
					});
					//}
					
					image.disableSelection();
					image.find('*').disableSelection();
					
					image.mousedown(function() {
						image.addClass(IMAGE_MOUSEDOWN_CLASS);
						
						// поднимать выделяемый стикер над остальными
						var imageParent = image.parent();
						image.appendTo(imageParent);
					});
					$(document).mousemove(function(e) {
						
					}).mouseup(function(e){
						if (image.hasClass(IMAGE_MOUSEDOWN_CLASS)) {
							self.up(e);
							image.removeClass(IMAGE_MOUSEDOWN_CLASS);
							
							if (!save) image.remove();
						}
					});
				}
			},
			
			up: function(e) {
				if (storage.is(':visible')) {
					// попал ли стикер в лукошко
					if (e.pageX > storageProps.x &&
					e.pageY > storageProps.y &&
					e.pageX < (storageProps.x + storageProps.w) &&
					e.pageY < (storageProps.y + storageProps.y)
					) {
						if (!save) {
							image.css({
								left: image.offset().left - storageProps.x,
								top: image.offset().top - storageProps.y
							});
							
							imageAPI.setNewPos(e);
							image.appendTo(storage);
							
							save = true;
						}							
					}
					else {
						if (save) {
							
							image.css({
								left: image.offset().left,
								top: image.offset().top
							});
							
							imageAPI.setNewPos(e);
							image.appendTo('body');
							
							save = false;
						}
					}
				}
			}
		});		
		
		self.init();
		
		
		// events
		
		return self;
	}	

	$.fn.sticker = function(conf) {
		
		// already constructed --> return API
		var el = this.data("sticker");
		if (el) { return el; }	
		
		conf = $.extend({}, $.sticker.conf, conf);
		
		this.each(function() {			
			el = new Sticker($(this), conf);
			$(this).data("sticker", el);	
		});
		
		return conf.api ? el: this; 
	};
})(jQuery);

(function($){
	// static constructs
	$.stickersStorage = $.stickersStorage || {version: '1.0'};
	
	$.stickersStorage.conf = {
		
	}
	
	function StickersStorage (root, conf) {		
		// stickersStorage properties
		var self = this;			
		var storage = $();
		var iconHide = $();
		var titles = $();
		
		$.extend(self, {			
			init: function() {
				storage = $('<div id="' + STORAGE_ID + '" />').appendTo('body');
				iconHide = $('<div id="' + STORAGE_TRIGGER_ID + '" />').appendTo('body');
			},
			
			show: function(fn) {
				storage.slideDown(fn);
			},
			
			hide: function(fn) {
				storage.slideUp(fn);
			}
		});
		
		
		self.init();
		
		
		// events
		$(document).keypress(function(e) {
			if (e.keyCode == 1105 || e.keyCode == 96) {
				if (storage.is(':visible')) {
					self.hide();
				}
				else {
					self.show();
				}
			}
		}).keydown(function(e) {
			if (e.keyCode == 27) {
				self.hide();
			}
		});		
		
		
		iconHide.click(function() {
			if (storage.is(':visible')) {
				self.hide(function() {
					iconHide.appendTo('body');
				});
			}
			else {
				iconHide.appendTo(storage);
				self.show();
			}
		});
		
		
		return self;
	}	

	$.fn.stickersStorage = function(conf) {
		
		// already constructed --> return API
		var el = this.data("stickersStorage");
		if (el) { return el; }	
		
		conf = $.extend({}, $.stickersStorage.conf, conf);
		
		this.each(function() {			
			el = new StickersStorage($(this), conf);
			$(this).data("stickersStorage", el);	
		});
		
		return conf.api ? el: this; 
	};
})(jQuery);



