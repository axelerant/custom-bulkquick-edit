# API Custom Bulk/Quick Edit

The [Custom Bulk/Quick Edit plugin](http://wordpress.org/plugins/custom-bulkquick-edit/) comes with its own set of actions and filters, as described below.

## Actions

* `cbqe_save_post`

	Custom save post handler. Called at end of `save_post`. Therefore, generally verified to do something if your `$_POST` parameters exist.

## Filters

* `cbqe_settings_config_script`

	Modify settings page JavaScript for hiding not needed configuration inputs.

* `cbqe_post_types_ignore`

	Customize the ignored post_types array. [Example](https://gist.github.com/michael-cannon/6987053)

* `cbqe_quick_scripts_bulk`

	Add bulk edit related JavaScript. Provides parameters `$post_type`, `$column_name`, `$field_name`, `$field_type`, and `$field_name_var`. [example](https://gist.github.com/michael-cannon/6490317)

* `cbqe_quick_scripts_extra`

	Add extra edit related JavaScript. Provides parameters `$post_type`, `$column_name`, `$field_name`, `$field_type`, and `$field_name_var`. [example](https://gist.github.com/michael-cannon/6490325)

* `cbqe_quick_scripts_quick`

	Add quick edit related JavaScript. Provides parameters `$post_type`, `$column_name`, `$field_name`, `$field_type`, and `$field_name_var`. [example](https://gist.github.com/michael-cannon/6490331)

* `cbqe_settings_taxonomies`

	Modify the taxonomies. Useful in instances like Edit Flow's, where taxonomies control the fields.

* `cbqe_settings_fields`

	Modify the fields key names. Useful in instances like Edit Flow's, one name for columns and another for meta data.

* `cbqe_settings_as_types`

	Modify the field input types offered. [example](https://gist.github.com/michael-cannon/6333075)

* `cbqe_settings_save_as_types`

	Modify the field save types offered.

* `cbqe_settings_as_taxonomy`

	Modify the field taxonomy types offered.

* `cbqe_settings_display_setting`

	Display the additional field input types offered. [example](https://gist.github.com/michael-cannon/6333132)

* `cbqe_validate_settings`

	Validate settings helper. [example](https://gist.github.com/michael-cannon/5833768)

* `cbqe_configuration_default`

	Validate settings default helper. [example](https://gist.github.com/michael-cannon/)

* `cbqe_settings_config_desc`

	Customize the configuration text. [example](https://gist.github.com/michael-cannon/)

* `cbqe_settings_save_as_desc`

	Customize the save type text.

* `cbqe_version`

	Version tracking for settings. [example](https://gist.github.com/michael-cannon/5833774)

* `cbqe_sections`

	Alter section options. [example](https://gist.github.com/michael-cannon/5833757)

* `cbqe_settings`

	Alter setting options. [example](https://gist.github.com/michael-cannon/5833757)

* `cbqe_columns`

	Customize post type column headers. [example](https://gist.github.com/michael-cannon/5833693)

* `cbqe_posts_custom_column`

	Customize post type column contents. [example](https://gist.github.com/michael-cannon/5833716)
	
* `cbqe_manage_posts_custom_column_field_type`

	Customize post type column contents by `field_type`. [example](https://gist.github.com/michael-cannon/6333181)
	
* `cbqe_settings_post_type`

	Customize settings variable based upon `post_type`. [example]()
	
* `cbqe_quick_edit_custom_box_column`

	Modify column key names. Useful in instances like Admin Columns's, one name for columns and another for meta data.

* `cbqe_quick_edit_custom_box_field`

	Edit field contents by `field_type`. Has parameters `$field_type`, `$field_name`, `$options`, and `$bulk_mode`. [example](https://gist.github.com/michael-cannon/6490341)

* `cbqe_get_post_types_args`
	
	Alter `get_post_types` arguments for loading post types. [example](https://gist.github.com/michael-cannon/6490357)

* `cbqe_ignore_bulk_edit`

	Ignore editing of these fields for the bulk edit panel.

* `cbqe_ignore_quick_edit`

	Ignore editing of these fields for the quick edit panel.

* `cbqe_ignore_posts_custom_column`

	Ignore applying the hook `manage_${post_type}_posts_columns` for these columns. Useful in plugins like Admin Columns.

* `cbqe_edit_field_type`

	Alter editing type of fields for the bulk and quick edit panels.

* `cbqe_post_save_fields`

	Designate fields to be saved to post entry directly than postmeta.

* `cbqe_post_save_value`

	Compute post value to be saved based upon given field_name.

* `cbqe_field_type_core`
	
	Useful for when the given `field_type` is custom, but the base field type is essentially `checkbox` or the like for RESET and similar operations. Since 1.5.3.

## Need More?

Further examples and more can be found by reading and searching the [Custom Bulk/Quick Edit Knowledge Base](https://axelerant.atlassian.net/wiki/display/WPFAQ) and [source code](https://github.com/michael-cannon/custom-bulkquick-edit).
