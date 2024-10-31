<?php	
if ( ! current_user_can( 'edit_posts' ) ) {
    echo "<div class='error'>You do not have the correct privileges to access this page</div>"; 
    die();
}
?>
<style>
label{
    width:250px;
    margin-right:5px;
    display:inline-block;
}
#albums{
    margin-left:4px !important;
}
.button{
    margin:20px 0;
}
</style>
<div class='nf-content'>
<div class='main-div'><h2>Album Shortcode Information</h2>
    <p>Your image gallery albums are implemented on the website using simple, customisable shortcodes.</p>
    <p>A shortcode can be added to any page using the <a href='https://codex.wordpress.org/Shortcode_API' target='_blank'>standard shortcode</a> method, or via the <a href='https://developer.wordpress.org/reference/functions/do_shortcode/' target='_blank'>PHP 'do_shortcode'</a> method.</p>
    
    <h2>Album Images Display Shortcode Generator</h2>
  
    <div class='field'><label>Number of images required (optional, leave blank for all)</label> <input type='number' id='num' name='num' /></div>
    <div class='field'><label>Paginate?</label> <select id='paginate' name='paginate'><option value='0'>No</option><option value='1'>Yes</option></select></div>
    <div class='field'><label>If paginating, enter num. images per page</label> <input type='number' id='perpage' name='perpage' /></div>
    <div><input type='button' id='album-shortcode-generator' class='button button-primary' value='Generate Shortcode' /></div>
    
    <p>Your generated Album Images Display shortcode will be placed in the box below. Please note these shortcodes will not be saved. To use simply copy the shortcode and paste it in to a new or existing page.</p>
    <div id='album-images-shortcodes'style="border:dashed 1px #ccc; background-color #fff;margin:20px 0 20px 0;padding:5px;width:70%;min-height:5px;"></div>
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
</div>
