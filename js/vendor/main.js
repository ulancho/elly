// Все, что нужно растянуть до низу, нужно растянуть до низу
$(function() {
	$('#content-stretched-wrap, #sidebar').stretch();
	//$('body *').disableSelection();

	// $('body').stickersStorage();

	// setTimeout(function() {
	// 	$('.sticker-title').sticker();
	// }, 500);
});

// показать/спрятать дерево папок
$(function($){
	$('#panels-container').removeClass('content-panels-wide');
	$('#content-tree').removeClass('hidden');

	$('#sp-expand-tree').addClass('expanded').click(function(){
		$(this).toggleClass('expanded');
		$('#content-tree').toggleClass('hidden');
		$('#panels-container').toggleClass('content-panels-wide');
		$('#content-actions').toggleClass('content-actions-wide');
	});
});

// инициализация разворачивалки и подсветки в списке папок
$(function(){
    $(document).on('mousedown', '.fl-expand', function(){
		var icon = $(this);
        var parent = icon.parents('li.fl-group').eq(0).toggleClass('fl-closed').toggleClass('fl-open');
		if (parent.is('.fl-closed')) icon.html('&#9654;');
		if (parent.is('.fl-open')) icon.html('&#9660;');
    });
    $('.fl-selected a').append('<div class="fl-arrow-left"></div><div class="fl-arrow-right"></div>');
});


// работа с панелью инструментов
var toolbarObserver = function() {
	if ($('.content-tools-item-dropped').size()) {
		$('#content-tools-toolbar-more').show();
	} else {
		$('#content-tools-toolbar-more').hide();
	}
    var bLast = false;
	$('#content-tools-toolbar .content-tools-main-group .content-tools-item').each(function(i){
		var current = $(this);
        current
            .removeClass('content-tools-item-dropped')
            .removeClass('content-tools-item-dropped-visible');

        //console.log(i +' ' +current.position().top);
		if (current.position().top > 0 || bLast) {
			current.addClass('content-tools-item-dropped');
            bLast = true;
		} else {
			//current.removeClass('content-tools-item-dropped').removeClass('content-tools-item-dropped-visible');
		}
	});
}
$(function(){
	if (!$('#content-tools-toolbar #content-tools-toolbar-more').size()) {
		$('#content-tools-toolbar').append('<div class="content-tools-item" id="content-tools-toolbar-more"><span class="icon more"></span></div>');
	}
	toolbarObserver();
	$(window).resize(toolbarObserver);
	$('#content-tools-toolbar-more').click(function(){
       $('.content-tools-item-dropped').each(function(index){
			var
                $This = $(this)
            ;
            $This
                .toggleClass('content-tools-item-dropped-visible')
            ;
            var iHeight = $This[0].offsetHeight
			    //.css('top', 24 * index + 27)
            $This.css('top', iHeight * index + iHeight)
                //.parents()                    .css('zIndex','1000')

            ;

            $This.parents('.content-tools-main-group')
                .css('overflow', $This.hasClass('content-tools-item-dropped-visible') ? 'visible':'')

		});

        return false;
	});

    $(document).click(function(){
        $('.content-tools-item-dropped')
            .removeClass('content-tools-item-dropped-visible')
    })
});

// инициализация табов в документе
$(function(){
	$('.document-content-tab-list a').click(function(e){ alert(1);
		$('.document-content-tab-list li, .document-content-tab').removeClass('document-content-tab-active');
		$(this).parent().addClass('document-content-tab-active');
		$($(this).attr('href')).addClass('document-content-tab-active');
		$('#document-container').get(0).scrollTop = 0;
		if(this.hash != '#document-content-timeline') {
			e.preventDefault();
		}
		else {
			this.hash = '#last-timelime-item';
		}
	});
});

$(function(){
	$('.document-content-tab2-list a').click(function(e){
		$('.document-content-tab2-list li, .document-content-tab2').removeClass('document-content-tab2-active');
		$(this).parent().addClass('document-content-tab2-active');
		$($(this).attr('href')).addClass('document-content-tab2-active');
		e.preventDefault();
	});
});


