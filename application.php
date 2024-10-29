<?php 
/*
Plugin Name: AlternativeTo Widgets
Plugin URI: http://blog.alterativeto.net
Description: Enables some shortcodes to display various information from AlternativeTo.net
Author: Ola Johansson - AlternativeTo.net
Version: 0.9.8
Author URI: http://blog.alternativeto.net
*/

/*  Copyright 2009 27 Kilobyte AB (email : hello@alternativeto.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Function to inject the markup and Javascript to the post. */
function alt2app_func($atts) {
  extract(shortcode_atts(array(
    'app' => 'internet-explorer',
    'category' => 'desktop',
    'license' => '',
    'platform' => ''
  ), $atts));
  
  $numberOfAlternatives = get_option('alt2_options_numberofalternatives');  
  $alt2license = $atts["license"];
  $alt2platform = $atts["platform"];
  
  if($alt2platform == '') {
     $alt2platform = get_option('alt2_options_platform');
  }
  
  if($alt2license == '') {
     $alt2license = get_option('alt2_options_license');
  }

  if($numberOfAlternatives == "") {
    $numberOfAlternatives = "5";
  }

  $wp_alt2_theme = get_option('alt2_options_theme');
  if($wp_alt2_theme == "") {
    $wp_alt2_theme = "default";
  }

  if (is_single()){
    
    $mycomma = ',';
    $alt2settings = $numberOfAlternatives.$mycomma.$atts["app"].$mycomma.$atts["category"].$mycomma.$wp_alt2_theme.$mycomma.is_preview().$mycomma.$alt2license.$mycomma.$alt2platform;

    return "<input type='hidden' value='".$alt2settings."' class='a2options' id='a2options' /><div class='a2widget' id='a2widget'></div>";
  }
}

/* Add the references to the javascript and ensure that jQuery is there. */
function alt2_scripts() {
  if (is_single()){
    $wp_alt2_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
    wp_enqueue_script('jquery');
    wp_enqueue_script('wp_alt2_script', $wp_alt2_plugin_url.'/alt2.js', array('jquery'));
  }
}

//function alt2_add_alt2url( &$links ) {
//  array_push($links,'http://dev.ohso.se/desktop/adobe-photoshop/');
//}



// Add the CSS.
function alt2_header() {
  $wp_alt2_theme = get_option('alt2_options_theme');
  $wp_alt2_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
  
  if($wp_alt2_theme == "") {
    $wp_alt2_theme = "default";
  }
  
  if($wp_alt2_theme != "minimal") {
    echo '<link type="text/css" rel="stylesheet" href="'. $wp_alt2_plugin_url.'/'.$wp_alt2_theme.'.css" />' . "\n";
  }
}

// Hook it up
add_shortcode('alt2app', 'alt2app_func');
add_action('wp_print_scripts', 'alt2_scripts');
add_action('wp_head', 'alt2_header', 1);
//add_action('pre_ping', 'alt2_add_alt2url' );

// **********************
// ADMIN
// **********************

function alt2_admin_menu() {
  add_options_page('AlternativeTo Options', 'AlternativeTo', 'administrator', 'alternativeto-options', 'alt2_admin_markup');
}

function alt2_admin_markup() {
?>
<div class="wrap">
<h2>AlternativeTo - Options</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<p>Use <strong>[alt2app app=internet-explorer category=desktop]</strong> to list alternatives to Internet Explorer.</p>
<h3> License and platform filters</h3>
<p>You can also add filter for license and platform.</p>
<ol>
<li>License filters: free,opensource and commercial. If you use free all applications that has Free, Open Source, Free with limited functionality and Free for personal use will be listed. Open Source and Commercial just list the items that has these licenses.</li>
<li>Platform filter: You can filter by multiple platforms but they will do a OR query. So if you send in windows|linux we will give you all items that is Windows OR Linux.</li>
</ol>
<h3>More examples</h3>
<ul>
<li> [alt2app app=runkeeper category=mobile platform=blackberry|iphone]</li>
<li> [alt2app app=runkeeper category=mobile license=free]</li>
<li> [alt2app app=internet-explorer category=desktop license=opensource platform=linux]</li>
</ul>
<p>Requries &lt;?php wp_head(); ?&gt; in the header.php file for it to work. If you have any problems at all please send a mail to hello@alternativeto.net and we try to track down any bugs. </p>

<table class="form-table">
<tr valign="top">
<th scope="row">Show number of alternatives</th>
<td><input type="text" name="alt2_options_numberofalternatives" value="<?php echo get_option('alt2_options_numberofalternatives'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Default license</th>
<td><input type="text" name="alt2_options_license" value="<?php echo get_option('alt2_options_license'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Default platform</th>
<td><input type="text" name="alt2_options_platform" value="<?php echo get_option('alt2_options_platform'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Default theme</th>
  <td>
  <?php $alt2_selected = get_option('alt2_options_theme'); ?>
  <select name="alt2_options_theme">
    <option value="default" <?php if ($alt2_selected == "default") { echo 'selected'; }?>>Default</option>
    <option value="small" <?php if ($alt2_selected == "small") { echo 'selected'; }?>>Small</option>
    <option value="minimal" <?php if ($alt2_selected == "minimal") { echo 'selected'; }?>>Minimal</option>
    </select>
  </td>
</tr>
</table>
  
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="alt2_options_numberofalternatives,alt2_options_theme,alt2_options_license,alt2_options_platform" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>

<?php
}

add_action('admin_menu', 'alt2_admin_menu');

?>