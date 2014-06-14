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
	<h3><?php _ex( 'Configuration', 'Heading', $this->plugin_slug ) ?></h3>
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
							<?php _e( 'Enable logging' ); ?>
						</label>
						<p class="description"><?php _e('This is recommended when you setup a process based on the IFTTT WordPress Bridge the first time. In the field below you can see helpful information about how the IFTTT request is processed.' ); ?></p>
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
	<h3><?php _ex( 'Send test request', 'Heading', $this->plugin_slug ) ?></h3>
	<p class="description"><?php _e( 'Send a test request if you want to make sure that your WordPress installation is ready for IFTTT. Use the form below to send a request which is identical to the ones sent by IFTTT.') ?></p>
	<form action="admin-post.php" method="post">
	  <input type="hidden" name="action" value="sent_post_request">
	  <input type="hidden" name="redirect_url" value="sent_post_request">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="test-request-username"><?php _ex( 'Username', 'Test request form label', $this->plugin_slug ); ?></label></th>
					<td><input type="text" class="regular-text" id="test-request-username" name="test-request-username"></td>
				</tr>
				<tr>
					<th scope="row"><label for="test-request-password"><?php _ex( 'Password', 'Test request form label', $this->plugin_slug ); ?></label></th>
					<td><input type="password" class="regular-text" id="test-request-password" name="test-request-password"></td>
				</tr>
				<tr>
					<th scope="row"><label for="test-request-title"><?php _ex( 'Title', 'Test request form label', $this->plugin_slug ); ?></label></th>
					<td><input type="text" class="regular-text" id="test-request-title" name="test-request-title"></td>
				</tr>
				<tr>
					<th scope="row"><label for="test-request-description"><?php _ex( 'Description', 'Test request form label', $this->plugin_slug ); ?></label></th>
					<td><input type="text" class="regular-text" id="test-request-description" name="test-request-description"></td>
				</tr>
				<tr>
					<th scope="row"><label for="test-request-tags"><?php _ex( 'Tags', 'Test request form label', $this->plugin_slug ); ?></label></th>
					<td><input type="text" class="regular-text" id="test-request-tags" name="test-request-tags">
					<p class="description"><?php _ex( "Comma-separated list. The label 'ifttt_wordpress_bridge' will be used automatically.", 'Test request form description', $this->plugin_slug ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="test-request-draft"><?php _ex( 'Draft', 'Test request form label', $this->plugin_slug ); ?></label></th>
					<td><input name="test-request-draft" type="checkbox" id="test-request-draft" value="1" /></td>
				</tr>
			</tbody>
		</table>
		<?php submit_button( _x( 'Send request', 'Button label', $this->plugin_slug ), 'primary', 'send-test-request' ); ?>
	</form>
</div>
