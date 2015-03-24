<div class="updated" style="border-left: 0;">
	<form method="get" action="users.php">

		<input type="hidden" name="page" value="wpal">

		<table class="form-table">
			<tbody>

				<tr>
					<td style="padding-top: 0;">

						<h3><span class="dashicons dashicons-admin-users"></span> Choose a User</h3>
						<p>Please select an existing user to manage additional logins with.</p>

						<select name="user" id="user">

							<?php foreach ( $users as $user ) : ?>
								<option value="<?= $user->ID ?>"><?= $user->display_name ?> (<?= $user->user_email ?>)</option>
							<?php endforeach; ?>

						</select>
		
						<p>
							<input type="submit" class="button button-primary" value="Manage"></input> <a href="options-general.php?page=wpal-options" class="button">Plugin Settings</a>
						</p>

					</td>
				</tr>

			</tbody>
		</table>

	</form>
</div>