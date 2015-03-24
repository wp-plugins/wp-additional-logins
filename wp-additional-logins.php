<?php
/**
 * Plugin Name: WordPress Additional Logins
 * Plugin URI: https://github.com/NikSudan/wp-additional-logins/
 * Description: Adds additional login details to a user
 * Version: 1.0.0
 * Author: Nik Sudan
 * Author URI: http://niksudan.com
 */

// -------------------------------------------------
// Constants
// -------------------------------------------------

// Whether or not the plugin is enabled internally or not
define( 'WPAL_DEFAULT_ENABLED', 'TRUE' );

// The option for storing whether the plugin is enabled
define( 'WPAL_OPTION_ENABLED', 'wpal_enabled' );

// The user meta for storing the additional logins
define( 'WPAL_USER_META', 'wpal_additional_logins' );

// Session variable used to determine if you logged in with an additional login
define( 'WPAL_LOGIN_SESSION', 'wpal_additional_login_user' );

// -------------------------------------------------
// Static Functionality
// -------------------------------------------------

class Wpal
{
	/**
	 * Checks if the additional logins should be able to login
	 * @author Nik Sudan
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public static function is_enabled()
	{
		return get_option( WPAL_OPTION_ENABLED, WPAL_DEFAULT_ENABLED ) == 'TRUE';
	}

	/**
	 * Returns the parent user related to an additional login
	 * @author Nik Sudan
	 * @since 1.0.0
	 * 
	 * @param string $username
	 * @param string $password
	 * @return object/boolean
	 */
	public static function get_login( $username, $password )
	{
		$username = sanitize_user( $username, true );
		$password = trim( $password );
		foreach ( get_users() as $user ) {
			$userID = $user->data->ID;
			foreach ( Wpal::get_login_details( $userID ) as $login ) {
				if ( strtoupper( $login['user_login'] ) == strtoupper( $username ) && wp_check_password( $password, $login['password'] ) ) {
					return $user;
				}
			}
		}
		return false;
	}

	/**
	 * Adds a new login
	 * @author Nik Sudan
	 * @since 1.0.0
	 * 
	 * @param int $userID
	 * @param string $username
	 * @param string $password
	 * @return int $loginID
	 */
	public static function add_login_details( $userID, $username, $password, $description )
	{
		$password = wp_hash_password( trim( $password ) );
		$logins = Wpal::get_login_details( $userID );
		$index = array_push( $logins, array(
			'user_login' => $username,
			'password' => $password,
			'description' => $description,
		) );
		update_user_meta( $userID, WPAL_USER_META, $logins );
		return $index - 1;
	}

	/**
	 * Removes a login
	 * @author Nik Sudan
	 * @since 1.0.0
	 * 
	 * @param int $userID
	 * @param int $loginID
	 * @return boolean
	 */
	public static function remove_login_details( $userID, $loginID )
	{
		$logins = Wpal::get_login_details( $userID );
		if ( !empty( $logins ) ) {
			if ( isset( $logins[$loginID] ) ) {
				unset( $logins[$loginID] );
				array_values( $logins );
				update_user_meta( $userID, WPAL_USER_META, $logins );
				return true;
			}
		}
		return false;
	}

	/**
	 * Get a user's login details
	 * @author Nik Sudan
	 * @since 1.0.0
	 * 
	 * @param int $userID
	 * @return array
	 */
	public static function get_login_details( $userID )
	{
		$logins = get_user_meta( $userID, WPAL_USER_META, true );
		return empty($logins) ? array() : $logins;
	}

