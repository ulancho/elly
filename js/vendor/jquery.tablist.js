(function($) {
	var
		defaults = {
			colorBox: null,
			showTitles: false
		},
		containerElement,
		workContainer,
		observer,
		scrollBarWidth,
		scrollCorrected = false
	;

	var row, grID, subrow;

	var fns = {
		'checkTLInners': function() {
			var res = false;
			workContainer.find('.tl-field-left').not('.tl-hidable').each(function(){
				if ($(this).outerWidth() <= $(this).find('span').innerWidth() && $(this).find('span').innerWidth() && workContainer.hasClass('tl-wide')) {
					settings.minsize = workContainer.innerWidth();
					res = true;
					return false;
				}
			});
			return res;
		},

		'checkTLSize': function() {
			if (workContainer.innerWidth() <= settings.minsize || fns.checkTLInners()) {
				workContainer.removeClass('tl-wide').addClass('tl-narrow');
			} else {
				workContainer.removeClass('tl-narrow').addClass('tl-wide');
				$('#tl-title').removeClass('tl-narrow').addClass('tl-wide');

				// Фаерфокс не может пропердеться.
				/*var test = $('<div id="tl-check-font-size" style="display: none;">a</div>');
				test.appendTo(workContainer);
				var testSize = test.height();
				test.remove();

				var shouldHide = false;
				$('.tl-hidable').each(function(){
					if ($(this).height() > testSize) {
						shouldHide = true;
						return false;
					}
				});
				if (shouldHide) {
					$('.tl-hidable').addClass('tl-hidden');
				} else {
					$('.tl-hidable').removeClass('tl-hidden');
				}*/
			}
			if (containerElement.innerHeight() < workContainer.outerHeight() && !scrollCorrected) {
				$('#tl-title').css('padding-right', parseInt($('#tl-title').css('padding-right')) + scrollBarWidth);
				scrollCorrected = true;
			}
			if (containerElement.innerHeight() >= workContainer.outerHeight() && scrollCorrected) {
				$('#tl-title').css('padding-right', parseInt($('#tl-title').css('padding-right')) - scrollBarWidth);
				scrollCorrected = false;
			}
			obsertver = window.setTimeout(fns.checkTLSize, 500);
		},

		'clickCounter': function() {
			$(this).toggleClass('tl-counter-active');
			$('#'+$(this).parent().parent().attr('id').replace('tl-gr', 'tl-sgr')).toggle();
			event.stopPropagation();
		},

		'clickRow': function(e) {
			if (
				//!e.shiftKey
				!e.ctrlKey
			) {
				$('.tl-row-selected').removeClass('tl-row-selected');
			}
			$(this).toggleClass('tl-row-selected');
			if (settings.colorBox) {
				$('#tl-cb-selected').css('background-color', $(this).find('.tl-field-highlight span').css('background-color'));
			}
		},

		'toggleColorTable': function() {
			$('#tl-cb-colortable').toggle();
		},

		'changeHighlight': function() {
			var color = $(this).css('background-color');
			$('#tl-cb-selected').css('background-color', color);
			$('.tl-row-selected .tl-field-highlight span').css('background-color', color);
			$('#tl-cb-colortable').hide();
		},

		'toggleStar': function() {
			$(this).find('div').toggleClass('tl-icon-not-starred').toggleClass('tl-icon-starred');
			event.stopPropagation();
		},

		'toggleFolder': function() {
			$(this).toggleClass('tl-icon-open').toggleClass('tl-icon-closed');
			$(this).closest('.tl-folder').children('.tl-folder-data').toggle();
		},

		'drawGroup': function(grIdx, grData, container) {
			var folderContainer = null;
			var x = null;
			var where = container;
			if (grData.type == 'folder') {
				folderContainer = $('<div class="tl-folder-data"></div>');
				x = $('<div class="tl-folder"><div class="tl-folder-title" id="tl-f-'+grID+'"><div id="tl-fa-'+grID+'" class="tl-folder-switch tl-icon tl-icon-open"></div><div class="tl-icon tl-icon-folder"></div>'+grData.title+'</div></div>');
				folderContainer.appendTo(x);
							  x.appendTo(container);
				$('#tl-fa-'+grID).click(fns.toggleFolder);
				where = folderContainer;
			} else if (grData.title != undefined) {
				$('<div class="tl-row-group" id="tl-gr-'+grID+'">'+grData.title+'</div>').appendTo(where);
			}
			if (grData.group != undefined && grData.group.length) {
				$.each(grData.group, function(sgrIdx, sgrData) {
					grID = (sgrData.id == undefined ? 0 : sgrData.id);
					fns.drawGroup(sgrIdx, sgrData, where);
				});
			}
			$.each(grData.items, function(rowID, rowData) {
				row = $('<div class="tl-row'+(rowID == settings.selected ? ' tl-row-selected' : '')+'" id="tl-gr-'+grID+'-rw-'+rowID+'"></div>');
				row.click(fns.clickRow);
				$.each(settings.fields, function(fk, fv) {
					row.append('<div class="tl-field '+fv.clName+'" id="tl-gr-'+grID+'-rw-'+rowID+'-fl-'+fv.name+'">'+(rowData[fv.name] != undefined ? '<span>'+rowData[fv.name]+'</span>' : '')+'</div>');
				});
				row.append('<div class="tl-clear"></div>');
				row.appendTo(where);
				if (rowData['subitems'] != undefined) {
					subrow = $('<div class="tl-subgroup" id="tl-sgr-'+grID+'-rw-'+rowID+'"></div>');
					$.each(rowData['subitems'], function(subrowID, subrowData) {
						row = $('<div class="tl-row'+(subrowID == settings.selected ? ' tl-row-selected' : '')+'" id="tl-gr-'+grID+'-rw-'+subrowID+'"></div>');
						row.click(fns.clickRow);
						$.each(settings.fields, function(fk, fv) {
							row.append('<div class="tl-field '+fv.clName+'" id="tl-gr-'+grID+'-rw-'+subrowID+'-fl-'+fv.name+'">'+(subrowData[fv.name] != undefined ? '<span>'+subrowData[fv.name]+'</span>' : '<span></span>')+'</div>');
						});
						row.appendTo(subrow);
					});
					row.append('<div class="tl-clear"></div>');
					subrow.appendTo(where);
				}
			});
		}

	};

	var methods = {
		init: function (options) {
			settings = $.extend({}, defaults, options);
			containerElement = this;

			// ���������� ������� ����������
			containerElement.append('<div id="scroll-bar-width-check" style="height: 1px; width: 50px; overflow: hidden;"><div>&nbsp;<br />&nbsp;</div></div>');
			var x1 = $('#scroll-bar-width-check div').innerWidth();
			$('#scroll-bar-width-check').css('overflow', 'auto');
			var x2 = $('#scroll-bar-width-check div').innerWidth();
			$('#scroll-bar-width-check').remove();
			scrollBarWidth = x1-x2;

			workContainer = $('<div id="tablist-container"></div>').appendTo(containerElement);
			if (settings.showTitles) {
				row = $('<div id="tl-title"></div>');
				$.each(settings.fields, function(fk, fv) {
					row.append('<div class="tl-title-field '+fv.clName+'">'+(fv.title != '' ? '<span>'+fv.title+'</span>' : '')+'</div>');
				});
				containerElement.before(row);
				containerElement.css('top', row.outerHeight());
			}
			$.each(settings.content.group, function(grIdx, grData) {
				grID = (grData.id == undefined ? 0 : grData.id);
				fns.drawGroup(grIdx, grData, workContainer);
			});

			if (settings.colorBox) {
				var colorBox = $('<div id="tl-color-box"><div id="tl-cb-selected"></div><div id="tl-cb-dropdown"></div><div id="tl-cb-colortable"><table>'+
								'<tr><td style="background-color: transparent;"></td><td style="background-color: #ff0000;">&nbsp;</td><td style="background-color: #0000ff;">&nbsp;</td><td style="background-color: #00ff00;">&nbsp;</td></tr>'+
								'<tr><td style="background-color: #ffffff;"></td><td style="background-color: #000000;">&nbsp;</td><td style="background-color: #00ffff;">&nbsp;</td><td style="background-color: #ffff00;">&nbsp;</td></tr>'+
								'</table></div></div>');
				colorBox.appendTo(settings.colorBox);
				$('#tl-cb-dropdown').click(fns.toggleColorTable);
				$('#tl-cb-colortable td, #tl-cb-selected').click(fns.changeHighlight);
			}

			$('.tl-field-star').click(fns.toggleStar);

			fns.checkTLSize();
			//obsertver = window.setTimeout(fns.checkTLSize, 500);
			$('.tl-field-counter span').click(fns.clickCounter);

			initBottomReload();

		},
		resize: fns.checkTLSize
	};
	$.fn.tablist = function (method) {
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist' );
		}





	};
})(jQuery);


function initBottomReload(){
	var
		$Box = $('#document-container'),
		$Head = $Box.find('#document-title, h2').eq(0)
	;

	$('.tl-row').each(function(){
		var
			$This = $(this),
			$Title = $This.find('.sticker-title a').eq(0)
		;

		$This.click(function(){
			//alert('x');
			$Box.fadeOut('fast',function(){
				$Head.html($Title.html());
				$Box.fadeIn('fast',function(){});
			});
		});

	})
};