// инициализация хаверов связанных документов
$(function(){
	$('.document-related-item').mouseenter(function(){
		$(this).addClass('document-related-item-hover');
	}).mouseleave(function(){
		$(this).removeClass('document-related-item-hover');
	});
});

/* Что это за хрень вообще?
// пряталка блока превьюшек у темплейтов
$(function(){
	$('#template-preview-toggle').click(function(){
		$('#template-preview .template-preview-img').toggle();
		$(this).toggleClass('icon-showblock').toggleClass('icon-hideblock');
	});
});
*/

// Сайдбар-стрелочки
$(function() {
	var clonedClass = 'do-not-seek-to-this';

	var s = $('#sidebar');
	var ul = $('ul', s);
	var ulw = $('#tasks-wrap');
	var li = $('li', ul);
	var arrs = $('.arrs', s);

	var liH = $('li', s).outerHeight();
	var itemsInvisible = 0;

	$(window).resize(function() {
		var sH = s.outerHeight() - 50; // с поправкой на место для кнопок
		var ulH = ul.outerHeight();

		var itemsVisible = Math.floor(sH / liH);

		// if (sH < ulH) {

		// 	ulw.height(itemsVisible * liH);
		// 	arrs.show();
		// } else
		{
			arrs.hide();
			ulw.height('auto');
		}


		// это хак для плагина скроллабл, чтобы он переставал скроллить,
		// когда последний пункт становится видимым
		$('.' + clonedClass, ul).removeClass(clonedClass);
		itemsInvisible = li.size() - itemsVisible;
		if (itemsInvisible < 0) itemsInvisible = 0;
		$('li:gt(' + itemsInvisible + ')', ul).addClass(clonedClass);
	});

	$(window).resize();

	// это индекс верхнего пункта меню,
	// чтобы выделенный пункт не оказался за пределами видимости
	var initialIndex = li.index('.selected');

	if (initialIndex > itemsInvisible) {
		initialIndex = itemsInvisible;
	}

	s.scrollable({
		prev: '.top',
		next: '.bottom',
		vertical: true,
		items: 'ul',
		item: 'li',
		clonedClass: clonedClass,
		mousewheel: true,
		keyboard: false,
		initialIndex: initialIndex
	});
});





$(function(){
	var
		$Button = $('.mark_as_button'),
		$Bubble = $('#mark_as_bubble')
	;

	$Button
		.click(function(e){
			var oPos = $Button.position();

            $('#create_doc_bubble').hide();

			$Bubble
				.toggle()
				.css({
					'margin':'0'
				})
				.offset($Button.offset())

				.css({
					'margin'	: '40px 0 0 -25px'
				})
			;
            if($Bubble.is(':visible')){
                $(document).one('click',function(){
                     $Bubble.hide();
                })
            }
            //e.preventDefault();
            //e.stopPropagation();

            return false;
		})
});


$(function(){
	var
		$All = $('.Hn_All'),
		$Cur = $('.Hn_Cur')
	;

	$('.Hn_Up').click(function(){
		$All.hide();
		$Cur.show();
	});
	$('.Hn_Down').click(function(){
		$All.show();
		$Cur.hide();
	})
});

$(function(){
	var
		$Items = $('.content-tools-item'),
		$Bubble = $('.content_tools_bubble'),
		$BubbleContent = $Bubble.find('.bubble_content')
	;

	$Items.each(function(){
		var
			$This = $(this),
			$Content = $This.find('.content_tools_item_sub')
		;

		$This.click(function(){
			var b = $This.hasClass('content-tools-item-active');

			$Items.removeClass('content-tools-item-active');
			$This.addClass('content-tools-item-active');



			if(!b || $Bubble.is(':hidden')){

				if($Content.length){
					$BubbleContent.html($Content.html());
					$Bubble
						.css('margin','0')
						.show()
						.offset($This.offset())
						.css('margin','60px 0 0 0')
					;
				}
				else {
					$Bubble.hide();
				}
			}
			else {
				$Bubble.hide();
			}
		});
	});

});
