# Changelog Custom Bulk/Quick Edit plugin for WordPress

## master
* Abstract bulk/quick input operations
* Add API filter `cbqe_settings_config_desc`
* Add API filter `cbqe_validate_default`
* Add bulk_edit_custom_box to help with separating editable fields per edit view
* Add disable donation option
* Add trim validator to settings
* Alter scripts_extra location
* Auto-suggest for tag-based taxonomy during bulk and quick editing
* BUGFIX Select No Change option missing
* BUGFIX attributes with hyphens no populate or save in bulk/quick edit
* BUGFIX category taxonomy saving incorrect
* BUGFIX field name has plugin prefix
* BUGFIX incorrect multiple select HTML
* BUGFIX multiple select quick edit not saving
* BUGFIX show unset checkboxes during quick edit
* BUGFIX show unset taxonomies during bulk edit
* BUGFIX taxonomy CSV entries not treated as individual terms
* Begin `has_config` coding to hide configuration textareas for unset options
* Category bulk editing
* Correct settings page title
* Don't edit common/static fields like cb, id, author, etc.
* Ignore checkbox and ID fields
* Populate quick edit checkbox fields
* Populate quick edit radio fields
* Prevent extraneous options from showing in edit screen columns
* Reduce echo calls
* Refactor bulk/quick custom box operations
* Refactor options labeling
* Remove `cbqe_settings_as_category` filter
* Remove auto-suggest and force reset options - now part of bulk operations by default
* Rename field label with img tag to its alt or title attribute
* Revise premium features list
* Sample configurations provided on initial save
* Save taxonomy data
* Select category values during quick editing
* Show unset category/taxonomy checkbox in bulk edit
* Test checkbox, radio, and select with 0/1 options
* Travis ignore WordPress.WhiteSpace.ControlStructureSpacing - false positives
* Unset checkbox values
* Unset radio values
* Unset select values
* Update API
* Update TODO
* Update readme options
* Use WordPress's own taxonomy handlers for category and tag inputs
* View category and tag relations on edit screen columns
* https jQuery transport

## 1.0.1
* API updates
* Add field_type identifier to edit screen fieldset
* Add filter cbqe_manage_posts_custom_column_field_type
* Add filter cbqe_quick_edit_custom_box_field
* Add filter cbqe_quick_scripts_bulk
* Add filter cbqe_quick_scripts_extra
* Add filter cbqe_quick_scripts_quick
* Add filter cbqe_settings_as_types
* Add filter cbqe_settings_display_setting
* Add filter cbqe_settings_post_type
* Add option to remove current taxonomy entries
* BUGFIX Built-in fields don't have correct field type
* BUGFIX Bulk edit not saving
* BUGFIX Display column data when premium is activated
* BUGFIX Save during Ajax calls
* Convert &$this to $this
* Correct media image path
* Keep Remove taxonomy out of post columns
* Only load plugin if admin or doing Ajax
* Only load settings class admin_init if on edit, plugin, or settings page
* Quote fields in settings
* Shorten filter names
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