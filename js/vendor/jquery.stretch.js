
// растягивает элемент по высоте на все свободное пространство
(function($){
	$.fn.stretch = function(options) {  
	
		// options
	    var o = {
			className: 'stretched',
			debug: false
	    };
		
		$.extend(o, options);
	
		// максимальная высота элемента от сих до нижнего предела, 
		// чтобы данный элемент заполнил все свободной пространство родителя
		function GetMaxHeight(el) {
			var prev, next, thisY, nextY;
			
			var parent = el.parent();
				parent.css('position', 'relative');
			
			// верхний край
			thisY = el.position().top;
			
			// нижний край
			nextY = el.parent().height();
	
			return (nextY - thisY);
		}
		
		var self = this;
		
		$(window).resize(function(){
			self.each(function() {
				var el = $(this);
				
				var h = GetMaxHeight(el);
				el.height(h);
				
				if (el.outerHeight() != h) el.height(2 * h - el.outerHeight());			
	    	});
		});
		$(window).resize();
			
	    return this;
	};
})(jQuery);