document.set_to_post_id = null; //If you're having privilege issues uploading new images to media library, try changin this id to 10, 100 or any other number

(function ($, root, undefined) {
	
	$(function () {
		
		'use strict';

		$('.uploader').show();

		var file_frame;
		var wp_media_post_id = wp.media.model.settings.post.id; 


		jQuery('.img-button').bind('click', function( event ){
		    event.preventDefault();
		    openMedia();
		});


		function openMedia(){
		    if ( file_frame ) {
		      file_frame.uploader.uploader.param( 'post_id', document.set_to_post_id );
		      file_frame.open();
		      return;
		    } else {
		      wp.media.model.settings.post.id = document.set_to_post_id;
		    }
		    file_frame = wp.media.frames.file_frame = wp.media({
		      title: jQuery( this ).data( 'uploader_title' ),
		      button: {
		        text: jQuery( this ).data( 'uploader_button_text' ),
		      },
		      multiple: true
		    });

		    file_frame.on( 'select', function() {

		      var selection = file_frame.state().get('selection');

			  selection.map( function( attachment ) {

			      attachment = attachment.toJSON();

			      createThumbnail(attachment.id);

			  });
			  	      
		      wp.media.model.settings.post.id = wp_media_post_id;
		    });

		    file_frame.open();
		}


		function createThumbnail(id){
			  			  		
			var data = {
						'action': 'get_thumb',
						'id': id
					};

					jQuery.post(ajaxurl, data, function(response) {

						if(response) {

							if(!hasImgId(id)){

								addImgId(id);

								$('.sortable').append("<li class='image_gallery' data-id='"+id+"' style='background-image:url("+response+");background-size:cover;background-position:center;' draggable='true'><div class='close-btn'>X</div><div class='link'><input type='text' maxlength='250' placeholder='Slideshow web-link ie. http://...' class='url' /></div></li>");

								reInitSort();

								updateImgData();

								updateDel();

							}else{

								alert("This photo is already in your album");

							}
						}
			});


		  }


		  function hasImgId(id){

		  	var arr = document.photoids;

			if(arr.indexOf("id"+id)!=-1){

				return true;

			}else{

				return false;

			}

		  }


		  function addImgId(id){

		  	var pid = "id"+id;

		  	document.photoids.push(pid);

		  }


		  function updateDel(){

				$('.close-btn').unbind("click");

				$('.close-btn').bind("click",function(){

					var id = $(this).parent().attr("data-id");

					var prev_ids = $('#image_ids').val()+":";

					var new_ids = prev_ids.replace(":"+id+":", ":");

					document.photoids = jQuery.grep(document.photoids, function(value) {
					  return value != "id"+id;
					});

					$('#image_ids').val(new_ids);

					var removed = $(this).parent();

					removed.fadeOut("fast",function(){$(this).remove();});

				});

		  }

		  updateDel();

		  
		  jQuery('a.add_media').on('click', function() {
		  });


		  function updateImgData(){

		  	var imgs = "";
		  	var weblinks = "";

			$('.image_gallery').each(function( index ) {

					imgs=imgs+":"+$(this).attr("data-id");

					var curr_link = $(this).find(".url").val();
					if(curr_link.indexOf("<script")==-1 && 
						curr_link.indexOf("script>")==-1 && 
						curr_link.indexOf("<?")==-1 && 
						curr_link.indexOf("?>")==-1 && 
						curr_link.indexOf("&lt;script")==-1 && 
						curr_link.indexOf("script&gt;")==-1 && 
						curr_link.indexOf("&lt;?")==-1 &&
						curr_link.indexOf("?&gt;")==-1
					){
						weblinks = weblinks+":_:"+curr_link;
					}else{
						$(this).find(".url").val("");
						return false;
					}

			});

			$('#image_ids').val(imgs);

			$('#weblinks').val(weblinks);
			return true;

		  }
	  

		  $('form#image_gallery_form').on("submit",function(e){
			if(updateImgData()){
			}else{
				e.preventDefault();
				e.stopPropagation();	
				alert("Illegal word found in one or more textfields. You cannot use code script references.");			
			}
		  });


		  /*if($('#slideshow-shortcode-generator').length>0){
		  	$('#slideshow-shortcode-generator').on("click",function(){
		  		$(this).text("Generating...");
		  		var plus = "";
				if($('#num').val()<1){
					var num = "";
				}else{
					num = " num="+$('#num').val();
				}
				if($('#max_chars').val()<1){
					var max_chars = "";
				}else{
					max_chars = " max_chars="+$('#max_chars').val();
				}
				if($('#link').val()==""){
					var link = "";
				}else{
					link = " link='"+$('#link').val()+"'";
				}
				if($('#max-width').val()==""){
					var maxwidth = "";
				}else{
					maxwidth = " max-width='"+$('#max-width').val()+"'";
				}
				if($('#max-height').val()==""){
					var maxheight = "";
				}else{
					maxheight = " max-height='"+$('#max-height').val()+"'";
				}
				if($('#min-width').val()==""){
					var minwidth = "";
				}else{
					minwidth = " min-width='"+$('#min-width').val()+"'";
				}
				if($('#min-height').val()==""){
					var minheight = "";
				}else{
					minheight = " min-height='"+$('#min-height').val()+"'";
				}

		  		var shortcode = "[gallery_slideshow"+num+" rand="+$('#rand').val()+" text='"+$('#text').val()+"' originalsize="+$('#originalsize').val()+""+max_chars+""+link+""+minwidth+""+minheight+""+maxwidth+""+maxheight+"]";

		  		$(this).text("Generate another Shortcode");
		  		$('#slideshow-shortcodes').html("<p class='shortcode-result code'><i>"+shortcode+"</i></p>");

		  	});
		  }*/
		  if($('#slideshow-shortcode-generator').length>0){
		  	$('#slideshow-shortcode-generator').on("click",function(){
		  		$(this).text("Generating...");
		  		
				if($('#num').val()<1){
					var num = "";
				}else{
					num = " num="+$('#num').val();
				}
				if($('#max_chars').val()<1){
					var max_chars = "";
				}else{
					max_chars = " max_chars="+$('#max_chars').val();
				}
				if($('#link').val()==""){
					var link = "";
				}else{
					link = " link='"+$('#link').val()+"'";
				}
				if($('#max-width').val()==""){
					var maxwidth = "";
				}else{
					maxwidth = " max-width='"+$('#max-width').val()+"'";
				}
				if($('#max-height').val()==""){
					var maxheight = "";
				}else{
					maxheight = " max-height='"+$('#max-height').val()+"'";
				}
				if($('#min-width').val()==""){
					var minwidth = "";
				}else{
					minwidth = " min-width='"+$('#min-width').val()+"'";
				}
				if($('#min-height').val()==""){
					var minheight = "";
				}else{
					minheight = " min-height='"+$('#min-height').val()+"'";
				}

		  		var shortcode = "[gallery_slideshow"+num+" rand="+$('#rand').val()+" text='"+$('#text').val()+"' originalsize="+$('#originalsize').val()+""+max_chars+""+link+""+minwidth+""+minheight+""+maxwidth+""+maxheight+" transition_speed="+$('#transition_speed').val()+" wait="+$('#wait').val()+"]";

		  		$(this).text("Generate another Shortcode");
		  		$('#slideshow-shortcodes').html("<p class='shortcode-result code'>"+shortcode+"</p>");

		  	});
		  }



		  if($('#album-shortcode-generator').length>0){
		  	$('#album-shortcode-generator').on("click",function(){
		  		$(this).text("Generating...");
		  		if($('#num').val()<1){
					var num = "";
				}else{
					num = " num="+$('#num').val();
				}
				if($('#perpage').val()<1){
					var perpage = "";
				}else{
					perpage = " perpage="+$('#perpage').val();
				}
		  		var shortcode = "[gallery_album_photos"+num+" paginate="+$('#paginate').val()+perpage+"]";

		  		$(this).text("Generate another Shortcode");
		  		$('#album-images-shortcodes').html("<p class='shortcode-result code'><i>"+shortcode+"</i></p>");

		  	});
		  }
			
	});
	
})(jQuery, this);