<?php
/**
* Plugin Name: No Frills Gallery
* Plugin URI: http://www.jamestibbles.co.uk/no-frills-gallery/
* Description: A simple, easy to modify/customise image galleries and pictures. For additional features check out No Frills Gallery PRO (http://www.jamestibbles.co.uk/no-frills-gallery-pro)
* Version: 1.3.5
* Author: James Tibbles
* Author URI: http://www.jamestibbles.co.uk/no-frills-gallery
* License: GPL2
**/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function nfg_media_enqueue($hook) {

    try{
        wp_enqueue_media();
    }catch(Exception $e){
        //already initialised;
    }
    
}
add_action( 'admin_enqueue_scripts', 'nfg_media_enqueue' );


function nfg_additional_images_sizes(){

    add_image_size('gallery-image-widget',200, 145, true);

    add_image_size('gallery-image',310, 221, true);

}
add_action( 'init', 'nfg_additional_images_sizes' );


function nfg_install(){

    global $wpdb;

    $table_1_name = $wpdb->prefix . 'nfg_galleries';

    $table_2_name = $wpdb->prefix . 'nfg_imgs';
 
    if($wpdb->get_var("show tables like '$table_1_name'") != $table_1_name) 
    {

        $sql = "CREATE TABLE " . $table_1_name . " (
          `id` int(5) NOT NULL AUTO_INCREMENT,
          `_name` varchar(250) NOT NULL,
          `_desc` text NOT NULL,
          `_amended` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ); INSERT INTO {$table_1_name} VALUES (null,'','',0,'".current_time( 'mysql' )."');";

        
        $sql .= "CREATE TABLE " . $table_2_name . " (
         `id` int(5) NOT NULL AUTO_INCREMENT,
         `_photo_id` int(9) NOT NULL,
         `_weblink` varchar(250),
         `_created` datetime,
         PRIMARY KEY (`id`)
        );";
 
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);
    }
 
}
register_activation_hook(__FILE__,'nfg_install');


function nfg_register_gallery_table() {

    global $wpdb;

    $wpdb->nfg_galleries_table = "{$wpdb->prefix}nfg_galleries";

    $wpdb->nfg_gallery_images_table = "{$wpdb->prefix}nfg_imgs";

}
add_action( 'init', 'nfg_register_gallery_table', 1 );
add_action( 'switch_blog', 'nfg_register_gallery_table' );


function nfg_admin_menu() {

    add_menu_page( 'All Albums', 'NF Gallery', 'edit_posts', 'nfg', 'nfg_edit_album', 'dashicons-format-gallery', 9  );

    add_submenu_page( 'nfg', 'Slideshow Shortcode Generator', 'Slideshow Shortcode Generator', 'edit_posts', 'nfg_slideshow_shortcode_generator', 'nfg_slideshow_shortcode_generator');

    add_submenu_page( 'nfg', 'Album Display Shortcode Generator', 'Album Display Shortcode Generator', 'edit_posts', 'nfg_album_shortcode_generator', 'nfg_album_shortcode_generator');

}
add_action( 'admin_menu', 'nfg_admin_menu' );


function nfg_edit_album(){

    wp_enqueue_script( 'album-tools', plugins_url( '/js/album-tools.js' , __FILE__ ), array('jquery'), '1.0.0', true );

    wp_enqueue_script( 'jquery-sortable', plugins_url( '/js/jquery.sortable.min.js' , __FILE__ ), array('jquery'), '1.0.0', true );

    wp_enqueue_script( 'jquery-init-sort', plugins_url( '/js/init-sort.js' , __FILE__ ), array('jquery'), '1.0.0', true );

    wp_enqueue_style('admin-css', plugins_url( '/css/admin.css' , __FILE__ ));

    include('admin/nfg-edit-album-admin.php'); 
    
}


