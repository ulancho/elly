(function($) {
	var
		defaults = {
			width: 'auto',
			height: 'auto',
			top: 0,
			left: 0,
			background: 'transparent',
			popupBox: null,
			modalBox: null,
			targetButton: null,
			newButton: null
		},
		settings = []
	;
	

	var fns = {
		'genID':	function() {
			return ((1+Math.random(+new Date))*parseInt('1000000000',16)).toString(16);
		},
		'show':		function () {
			var thisID = this.parents('.jp-popup').attr('id').substr(3, 1000);
			settings[thisID].modalBox.show();
			settings[thisID].popupBox.show();
			$(document).bind('keyup', fns.keyclose);
			settings[thisID].popupBox.css({
				top: settings[thisID].top,
				left: settings[thisID].left
			});
			if (settings[thisID].targetButton) {
				if (settings[thisID].targetButton.offset().left - settings[thisID].newButton.offset().left) settings[thisID].popupBox.css({
					left: settings[thisID].targetButton.offset().left - settings[thisID].newButton.offset().left
				});
				if (settings[thisID].targetButton.offset().top - settings[thisID].newButton.offset().top) settings[thisID].popupBox.css({
					top: settings[thisID].targetButton.offset().top - settings[thisID].newButton.offset().top
				});
			}				
		},
		'hide':		function () {
			$('.jp-popup-modal').hide();
			$('.jp-popup').hide();
			$(document).unbind('keyup', fns.keyclose);
		},
		'keyclose':	function (e) {
			if (e.keyCode == 27) {
				$('.jp-popup').popup('hide');
			}
		}
	};

	var methods = {
		init: function (options) {
			var thisID = fns.genID();
			settings[thisID] = $.extend({}, defaults, options);
			this.wrap('<div class="jp-popup" id="jp-'+thisID+'" />').before('<ins class="t l"><ins></ins></ins><ins class="t r"><ins></ins></ins>').after('<a class="close"></a><ins class="b l"><ins></ins></ins><ins class="b r"><ins></ins></ins>').addClass('box');
			
			settings[thisID].popupBox = this.parents('.jp-popup');
			settings[thisID].popupBox.find('.close').click(fns.hide);
			settings[thisID].popupBox.css({
				width: settings[thisID].width,
				height: settings[thisID].height,
				top: settings[thisID].top,
				left: settings[thisID].left
			});
			
			if (settings[thisID].targetButton) {
				settings[thisID].newButton = settings[thisID].targetButton.clone().appendTo(this);
			}

			settings[thisID].modalBox = $('<div class="jp-popup-modal" id="jp-modal-'+thisID+'" />').appendTo('body');
			settings[thisID].modalBox.css('background-color', settings[thisID].background);
		},
		show: fns.show,
		hide: fns.hide
	};
	$.fn.popup = function (method) {
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist' );
		}  
	};
})(jQuery);

