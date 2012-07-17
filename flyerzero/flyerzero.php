<?php
/*
Plugin Name: Flyer Zero
Plugin URI: http://www.flyerzero.com/
Description: A plugin for the Flyer Zero Event Promotion System
Version: 1.0
Author: thurtt
Author URI: http://www.openformfoundation.org
License: GPL
*/

    register_activation_hook(__FILE__,'flyerzero_install');
    register_deactivation_hook( __FILE__, 'flyerzero_remove' );
    add_action('init','flyerzero_flyers');

    function flyerzero_flyers()
    {
	echo '<iframe src="'. get_option('src_url') . '" width="450px" height="650px"></iframe>';
    }

    function flyerzero_install() {
	add_option("method", "", "", "yes");
	add_option("email", "", "", "yes");
	add_option("lat", "", "", "yes");
	add_option("lng", "", "", "yes");
	add_option("venue_id", "", "", "yes");
	add_option("radius", "", "", "yes");
	add_option("all", "", "", "yes");
	add_option("src_url", "http://www.flyerzero.com/zerobox/box/?ll=38.029345,-78.476749&radius=30&size=medium", "", "yes");
	add_option("size", "", "", "yes");
    }

    function flyerzero_remove() {
	delete_option("method");
	delete_option("email");
	delete_option("lat");
	delete_option("lng");
	delete_option("venue_id");
	delete_option("radius");
	delete_option("all");
	delete_option("src_url");
	delete_option("size");
    }

    if ( is_admin() ) {

	/* Call the html code */
	add_action('admin_menu', 'flyerzero_admin_menu');

	function flyerzero_admin_menu() {
	    add_options_page('Flyer Zero', 'Flyer Zero', 'administrator', 'flyerzero', 'flyerzero_html_page');
	}
    }
?>


<?php
    function flyerzero_html_page() {
	wp_register_script( 'crypto-js', 'http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js');
	wp_enqueue_script('crypto-js');
	wp_enqueue_script('jquery');
?>
<div>
<h2>Flyer Zero Options</h2>

<form method="post" action="options.php" id="settings_form">
<?php wp_nonce_field('update-options'); ?>

<table>
    <tr>
	<td>
	    Flyer Presentation
	</td>
	<td>
	    <select id="method_control" name="method" value="<?php echo get_option('method'); ?>">
		<option value="local">Local</option>
		<option value="venue">Venue</option>
		<option value="promoter">Promoter</option>
	    </select>
	</td>
    </tr>
    <tr class="venue_options" style="display:none">
	<td>
	    Venue
	</td>
	<td>
	    <input type="text" id="venue_id" name="venue_id" value="<?php echo get_option('venue_id'); ?>">
	</td>
    </tr>
    <tr class="local_options">
	<td>
	    Location
	</td>
	<td>
	    <input type="text" id="lat" name="lat" value="<?php echo get_option('lat'); ?>"><input type="text" id="lng" name="lng" value="<?php echo get_option('lng'); ?>">
	</td>
    </tr>
    <tr class="local_options">
	<td>
	    Radius
	</td>
	<td>
	    <input type="text" id="radius" name="radius" value="<?php echo get_option('radius'); ?>"
	</td>
    </tr>
    <tr class="promoter_options" style="display:none">
	<td>
	    Promoter ID
	</td>
	<td>
	    <input type="text" id="email" name="email" value="<?php echo get_option('email'); ?>"
	</td>
    </tr>
    <tr class="promoter_options" style="display:none">
	<td>
	    Show Expired Flyers
	</td>
	<td>
	    <input type="checkbox" id="all" name="all" value="<?php echo get_option('all'); ?>"
	</td>
    </tr>
    <tr>
	<td>
	    Flyer Size
	</td>
	<td>
	    <select name="size" id="size" value="<?php echo get_option('size'); ?>">
		<option value="small">small</option>
		<option value="medium" selected="selected">medium</option>
		<option value="large">large</option>
	    </select>
	</td>
    </tr>
</table>
<input type="hidden" name="action" value="update" />
<input id="src_url" type="hidden" name="src_url" value="" />
<input type="hidden" name="page_options" value="method,venue_id,lat,lng,radius,email,all,size,src_url" />

<p>
<input id="save_button" type="button" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
<script>
jQuery.noConflict();

// a closure to give us back the $ shortcut
(function($){
    $(document).ready(function() {
	$('#method_control').bind('click change',function() {
	    switch($('#method_control').val()){
		case 'local':
		    $('.venue_options').hide();
		    $('.promoter_options').hide();
		    $('.local_options').show();
		    break;
		case 'venue':
		    $('.local_options').hide();
		    $('.promoter_options').hide();
		    $('.venue_options').show();
		    break;
		case 'promoter':
		    $('.local_options').hide();
		    $('.venue_options').hide();
		    $('.promoter_options').show();
		    break;
	    }
	});
    });

    $('#save_button').click(function(){
	$('#src_url').val(createUrl());
	$('#settings_form').submit();
    });

    function createUrl(){
	// http://www.flyerzero.com/zerobox/box/?ll=38.029345,-78.476749&radius=60&size=medium
	url = 'http://www.flyerzero.com/zerobox/'
	method = $('#method_control').val();
	switch(method){
	    case 'local':
		url += 'box/?ll=' + $('#lat').val() + ',' + $('#lng').val();
		url += '&radius=' + $('#radius').val();
		break;
	    case 'venue':
		url += 'venue/?venue_id=' + $('#venue_id').val();
		break;
	    case 'promoter':
		hash = CryptoJS.MD5($('#email').val().toLowerCase().trim());
		url += 'promoter/?promoter_hash=' + hash;
		if( $('#all').is(':checked')){
		    url += '&all=1';
		}
		break;
	}
	url += '&size=' + $('#size').val();
	return url;
    }
})(jQuery);
</script>
<?php
    }
?>