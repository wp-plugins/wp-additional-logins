<div class="updated" style="border-left: 0;">
	<form method="post" action="users.php?page=wpal&user=<?= $userID ?>&action=add-login" autocomplete="off">
		
		<input type="text" style="display:none">
		<input type="password" style="display:none">
		<input type="hidden" name="wpal-add-login" value="true">

		<?php settings_fields( 'wpal' ); ?>
		<?php do_settings_sections( 'wpal' ); ?>

		<table class="form-table">
			<tbody>
				<tr>
					<th colspan="2" style="padding-top: 0;">
						<h3 style="margin-bottom: 0;">Adding an Additional Login for <a href="user-edit.php?user_id=<?= $user->ID ?>"><?= $user->display_name ?></a></h3>
						<p><?= ucwords( implode( ', ', $user->roles ) ) ?></p>
					</th>
				</tr>
				<tr>
					<th><label for="username">Username *</label></th>
					<td><input type="text" name="username" value="<?= $_GET['username'] ?>"></td>
				</tr>
				<tr>
					<th><label for="password">Password *</label></th>
					<td><input type="password" name="password"></td>
				</tr>
				<tr>
					<th><label for="confirm_password">Confirm Password *</label></th>
					<td><input type="password" name="confirm_password"></td>
				</tr>
				<tr>
					<th><label for="new_username">Description</label></th>
					<td><textarea style="width: 400px; height: 80px;" name="description"><?= $_GET['description'] ?></textarea></td>
				</tr>
			</tbody>
		</table>

		<p>
			<input class="button button-primary" type="submit" value="Add Additional Login">
			<a href="users.php?page=wpal&user=<?= $userID ?>" class="button">Cancel</a>
		</p>

	</form>
</div>