function nfg_slideshow_shortcode_generator(){
    
    wp_enqueue_style('admin-css', plugins_url( '/css/admin.css' , __FILE__ ));

    wp_enqueue_script( 'album-tools', plugins_url( '/js/album-tools.js' , __FILE__ ), array('jquery'), '1.0.0', true );

    include('admin/nfg-slideshow-shortcode-generator-admin.php'); 

}
function nfg_album_shortcode_generator(){
    
    wp_enqueue_style('admin-css', plugins_url( '/css/admin.css' , __FILE__ ));

    wp_enqueue_script( 'album-tools', plugins_url( '/js/album-tools.js' , __FILE__ ), array('jquery'), '1.0.0', true );

    include('admin/nfg-album-shortcode-generator-admin.php'); 

}


function nfg_get_thumb() {

   $id = $_POST['id'];

   $img = wp_get_attachment_url( $id, 'gallery-image-widget' );

    echo $img;

    exit;
}
add_action( 'wp_ajax_get_thumb', 'nfg_get_thumb' );



function nfg_edit_gallery_form_sanitation() {
        
    global $wpdb;
        
    $table_1_name = $wpdb->prefix . 'nfg_galleries';

    $table_2_name = $wpdb->prefix . 'nfg_imgs';

    $ok = true;

    if ( is_admin() && isset($_POST) && wp_verify_nonce($_POST['custom_meta_box_nonce'], 'no-frills-my-new-gallery-nonce')  ) {

        $gallery_name    =  sanitize_text_field( $_POST["cf-gallery-name"] );

        $gallery_description = esc_textarea( $_POST["cf-gallery-description"] ); 



        $sql = "TRUNCATE " . $table_1_name;
        
        $result = $wpdb->query($wpdb->prepare($sql,array()));

        $data = array(
                        'id' => 1,
                        '_name' => $gallery_name,
                        '_desc' => $gallery_description,
                        '_amended'    => current_time( 'mysql' )
        );
        $format = array(
                        '%d', 
                        '%s',
                        '%s',
                        '%d',                    
                        '%s'
        );
            
        $success=$wpdb->insert( $table_1_name, $data, $format );

            
        if(!$success){
                
            echo "<div class='error'>Could not update gallery details.</div>";
            
            $ok = false;

        }

        if($success){

                $imgs = $_POST['image_ids'];
                
                $sql = "TRUNCATE " . $table_2_name;

                $result = $wpdb->query($wpdb->prepare($sql,array()));

                $imgs = explode(":",$imgs);

                $weblinks = explode(":_:",$_POST['weblinks']);

                $_count = 0;

                foreach($imgs as $img){

                    $weblink = "";

                    if($weblinks[$_count]!=""){
                        $weblink = "http://".str_replace("http://","",sanitize_text_field($weblinks[$_count]));
                    }

                    if($img>0){ 

                            $data = array(
                                '_photo_id' => $img,
                                '_weblink' => $weblink,
                                '_created'    => current_time( 'mysql' )
                            );

                            $format = array(
                                '%d',
                                '%s',
                                '%s'
                            );

                            $success=$wpdb->insert($table_2_name, $data, $format );

                            if(!$success){
                                echo "<div class='error'>Could not store photo id ".$img." in photos table.</div>";
                                $ok = false;
                            }
                    }

                    $_count++;

                }

        }
    }
    return $ok;
}


