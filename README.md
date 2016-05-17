# WP Provisioner

API to instantiate and manage your WordPress installation structure. 

## What & why

Assuming you planning your next web project based on WordPress. The concept of this project requires 15 sites, managed by one WordPress multisite installation. Each site has its own language and a different set of settings and activated plugins and themes. You might want to set up at least a testing server and a production system. Further you have 3 colleagues working with you on this project.

Thus, you set up your local development system, create the 15 sites and make all the settings to the sites. Now you have three options to deploy these state of your system to your fellows or to the testing/production systems:

 * Share database dumps. That might work for the initial setup but what happens, if the concept changes later?
 * Documentation: write down every parameter of the concept and do it manually.
 * Do it programmatic using WP Provisioner

## How it works
WP Provisioner is a standalone PHP commandline script, that looks for a `provisioner.php` in your working directory (mostly in your project directory). It uses WP-CLI as API to your WordPress application. The following example shows how to install WP multisite and create some sites and activate some plugins:

```php
<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

/**
 * The variable $api points to an instance of WpProvisioner that gives 
 * you access to the public API
 *
 * @var \WpProvision\Api\WpProvisioner $api
 */

// set the WordPess install directory. This is important for WP-CLI to run properly
$api->setWpDir( __DIR__ . '/wp' );

// add a provision routine named '1.0.0'. The VersionList contains all provision routines for all versions
$api->versionList()->addProvision(
	'1.0.0',
	/**
	 * The command provider gives you access to all Wp Commands like core, site, user or plugin
	 *
	 * @param \WpProvision\Api\WpCommandProvider $provider
	 */
	function( $provider ) {

		$admin_email = 'david@wp-provisioner.tld';
		$admin_login = 'david';

		// install a multisite
		$provider->core()->multisiteInstall(
			"http://myproject.net",
			[ 'login' => $admin_login, 'email' => $admin_email ]
		);

		// create some sites
		$site_1_id = $provider->site()->create(
			"http://de.myproject.net/",
			[ 'user_email' => $admin_email ]
		);
		$site_2_id = $provider->site()->create(
			"http://fr.myproject.net/shop/",
			[ 'user_email' => $admin_email ]
		);

		// install some plugins (they usually should be as you're using composer, aren't you?)
		$provider->plugin()->activate(
			[ 'woocommerce', 'akismet' ],
			[ 'site_url' => 'http://fr.myproject.net/shop/' ]
		);
		$provider->plugin()->activate(
			'multilingual-press',
			[ 'network' => TRUE ]
		);
	}
);
```

The WP directory depends on your local setup. After installing all dependencies you could simply run `$ vendor/bin/wp-provisioner provision 1.0.0` to run the routines you registered for version `1.0.0` in your `provision.php`.

## Goal
The idea of this tool is to automate the process of configuring WordPress as complete as possible to integrate it into already automated deployment processes. However, it is in a early _alpha_ state. Some features are not implemented jet and the API might change slightly.

## API

About the `$graceful` parameter: Every `create()` method has a boolean parameter called `$graceful` (mostly the last one) which make the method act like _create-if-not-exists_, which is always the default behavior. If set ot `FALSE`, the method will throw exceptions, if for example a site is created that already exists.

### Wp\Core

```
bool isInstalled( [ bool $network = FALSE ] )
```
Check if WordPress is installed.

```
bool install( string $url, array $admin [, array $options = [ ] [, bool $graceful = TRUE ] ] )
```
Installs WordPress.

```
bool multisiteConvert( [ array $options = [ ] ] )
```
Converts a single-site to a multisite. **Modifies your `wp-config.php`** (See issue #1)

```
bool multisiteInstall( $url, array $admin [, array $options = [ ] [, bool $graceful = TRUE ] ] );
```
Installs a multisite from scratch. **Modifies your `wp-config.php`** (See issue #1)

### Wp\Plugin

```
bool activate( string $plugin [ , array $options = [ ] ] )
```
Activates a plugin.

```
bool deactivate( string $plugin [ , array $options = [ ] ] )
```
Deactivates a plugin.

```
bool isInstalled( string $plugin [ , array $options = [ ] ] )
```
Checks, if a plugin is «installed». That means, if the plugin files are available for activation.

```
bool isActive( string $plugin [ , array $options = [ ] ] )
```
Checks, if a plugin is activated. Set `$options[ 'network' ]` to `TRUE` to check for network-wide activation.

```
bool uninstall( $plugin, array $options = [ ] )
```
Run uninstall routines for a plugin. This tries to deactivate the plugin before (unless you specify `$options[ 'deactivate' ] = FALSE`). _I suggest to manually deactivate the plugin depending on the plugin activation status._ If you want to also delete the plugin files, pass `$options[ 'delete' ] = TRUE` to the method.

### Wp\Site

```
bool Wp\Site::exists( string $site_url[, int $network_id = 0 ] )
```

Checks if a site URL already exists.

```
int Wp\Site::siteId( string $site_url[, int $network_id = 0 ] )
```

Gets the site ID from the site URL.

```
int Wp\Site::create( string $url [, array $attributes = [] [, $network_id = 0 [, bool $graceful = TRUE ] ] ] )
```
Creates a new site by the complete URL. This works independent of whether it's a sub-directory or sub-domain install but sub-domain install is highly recommended. You can however omit the URL and create a site by slug, of course.

Parameter: 

 * `$url` the site's complete URL, e.g. `http://ch.mysite.org/fr/`
 * `$attributes`
    * `$attributes[ 'user_email' ]` (string, required) the email address of the new sites admin (the user must already exist)
    * `$attributes[ 'private' ]` (boolean) whether the new site should be private
    * `$attribute[ 'title' ]` (string) the title of the new site.
    * `$attribute[ 'slug' ]` (string) when provided, the `$url` parameter is ignored and the site gets created with this slug (which becomes either the sub-domain or the sub-directory)
 * `$network_id` (int) the network ID the new site should created in. Default to `0` which means that the current network (defined by `wp-config.php`) is used
 * `$graceful` (boolean) `TRUE` means »create site if not exists« (default) and return existing ID if exists, `FALSE` will throw exception otherwise 

### Wp\User

```
int Wp\User::userId( string $email_or_login )
```
Get the user ID by email or login.

```
bool Wp\User::exists( string $email_or_login )
```
Checks whether a user exists by a given email or login.


```
int Wp\User::create( string $login, string $email [, array $attributes = [] [, string $site_url = '' [, bool $graceful = TRUE ] ] ] )
```
Creates a new user.

Parameter:
 * `$login` (string, required) the new users login name
 * `$email` (string, required) the new users email address
 * `$attributes`
    * `$attributes[ 'role' ]` (string) the new users role (must exists in the given site)
    * `$attributes[ 'password' ]` (string) password in plain text (should be omitted, when using VCS, use password lost function)
    * `$attributes[ 'first_name' ]` (string)
    * `$attributes[ 'last_name' ]` (string)
    * `$attributes[ 'display_name' ]` (string)
    * `$attributes[ 'send_mail' ]` (bool) send a confirmation mail to the user (default to `FALSE`)
    * `$attibutes[ 'registered_at' ]` (DateTimeInterface) the time the user was registered 
 * `$site_url` (string) The sites URL, the user should registered for. (Default to the networks main site)
 * `$graceful` (bool) `TRUE` means »create user if not exists« (default) and return existing ID if exists, `FALSE` will throw exception otherwise 

