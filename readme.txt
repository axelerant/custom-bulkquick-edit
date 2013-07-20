=== Custom Bulk/Quick Edit ===

Contributors: comprock
Donate link: http://aihr.us/about-aihrus/donate/
Tags: custom, bulk edit, quick edit, custom post types
Requires at least: 3.4
Tested up to: 3.6.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Bulk/Quick Edit plugin allows you to easily add previously defined custom fields to the edit screen bulk and quick edit panels.


== Description ==

Custom Bulk/Quick Edit plugin allows you to easily add previously defined custom fields to the edit screen bulk and quick edit panels.

Custom Bulk/Quick Edit automatically detects custom fields that use the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) filter to display additional columns on the edit screen.

It ties into the [bulk_edit_custom_box](http://codex.wordpress.org/Plugin_API/Action_Reference/bulk_edit_custom_box) and [quick_edit_custom_box](http://codex.wordpress.org/Plugin_API/Action_Reference/quick_edit_custom_box) actions.

[youtube http://www.youtube.com/watch?v=wd6munNz0gI]
**[Video introduction](http://youtu.be/UXvzdlvIPtk)**

To use this Custom Bulk/Quick Edit plugin with custom post types, please purchase [Custom Bulk/Quick Edit Premium](http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/).

= Help Me, Help You =

Do [let me know](http://wordpress.org/support/plugin/custom-bulkquick-edit) how well you're able to use this plugin or not. 

This plugin grew out of the frustration of having to custom write this code for every client. It works best when the custom post types have already added columns to the edit screen via the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) filter.

[Example](https://aihrus.zendesk.com/entries/24800411-How-do-I-add-custom-columns-).

= Limitations =

Currently, only text input and textarea fields are supported.

= Primary Features =

* API of actions and filters
* Auto detects most post custom fields
* Settings export/import
* Settings screen

= Settings Options =

**Post**

* Enable excerpts?
* Enable "Custom Field"? - As checkbox, radio, select, text input,r textarea
* "Custom Field" Configuration - This configuration section is only for use with checkbox, radio, and select modes. Please seperate options using newlines. Further, you may create options as "the-key|Pretty Value" pairs.
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

= 0.0.1 =

* Initial release


== Beta Testers Needed ==

I really want Custom Bulk/Quick Edit and Custom Bulk/Quick Edit Premium to be the best WordPress plugins of their type. However, it's beyond me to do it alone.

I need beta testers to help with ensuring pending releases of Custom Bulk/Quick Edit and Custom Bulk/Quick Edit Premium are solid. This would benefit us all by helping reduce the number of releases and raise code quality.

[Please contact me directly](http://aihr.us/contact-aihrus/).

Beta testers benefit directly with latest versions, a free 1-site license for Custom Bulk/Quick Edit Premium, and personalized support assistance.

== TODO ==

See [TODO](https://github.com/michael-cannon/custom-bulkquick-edit/blob/master/TODO.md)