	/**
	 * Get all additional logins
	 * @author Nik Sudan
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public static function get_all_logins()
	{
		$usernames = array();
		foreach ( get_users() as $user ) {
			$userID = $user->data->ID;
			array_push( $usernames, $user->data->user_login );
			$logins = Wpal::get_login_details( $userID );
			foreach ( $logins as $login ) {
				array_push( $usernames, $login['user_login'] );
			}
		}
		return $usernames;
	}

	/**
	 * Checks if a username has been used for an additional login or a user
	 * @author Nik Sudan
	 * @since 1.0.0
	 * 
	 * @param string $username
	 * @return boolean
	 */
	public static function username_exists( $username )
	{
		$username = sanitize_user( $username, true );
		foreach ( get_users() as $userID => $user ) {
			if ( strtoupper( $user->data->user_login ) == strtoupper( $username ) ) {
				return true;
			}
			$logins = Wpal::get_login_details( $userID + 1 );
			foreach ( $logins as $login ) {
				if ( strtoupper( $login['user_login'] ) == strtoupper( $username ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Checks if the current logged in user used an additional login
	 * @author Nik Sudan
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public static function is_sub_user()
	{
		if ( isset( $_SESSION[WPAL_LOGIN_SESSION] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Gets the current logged in user if they used an additional login
	 * @author Nik Sudan
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public static function get_sub_user()
	{
		if ( isset( $_SESSION[WPAL_LOGIN_SESSION] ) ) {
			return $_SESSION[WPAL_LOGIN_SESSION];
		}
		return false;
	}
}

// -------------------------------------------------
// Functionality
// -------------------------------------------------

/**
 * Starts a session
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_session_start()
{
	if ( !session_id() ) {
		session_start();
	}
}
add_action( 'init', 'wpal_session_start' );

/**
 * Admin init for plugin
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_admin_init()
{
	ob_start();
	register_setting( 'wpal', 'wpal_enabled' );
}
add_action( 'admin_init', 'wpal_admin_init' );

/**
 * Enables additional logins to login
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_authenticate_additional_login( $user, $username, $password )
{
	if ( Wpal::is_enabled() && is_wp_error( $user ) ) {
		$loggedUser = Wpal::get_login( $username, $password );
		if ( $loggedUser ) {
			$_SESSION[WPAL_LOGIN_SESSION] = $username;
			return $loggedUser;
		}
	}
	return $user;
}
add_filter( 'authenticate', 'wpal_authenticate_additional_login', 21, 3 );

/**
 * Reset the WPAL status on logout
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_logout()
{
	unset( $_SESSION[WPAL_LOGIN_SESSION] );
}
add_action( 'wp_logout', 'wpal_logout' );

/**
 * Disables access to profile.php and the additional logins management if using an additional login
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_disable_profile_access()
{
	if ( Wpal::is_sub_user() ) {
		remove_menu_page( 'profile.php' );
		remove_submenu_page( 'users.php', 'profile.php' );
		remove_submenu_page( 'users.php', 'wpal' );
		if ( IS_PROFILE_PAGE === true ) {
			wp_die( 'You do not have access to view this page.' );
		}
	}
}
add_action( 'admin_menu', 'wpal_disable_profile_access' );

/**
 * Modify the admin bar
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_modify_admin_bar( $wp_admin_bar )
{
	if ( Wpal::is_sub_user() ) {
		?>
		<script>
			jQuery(document).ready(function($)
			{
				$('#wp-admin-bar-user-info .username').text('<?= $_SESSION[WPAL_LOGIN_SESSION] ?>');
				$('#wp-admin-bar-edit-profile').remove();
				$('#wp-admin-bar-user-info>a').attr('href', '#');
			});
		</script>
		<?php
	}
}
add_action( 'admin_head', 'wpal_modify_admin_bar' );

/**
 * Prevent the use of a additional login username when making a new user
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_prevent_using_additional_login_username( $errors, $sanitized_user_login, $user_email )
{
	if ( Wpal::username_exists( $sanitized_user_login ) ) {
		$errors->add( 'wpal_username_error', '<strong>ERROR</strong>: That username is already in use.' );
		return $errors;
	}
}
add_filter( 'registration_errors', 'wpal_prevent_using_additional_login_username', 10, 3 );

/**
 * Ajax validation for usernames on the admin add new user
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_validate_username_on_admin( $user )
{
	?><script>
		jQuery(document).ready(function($)
		{
			var usernames = [ "<?= implode( '", "', Wpal::get_all_logins() ) ?>" ];
			$('#user_login').change(function() {
				var newUserLogin = $(this).val();
				$('#user_login_not_unique').remove();
				if ( $.inArray( newUserLogin, usernames ) !== -1 ) {
					$(this).parent().parent().addClass('form-invalid');
					$('#createusersub').attr('disabled', true);
					$(this).after('<em id="user_login_not_unique">&nbsp;&nbsp;&nbsp;The username must be unique</em>');
				} else {
					$(this).parent().parent().removeClass('form-invalid');
					$('#createusersub').attr('disabled', false);
				}
			})
		});
	</script><?php
}
add_action( 'user_new_form', 'wpal_validate_username_on_admin', 10, 1 );

// -------------------------------------------------
// Controllers
// -------------------------------------------------

/**
 * Adds an additional column on the users table for additional logins
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_add_additional_logins_column( $columns )
{
	if ( !Wpal::is_sub_user() ) {
		return array_merge( $columns, array( 'additional_logins' => __('Additional Logins') ) );
	} else {
		return $columns;
	}
}
add_filter( 'manage_users_columns' , 'wpal_add_additional_logins_column' );

/**
 * Displays content in the additional logins column
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_show_additional_logins_column_content( $value, $columnName, $userID )
{
	if ( !Wpal::is_sub_user() ) {
		if ( 'additional_logins' != $columnName ) {
			return $value;
		} else {
			$logins = Wpal::get_login_details( $userID );
			return '<strong>' . count( $logins ) .  '</strong> - <small><a href="users.php?page=wpal&user=' . $userID . '">Manage</a></small>';
		}
	} else {
		return $value;
	}
}
add_filter( 'manage_users_custom_column' , 'wpal_show_additional_logins_column_content', 10, 3 );

/**
 * Registers admin pages
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_register_admin()
{
	if ( !Wpal::is_sub_user() ) {
		add_submenu_page( 'users.php', 'Additional Logins', 'Additional Logins', 'administrator', 'wpal', 'wpal_logins_controller' );
		add_submenu_page( 'options-general.php', 'Additional Logins', 'Additional Logins', 'administrator', 'wpal-options', 'wpal_settings_controller' );
	}
}
add_action( 'admin_menu', 'wpal_register_admin' );

/**
 * Registers logins page content
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_logins_controller()
{
	?><div class="wrap"><?php
	include 'views/header.php';

	// If no user is set, show a dashboard to get the user
	if ( !isset($_GET['user']) ) {
		$users = get_users();
		include 'views/choose-user.php';

	// If a user is set, show the actual page
	} else {
		$userID = intval( $_GET['user'] );
		$user = get_userdata( $userID );

		// Add a login
		if ( isset( $_POST['wpal-add-login'] ) ) {

			// Make sure all required fields are added
			if ( $_POST['username'] != '' && $_POST['password'] != '' && $_POST['confirm_password'] != '' ) {

				// Make sure the two passwords match
				if ( $_POST['password'] == $_POST['confirm_password'] ) {

					// Check if the username is unique
					if ( !Wpal::username_exists( $_POST['username'] ) ) {

						// Add login details
						Wpal::add_login_details( $userID, sanitize_user( $_POST['username'], true ), $_POST['password'], $_POST['description'] );
						wp_redirect( site_url() . '/wp-admin/users.php?page=wpal&user=' . $userID . '&success=Successfully+added+new+login' );

					} else {
						$error = 'The username "' . sanitize_user( $_POST['username'], true ) . '" is already used, please choose another';
					}

				} else {
					$error = 'The two passwords didn\'t match up';
				}

			} else {
				$error = 'All required fields were not filled';
			}

			// Redirect if an error
			if ( isset( $error ) ) {
				wp_redirect( site_url() . '/wp-admin/users.php?page=wpal&user=' . $userID . '&action=add-login&error=' . urlencode( $error ) . '&username=' . urlencode( sanitize_user( $_POST['username'] ) ) . '&description=' . urlencode( $_POST['description'] ) );
			}
		}

		// Display the success
		if ( isset( $_GET['success'] ) ) {
			?><div class="updated"><p><?= stripslashes( $_GET['success'] ) ?></p></div><?php
		}

		// Display the error
		if ( isset( $_GET['error'] ) ) {
			?><div class="error"><p><strong>Error:</strong> <?= stripslashes( $_GET['error'] ) ?></p></div><?php
		}

		$logins = Wpal::get_login_details( $userID );

		// If an action is set, show the corresponding action
		if ( isset( $_GET['action'] ) ) {
			switch ( $_GET['action'] ) {

				// Adding an entry
				case 'add-login':
					include 'views/add-login.php';
					break;

				// Deleting an entry
				case 'remove-login':
					$message = Wpal::remove_login_details( $userID, intval( $_GET['login'] ) ) ? 'success=Removed+login' : 'error=Could+not+remove+login';
					wp_redirect( site_url() . '/wp-admin/users.php?page=wpal&user=' . $userID . '&' . $message);
					break;

				// Individual user page
				default:
					include 'views/view-logins.php';
			}

		// Show the individual user page
		} else {
			include 'views/view-logins.php';
		}

	}

	?></div><?php
}

/**
 * Registers settings page content
 * @author Nik Sudan
 * @since 1.0.0
 */
function wpal_settings_controller()
{
	?><div class="wrap"><?php
	include 'views/header.php';
	include 'views/settings.php';
	?></div><?php
}