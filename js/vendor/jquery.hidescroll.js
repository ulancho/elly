(function($) {
	var
		defaults = {
			containerElement: null,
			scrollElement: null,
			hiderElement: null,
			scrollTop: 0
		},
		settings = [],
		scrollBarWidth,
		re = /hider-[^\s"]+/
	;
	

	var fns = {
		'genID':	function() {
			return ((1+Math.random(+new Date))*parseInt('1000000000',16)).toString(16);
		},
		'mousein':	function(){
			var thisID = re.exec(this.className)[0].substring(6, 1000);
			if (fns.checkHeight(settings[thisID].scrollElement)) settings[thisID].scrollElement.css('padding-right', 0);
			settings[thisID].scrollElement.css('overflow', 'auto');
			settings[thisID].hiderElement.hide();
			settings[thisID].scrollElement.scrollTop(settings[thisID].scrollTop);
		},
		'mouseout':	function(){
			var thisID = re.exec(this.className)[0].substring(6, 1000);
			if (fns.checkHeight(settings[thisID].scrollElement)) settings[thisID].scrollElement.css('padding-right', scrollBarWidth);
			settings[thisID].scrollElement.css('overflow', 'hidden');
			settings[thisID].hiderElement.show();
			settings[thisID].scrollTop = settings[thisID].scrollElement.scrollTop();
		},
		'checkHeight': function(e){
			return e.innerHeight() < e.children().outerHeight();
		}
	};

	var methods = {
		init: function (options) {
			var thisID = fns.genID();
			settings[thisID] = $.extend({}, defaults, options);
			settings[thisID].containerElement = this;

			// вычисление размера скроллбара
			settings[thisID].containerElement.append('<div id="scroll-bar-width-check" style="height: 1px; width: 50px; overflow: hidden;"><div>&nbsp;<br />&nbsp;</div></div>');
			var x1 = $('#scroll-bar-width-check div').innerWidth();
			$('#scroll-bar-width-check').css('overflow', 'auto');
			var x2 = $('#scroll-bar-width-check div').innerWidth();
			$('#scroll-bar-width-check').remove();
			scrollBarWidth = x1-x2;

			settings[thisID].hiderElement = $('<div id="hider-'+thisID+'" class="scroll-hider-block" style="position: relative; float: right; top: 0; right: 0; width: 15px; height: 100%; xopacity: 0.4; xbackground-color: #ff0000; display: block; z-index: 100;"></div>')
							.prependTo(settings[thisID].containerElement);
			settings[thisID].containerElement.addClass('hider-'+thisID);
			//settings[thisID].scrollElement.css('overflow', 'hidden');
			if (fns.checkHeight(settings[thisID].scrollElement)) {	
				settings[thisID].scrollElement.css('padding-right', scrollBarWidth);
				console.log('ugu');
			}
			settings[thisID].containerElement.bind('mouseenter', fns.mousein).bind('mouseleave', fns.mouseout);
		}
	};
	$.fn.hidescroll = function (method) {
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist' );
		}  
	};
})(jQuery);

