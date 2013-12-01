# CHANGELOG Custom Bulk/Quick Edit

## master

## 1.3.2
* Add filter `cbqe_settings_taxonomies`
* BUGFIX No label taxnomies included in settings

## 1.3.1
* BUGFIX tw_get_option function not found

## 1.3.0
* $this to __CLASS__
* Abstract manage_posts_custom_column subroutines for easier reuse
* Add API filter `cbqe_edit_field_type`
* Add API filter `cbqe_ignore_quick_edit`
* Add API filter `cbqe_post_save_fields`
* Add API filter `cbqe_post_save_value`
* Add LICENSE
* Add default get_scripts & get_styles
* Add version_check
* BUGFIX Bulk edit not saving
* BUGFIX Donate notice shows despite `disable_donate` set
* BUGFIX Initial load of radio selection, not selected
* BUGFIX Multiple checkbox selections not recalled
* BUGFIX Multiple select with "space in value" entries aren't being selected
* BUGFIX Select with "space in value" entries aren't being selected
* BUGFIX Settings defaults not loading
* BUGFIX `field_type` is 1
* Enable page bulk/quick editing by default
* Integrate aihrus framework
* Rename action `cbeq_save_post` to `cbqe_save_post`
* Revise headers
* Update TODO
* Update premium listing
* Use abstract Aihrus_Settings

## 1.2.0
* Add API filter `cbqe_settings_config_script`
* Add API filter `cbqe_settings_fields`
* Add id to settings page selects
* Add reset to bulk radio edit
* Add var `bulk_edit_save`
* Change $2 donation request to $5
* Confirm works with [Edit Flow](http://wordpress.org/plugins/edit-flow/)'s checkbox, location, paragraph, and text types
* Edit post title in bulk mode
* Show/hide configuration boxes in settings as needed per as type selected
* Simplify `wp_enqueue_style` handling
* Update .travis for phpmd exclusions

## 1.1.0
* Abstract bulk/quick input operations
* Add API action `cbeq_save_post`
* Add API action `custom_bulkquick_edit_update`
* Add API filter `cbqe_configuration_default`
* Add API filter `cbqe_post_types_ignore`
* Add API filter `cbqe_settings_config_desc`
* Add bulk_edit_custom_box to help with separating editable fields per edit view
* Add disable donation option
* Add trim validator to settings
* Alter scripts_extra location
* Auto-suggest for tag-based taxonomy during bulk and quick editing
* BUGFIX Select No Change option missing
* BUGFIX attributes with hyphens no populate or save in bulk/quick edit
* BUGFIX category taxonomy saving incorrect
* BUGFIX cbeq_save_post bulk edits not saving
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
* Revise readme structure
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
* Update FAQ
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