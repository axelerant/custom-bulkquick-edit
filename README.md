# Aihrus Framework

A helper library for WordPress plugins by Aihrus.

## Features

* Donation links
* Helper methods
* Licensing
* Notifications

## Usage

* Change to plugin directory that's a Git clone
* Load and link the external library

```
git remote add aihrus https://github.com/michael-cannon/aihrus-framework.git
git fetch aihrus 
git subtree add -P lib/aihrus aihrus master
git commit -a -m "Readd aihrus framework"
git push origin master
```

* Link plugin to libary

```
require_once WPSP_PLUGIN_DIR_LIB . '/aihrus/class-aihrus-common.php';
```

* Extend plugin class to library

```
class Wordpress_Starter extends Aihrus_Common {
```

* Add class static members

```
public static $class;
public static $notice_key;
```

* Set notices…

```
…
if ( $bad_version )
	self::set_notice( 'notice_version' );
…
public static function notice_version( $free_base = null, $free_name = null, $free_slug = null, $free_version = null, $item_name = null ) {
	$free_base    = self::FREE_PLUGIN_BASE;
	$free_name    = 'Testimonials';
	$free_slug    = 'testimonials-widget';
	$free_version = self::FREE_VERSION;
	$item_name    = self::ITEM_NAME;

	parent::notice_version( $free_base, $free_name, $free_slug, $free_version, $item_name );
}
```

* Update the external library

```
git subtree pull -P lib/aihrus aihrus master
```

* Update the plugin repository

```
git push origin master
```
