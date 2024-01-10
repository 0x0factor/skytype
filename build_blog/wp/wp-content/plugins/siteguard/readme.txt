=== SiteGuard WP Plugin ===
Contributors: jp-secure
Donate link: -
Tags: security, waf, brute force, password list, login lock, login alert, captcha, pingback, fail once
Requires at least: 3.9
Tested up to: 4.7
Stable tag: 1.3.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SiteGurad WP Plugin is the plugin specialized for the protection against the attack to the management page and login.

== Description ==

You can find docs, FAQ and more detailed information on [English Page](http://www.jp-secure.com/cont/products/siteguard_wp_plugin/index_en.html) [Japanese Page](http://www.jp-secure.com/cont/products/siteguard_wp_plugin/index.html).

Simply install the SiteGuard WP Plugin, WordPress security is improved.
This plugin is a security plugin that specializes in the login attack of brute force, such as protection and management capabilities.

Notes

* It does not support the multisite function of WordPress.
* It only supports Apache 1.3, 2.x for Web servers.
* To use the CAPTCHA function, the expansion library “mbstring” and “gd” should be installed on php.
* To use the management page filter function and login page change function, “mod_rewrite” should be loaded on Apache.
* To use the WAF Tuning Support, WAF ( SiteGuard Lite ) should be installed on Apache.

There are the following functions.

* Admin Page IP Filter

It is the function for the protection against the attack to the management page (under wp-admin.)
To the access from the connection source IP address which does not login to the management page, 404 (Not Found) is returned.
At the login, the connection source IP address is recorded and the access to that page is allowed.
The connection source IP address which does not login for more than 24 hours is sequentially deleted.
The URL (under wp-admin) where this function is excluded can be specified.

* Rename Login

It is the function to decrease the vulnerability against an illegal login attempt attack such as a brute force attack or a password list attack.
The login page name (wp-login.php) is changed. The initial value is “login_<5 random digits>” but it can be changed to a favorite name.

* CAPTCHA

It is the function to decrease the vulnerability against an illegal login attempt attack such as a brute force attack or a password list attack,
or to receive less comment spam. For the character of CAPTCHA, hiragana and alphanumeric characters can be selected.

* Login Lock

It is the function to decrease the vulnerability against an illegal login attempt attack such as a brute force attack or a password list attack.
Especially, it is the function to prevent an automated attack. The connection source IP address the number of login failure of which reaches
the specified number within the specified period is blocked for the specified time.
Each user account is not locked.

* Login Alert

It is the function to make it easier to notice unauthorized login. E-mail will be sent to a login user when logged in.
If you receive an e-mail to there is no logged-in idea, please suspect unauthorized login.

* Fail Once

It is the function to decrease the vulnerability against a password list attack. Even is the login input is correct, the first login must fail.
After 5 seconds and later within 60 seconds, another correct login input make login succeed. At the first login failure, the following error message is displayed.

* Disable Pingback

The pingback function is disabled and its abuse is prevented.

* Updates Notify

Basic of security is that always you use the latest version. If WordPress core, plugins, and themes updates are needed , sends email to notify administrators.

* WAF Tuning Support

It is the function to create the rule to avoid the false detection in WordPress (including 403 error occurrence with normal access,)
if WAF ( SiteGuard Lite ) by JP-Secure is installed on a Web server. WAF prevents the attack from the outside against the Web server,
but for some WordPress or plugin functions, WAF may detect the attack which is actually not attack and block the function.
By creating the WAF exclude rule, the WAF protection function can be activated while the false detection for the specified function is prevented.

= Translate =

If you have created your own language pack, or have an update of an existing one, you can send [gettext PO and MO files](http://codex.wordpress.org/Translating_WordPress) to sgdev@jp-secure.com so that We can bundle it into SiteGuard WP Plugin. You can download the latest [POT file](http://plugins.svn.wordpress.org/siteguard/trunk/languages/siteguard.pot), and [PO files in each language](http://plugins.svn.wordpress.org/siteguard/branches/languages/).

== Installation ==

* WordPress dashboard
1. Please search and install "SiteGuard WP Plugin" from 'Plugins' menu of WordPress dashboard
2. Activate the plugin through the 'Plugins' menu of WordPress dashboard

* WordPress.org plugin directory
1. Please search and download "SiteGuard WP Plugin"
2. Please upload and install a ZIP file that you downloaded through 'Plugins' menu of WordPress dashboard
3. Activate the plugin through the 'Plugins' menu of WordPress dashboard

== Screenshots ==

* SiteGuard WP Plugin - Dashboard -

== Frequently Asked Questions ==

[English Page](http://www.jp-secure.com/cont/products/siteguard_wp_plugin/faq_en.html)
[Japanese Page](http://www.jp-secure.com/cont/products/siteguard_wp_plugin/faq.html)

== Changelog ==
= 1.3.4 =
* Fix an issue where CAPTCHA might fail in 1.3.3
= 1.3.3 =
* Fix bug that fatal error occurs when fails to send mail
* Inprove the security of the CAPTCHA function
* Disabling the Rename Login function when qTranslate X plugin is enabled in order to avoid conflicts
= 1.3.2 =
* Fix bug that fatal error occurs when fails to send mail
= 1.3.1 =
* Fix conflicts with other plugins in a session related
= 1.3.0 =
* Add the "Disable XMLRPC" feature
* In the Login History, add display the login type that indicates whether via login page or xmlrpc 
* Fix that the Fail Once error message to be not the same as the failure
* Fix that the permission of .htaccess to change from 0644 to 0604
* Delete the mistaken characters of CAPTCHA
= 1.2.5 =
* In the Admin Page IP Filter function, fix bug that can be accessed from the IP address that failed to login to the management page
* In the Rename Login function, correct the problem that is redirected to the renamed login page from the /wp-signup.php
= 1.2.4 =
* Fix bug that there is a case which can acccess management pages from non login client
* Disabling the several functions when there is no .htaccess write permission
= 1.2.3 =
* Fix bug that you can not reply comments from the dashboard, if the CAPTCHA is enabled
* Fix bug that the login page is displayed in '/wp-login' even if the Rename Login is enabled
= 1.2.2 =
* Fix bug that XML-RPC access which doesn't need login is recorded as the nameless login history
* Disabling the all functions when installed in multisite environment
* Disabling the several functions when settings of .htaccess was eliminated
= 1.2.1 =
* Supported with WP 4.2
= 1.2.0 =
* Add the "Updates Notify" feature
* Fix bug that login via XML-RPC to fail, if the CAPTCHA is enabled
* Fix bug that sometimes can't login when you enable the Fail once
= 1.1.2 =
* Supported with WP 4.1
* Disabling the Admin IP Filter function by default
= 1.1.1 =
* Fix bug that can not save "Login Alert" settings
* Add the "Login Alert" notification variables, IP Address, User-Agent and Referer
= 1.1.0 =
* Add the "Login Alert" feature
* Add the function of inform the new Login page URL by e-mail
* Fix bug that work "Fail Once" even when the password is a mistake
* Fix bug that even if the "Rename Login" has been enabled, and have specified a permanent link to the non-standard, jump to the new login page in /login
= 1.0.6 =
* Supported with Apache 1.3
* Fix garbling of CAPTCHA by environment
* Fix input check of Rename login path
* Fix some other bugs
= 1.0.5 =
* Add display a warning about changing the login page URL, when activate the plugin
= 1.0.4 =
* Fix bug that fails to update .htaccess, if there is no WordPress settings in .htaccess
= 1.0.3 =
* Fix a problem that "Rename Login" does not work, if you change Permalink settings
* Fix the collision of class name of Really Simple CAPTCHA
= 1.0.2 =
* Fix a minor html escape leakage
* Reduced the problem of affinity with other plugin [WordPress HTTPS (SSL)]
= 1.0.1 =
* Supported with WP 4.0
= 1.0.0 =
* First release
