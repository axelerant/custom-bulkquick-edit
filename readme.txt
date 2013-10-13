=== Custom Bulk/Quick Edit ===

Contributors: comprock
Donate link: http://aihr.us/about-aihrus/donate/
Tags: custom, bulk edit, quick edit, custom post types, woocommerce
Requires at least: 3.4
Tested up to: 3.6.0
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Bulk/Quick Edit plugin allows you to easily add previously defined custom fields to the edit screen bulk and quick edit panels.


== Description ==

Custom Bulk/Quick Edit plugin allows you to easily add previously defined custom fields to the edit screen bulk and quick edit panels.

[youtube http://www.youtube.com/watch?v=wd6munNz0gI]
**[Video introduction](http://youtu.be/UXvzdlvIPtk)**

Custom Bulk/Quick Edit automatically detects custom fields that use the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) filter to display additional columns on the edit screen. Therefore, unless it's already configured, your theme's `functions.php` file will have to modified to add custom field columns. See "[How do I add custom columns to my edit page?](https://aihrus.zendesk.com/entries/24800411-How-do-I-add-custom-columns-to-my-edit-page-)" for help.

This plugin ties into the [bulk_edit_custom_box](http://codex.wordpress.org/Plugin_API/Action_Reference/bulk_edit_custom_box) and [quick_edit_custom_box](http://codex.wordpress.org/Plugin_API/Action_Reference/quick_edit_custom_box) actions.

To use this Custom Bulk/Quick Edit plugin with custom post types, please purchase [Custom Bulk/Quick Edit Premium](http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/).

= Help Me, Help You =

Do [let me know](http://wordpress.org/support/plugin/custom-bulkquick-edit) how well you're able to use this plugin or not. 

This plugin grew out of the frustration of having to custom write this code for every client. It works best when the custom post types have already added columns to the edit screen via the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) filter.

[Example](https://aihrus.zendesk.com/entries/24800411-How-do-I-add-custom-columns-).

= Limitations =

Unless the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) is already configured, your theme's `functions.php` file will have to modified to add custom field columns. See "[How do I add custom columns to my edit page?](https://aihrus.zendesk.com/entries/24800411-How-do-I-add-custom-columns-to-my-edit-page-)" for help.

= Primary Features =

* API of actions and filters
* Auto detects most post custom fields
* Auto-suggest for bulk and quick edit taxonomy entries
* Easily remove `category` and `taxonomy` relations
* Easily unset checkbox, radio, and select values
* Edit excerpts
* Performance minded – Plugin automatically loads when needed
* Sample configurations set if none given
* Setting options export/import
* Settings screen
* Supports category and tag taxonomies
* Use's WordPress's own taxonomy handlers for category and tag inputs

= Custom Bulk/Quick Edit Premium Plugin Features =

Custom Bulk/Quick Edit Premium plugin extends [Custom Bulk/Quick Edit](http://wordpress.org/extend/plugins/custom-bulkquick-edit/) with custom post types handling and other helpful features.

* Disable donate references
* Flexible API
* Works with Custom Post Types
* Works with [WooCommerce product attributes](http://www.woothemes.com/woocommerce/)
* `date` input type with date picker

[Buy Custom Bulk/Quick Edit Premium](http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/) plugin for WordPress.

= Settings Options =

**Post**

* Enable "Excerpt"? – Enable editing of post_type' excerpt.
* Edit "TBD" taxonomy? – Force making TBD an editable taxonomy field like checked categories or free-text tags.
* Remove "taxonomy" Relations? – During bulk editing, easily remove all of the current taxonomy' relationships. You'll need to edit the Testimonials again to set new taxonomy relations.
* Enable "Custom Field"? - As checkbox, radio, select, text input, or textarea
* "Custom Field" Configuration - This configuration section is only for use with checkbox, radio, and select modes. Please separate options using newlines. Further, you may create options as "the-key|Pretty Value" pairs.
	* Example configuration
`1
Two
3|Three
four|Four
five-five|55`

**Compatibility & Reset**

* Export Settings – These are your current settings in a serialized format. Copy the contents to make a backup of your settings.
* Import Settings – Paste new serialized settings here to overwrite your current configuration.
* Remove Plugin Data on Deletion? - Delete all Custom Bulk/Quick Edit data and options from database on plugin deletion
* Reset to Defaults? – Check this box to reset options to their defaults

= API =

* Read the [Custom Bulk/Quick Edit API](https://github.com/michael-cannon/custom-bulkquick-edit/blob/master/API.md).

= Languages =

You can translate this plugin into your own language if it's not done so already. The localization file `custom-bulkquick-edit.pot` can be found in the `languages` folder of this plugin. After translation, please [send the localized file](http://aihr.us/contact-aihrus/) to the plugin author.

See the FAQ for further localization tips.

= Support =

Please visit the [Custom Bulk/Quick Edit Knowledge Base](https://aihrus.zendesk.com/categories/20112546-Custom-Bulk-Quick-Edit) for frequently asked questions, offering ideas, or getting support.

If you want to contribute and I hope you do, visit the [Custom Bulk/Quick Edit Github repository](https://github.com/michael-cannon/custom-bulkquick-edit).

= Thank You =
A big, special thank you to [Joe Weber](https://plus.google.com/100063271269277312276/posts) of [12 Star Creative](http://www.12starcreative.com/) for creating the Custom Bulk/Quick Edit banner.

Kudos to [Alex Stone](http://eoionline.org) for documentation revisions.


== Installation ==

1. Via WordPress Admin > Plugins > Add New, Upload the `custom-bulkquick-edit.zip` file
1. Alternately, via FTP, upload `custom-bulkquick-edit` directory to the `/wp-content/plugins/` directory
1. Activate the 'Custom Bulk/Quick Edit' plugin after uploading or through WordPress Admin > Plugins


== Frequently Asked Questions ==

Please visit the [Custom Bulk/Quick Edit Knowledge Base](https://aihrus.zendesk.com/categories/20112546-Custom-Bulk-Quick-Edit) for frequently asked questions, offering ideas, or getting support.


== Screenshots ==

1. Custom Bulk/Quick Edit Settings panel
2. TwentyTwelve theme with Posts Post Excerpts enabled
3. Posts Quick Edit with excerpts
4. Posts Bulk Edit with excerpts

== Changelog ==

See [Changelog](https://github.com/michael-cannon/custom-bulk-quick-edit/blob/master/CHANGELOG.md)


== Upgrade Notice ==

= 1.1.0 =

* Please review your settings as some option keys have changed. There's no auto-upgradeat this time.


== Beta Testers Needed ==

I really want Custom Bulk/Quick Edit and Custom Bulk/Quick Edit Premium to be the best WordPress plugins of their type. However, it's beyond me to do it alone.

I need beta testers to help with ensuring pending releases of Custom Bulk/Quick Edit and Custom Bulk/Quick Edit Premium are solid. This would benefit us all by helping reduce the number of releases and raise code quality.

[Please contact me directly](http://aihr.us/contact-aihrus/).

Beta testers benefit directly with latest versions, a free 1-site license for Custom Bulk/Quick Edit Premium, and personalized support assistance.

== TODO ==

See [TODO](https://github.com/michael-cannon/custom-bulkquick-edit/blob/master/TODO.md)
