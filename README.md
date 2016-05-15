# WP Provisioner

API to instantiate and manage your WordPress installation structure. 

## API

About the `$graceful` parameter: Every `create()` method has a boolean parameter called `$graceful` (mostly the last one) which make the method act like _create-if-not-exists_, which is always the default behavior. If set ot `FALSE`, the method will throw exceptions, if for example a site is created that already exists.

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

