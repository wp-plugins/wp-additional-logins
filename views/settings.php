<div class="updated" style="border-left: 0;">
	<form method="post" action="options.php">

		<?php settings_fields( 'wpal' ); ?>
		<?php do_settings_sections( 'wpal' ); ?>

		<table class="form-table">
			<tbody>

				<tr>
					<th colspan="2" style="padding-top: 0;">
						<h3><span class="dashicons dashicons-admin-generic"></span> Configuration</h3>
					</th>
				</tr>

				<tr>
					<th><label for="<?= WPAL_OPTION_ENABLED ?>">Plugin Enabled</label></th>
					<td>
						<select name="<?= WPAL_OPTION_ENABLED ?>" id="<?= WPAL_OPTION_ENABLED ?>">
							<option value="TRUE" <?php echo get_option( WPAL_OPTION_ENABLED, WPAL_DEFAULT_ENABLED ) == 'TRUE' ? 'selected' : '' ?>>Yes</option>
							<option value="FALSE" <?php echo get_option( WPAL_OPTION_ENABLED, WPAL_DEFAULT_ENABLED ) == 'FALSE' ? 'selected' : '' ?>>No</option>
						</select>
						<p class="description">Whether additional logins should actually be allowed to login.</p>
					</td>
				</tr>

			</tbody>
		</table>

		<?php submit_button(); ?>

	</form>
</div>