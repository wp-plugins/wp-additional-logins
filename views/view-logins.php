<div class="updated" style="border-left: 0;">
	<form method="get" action="users.php">

		<input type="hidden" name="page" value="wpal">

		<table class="form-table">
			<tbody>
				<tr>
					<td style="padding-top: 0;">

						<span style="float: left; margin-right: 10px; margin-top: .67em;"><?= get_avatar( $user->user_email ) ?></span>

						<h1 style="margin-bottom: 0;"><a href="user-edit.php?user_id=<?= $user->ID ?>"><?= $user->display_name ?> (<?= $user->user_login ?>)</a></h1>
						<p><a href="mailto:<?= $user->user_email ?>"><?= $user->user_email ?></a></p>
						<p><strong><?= ucwords( implode( ', ', $user->roles ) ) ?></strong></p>

					</td>
				</tr>
			</tbody>
		</table>

		<p><em>If an additional login is used, the account cannot manage other additional logins, and cannot edit the main account's profile.</em></p>

		<table cellpadding="5" class="wp-list-table widefat" style="margin-bottom: 30px;">
		
			<thead>
				<tr>
					<th>Username</th>
					<th>Description</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="the-list">

				<?php if ( !empty( $logins ) ) : ?>
			
					<?php $i = 0 ?>
					<?php foreach ( $logins as $index => $login ) : ?>
						<tr class="<?= $i % 2 == 1 ? 'alternate' : '' ?>">
							<td><strong><?= $login['user_login'] ?></strong></td>
							<td><?= $login['description'] ?></td>
							<td><a class="button" href="users.php?page=wpal&user=<?= $userID ?>&action=remove-login&login=<?= $index ?>" onclick="return confirm('Are you sure you want to remove the login <?= $login['user_login'] ?>?')">Remove</a></td>
						</tr>
						<?php $i ++ ?>
					<?php endforeach; ?>

				<?php else : ?>
					<tr>
						<td colspan="4">
							None found - <a href="users.php?page=wpal&user=<?= $userID ?>&action=add-login">add a new login?</a>
						</td>
					</tr>
				<?php endif; ?>

			</tbody>

		</table>

		<p><a href="users.php?page=wpal&user=<?= $userID ?>&action=add-login" class="button button-primary">Add a new login</a></p>

	</form>
</div>