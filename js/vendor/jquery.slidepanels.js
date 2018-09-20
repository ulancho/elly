(function($) {
	var
		defaults = {
			'leftPanelSize':			0.4,
			'orientation':			'vertical'
		},

		containerElement, containerSize,
		leftColumnSize, rightColumnWidth,
		leftPanel, moverPanel, rightPanel,
		settings,
		mouseInUse = false,
		mouseInitPosition = false,
		isVertical = true;

		minBeforeCollapse = '100',
		currentWidth = defaults.leftPanelSize,
		savedForUncollapse = null,
		hiddenRight = false,
		hiddenLeft = false
	;


	var fns = {
		mousePosition: function(e){
			return (isVertical ? e.pageX : e.pageY);
		},
		mouseDown: function(e){
			mouseInUse = true;
			mouseInitPosition = fns.mousePosition(e);
			containerElement.css({'user-select': 'none', '-webkit-user-select': 'none', '-moz-user-select': 'none'});
			if ($.browser.msie) {
				containerElement.attr('onselectstart', 'return false;');
			}
		},
		mouseOver: function(){
			if (!hiddenLeft) { $('#slidepanel-collapseleft').show(); }
			if (!hiddenRight) { $('#slidepanel-collapseright').show(); }
		},
		mouseOut: function(){
			if (!mouseInUse) {
				$('#slidepanel-collapseleft, #slidepanel-collapseright').hide();
			}
		},
		mouseUp: function(e){
			if (mouseInUse) {
				mouseInUse = false;
				mouseInitPosition = false;
				containerElement.css({'user-select': 'all', '-webkit-user-select': 'auto', '-moz-user-select': 'auto'});
				if ($.browser.msie) {
					containerElement.attr('onselectstart', 'return true;');
				}
			}
		},
		mouseMove: function(e){
			if (mouseInUse && mouseInitPosition) {
				hiddenLeft = hiddenRight = false;
				$('#slidepanel-collapseleft, #slidepanel-collapseright').show();
				var currentSlide = settings.leftPanelSize + (fns.mousePosition(e) - mouseInitPosition) / containerSize;
				if (currentSlide < minBeforeCollapse / containerSize) { currentSlide = minBeforeCollapse / containerSize; }
				if (currentSlide > (containerSize - minBeforeCollapse) / containerSize) { currentSlide = (containerSize - minBeforeCollapse) / containerSize; }
				if (currentWidth != currentSlide) {
					currentWidth = currentSlide;
					mouseInitPosition = fns.mousePosition(e);
					methods.resize.apply(containerElement, [{'leftPanelSize': currentSlide}]);
				} else if (currentSlide <= minBeforeCollapse / containerSize) {
					hiddenLeft = true;
					methods.resize.apply(containerElement, [{'leftPanelSize': -2 / containerSize}]);

					$('#slidepanel-collapseleft').hide();
				} else if (currentSlide >= (containerSize - minBeforeCollapse) / containerSize) {
					hiddenRight = true;
					methods.resize.apply(containerElement, [{'leftPanelSize': (containerSize - 7) / containerSize}]);

					$('#slidepanel-collapseright').hide();
				}
				savedForUncollapse = null;
			}
		},
		collapseLeft: function(){
			var currentSlide;
			if (savedForUncollapse == null) {
				// коллапсируем из раскрытого положения
				savedForUncollapse = settings.leftPanelSize;
				currentSlide = -2 / containerSize;
				hiddenLeft = true;
				hiddenRight = false;
			} else {
				// расколлапс в сохраненное положение
				currentSlide = savedForUncollapse;
				savedForUncollapse = null;
				hiddenLeft = false;
				hiddenRight = false;
			}
			methods.resize.apply(containerElement, [{'leftPanelSize': currentSlide}]);
		},
		collapseRight: function(){
			var currentSlide;
			if (savedForUncollapse == null) {
				// коллапсируем из раскрытого положения
				savedForUncollapse = settings.leftPanelSize;
				currentSlide = (containerSize - 7) / containerSize;
				hiddenLeft = false;
				hiddenRight = true;
			} else {
				// расколлапс в сохраненное положение
				currentSlide = savedForUncollapse;
				savedForUncollapse = null;
				hiddenLeft = false;
				hiddenRight = false;
			}
			methods.resize.apply(containerElement, [{'leftPanelSize': currentSlide}]);
		},
		switchVertHor: function(){
			methods.resize.apply(containerElement, [{'orientation': (isVertical ? 'horizontal' : 'vertical')}]);
			settings.switchPlaceholder.removeClass('slidepanel-horizontal').removeClass('slidepanel-vertical').addClass('slidepanel-'+settings.orientation);
            console.log(settings.switchPlaceholder[0].className)
		}

	};

	var methods = {
		init: function (options) {
			settings = $.extend(defaults, options);
			settings.orientation = ((settings.orientation == 'horizontal') ? 'horizontal' : 'vertical');
			isVertical = ((settings.orientation == 'vertical') ? true : false);
			containerElement = this;
			containerElement.addClass('slidepanel-container').removeClass('slidepanel-horizontal').removeClass('slidepanel-vertical').addClass('slidepanel-'+settings.orientation);

			if (settings.leftPanel) { settings.leftPanel.detach(); }
			if (settings.rightPanel) { settings.rightPanel.detach(); }

			leftPanel = $('<div id="slidepanel-left"><div id="slidepanel-left-wrapper"></div></div>').appendTo(containerElement);
			moverPanel = $('<div id="slidepanel-mover"><img class="mover-ico" src="img/icons/mover-icon-vert.gif" alt="" width="7" height="8" border="0" /><div id="slidepanel-collapseleft"></div><div id="slidepanel-collapseright"></div></div>').appendTo(containerElement);
			rightPanel = $('<div id="slidepanel-right"><div id="slidepanel-right-wrapper"></div></div>').appendTo(containerElement);

			if (settings.leftPanel) { settings.leftPanel.appendTo($('#slidepanel-left-wrapper')); }
			if (settings.rightPanel) { settings.rightPanel.appendTo($('#slidepanel-right-wrapper')); }

			if (settings.switchPlaceholder) {
				settings.switchPlaceholder.addClass('slidepanel-'+settings.orientation);
				$('<div id="slidepanel-swith-vertical"></div>').click(function(){
                    if(!settings.switchPlaceholder.hasClass('slidepanel-vertical')){
                        fns.switchVertHor();
                    }
                }).appendTo(settings.switchPlaceholder);
				$('<div id="slidepanel-swith-horizontal"></div>').click(function(){
                    if(!settings.switchPlaceholder.hasClass('slidepanel-horizontal')){
                        fns.switchVertHor();
                    }
                }).appendTo(settings.switchPlaceholder);
			}

			methods.resize.apply(this, [options]);

			moverPanel.bind('mousedown', fns.mouseDown).bind('mouseover', fns.mouseOver).bind('mouseout', fns.mouseOut);
			$('#slidepanel-collapseleft').bind('click', fns.collapseLeft);
			$('#slidepanel-collapseright').bind('click', fns.collapseRight);
			$(document).bind('mouseup', fns.mouseUp).bind('mousemove', fns.mouseMove);
		},
		resize: function (options) {
			if (containerElement) {


				$.extend(settings, options);
				settings.orientation = ((settings.orientation == 'horizontal') ? 'horizontal' : 'vertical');
				isVertical = ((settings.orientation == 'vertical') ? true : false);
				containerElement.removeClass('slidepanel-horizontal').removeClass('slidepanel-vertical').addClass('slidepanel-'+settings.orientation);

				containerSize = (isVertical ? containerElement.innerWidth() : containerElement.innerHeight());

				leftColumnSize = (containerSize * settings.leftPanelSize) / containerSize * 100;
				rightColumnWidth = (containerSize - (containerSize * settings.leftPanelSize)) / containerSize * 100;

				if (isVertical) {
					leftPanel.css({'width': leftColumnSize+'%', 'height': '100%'});
					rightPanel.css({'width': rightColumnWidth+'%', 'height': '100%'});
					moverPanel.css({'left': leftColumnSize+'%', 'top': 0});
				} else {
					leftPanel.css({'height': leftColumnSize+'%', 'width': '100%'});
					rightPanel.css({'height': rightColumnWidth+'%', 'width': '100%'});
					moverPanel.css({'top': leftColumnSize+'%', 'left': 0});
				}
				//console.log(hiddenLeft);
				//console.log(hiddenRight);
				leftPanel.toggle(!hiddenLeft);
				rightPanel.toggle(!hiddenRight);

			}
		}

	};
	$.fn.slidepanels = function (method) {
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist' );
		}
	};
})(jQuery);