function nfg_create_slideshow($atts){ 


    wp_enqueue_script(
        'slideshow',
        plugins_url( '/js/slideshow.js' , __FILE__ ),
        array( 'jquery' )
    );

    wp_enqueue_style('nfg-style-css', plugins_url( '/css/no-frills.css' , __FILE__ ));
    wp_enqueue_style('nfg-slideshow-css', plugins_url( '/css/slideshow.css' , __FILE__ ));

    global $wpdb;

    /*$attribs = shortcode_atts( array(
        'num'=>10,
        'rand'=>1,
        'speed'=>5000,
        'text'=>'alb_all',
        'max_chars' =>150,
        'originalsize'=>1,
        'link'=>'lightbox',
        'max-width'=>'100%',
        'max-height'=>'100%',
        'min-width'=>'100%',
        'min-height'=>'auto',
        'height' => 'auto',
        'nav-arrows' => 1,
        'nav-bullets' => 1,
    ), $atts);
    
    $num_images=$attribs['num'];

    $random=$attribs['rand'];

    $speed=$attribs['speed'];

    $text=$attribs['text'];

    $max_chars=$attribs['max_chars'];

    $originalsize = $attribs['originalsize'];

    $link = $attribs['link'];

    $maxh = $attribs['max-height'];

    $maxw = $attribs['max-width'];

    $minh = $attribs['min-height'];

    $minw = $attribs['min-width'];

    $h = $attribs['height'];

    $nav_arrows = $attribs['nav-arrows'];

    $nav_bullets = $attribs['nav-bullets'];

    if($link == "lightbox"){

        wp_enqueue_script(
            'lightbox',
            plugins_url( '/js/lightbox.js' , __FILE__ ),
            array( 'jquery' )
        );

        wp_enqueue_style('nfg-lightbox', plugins_url( '/css/lightbox.css' , __FILE__ ));

    }

    $html = "<div class='no-frills-slideshow'>";

    if($random==1 || $random==true){

        $order = "rand_ind";

    }else{

        $order = "a.id ASC";

    }

    
    $result = $wpdb->get_results($wpdb->prepare("SELECT a._photo_id as pid, FLOOR(1 + RAND() * a.id) as `rand_ind`, b._name as `title`, b._desc as `descr`, a._weblink as `_weblink` from {$wpdb->nfg_gallery_images_table} a LEFT JOIN {$wpdb->nfg_galleries_table} b ON b.id=1 ORDER BY {$order} LIMIT 0,{$num_images}",array()));
    

    foreach ( $result as $row )
    {

        $pid = $row->pid;

        $url = wp_get_attachment_image_src($pid, "original", false);

        if($originalsize=="1" || $originalsize==true) {
            $img_code = wp_get_attachment_image( $pid, 'original' );
        }else{
            $img_code = wp_get_attachment_image( $pid, 'gallery-image' );
        }
            //$img_code = str_replace("img ", "img style='max-width:".$maxw.";max-height:".$maxh.";min-width:".$minw.";min-height:".$minh.";'", $img_code);
            $img_code = str_replace("img ", "img style='max-width:".$maxw.";max-height:".$maxh.";min-width:".$minw.";min-height:".$minh.";height:".$h.";'", $img_code);

        $weblink = $row->_weblink;

        $show_title = "";
        $show_desc = "";
        $albumname = "";
        
        $parsed_title = stripslashes($row->title);
        $parsed_title = str_replace("'","&#39;",$parsed_title);
        $parsed_title = str_replace('"',"&quot;",$parsed_title);
        $parsed_title = str_replace('\n',"<br>",$parsed_title);
        $parsed_title = str_replace('\\',"",$parsed_title);


        $parsed_desc = stripslashes($row->descr);
        $parsed_desc = str_replace("'","&#39;",$parsed_desc);
        $parsed_desc = str_replace('"',"&quot;",$parsed_desc);
        $parsed_desc = str_replace('\n',"<br>",$parsed_desc);
        $parsed_desc = str_replace('\\',"",$parsed_desc);

        $text_opts = explode("+", $text);

        $text_code = "";

        $tmp_max_chars = $max_chars;

        foreach($text_opts as $curr_t){

                if($curr_t == "alb_all"){


                        if(strlen($parsed_title) < $tmp_max_chars && strlen($parsed_title)>0){

                            $text_code .= "<div class='album-title'>".$parsed_title."</div>";

                        }else if(strlen($parsed_title)>0){

                            if(strlen($parsed_title) > $tmp_max_chars){
                                $extra = "...";
                            }else{
                                $extra = "";
                            }

                            $text_code .= "<div class='album-title'>".substr($parsed_title,0,$tmp_max_chars).$extra."</div>";
                        
                        }

                        $tmp_max_chars = $tmp_max_chars - strlen($parsed_title);
                        $extra = "";


                        if(strlen($parsed_desc) < $tmp_max_chars && $tmp_max_chars>0 && strlen($parsed_desc)>0){

                            $text_code .= "<div class='album-desc'>".$parsed_desc."</div>";

                        }else if($tmp_max_chars>0 && strlen($parsed_desc)>0){

                            if(strlen($parsed_desc) > $tmp_max_chars){
                                $extra = "...";
                            }else{
                                $extra = "";
                            }

                            $text_code .= "<div class='album-desc'>".substr($parsed_desc,0,$tmp_max_chars).$extra."</div>";
                        
                        }

                        $tmp_max_chars = $tmp_max_chars - strlen($parsed_desc);
                        $extra = "";

                }else if($curr_t == "alb_desc"){

                        if(strlen($parsed_desc) < $tmp_max_chars && $tmp_max_chars>0 && strlen($parsed_desc)>0){

                            $text_code .= "<div class='album-desc'>".$parsed_desc."</div>";

                        }else if($tmp_max_chars>0 && strlen($parsed_desc)>0){

                            if(strlen($parsed_desc) > $tmp_max_chars){
                                $extra = "...";
                            }else{
                                $extra = "";
                            }

                            $text_code .= "<div class='album-desc'>".substr($parsed_desc,0,$tmp_max_chars).$extra."</div>";
                        
                        }

                        $tmp_max_chars = $tmp_max_chars - strlen($parsed_desc);
                        $extra = "";

                }else if($curr_t == "alb_title"){

                        if(strlen($parsed_title) < $tmp_max_chars && strlen($parsed_title)>0){

                            $text_code .= "<div class='album-title'>".$parsed_title."</div>";

                        }else if(strlen($parsed_title)>0){

                            if(strlen($parsed_title) > $tmp_max_chars){
                                $extra = "...";
                            }else{
                                $extra = "";
                            }

                            $text_code .= "<div class='album-title'>".substr($parsed_title,0,$tmp_max_chars).$extra."</div>";
                        
                        }

                        $tmp_max_chars = $tmp_max_chars - strlen($parsed_title);
                        $extra = "";

                }

        } 


        if($link == "lightbox"){

            if($parsed_photo_desc!=""){

                $slideshow_desc = " - ".$parsed_photo_desc;

            }else{
                $slideshow_desc = "";
            }

            $itm = "<a class='slideshow-item' href='".$url[0]."' data-lightbox='nf-popup' data-title='".$parsed_title.$slideshow_desc."'  data-url='".$weblink."' alt='".$parsed_title.$slideshow_desc."'>".$img_code."</a>";

        }else if($link == "url"){

            $itm = "<a class='slideshow-item' href='".$weblink."' target='_blank' alt='".$slideshow_desc."'>".$img_code."</a>";

        }else{

            $itm = "<div class='slideshow-item'>".$img_code."</div>";

        }

        $html.="<div class='slide' data-speed='".$speed."'>".$itm.$text_code."</div>";

    }

    $html.="</div>";

    return $html;*/



    $attribs = shortcode_atts( array(
        'num'=>10,
        'rand'=>1,
        'transition_speed'=>500,
        'wait'=>5000,
        'text'=>'alb_all',
        'max_chars' =>150,
        'originalsize'=>1,
        'link'=>'',
        'max-width'=>'100%',
        'max-height'=>'100%',
        'min-width'=>'100%',
        'min-height'=>'auto',
        'height' => 'auto',
        'nav-arrows' => 1,
        'nav-bullets' => 1,
    ), $atts);


    $num_images=$attribs['num'];

    $random=$attribs['rand'];

    $transition_speed=$attribs['transition_speed'];

    $wait=$attribs['wait'];

    $text=$attribs['text'];

    $max_chars=$attribs['max_chars'];

    $originalsize = $attribs['originalsize'];

    $link = $attribs['link'];

    $maxh = $attribs['max-height'];

    $maxw = $attribs['max-width'];

    $minh = $attribs['min-height'];

    $minw = $attribs['min-width'];

    $h = $attribs['height'];

    $nav_arrows = $attribs['nav-arrows'];

    $nav_bullets = $attribs['nav-bullets'];

    $html = "";
        
    $slidecount=0;

    $bullets="";


    if($link == "lightbox"){

        wp_register_script( 'lightbox', plugins_url( '/js/lightbox.js' , __FILE__ ));
        wp_enqueue_script('lightbox');
        wp_enqueue_style('nfgp-lightbox', plugins_url( '/css/lightbox.css' , __FILE__ ));

    }


    if($num_images>0){


        $rndname= rand(0,99999).rand(0,99999);

        $html .= "<div class='nfg-slideshow slideshow_".$rndname."' data-name='".$rndname."'>";

        if($random==1 || $random==true){

            $order = "rand_ind";

        }else{

            $order = "a.id ASC";

        }
        
        $result = $wpdb->get_results($wpdb->prepare("SELECT a._photo_id as pid, FLOOR(1 + RAND() * a.id) as `rand_ind`,                          b._name as `title`, b._desc as `descr`, a._weblink as `_weblink` from {$wpdb->nfg_gallery_images_table} a  LEFT JOIN {$wpdb->nfg_galleries_table}  b ON b.id=1             ORDER BY {$order} LIMIT 0,{$num_images}",array()));
       


        foreach ( $result as $row )
        {


            $pid = $row->pid;

            $url = wp_get_attachment_image_src($pid, "original", false);

            if($originalsize=="1" || $originalsize==true) {
                $img_code = wp_get_attachment_image( $pid, 'original' );
            }else{
                $img_code = wp_get_attachment_image( $pid, 'gallery-image' );
            }
            $img_code = str_replace("img ", "img style='max-width:".$maxw.";max-height:".$maxh.";min-width:".$minw.";min-height:".$minh.";height:".$h.";'", $img_code);

            $weblink = $row->_weblink;

            $show_title = "";
            $show_desc = "";
            $albumname = "";
            

            $parsed_title = stripslashes($row->title);
            $parsed_title = str_replace('\n',"<br>",$parsed_title);
            $parsed_title = str_replace('[DQ]','"',$parsed_title);
            $parsed_title = str_replace('[QQ]',"'",$parsed_title);
            $parsed_title = str_replace('\\',"",$parsed_title);



            $parsed_desc = stripslashes($row->descr);
            $parsed_desc = str_replace('\n',"<br>",$parsed_desc);
            $parsed_desc = str_replace('[DQ]','"',$parsed_desc);
            $parsed_desc = str_replace('[QQ]',"'",$parsed_desc);
            $parsed_desc = str_replace('\\',"",$parsed_desc);


            $text_opts = explode("+", $text);

            $text_code = "";

            $tmp_max_chars = $max_chars;

            foreach($text_opts as $curr_t){


                    if($curr_t == "all"){

                            if(strlen($parsed_title) < $tmp_max_chars && strlen($parsed_title)>0){

                                $text_code .= "<div class='album-title'>".$parsed_title."</div>";

                            }else if(strlen($parsed_title)>0){

                                if(strlen($parsed_title) > $tmp_max_chars){
                                    $extra = "...";
                                }else{
                                    $extra = "";
                                }

                                $text_code .= "<div class='album-title'>".substr($parsed_title,0,$tmp_max_chars).$extra."</div>";
                            
                            }

                            $tmp_max_chars = $tmp_max_chars - strlen($parsed_title);
                            $extra = "";


                            if(strlen($parsed_desc) < $tmp_max_chars && $tmp_max_chars>0 && strlen($parsed_desc)>0){

                                $text_code .= "<div class='album-desc'>".$parsed_desc."</div>";

                            }else if($tmp_max_chars>0 && strlen($parsed_desc)>0){

                                if(strlen($parsed_desc) > $tmp_max_chars){
                                    $extra = "...";
                                }else{
                                    $extra = "";
                                }

                                $text_code .= "<div class='album-desc'>".substr($parsed_desc,0,$tmp_max_chars).$extra."</div>";
                            
                            }

                            $tmp_max_chars = $tmp_max_chars - strlen($parsed_desc);
                            $extra = "";
                                 
                            

                    }else if($curr_t == "alb_all"){


                            if(strlen($parsed_title) < $tmp_max_chars && strlen($parsed_title)>0){

                                $text_code .= "<div class='album-title'>".$parsed_title."</div>";

                            }else if(strlen($parsed_title)>0){

                                if(strlen($parsed_title) > $tmp_max_chars){
                                    $extra = "...";
                                }else{
                                    $extra = "";
                                }

                                $text_code .= "<div class='album-title'>".substr($parsed_title,0,$tmp_max_chars).$extra."</div>";
                            
                            }

                            $tmp_max_chars = $tmp_max_chars - strlen($parsed_title);
                            $extra = "";


                            if(strlen($parsed_desc) < $tmp_max_chars && $tmp_max_chars>0 && strlen($parsed_desc)>0){

                                $text_code .= "<div class='album-desc'>".$parsed_desc."</div>";

                            }else if($tmp_max_chars>0 && strlen($parsed_desc)>0){

                                if(strlen($parsed_desc) > $tmp_max_chars){
                                    $extra = "...";
                                }else{
                                    $extra = "";
                                }

                                $text_code .= "<div class='album-desc'>".substr($parsed_desc,0,$tmp_max_chars).$extra."</div>";
                            
                            }

                            $tmp_max_chars = $tmp_max_chars - strlen($parsed_desc);
                            $extra = "";

                    }else if($curr_t == "alb_desc"){

                            if(strlen($parsed_desc) < $tmp_max_chars && $tmp_max_chars>0 && strlen($parsed_desc)>0){

                                $text_code .= "<div class='album-desc'>".$parsed_desc."</div>";

                            }else if($tmp_max_chars>0 && strlen($parsed_desc)>0){

                                if(strlen($parsed_desc) > $tmp_max_chars){
                                    $extra = "...";
                                }else{
                                    $extra = "";
                                }

                                $text_code .= "<div class='album-desc'>".substr($parsed_desc,0,$tmp_max_chars).$extra."</div>";
                            
                            }

                            $tmp_max_chars = $tmp_max_chars - strlen($parsed_desc);
                            $extra = "";

                    }else if($curr_t == "alb_title"){

                            if(strlen($parsed_title) < $tmp_max_chars && strlen($parsed_title)>0){

                                $text_code .= "<div class='album-title'>".$parsed_title."</div>";

                            }else if(strlen($parsed_title)>0){

                                if(strlen($parsed_title) > $tmp_max_chars){
                                    $extra = "...";
                                }else{
                                    $extra = "";
                                }

                                $text_code .= "<div class='album-title'>".substr($parsed_title,0,$tmp_max_chars).$extra."</div>";
                            
                            }

                            $tmp_max_chars = $tmp_max_chars - strlen($parsed_title);
                            $extra = "";

                    }
             

            } 

            
            if($link == "lightbox"){           

                $itm = "<a class='slideshow-item' href='".$url[0]."' data-lightbox='nf-popup' data-title='".strip_tags($parsed_title, '<b><strong><br>')."' data-url='".$weblink."'  alt='".strip_tags($parsed_title, '<b><strong><br>')."'>".$img_code."</a>";

            }else if($link == "url" && ($weblink!="" && $weblink!="http://") ){

                $itm = "<a href='".$weblink."' target='_blank' class='slideshow-item' >".$img_code."</a>";

            }else{

                $itm = "<div class='slideshow-item' >".$img_code."</div>";

            }

            $slidecount++;

            $html.="<div class='slide' data-transpeed='".$transition_speed."' data-wait='".$wait."'  id='".$rndname."_slide_".$slidecount."' dat-slide='".$slidecount."'";

            if($num_images==1){
                    $html.=" style='display:block;'";
            }

            if($text_code!=""){
                $text_code = "<div class='desc_surround'>".$text_code."</div>";
            }

            $html.=">".$itm.$text_code."</div>";
                
            $bullets.="<li class='bullet bullet-".$slidecount."' data-slide-num='".$slidecount."'></li>";


        }

        if($nav_arrows==1){
                $html.="<div class='previous-arrow' data-slideshow='".$rndname."' ready='1'><div class='arrow' style='background-image: url(".plugins_url( '/img/arrows.png' , __FILE__ ).");'></div></div>";
                $html.="<div class='next-arrow' data-slideshow='".$rndname."' ready='1'><div class='arrow' style='background-image: url(".plugins_url( '/img/arrows.png' , __FILE__ ).");'></div></div>";
        }
        
        if($nav_bullets==1){
                $html.="<ul class='bullets' data-slideshow='".$rndname."' ready='1'>";
                $html.=$bullets;
                $html.="</ul>";
        }

        $html.="</div>";

    }else{
        $html.="<script>document.noanim=1;</script>";
    }   

    return $html;
}
add_shortcode("gallery_slideshow", "nfg_create_slideshow");



 
function nfg_create_gallery_album_photos($atts){

    wp_enqueue_script(
        'gallery',
        plugins_url( '/js/gallery.js' , __FILE__ ),
        array( 'jquery' )
    );

    wp_enqueue_script(
        'lightbox',
        plugins_url( '/js/lightbox.js' , __FILE__ ),
        array( 'jquery' )
    ); 

    wp_enqueue_style('nfg-lightbox', plugins_url( '/css/lightbox.css' , __FILE__ ));

    wp_enqueue_style('nfg-style-css', plugins_url( '/css/no-frills.css' , __FILE__ ));

    $a = shortcode_atts(array('num'=>9999999,'paginate'=>0,'perpage'=>0),$atts);
        
    global $wpdb;

    $select = "";

    $html = "<div class='full-photo-grid'>";

    $albumname = "";

    $albumdesc = "";

    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);

    $pageurl = $uri_parts[0];

    $num_images=$a['num'];

    $album_details =  $wpdb->get_row($wpdb->prepare("SELECT _name as nam,id,_desc as descr FROM {$wpdb->nfg_galleries_table};",array()) );

    $parsed_desc = stripslashes($album_details->descr);
    $parsed_desc = str_replace("'","&#39;",$parsed_desc);
    $parsed_desc = str_replace('"',"&quot;",$parsed_desc);
    $parsed_desc = str_replace("\n","<br>",$parsed_desc);

    $parsed_name = stripslashes($album_details->nam);
    $parsed_name = str_replace("'","&#39;",$parsed_name);
    $parsed_name = str_replace('"',"&quot;",$parsed_name);

    $albumname = $parsed_name;
    $albumdesc = $parsed_desc;


    $intro = "<div class='intro'><div class='title-text'><h2>".$albumname."</h2><p>".$albumdesc."</p></div></div><hr>";
    
    if($a['paginate'] == 1){

        
        $page = (@$_REQUEST['pg']>0)?$_REQUEST['pg']:1;
        
        
        $total_imgs = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$wpdb->nfg_gallery_images_table};",array())); 

        $perpage = intval($a['perpage']);

        $startpos = $perpage*($page-1);

        if($total_imgs>0){

            $sql = $wpdb->prepare("SELECT _photo_id as pid, _weblink  FROM {$wpdb->nfg_gallery_images_table} ORDER BY id asc LIMIT {$startpos}, {$perpage};",array());

            foreach( $wpdb->get_results($sql) as $key => $row) {
            
                $desc = "";
                $pid = $row->pid;
                $url = wp_get_attachment_image_src($pid, "original", false);
                $img_code = wp_get_attachment_image_src($pid, "gallery-image", false);
                $img_code = $img_code[0];
            
                if(!$url[0]){
                     $url[0] = plugin_dir_url( __FILE__ )."img/missing_image.jpg";
                     $img_code = $url[0];
                }

                $weblink = $row->_weblink;

                $html.="<div class='full-photo-grid-item'><a href='".$url[0]."' data-lightbox='nf-popup' data-title='".$desc."'  data-url='".$weblink."' style='background-image:url(".$img_code.")'></a></div>";
            }

            $totalpages = ceil($total_imgs / $perpage);

            list($pages) = nfg_custom_pagination($pageurl,$totalpages,$perpage,$page);

            $html.="<div class='no-frills-pagination'>".$pages."</div>";

        }else{
            $html.="<p><i>No photos have been uploaded yet.</i></p>";
        }

    }else{
        
        $num_images=$a['num'];

        $sql = $wpdb->prepare("SELECT _photo_id as pid, _weblink FROM {$wpdb->nfg_gallery_images_table} ORDER BY id asc LIMIT 0, {$num_images};",array());

        foreach( $wpdb->get_results($sql) as $key => $row) {
            
                $desc = "";
                $pid = $row->pid;
                $url = wp_get_attachment_image_src($pid, "original", false);
                $img_code = wp_get_attachment_image_src($pid, "gallery-image", false);
                $img_code = $img_code[0];
            
                if(!$url[0]){
                     $url[0] = plugin_dir_url( __FILE__ )."img/missing_image.jpg";
                }
                $weblink = $row->_weblink;

                if($albumname!=""){
                    $desc = "".$albumname;
                }

                $html.="<div class='full-photo-grid-item'><a href='".$url[0]."' data-lightbox='nf-popup' data-title='".$desc."'  data-url='".$weblink."' style='background-image:url(".$img_code.")'></a></div>";
        }

    }
    $html.="</div>";

    return $intro.$html;
}
add_shortcode("gallery_album_photos", "nfg_create_gallery_album_photos");



