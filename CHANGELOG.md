# Changelog Custom Bulk/Quick Edit plugin for WordPress

## master
* Add filter custom_bulkquick_edit_manage_posts_custom_column_field_type
* Add filter custom_bulkquick_edit_quick_edit_custom_box_field
* Add filter custom_bulkquick_edit_settings_as_types
* Add filter custom_bulkquick_edit_settings_display_setting
* Add filter custom_bulkquick_edit_settings_post_type
* Add option to remove current taxonomy entries
* BUGFIX Built-in fields don't have correct field type
* Convert &$this to $this
* Correct media image path
* Keep Remove taxonomy out of post columns
* Quote fields in settings
* Suppress warning PHPMD.UnusedFormalParameter
* Update readme for usability

## 1.0.0
* Account for deprecated manage_edit-${post_type}_columns
* Add StillMaintained.com notice
* BUGFIX Call to method on non-object
* Capture non-CBQE `manage_ . $post_type . _posts_custom_column` content
* Correct links
* Create example configuration
* Display chosen checkbox, radio, or select options on bulk and quick edit screens
* Display chosen checkbox, radio, or select values on edit page
* Enable radio and select pre-setting for Quick Edit
* Enable radio and select setting for Bulk Edit
* Handle checkbox presetting for quick edit
* Handle checkbox saving for bulk edit
* Highlight video introduction
* Initialize loading later
* Link to example
* Prevent duplicate echo of content in manage_custom_column (done in 0.0.3)
* Redo screenshots
* Remove unused styles/scripts methods
* Remove usused CSS file
* Restrict settings page to admins only
* Simplify JavaScript script bulk/quick
* TEST radio/checkbox implementation (need to add custom config to know key/values)
* Update about image path
* Update travis

## 0.0.3
* Revise donation text
* Update description
* Strip slashes incoming data
* Correct data grab for Quick Edit
* Use after_setup_theme than plugins_loaded
* BUGFIX post excerpt support detection
* BUGFIX post excerpt column add
* Add video introduction

## 0.0.2
* Add media
* Add screenshots
* Bulk Edit working
* Call manage_posts_custom_column and manage_post_posts_columns on demand
* Correct (de)activate and uninstall routines
* Correct settings page URL
* Disable General section for now
* Disable Travis check Squiz.PHP.CommentedOutCode.Found
* Display correct field label in editor
* Don't echo and set columns if not enabled
* Don't load JavaScript if not needed
* Don't save blanks on bulk edit save
* Dynamically adapt for post_excerpt
* Enable XSS EscapeOutput checking
* Initialize localization
* Link to premium on plugins page
* Move plugin load to plugins_loaded
* PHP 5.3 WordPress master testing
* Pull fields and labels from various custom field helpers
* Quick Edit working
* Reduce setting options to core
* Remove class from settings page link
* Remove unused code
* Rename filters and methods to fit plugin naming
* Setup bulk/quick edit field as input and textarea
* Static cache enabled fields
* Test grabbing post types
* Update API
* Update POT

## 0.0.1
* Initial code release 