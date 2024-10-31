<?php   
if ( ! current_user_can( 'edit_posts' ) ) {
    echo "<div class='error'>You do not have the correct privileges to access this page</div>"; 
    die();
}?>
<div class="wrap"><h2>Edit Album</h2></div>
<?php 
global $wpdb;
    
 if(isset($_POST['cf-gallery-name'])){
        
    if(nfg_edit_gallery_form_sanitation()){

        echo '<script>window.location = "admin.php?page=nfg&edited=1";</script>'; 
        exit; 

    }else{
        echo "there was an error saving the details";
    }
}

$table_1_name = $wpdb->prefix . 'nfg_galleries';

$table_2_name = $wpdb->prefix . 'nfg_imgs';

$row= $wpdb->get_row("SELECT _name as nam, _desc as descr,_thumb_id as thumb FROM {$table_1_name};");

$thumbid = $row->thumb;

$title = $row->nam;
$desc = $row->descr;

$url = wp_get_attachment_image_src($row->thumb, 'gallery-image-widget');  

if(!$url[0]){

    $img_code = plugins_url( '../img/missing_image.jpg', __FILE__ );

}else{

    $img_code = $url[0];

}

$thumb = "<div class='album_thumbnail' style='background-image:url(".$img_code.");'></div>";    

$image_gallery = "";

$photo_ids = "";

$photoidarray_js = array();

$result = $wpdb->get_results("SELECT id, _photo_id as pid, _weblink from {$table_2_name} order by id asc;");

foreach ( $result as $row )
{
    
    $weblink = $row->_weblink;

    $pid = $row->pid;

    $photo_ids.=":".$pid;
    
    array_push($photoidarray_js,"id".$pid);

    $url = wp_get_attachment_image_src($pid, "gallery-image-widget", false);  

    if(!$url[0]){

        $img_code = "img/missing_image.jpg";

    }else{
        
        $img_code = $url[0];

    }


    $image_gallery .= "<li class='image_gallery' data-id='".$pid."' style='background-image:url(".$img_code.");'><div class='close-btn'>X</div><div class='link'><input type='text' maxlength='250' placeholder='weblink ie. http://...' class='url' value='".$weblink."' /></div></li>";    

}

?><div class='main-div'>

    <form  method="post" action="" enctype="multipart/form-data" id="image_gallery_form">
            <input type="hidden" name="custom_meta_box_nonce" value="<?php echo wp_create_nonce('no-frills-my-new-gallery-nonce'); ?>" />
            <div class='form-item'>Album Name* <br />
               <input type="text" name="cf-gallery-name"  value="<?php echo ( isset( $title ) ? esc_attr( stripslashes($title) ) : '' ); ?>" size="150" required />    
            </div>
            <div class='form-item'>Album Description<br /><i>Text only. Html code will be removed on save.</i><br />
                <textarea rows="10" cols="35" name="cf-gallery-description"><?php echo( isset( $desc ) ? esc_attr( stripslashes($desc) ) : '' ); ?></textarea>
            </div>
            <div class="wrap form-item"><h2>Album Photos</h2><p><i>Select multiple photos from the Media Library by clicking the button below.<br><br>Images can include a web-link (optional), for use when viewing as a slidehow (see slidehow shortcode generator page).<br><strong>You can re-arrange your images</strong> by simply dragging and dropping an image with your mouse.</i></p></div>   
            <div class="uploader">
               <input id="image_ids" name="image_ids" type="hidden" value="<?php echo $photo_ids; ?>" />
               <input id="weblinks" name = "weblinks" type="hidden" value="" />
               <input id="library_selector_button" class="button img-button" name="library_selector_button" type="text" value="Add Images To Album" />
               <ul class="sortable grid">
                <?php echo $image_gallery; ?>
               </ul>
            </div>
            <div class='dashed'></div>
            <p class='submit'>
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Album Changes">
            </p>
    </form>
    <input type="button" onclick="location.href='admin.php?page=nfg';" class="button button-secondary" value="Cancel Changes" />

    <div class='question'>
        <div class='questionmark'></div><div class='questiontext'>
                <strong>How do I view the album on my website?</strong><br>Albums can be added to any page or element on your website using <i>Shortcodes</i>. You can customise your gallery shortcode parameters quickly and easily.<br>Simply click the relevant Shortcode links to create your shortcode, then copy and paste the code directly in to your page.
        </div>
    </div>

</div><div class="help-div">
    <div class='box'>
        <h2>Why Go PRO?</h2>
        <p>Upgrade to <strong>No Frills Gallery PRO</strong> now for extended features:</p>
        <ul><li>Multiple Galleries</li><li>List albums as grid or drop-down</li><li>Create Categories</li><li>Create slideshows quickly and easily</li><li>Place shortcodes ANYWHERE on your site</li><li>Individual Descriptions and web links per image</li><li>Extra customisable features</li></ul>
        <p>More features, and still so simple and customisable!</p><div class='center'><a href='http://www.jamestibbles.co.uk/no-frills-gallery-pro' class='buybutton'>Get No Frills Gallery PRO Now for just €4</a></p></div>
    </div>
    <br><Br>
    <div class='box'>
        <h2>No Frills Prize Draw (+PRO)</h2>
        <p>Check out our new FREE prize draw / competition / sweepstakes manager, <strong>No Frills Prize Draw</strong>.</p>
        <p>Very customisable but SO SIMPLE to use. Set up different competition types and handle the entry/winner selection all from the admin area. You can also export entrant details to CSV.</p>
        <div class='center'><a href='http://www.jamestibbles.co.uk/no-frills-prize-draw' class='buybutton'>No Frills Prize Draw (free)</a></div>
        <p>Our <strong>PRO</strong> version comes with EVEN MORE NEW FEATURES, including multiple competitions, customisable fields, multiple winners, winner re-picking AND MORE!</p>
        <div class='center'><a href='http://www.jamestibbles.co.uk/no-frills-prize-draw-pro' class='buybutton'>No Frills Prize Draw PRO</a></div>
    </div>
    <br><Br>
    <div class='box'>
        <h2>WP Business Driectory (+PRO)</h2>
        <p>We're nearly ready to launch our advanced customisable, mobile-friendly Business Directory!<br>Register your interest now and get a discount when we launch!</p>
        <div class='center'><a href='http://www.jamestibbles.co.uk/wordpress-business-directory-pro/' class='buybutton'>WP Business Driectory (PRO)</a></div>
    </div>
    <br><Br>
    <!--div class='box'>
    </div-->
</div>

<script><?php 
           
            function js_str($s)
            {
                return '"' . addcslashes($s, "\0..\37\"\\") . '"';
            }

            function js_array($array)
            {
                $temp = array_map('js_str', $array);
                return '[' . implode(',', $temp) . ']';
            }

            echo 'document.photoids = ', js_array($photoidarray_js), ';';
            ?>
</script>