function nfg_custom_pagination($pageurl,$total,$resultsperpage,$pagenum){
  
  $count = $total;

  if($pagenum>1){$pagenum_prev = $pagenum-1;}else{$pagenum_prev=1;}

  if($pagenum<$count){$pagenum_next = $pagenum+1;}else{$pagenum_next=$count;}

  if($pagenum==1){$poff = " off";}else{$poff="";}

  $pagination = "<a href ='".$pageurl."?pg=1' class='pagination".$poff."'>«</a><a href='".$pageurl."?pg=".$pagenum_prev."' class='pagination".$poff."'>‹ Previous</a>";

  for($i=$pagenum-5;$i<=$pagenum+5;$i++){
    if($i>0 && $i<=$count) {
        if($pagenum == $i){

            $pagination .= "<span class='pagination'> ".$i."</span>";
        }else{

            $pagination .= "<a href='".$pageurl."?pg=".$i."' class='pagination'>".$i."</a>";
          }
    }
  }

  if($pagenum==$total){$poff = " off";}else{$poff="";}

  $pagination .= "<a href ='".$pageurl."?pg=".$pagenum_next."' class='pagination".$poff."'>Next ›</a><a href ='".$pageurl."?pg=".$count."' class='pagination".$poff."'>»</a>";

  return array($pagination);

}