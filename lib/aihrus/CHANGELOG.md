# CHANGELOG Aihrus Framework

## 1.0.1-alpha
* Add strip_protocol
* Add valid_hash check
* Move relicensing to plugin level

## 1.0.0
* Enable aihr_check_aihrus_framework

## master
* Abstract notice helper methods as functions
* Add TODO
* Add `add_media` post attachment helper
* Add `clean_string` trim, strip_shortcodes, and strip_tags a string
* Add `create_link( $link )`
* Add `create_nonce( $action )`
* Add `file_get_contents_curl`
* Add `get_image_src` 
* Add `is_true`, terms, url validate cases
* Add `truncate` 
* Add `verify_nonce( $nonce, $action )`
* Add abstract class Aihrus_Settings
* Add abstract class Aihrus_Widget
* Add aihr_notice_license
* Add name filed to aihr_check_php
* Add requirements helper
* Add shortcode id helpers
* Allow reset_defaults in widget
* BUGFIX Method version not static
* BUGFIX Widget title is same as link
* CLOSES #2 Disable purchase premium links if premium is active
* Check for PHP 5.3
* Convert TODO to https://github.com/michael-cannon/aihrus-framework/issues
* Display option values as is
* Encase pronouns in double-quotes
* RESOLVES #1 PHP Version checking
* RESOLVES #3 Add WordPress version check
* Remove unused methods
* Rename $options[$id] to $field_value
* Rename no_code to show_code
* Revise create_link parameters
* Settings link with null post_type
* Switch from require_once to require file inclusion
* Use `aihr_` as function prefix
* Widget title uses create_link
* static::ITEM_NAME to static::NAME

## 0.0.0
* Initial code release 