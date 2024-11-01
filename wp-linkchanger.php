<?php
/*
Plugin Name: WP-LinkChanger
Plugin URI: http://www.webservicexxl.de/wp-linkchanger/
Description: This plugin will change all of your affiliate links into good-looking internal links. To personalize your link please visit the <a href="options-general.php?page=wp-linkchanger/wp-linkchanger.php">configuration panel</a>. 
Version: 0.25
Author: HVBX
Author URI: http://www.webservicexxl.de
Min WP Version: 2.5
Max WP Version: 2.7.1
*/

/*  Copyright 2008  H.Vogelgesang  (email : info@webservicexxl.de)
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

/*** Ab hier nichts mehr ändern! -- do not change the code below ***/

// START main function -- linkchanger
function afflinktauscher($content) {

	// Pre-2.6 compatibility
	if ( !defined( 'WP_CONTENT_URL' ) )	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
	if ( !defined( 'WP_CONTENT_DIR' ) ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( !defined( 'WP_PLUGIN_URL' ) ) define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	if ( !defined( 'WP_PLUGIN_DIR' ) ) define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

	if (get_option('linkchanger_exitpath')) $exitpath = get_option('linkchanger_exitpath');
	else									$exitpath = WP_PLUGIN_URL .'/'. plugin_basename( dirname(__FILE__) ) .'/';

	if (get_option('linkchanger_exitfilename')) $exitfilename = get_option('linkchanger_exitfilename');
	else										$exitfilename = "exit.php";
	
	if (get_option("linkchanger_subaid"))	$subaid = get_option("linkchanger_subaid");
	else									$subaid = "Blog";

	$blogurl = $exitpath.''.$exitfilename;
	
	/* zanox links ersetzen */
	$content = preg_replace('/"http:\/\/ad.zanox.com\/ppc\/\?(.*?)"/ie',"'\"$blogurl?mm=zx&amp;id='.urlencode('\\1').'&amp;c=$subaid\" rel=\"nofollow\" class=\"alink\"'",$content);
	$content = preg_replace('/"http:\/\/www.zanox.affiliate.de\/ppc\/\?(.*?)"/ie',"'\"$blogurl?mm=zx&amp;id='.urlencode('\\1').'\" rel=\"nofollow\" class=\"alink\"'",$content);

	/* affilinet links ersetzen */
	$content = preg_replace('/"http:\/\/partners.webmasterplan.com\/click.asp\?ref=(.*?)"/ie',"'\"$blogurl?mm=an&amp;id='.urlencode('\\1').'&amp;c=$subaid\" rel=\"nofollow\" class=\"alink\"'",$content);

	/* tradedoubler links ersetzen */
	$content = preg_replace('/"http:\/\/clkde.tradedoubler.com\/click\?p=(.*?)"/ie',"'\"$blogurl?mm=td&amp;id='.urlencode('\\1').'&amp;c=$subaid\" rel=\"nofollow\" class=\"alink\"'",$content);

	/* adbutler links ersetzen */
	$content = preg_replace('/"http:\/\/james.adbutler.de\/click.php\?pid=(.*?)"/ie',"'\"$blogurl?mm=ab&amp;id='.urlencode('\\1').'&amp;c=$subaid\" rel=\"nofollow\" class=\"alink\"'",$content);

	/* amazon links ersetzen */ /* class="afflink" noch hinzufügen */ /* amztag noch übergeben */
	//$content = preg_replace('/"http:\/\/www.amazon.de\/([a-z0-9&;=]*)"/ie',"'\"$blogurl?mm=am&amp;id='.urlencode('\\1').'\" rel=\"nofollow\"'",$content);

	return $content;
}
// END main function -- linkchanger


if ('updatelinkchangeroptions' == $HTTP_POST_VARS['action'])
{
    update_option("linkchanger_exitpath",$HTTP_POST_VARS['linkchanger_exitpath']);
	update_option("linkchanger_exitfilename",$HTTP_POST_VARS['linkchanger_exitfilename']);
	update_option("linkchanger_subaid",$HTTP_POST_VARS['linkchanger_subaid']);
	update_option("linkchanger_amazonid",$HTTP_POST_VARS['linkchanger_amazonid']);
}

// Adminmenu Optionen erweitern
function wp_linkchanger_add_new_menu() {
	add_options_page('WP LinkChanger Config', 'WP LinkChanger', 9, __FILE__, 'wp_linkchanger_option_page');
}

// START Optionen im Adminbereich
function wp_linkchanger_option_page() {
	
	if (get_option('linkchanger_exitpath')) $exitpath = get_option('linkchanger_exitpath');
	else									$exitpath = WP_PLUGIN_URL .'/'. plugin_basename( dirname(__FILE__) ) .'/';

	if (get_option('linkchanger_exitfilename')) $exitfilename = get_option('linkchanger_exitfilename');
	else										$exitfilename = "exit.php";
	
	if (get_option("linkchanger_subaid"))	$subaid = get_option("linkchanger_subaid");
	else									$subaid = "Blog";
?>
<div class="wrap" style="width:80%;">
	<h2>WP LinkChanger Configuration</h2><br />
	<form name="form_wplinkchanger" method="post" action="<?=$_SERVER["REQUEST_URI"]?>">
		<?php wp_nonce_field('update-options'); ?>
		<input name="action" value="updatelinkchangeroptions" type="hidden" />

		<div style="float:right; text-align:center; margin-top:10px; margin-left:10px;" align="center">
			<b>WP LinkChanger 0.25</b><br />
			<a href="http://www.amazon.de/gp/registry/registry.html?ie=UTF8&type=wishlist&id=F2MOP81FUQWJ" target="_blank" title="Spenden">Danke sagen!</a><br />
			<a href="http://www.webservicexxl.de/wp-linkchanger/" target="_blank">Feedback</a>
		</div>

		<p style="font-size:smaller;"><b>I. Path and Filename (optional)</b><br />
		Hier kannst den Pfad und den Dateinamen deiner 'exit.php' anpassen.<br />
		Standardmäßig ist der ausgehende Link ziemlich lang.<br />
		Bsp.: http://www.domain.de/wp-content/plugins/wp-linkchanger/exit.php?mm=zx&id=12345C12345678T<br /><br />
		Mit dieser Option kannst du den Link deinen Wünchen anpassen.<br />Bsp.: http://www.domain.de/cu.php?mm=zx&id=12345C12345678T</p>

		<div style="width:80%; border:1px solid #666; padding:10px; background-color:#E4F2FD;">
		<img src="../wp-includes/js/tinymce/plugins/wordpress/img/help.gif" alt="Hilfe" style="float:right;" />
			<h3>Individueller Pfad zur <i>exit.php</i>:</h3>
			<p>Hier kannst du den Pfad f&uuml;r externe Links festlegen.</p>
			<input name="linkchanger_exitpath" value="<?=$exitpath;?>" type="text" style="width:90%;" /><br />
		</div>
		<br />
		<div style="width:80%; border:1px solid #666; padding:10px; background-color:#E4F2FD;">
		<img src="../wp-includes/js/tinymce/plugins/wordpress/img/help.gif" alt="Hilfe" style="float:right;" />
			<h3>Individueller Dateiname der <i>exit.php</i>:</h3>
			<p>Hier kannst du den Dateiname f&uuml;r externe Links festlegen.</p>
			<input name="linkchanger_exitfilename" value="<?=$exitfilename;?>" type="text" style="width:90%;" /><br />
		</div>
		<br />
		<p style="font-size:smaller; color:red; width:80%;"><b>ACHTUNG:</b><br />Wenn die obereen Feldern geändert werden, muss die 'exit.php' aus dem Plugin-Ordner an die angegebene Stelle verschoben und ggf. umbenannt werden.</p>
		<hr noshade="noshade" />
		<!--<p style="font-size:smaller;"><b>II. Amazon</b><br /></p>
		<div style="width:70%; border:1px solid #666; padding:10px; background-color:#E4F2FD;">
		<img src="../wp-includes/js/tinymce/plugins/wordpress/img/help.gif" alt="Hilfe" style="float:right;" />
			<h3>Amazon Affiliate ID:</h3>
			<p>Die Kennung deiner Amazon Partnerschaft.</p>
			<input name="linkchanger_amazonid" value="<?=get_option("linkchanger_amazonid");?>" type="text" style="width:90%;" /><br />
			<small>Momentan nicht aktiv. Funktion geplant. F&uuml;r Feedback: <a href="http://hvbx.uservoice.com/" target="_blank">http://hvbx.uservoice.com/</a> </small>
		</div>-->
		<p style="font-size:smaller;"><b>II. Sonstige Einstellungen (Misc.)</b><br /></p>
		<div style="width:80%; border:1px solid #666; padding:10px; background-color:#E4F2FD;">
		<img src="../wp-includes/js/tinymce/plugins/wordpress/img/help.gif" alt="Hilfe" style="float:right;" />
			<h3>SubAffiliateID</h3>
			<p>Die Affiliate-Netzwerke bieten die Möglichkeit eingegangene Leads und Sales mit einer bestimmten Kennung zu versehen. Dies bietet dir z.B. die Möglichkeit Provisionen aus verschiedenen Blogs zu unterscheiden.</p>
			<input name="linkchanger_subaid" value="<?=$subaid;?>" type="text" style="width:90%;" /><br />
			<small></small>
		</div>
		<br />
		<div align="right" style="width:70%;"><input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" /></div>
	</form>
</div>
<?php
} // END Optionen im Adminbereich

if ( is_admin() ) {
	add_action('admin_menu','wp_linkchanger_add_new_menu');
}

add_filter('the_content','afflinktauscher');
?>