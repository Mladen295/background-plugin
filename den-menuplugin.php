<?php
/*
Plugin Name: Den-Menu
Plugin URI: http://denreno.000webhostapp.com/wordpress/plugins
Description: Simple menu
Author: Mladen Renovica
Author URI: http://denreno.000webhostapp.com
*/

require_once( trailingslashit( ABSPATH ) .'wp-load.php' ); 
function get_color() {
global $wpdb;
$results = $wpdb->get_var( "SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'mt_favorite_color'");
return $results;
}
add_action('init', 'write_to_file');
function write_to_file() {
$file = fopen($_SERVER['DOCUMENT_ROOT'] . "/wp-content/plugins/den-menuplugin/my-style.css", "w");
fwrite($file,".blog-post{ background-color:". get_color().";}");
fclose($file);
}
add_action( 'wp_enqueue_scripts', 'my_plugin_style', 50 );
function my_plugin_style() {
    wp_register_style( 'den-menuplugin', plugins_url( 'den-menuplugin/my-style.css' ) );
	wp_enqueue_style( 'den-menuplugin');
	}
// Hook for adding admin menus
add_action('admin_menu', 'mt_add_pages');

// action function for above hook
function mt_add_pages() {
    // Add a new submenu under Settings:
    add_options_page(__('Test Settings','menu-test'), __('Test Settings','menu-test'), 'manage_options', 'testsettings', 'mt_settings_page');

    // Add a new submenu under Tools:
    add_management_page( __('Test Tools','menu-test'), __('Test Tools','menu-test'), 'manage_options', 'testtools', 'mt_tools_page');

    // Add a new top-level menu (ill-advised):
    add_menu_page(__('Test Toplevel','menu-test'), __('Test Toplevel','menu-test'), 'manage_options', 'mt-top-level-handle', 'mt_toplevel_page' );

    // Add a submenu to the custom top-level menu:
    add_submenu_page('mt-top-level-handle', __('Test Sublevel','menu-test'), __('Test Sublevel','menu-test'), 'manage_options', 'sub-page', 'mt_sublevel_page');

    
}

// mt_settings_page() displays the page content for the Test Settings submenu
function mt_settings_page() {

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names 
    $opt_name = 'mt_favorite_color';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'mt_favorite_color';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );

        // Put a "settings saved" message on the screen
    
?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php

    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Menu Test Plugin Settings', 'menu-test' ) . "</h2>";

    // settings form
    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Favorite Color:", 'menu-test' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
</p><hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>
