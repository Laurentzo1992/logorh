$(document).ready(function () {		
	$('#header > ul > li').click(function(){
		var clicked = $(this);
		if(clicked.hasClass('active')){
			$('ul', '#header > ul li.active').stop(true, true).hide();
			clicked.removeClass('active');
			$(document).off('mouseenter mouseleave','#header > ul > li');
		}else{
			clicked.addClass('active');
			$('ul', '#header > ul li.active').stop(true, true).show();
			
			$(document).on('mouseenter mouseleave','#header > ul > li', function(){
				$('ul', '#header > ul li.active').stop(true, true).hide();
				$('#header > ul li.active').removeClass('active');
				
				var hover = $(this);
				hover.addClass('active');
				$('ul', '#header > ul li.active').stop(true, true).show();
			});
		}
	});
	
	$("body").click(function(event) {		
		resizeContent();
		if ( !($(event.target).is("#header") || $(event.target).is("#header > ul") || $(event.target).is("#header > ul > li")) ) {			
			$('ul', '#header > ul li.active').stop(true, true).hide();
			$('#header > ul li.active').removeClass('active');
			$(document).off('mouseenter mouseleave','#header > ul > li');
		}
	});
	
	/*$('#menuButton').click(function(){
		$('#slidePanel').height($(window).height()-20);
		$("#slidePanel").animate({width:'toggle'},350);
		$("#menuButton").toggleClass('on');
		setTimeout(function(){resizeContent();},355);
	})*/
	
	$(window).resize(function() {
		setTimeout(function(){resizeContent();},100);
	});
	setTimeout(function(){resizeContent();},100);
});

function resizeContent(){
	var myHeight = $(window).height()-($('#header').outerHeight(true)+$('#title').outerHeight(true)+$('#toolbar').outerHeight(true)+$('#footer').outerHeight(true)+20);
	$('.padding').height(myHeight);
}