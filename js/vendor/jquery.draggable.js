
 
(function($){
	// static constructs
	$.draggable = $.draggable || {version: '1.0'};
	
	$.draggable.conf = {
		sensitivity: 1.0,
		edges: false
	}
	
	function Draggable (root, conf) {
		// Flags
		var isMoved = false;
		
		// draggable properties
		var self = this,
			sensitivity = conf.sensitivity
			pos = {
				x: root.position().left,
				y: root.position().top
			},
			width = root.width(),
			height = root.height(),
			mousePos_ =  { x: 0, y: 0 },
			mousePos =  { x: 0, y: 0 },
			mouseShift =  { x: 0, y: 0 },
			min = { x: 0, y: 0 },
			max = { x: 0, y: 0 },
			start = { x: 0, y: 0 },
			end = { x: width, y: height },
			
			imitation = {}; // позиция при имитации клика
		
		$.extend(self, {
			init: function() {
				self.updateEdges();
			},
			
			setSens: function(val) {
				sensitivity = val;
			},
			
			setPos: function(_pos) {
				pos.x = _pos.x;
				pos.y = _pos.y;
				
				if (_pos.x < min.x && conf.edges) pos.x = min.x;
				if (_pos.y < min.y && conf.edges) pos.y = min.y;
				if (_pos.x > max.x && conf.edges) pos.x = max.x;
				if (_pos.y > max.y && conf.edges) pos.y = max.y;
				
				root.css({
					left: Math.round(pos.x),
					top: Math.round(pos.y)
				});
			},
			
			setSensitivity: function(val) {
				sensitivity = val;
			},
			
			getPos: function(){
				return pos;
			},
			
			imitateMousedown: function(fakePos) {
				imitation.x = fakePos.x;
				imitation.y = fakePos.y;
				
				root.mousedown();
			},
			
			setNewPos: function(e) {
				mousePos_.x = e.pageX;
				mousePos_.y = e.pageY;
				
				pos.x = root.position().left,
				pos.y = root.position().top;
			},
			
			/*getPosPercents: function(){
				if (posX < minX) posX = minX;
				if (posX > maxX) posX = maxX;
			
				return (100 * (posX - startX) / (endX - startX)).toFixed(2);
			},
			
			setPosPercents: function(posPercent) {				
				pos = startX - 1 * posPercent * Math.abs(endX - startX) / 100 ;
				
				if (pos < minX) pos = minX;
				if (pos > maxX) pos = maxX;
								
				root.css('left', Math.round(pos) + 'px');	
			},
			
			setStartX: function(val) {
				startX = val;
			},
			
			setEndX: function(val) {
				endX = val;
			},*/
			
			updateEdges: function(){
				minX = root.parent().outerWidth() - root.width();
				maxX = 0;
			} 
		});
		
		// Init
		self.init();
		
		// Events
		root.mousedown(function(event){			
			isMoved = true;
			
			if (imitation.x || imitation.y) {
				mousePos_.x = imitation.x;
				mousePos_.y = imitation.y;
			} else {
				mousePos_.x = event.pageX;
				mousePos_.y = event.pageY;
			}
			
			pos.x = root.position().left,
			pos.y = root.position().top;
			
			imitation = {};
			
			return false;
		});
		
		$(document).mouseup(function(){			
			isMoved = false;
		}).mousemove(function(event){
			if (isMoved) {
				if (conf.move) conf.move();
				
				mousePos.x = event.pageX;
				mousePos.y = event.pageY;
				
				mouseShift.x = mousePos.x - mousePos_.x;
				mouseShift.y = mousePos.y - mousePos_.y;
				
				pos.x += mouseShift.x * sensitivity;
				pos.y += mouseShift.y * sensitivity;
				
				self.setPos({
					x: pos.x,
					y: pos.y
				});
				
				mousePos_.x = mousePos.x;
				mousePos_.y = mousePos.y;
			}
		}).resize(function(){
			if (conf.edges) self.updateEdges();
		});
		
		return self;
	}	

	$.fn.draggable1 = function(conf) {
		
		// already constructed --> return API
		var el = this.data("draggable");
		if (el) { return el; }	
		
		conf = $.extend({}, $.draggable.conf, conf);
		
		this.each(function() {			
			el = new Draggable($(this), conf);
			$(this).data("draggable", el);	
		});
		
		return conf.api ? el: this; 
	};
})(jQuery);

jQuery.fn.extend({ 
	disableSelection : function() {
		this.each(function() { 
			this.onselectstart = function() { return false; }; 
			this.unselectable = "on"; 
			jQuery(this).css('-moz-user-select', 'none'); 
		}); 
	},
	enableSelection : function() { 
		this.each(function() { 
			this.onselectstart = function() {}; 
			this.unselectable = "off"; 
			jQuery(this).css('-moz-user-select', 'auto'); 
		}); 
	} 
});