=== WordPress Additional Logins ===
Contributors: niksudan
Tags: users, register, login, multiple, additional, details, authentication, group
License: MIT
License URI: https://raw.githubusercontent.com/NikSudan/wp-additional-logins/master/LICENSE

Adds additional login details to users

== Description ==
This plugin allows administrators to create additional login details (username and password) for a registered user account. It restricts the additional logins from accessing their linked user's profile page, as well as accessing the additional logins panel themselves.

The additional logins can be turned off at any time, preventing users from using the credentials to sign in. Descriptions can be given to logins that you create. Additional login usernames are treated like normal WordPress usernames, and cannot match an existing login. Likewise, new logins cannot have the same username as an additional login.

Useful for when wanting to have a few pairs of login details without giving away the main account's password, as well as multiple team members, or just for convenience.

View it on GitHub: https://github.com/NikSudan/wp-additional-logins

== Installation ==
= Setup =

Upload the plugin folder (`wp-additional-logins`) to your plugins directory.

= Changing Options =

You can turn off the additional login feature at any time by visiting the plugin's settings (under the settings tab) and turning off the plugin functionality via the dropdown.

= Adding Logins =

You can add logins through two ways:

- Selecting the 'Manage' option next to the respective user in the users archive
- Manually choosing from a users dropdown from Users > Additional Logins

Here you can view all credentials assigned to an account. You can add as many as you like, but they must include a unique username, a password, and an optional description. Logins can be removed at any time. Only 'pure' administrators can view the additional logins management page.

== Frequently Asked Questions ==
= I can't log in with an additional login =
Make sure that the plugin is enabled in the settings

= I can't add an additional login =
Respective errors should appear above the form.

= Can additional logins change the root user's password? =
No, the profile is off limits to any user using additional logins

= I've managed to add a user with the same username as another user =
You've broken the system! Let me know how you've done that exactly

= Can users sign up with valid usernames by themselves? =
Yes, it shouldn't let new users pick taken additional logins

= Will this work on front-end forms? =
It should do, as it hooks into WordPress's core login mechanics

= Where are the additional logins stored? =
They are stored as an array in user meta

= If an administrator makes an additional login, can they manage other logins? =
No, only 'pure' administrators have access to manage this plugin

== Changelog ==
= 1.0.0 =
* Initial release