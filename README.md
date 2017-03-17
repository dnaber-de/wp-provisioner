# WP Provisioner

API to instantiate and manage your WordPress installation structure. *Work in progress*

## Installation

```
$ composer require dnaber/wp-provisioner:dev-master
```

## What & why

Assuming you planning your next web project based on WordPress. The concept of this project requires 15 sites, managed by one WordPress multisite installation. Each site has its own language and a different set of settings and activated plugins and themes. You might want to set up at least a testing server and a production system. Further you have 3 colleagues working with you on this project.

Thus, you set up your local development system, create the 15 sites and make all the settings to the sites. Now you have three options to deploy these state of your system to your fellows or to the testing/production systems:

 * Share database dumps. That might work for the initial setup but what happens, if the concept changes later?
 * Documentation: write down every parameter of the concept and do it manually.
 * Do it programmatic using WP Provisioner

## How it works
WP Provisioner is a standalone PHP commandline script that executes a set of tasks defined in a separate PHP file. Right now it provides two commands to do this.

### provision

```
$ vendor/bin/wp-provisioner provision <VERSION> [--file <PROVISION_FILE>] [--wp-dir <WP_DIR>] [--wp_cli <WP_CLI>]
```

This command executes the `<VERSION>` defined in `<PROVISION_FILE>`. By default, the provision file is the `provision.php` in your current working directory.

Here's an example provision file that defines version `1.0.0` and installs a multisite, sets up two sites and activates two plugins:

```php
<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

/**
 * @param \WpProvision\Api\WpProvisioner $api
 * @param \WpProvision\Api\WpCommandProvider $wp
 * @param \WpProvision\Api\ConsoleOutput $output
 */
return function( Versions $versions, WpCommandProvider $wp, ConsoleOutput $output ) {

    // add a provision routine named '1.0.0'. The VersionList contains all provision routines for all versions
    $versions->addProvision(
        '1.0.0',
        function() use ( $wp, $output ) {

            $admin_email = 'david@wp-provisioner.tld';
            $admin_login = 'david';

            // install a multisite
            $wp->core()->multisiteInstall(
                "http://myproject.net",
                [ 'login' => $admin_login, 'email' => $admin_email ]
            );

            // create some sites
            $site_1_id = $wp->site()->create(
                "http://de.myproject.net/",
                [ 'user_email' => $admin_email ]
            );
            $site_2_id = $wp->site()->create(
                "http://fr.myproject.net/shop/",
                [ 'user_email' => $admin_email ]
            );

            // install some plugins (they usually should be as you're using composer, aren't you?)
            $wp->plugin()->activate(
                [ 'woocommerce', 'akismet' ],
                [ 'site_url' => 'http://fr.myproject.net/shop/' ]
            );
            $wp->plugin()->activate(
                'multilingual-press',
                [ 'network' => TRUE ]
            );

            $output->writeln( "Successfully set up version 1.0.0" );
        }
    );
};
```

### task

```
$ vendor/bin/wp-provisioner task tasks.php [--wp-dir <WP_DIR>] [--wp_cli <WP_CLI>]
```


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

