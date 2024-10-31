document.timer = [];document.nav = [];
(function ($, root, undefined) {
	
	$(function () {
		
		'use strict';

			if(document.noanim!=1){

				$( ".nfg-slideshow" ).each(function( index ) { 

					nextSlide(null,$('.slideshow_'+$(this).attr('data-name')),$(this).attr('data-name'));
				});

			}
				

			function nextSlide(curr_slide,slideshow,ssname){
				if($('#jnf-popup').length==0){
					if(document.timer["timer_"+ssname]!="undefined") {clearTimeout(document.timer["timer_"+ssname]);}
					if(curr_slide!=null){
						curr_slide.removeClass('curr_slide');
						var new_slide = curr_slide.next();
						if(new_slide.length == 0){
							new_slide = slideshow.find("div.slide").first();
						}
						var transition_speed = curr_slide.attr("data-transpeed");
						curr_slide.fadeOut(transition_speed);	
						curr_slide.css({"position":"absolute"});				
					}else{
						new_slide = slideshow.find("div.slide").first();
						new_slide.show();
					}
					new_slide.css({"position":"static"});
					var wait = new_slide.attr("data-wait");
				}else{
					new_slide = curr_slide;
					wait = new_slide.attr("data-wait");
				}
				new_slide.addClass('curr_slide');
				setNavTo("0",ssname);
				var slidenum = new_slide.attr("dat-slide");
				if(document.nav["ss_"+ssname]!="undefined") {clearTimeout(document.nav["ss_"+ssname]);}
				document.nav["ss_"+ssname] =  window.setTimeout(function(){ 	 setNavTo("1",ssname);	}, 500);
				activeBullet(ssname,slidenum);
				new_slide.fadeIn(wait, function(){ 
					document.timer["timer_"+ssname] = window.setTimeout(function(){ 	nextSlide(new_slide,slideshow,ssname); 	}, wait);
				});
				
			}

			function prevSlide(curr_slide,slideshow,ssname){
				if($('#jnf-popup').length==0){
					if(document.timer["timer_"+ssname]!="undefined") {clearTimeout(document.timer["timer_"+ssname]);}
					if(curr_slide!=null){
						curr_slide.removeClass('curr_slide');
						var new_slide = curr_slide.prev();
						if(new_slide.length == 0){
							new_slide = slideshow.find("div.slide").last();
						}
						var transition_speed = curr_slide.attr("data-transpeed");
						curr_slide.fadeOut(transition_speed);	
						curr_slide.css({"position":"absolute"});				
					}else{
						new_slide = slideshow.find("div.slide").last();
						new_slide.show();
					}
					new_slide.css({"position":"static"});
					var wait = new_slide.attr("data-wait");
				}else{
					new_slide = curr_slide;
					wait = new_slide.attr("data-wait");
				}
				new_slide.addClass('curr_slide');
				setNavTo("0",ssname);
				var slidenum = new_slide.attr("dat-slide");
				if(document.nav["ss_"+ssname]!="undefined") {clearTimeout(document.nav["ss_"+ssname]);}
				document.nav["ss_"+ssname] =  window.setTimeout(function(){ 	 setNavTo("1",ssname);	}, 500);
				activeBullet(ssname,slidenum);
				new_slide.fadeIn(wait, function(){ 
					document.timer["timer_"+ssname] = window.setTimeout(function(){ 	nextSlide(new_slide,slideshow,ssname); 	}, wait);
				});
				
			}

			function goSlide(slide,slideshow,ssname){
				if($('#jnf-popup').length==0){
					if(document.timer["timer_"+ssname]!="undefined") {clearTimeout(document.timer["timer_"+ssname]);}
					var curr_slide = slideshow.find('.slide.curr_slide');
					if(curr_slide!=null){
						curr_slide.removeClass('curr_slide');
						var transition_speed = curr_slide.attr("data-transpeed");
						curr_slide.fadeOut(transition_speed);	
						curr_slide.css({"position":"absolute"});				
					}
					var new_slide = slideshow.find("#"+ssname+"_slide_"+slide);
					if(new_slide.length == 0){
						new_slide = slideshow.find("div.slide").first();
					}
					new_slide.show();
					new_slide.css({"position":"static"});
					var wait = new_slide.attr("data-wait");
				}else{
					new_slide = slideshow.find(ssname+"_slide_"+slide);
					wait = new_slide.attr("data-wait");
				}
				new_slide.addClass('curr_slide');
				setNavTo("0",ssname);
				if(document.nav["ss_"+ssname]!="undefined") {clearTimeout(document.nav["ss_"+ssname]);}
				document.nav["ss_"+ssname] =  window.setTimeout(function(){ 	 setNavTo("1",ssname);	}, 500);
				activeBullet(ssname,slide);
				new_slide.fadeIn(wait, function(){ 
					document.timer["timer_"+ssname] = window.setTimeout(function(){ 	nextSlide(new_slide,slideshow,ssname); 	}, wait);
				});
			}

			if($('.next-arrow').length>0){
				$('.next-arrow').bind("click",function(){
					if($(this).attr("ready")=="1"){
						var ssname = $(this).attr("data-slideshow");						
						var sl = $(this).parent();//.find('.nfg-slideshow');
						var curr_slide = sl.find('.slide.curr_slide');
						nextSlide(curr_slide,$('.slideshow_'+ssname),ssname);
					}
				});
			}

			if($('.previous-arrow').length>0){
				$('.previous-arrow').bind("click",function(){
					if($(this).attr("ready")=="1"){
						var ssname = $(this).attr("data-slideshow");
						var sl = $(this).parent();//.find('.nfg-slideshow');
						var curr_slide = sl.find('.slide.curr_slide');
						prevSlide(curr_slide,$('.slideshow_'+ssname),ssname);
					}
				});
			}

			if($('.bullets .bullet').length>0){
				$('.bullets .bullet').bind("click",function(){
					if($(this).parent().attr("ready")=="1"){
						var ssname = $(this).parent().attr("data-slideshow");
						goSlide($(this).attr("data-slide-num"),$('.slideshow_'+ssname),ssname);
					}
				});
			}

			function setNavTo(what,ssname){
				var slideshow = $('.slideshow_'+ssname);
				var prev = slideshow.find('.previous-arrow');
				var next = slideshow.find('.next-arrow');
				var bullets = slideshow.find('.bullets');
				prev.attr("ready",what);
				next.attr("ready",what);
				bullets.attr("ready",what);
			}

			function activeBullet(ssname,slide){
				var slideshow = $('.slideshow_'+ssname).parent();
				var bulall = slideshow.find(".bullet");
				var bul = slideshow.find(".bullet.bullet-"+slide);
				bulall.removeClass("active");
				bul.addClass("active");
			}



			
	});
	
})(jQuery, this);