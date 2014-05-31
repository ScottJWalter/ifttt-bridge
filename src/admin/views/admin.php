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
	<form method="post" action="options.php">
<?php
	settings_fields( 'ifttt_wordpress_bridge_options_group' );
	do_settings_sections( 'ifttt_wordpress_bridge_options_group' );
?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" colspan="2" class="th-full">
						<label for="ifttt_wordpress_bridge_options_log_enabled">
							<input name="ifttt_wordpress_bridge_options[log_enabled]" type="checkbox" id="ifttt_wordpress_bridge_options_log_enabled" value="1"<?php checked( '1', $this->log_enabled ); ?> />
							<?php _e( 'Log enabled' ); ?>
						</label>
					</th>
				</tr>
				<tr>
					<th scope="row" colspan="2" class="th-full">
						<textarea id="ifttt-wordpress-bridge-log" readonly="readonly" style="width: 100%; height: 200px"<?php echo $this->log_enabled ? '' : ' disabled="disabled"'; ?>><?php 
foreach ( $this->log as $log_entry ) {
	echo esc_html( $log_entry ), "\n";
}
?></textarea>
					</th>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
