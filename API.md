# API Custom Bulk/Quick Edit

The [Custom Bulk/Quick Edit plugin](http://wordpress.org/plugins/custom-bulkquick-edit/) comes with its own set of actions and filters, as described below.

## Actions

None at this time.

## Filters

* `cbqe_quick_scripts_bulk`

	Add bulk edit related JavaScript. Provides parameters `$post_type`, `$column_name`, `$field_name`, `$field_type`, and `$field_name_var`. [example](https://gist.github.com/michael-cannon/6490317)

* `cbqe_quick_scripts_extra`

	Add extra edit related JavaScript. Provides parameters `$post_type`, `$column_name`, `$field_name`, `$field_type`, and `$field_name_var`. [example](https://gist.github.com/michael-cannon/6490325)

* `cbqe_quick_scripts_quick`

	Add quick edit related JavaScript. Provides parameters `$post_type`, `$column_name`, `$field_name`, `$field_type`, and `$field_name_var`. [example](https://gist.github.com/michael-cannon/6490331)

* `cbqe_settings_as_types`

	Modify the field input types offered. [example](https://gist.github.com/michael-cannon/6333075)

* `cbqe_settings_as_category`

	Modify the field category types offered.

* `cbqe_settings_as_taxonomy`

	Modify the field taxonomy types offered.

* `cbqe_settings_display_setting`

	Display the additional field input types offered. [example](https://gist.github.com/michael-cannon/6333132)

* `cbqe_validate_settings`

	Validate settings helper. [example](https://gist.github.com/michael-cannon/5833768)

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
	
* `cbqe_quick_edit_custom_box_field`

	Edit field contents by `field_type`. Has parameters `$field_type`, `$field_name`, and `$options`. [example](https://gist.github.com/michael-cannon/6490341)

* `cbqe_get_post_types_args`
	
	Alter `get_post_types` arguments for loading post types. [example](https://gist.github.com/michael-cannon/6490357)

## Need More?

Further examples and more can be found by reading and searching the [Custom Bulk/Quick Edit Knowledge Base](https://aihrus.zendesk.com/categories/20112546-Custom-Bulk-Quick-Edit) and [source code](https://github.com/michael-cannon/custom-bulkquick-edit).
