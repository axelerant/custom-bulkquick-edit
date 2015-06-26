# CHANGELOG - Aihrus Framework

## master

## 1.2.2
* Add Inside Axelerant link
* Store branding updates
* Update about Axelerant verbiage

## 1.2.1
* RESOLVE PHP Fatal error: Access to undeclared static property: CLASS::$scripts_called

## 1.2.0
* Add Axelerant careers link
* Update about Axelerant data
* Update Axelerant support URLs
* Update branding from Aihrus to Axelerant

## 1.1.7
* RESOLVE michael-cannon/testimonials-widget#156 License not saving
* Update copyright year

## 1.1.6
* Denote included libraries
* RESOLVE michael-cannon/testimonials-widget#162 Error: This is not a valid feed template with WordPress SEO sitemap
* Use https for Aihrus store

## 1.1.5
* Abstract do_validate_settings for easier reuse
* Add 'content' option to settings display
* Add CPT date archives helper
* Add markdown2html helper
* Add suggest ability to text filed
* Add widget's form_parts method
* Auto-detect file_get_contents or curl availability
* Coding standards update
* Don't shortcode attribute for reset options
* Move get_suggest to widget abstract class from interface
* On aihr_notice_error usage, push to server error log
* Remove interface class usage
* RESOLVE #4 Enable std default for metabox class
* RESOLVE #5 Check for file_get_contents and curl_init before file grabbing
* RESOLVE Add widget option defaults
* RESOLVE form_parts not allowing no arguments
* RESOLVE michael-cannon/testimonials-widget#6 On widget options, switch labels expand/collapse when using them
* RESOLVE michael-cannon/testimonials-widget#78 Show revert link in deactivation admin notice
* RESOLVE Move form_instance to abstract class Aihrus_Widget
* RESOLVE Move get_suggest to abstract class Aihrus_Widget
* RESOLVE Remove tw- reference
* RESOLVE Space missing before Collapse
* RESOLVE Use widget args than globals
* Revise widget class for easier reuse
* Update licensing GPL 2.0
* Use parsedown for Markdown to HTML conversion

## 1.1.4
* Carry over core baseline methods

## 1.1.3
* Add check_user_role method
* RESOLVE michael-cannon/testimonials-widget#85 New testimonial rating field is blank
* RESOLVE Undefined index notice

## 1.1.2
* Add licensing status notices
* Code formatting
* Don't remove license from settings
* RESOLVE Undefined index
* Restore default wp_remote_get timeout
* Return if library class exists already
* Update settings page detection

## 1.1.1
* Remove unused `widget_options`
* RESOLVE michael-cannon/testimonials-widget#65 Widget blank options aren't saving correctly

## 1.1.0
* Abstract deactivate_plugins actual to aihr_deactivate_plugin_do
* Add class redrokk_metabox_class
* Add class wp_custom_post_status
* Add date, time, and datetime types to redrokk_metabox_class
* Add rich_editor type
* Add validate_email
* Consolidate deactivate_plugins activity into aihr_deactivate_plugin
* Don't esc_attr rich editor value
* Limit wp_custom_post_status to post type
* Move classes/interfaces to includes
* Register `jquery-style`
* Rename requirements.php to aihrus-framework.php
* RESOLVE michael-cannon/testimonials-widget#63 Unable to activate premium license
* RESOLVE michael-cannon/testimonials-widget#65 Widget blank options aren't saving correctly
* RESOLVE michael-cannon/testimonials-widget#76 Undefined index: hide_image_single
* RESOLVE redrokk_metabox_class prepare warning
* Revise get_styles handling
* Set AIHR_DIR* helpers
* Set defaults for validators
* Set premium license key for 2 years though it expires at 1
* Update copyright year

## 1.0.3
* RESOLVE Fatal error: Call to undefined function add_settings_error() in class-aihrus-settings.php on line 559
* RESOLVE Featured image via Gravatar not saving correctly
* Revise data deletion description - Thank you Mark

## 1.0.2
* Add phpunit.xml
* Add `slash_sanitize_title` verification helper
* Coding standards
* Display "Settings saved."
* Move ci to tests
* Remove Travis configuration
* RESOLVE michael-cannon/testimonials-widget#48 Activation on WP 3.6 not working
* RESOLVE michael-cannon/wootheme-testimonials-to-testimonials#2 No settings saved notice upon saving settings
* Update usage instructions
* Use $plugin_assets than $plugin_path

## 1.0.1
* Add strip_protocol
* Add valid_hash check
* Move relicensing to plugin level

## 1.0.0
* Enable aihr_check_aihrus_framework

## 0.0.0
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