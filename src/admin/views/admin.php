<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Ifttt_Wordpress_Bridge_Admin
 * @author    Björn Weinbrenner <info@bjoerne.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 */
?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<textarea id="ifttt-wordpress-bridge-log" readonly="readonly" style="width: 100%; height: 200px"><?php 
foreach ( $this->log as $log_entry ) {
	echo esc_html( $log_entry ), "\n";
}
?></textarea>
	</form>
</div>
