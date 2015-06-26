# Aihrus Framework

Tested up to: 4.1.0
Stable tag: 1.2.2

A helper library for WordPress plugins by Aihrus and maintained by [Axelerant](//axelerant.com).

## Features

* Content truncation helper methods
* Donation links
* Image source and media attachment helper methods
* Licensing
* Link creation helper methods
* Nonce helper methods
* Notifications
* Validation helper methods

## Usage

* Change to plugin directory that's a Git clone
* Load and link the external library

```
git remote add aihrus https://github.com/michael-cannon/aihrus-framework.git
git subtree add -P include/libraries/aihrus-framework aihrus master
git commit -a -m "Read aihrus framework"
git push origin master
```

* Link plugin to libary

```
require WPS_DIR_LIB . '/aihrus-framework/class-aihrus-common.php';
```

* Extend plugin class to library

```
class Wordpress_Starter extends Aihrus_Common {
```

* Add class static members

```
public static $class = __CLASS__;
public static $notice_key;
```

* Set notices…

```
…
if ( $bad_version )
	add_action( 'admin_notices', 'wps_notice_aihrus' );
…
function wps_notice_aihrus() {
	$help_url  = esc_url( 'https://nodedesk.zendesk.com/hc/en-us/articles/202381391' );
	$help_link = sprintf( __( '<a href="%1$s">Update plugins</a>. <a href="%2$s">More information</a>.' ), self_admin_url( 'update-core.php' ), $help_url );

	$text = sprintf( esc_html__( 'Plugin "%1$s" has been deactivated as it requires a current Aihrus Framework. Once corrected, "%1$s" can be activated. %2$s' ), WPS_NAME, $help_link );

	aihr_notice_error( $text );
}
```

* Update the external library

```
git subtree pull -P include/libraries/aihrus-framework aihrus master
```

* Update the plugin repository

```
git push origin master
```

## Included Libraries

* [Parsedown](http://parsedown.org/)
