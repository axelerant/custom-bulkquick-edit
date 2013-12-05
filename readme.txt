=== Custom Bulk/Quick Edit ===

Contributors: comprock
Donate link: http://aihr.us/about-aihrus/donate/
Tags: custom, bulk edit, quick edit, custom post types, woocommerce
Requires at least: 3.6
Tested up to: 3.8.0
Stable tag: 1.3.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Bulk/Quick Edit plugin allows you to easily add previously defined custom fields to the edit screen bulk and quick edit panels.


== Description ==

Through Custom Bulk/Quick Edit, you have the option to edit post meta via text, checkbox, radio, select, and textarea inputs within Bulk Edit and Quick Edit screens. Further, you can enable editing of category and tag taxonomies that don't show up already. Next, taxnomony, checkbox, radio, and select fields have an option to be reset, as in remove current options during Bulk Editing. This is very helpful when you want to mass reset or remove information.

To use this Custom Bulk/Quick Edit plugin with custom post types, please purchase [Custom Bulk/Quick Edit Premium](http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/). Read more of the premium features below.

[youtube http://www.youtube.com/watch?v=wd6munNz0gI]
**[Video introduction](http://youtu.be/UXvzdlvIPtk)**

Custom Bulk/Quick Edit automatically detects custom fields that use the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) filter to display additional columns on the edit screen. Therefore, unless it's already configured, your theme's `functions.php` file will have to modified to add custom field columns. See "[How do I add custom columns to my edit page?](https://aihrus.zendesk.com/entries/24800411-How-do-I-add-custom-columns-to-my-edit-page-)" for help.

This plugin ties into the [bulk_edit_custom_box](http://codex.wordpress.org/Plugin_API/Action_Reference/bulk_edit_custom_box) and [quick_edit_custom_box](http://codex.wordpress.org/Plugin_API/Action_Reference/quick_edit_custom_box) actions.

**Version 1.3.0+ requires PHP 5.3+** [notice](https://aihrus.zendesk.com/entries/30678006-Testimonials-2-16-0-Requires-PHP-5-3-)

= Primary Features =

* API of actions and filters
* Auto detects most post custom fields
* Auto-suggest for bulk and quick edit taxonomy entries
* Easily remove or replace `category` and `taxonomy` relations
* Edit excerpts and titles
* Performance minded – Plugin automatically loads when needed
* Prevents editing of common and static fields like cb, id, author, etc.
* Sample configurations provided as needed
* Save post handler can be customized for your needs.
* Setting options export/import
* Settings screen
* Supports WordPress's own taxonomy handlers for category and tag relations
* Unset checkbox, radio, and select values during bulk edit
* View category and tag relations on edit screen columns
* Works with [Edit Flow](http://wordpress.org/plugins/edit-flow/)'s checkbox, location, paragraph, and text types
* Works with posts and pages

= Custom Bulk/Quick Edit Premium =

Custom Bulk/Quick Edit Premium adds onto the best WordPress bulk edit plugin there is, [Custom Bulk/Quick Edit](http://wordpress.org/extend/plugins/custom-bulkquick-edit/). Custom Bulk/Quick Edit Premium supports [custom post types and WooCommerce](https://aihr.us/custom-bulkquick-edit-premium/). Plus, it offers additional inputs options like date and multiple selects for use during bulk/quick edit operations.

= Primary Premium Features =

* Adds float, integer, and user inputs
* Date input with date picker
* Disable donate references
* Flexible API
* Multiple select selector
* Works with Custom Post Types
* Works with [Edit Flow](http://wordpress.org/plugins/edit-flow/) date, number, and user types
* Works with [WooCommerce product attributes](http://www.woothemes.com/woocommerce/)

= Add Ons =
* [WordPress SEO](https://aihr.us/products/wordpress-seo-custom-bulkquick-edit-premium/) - Modify WordPress SEO options via bulk and quick edit panels

[Buy Custom Bulk/Quick Edit Premium](http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/) plugin for WordPress.

= Settings Options =

**Post**

* Enable "Title"? – Enable editing of post_type' title.
* Enable "Excerpt"? – Enable editing of post_type' excerpt.
* Edit "TBD" taxonomy? – Force making TBD an editable taxonomy field like checked categories or free-text tags.
	* No
	* No, but enable column view (view the column on the admin edit screen)
	* Like categories
	* Like tags
* Reset "taxonomy" Relations? – During bulk editing, easily remove all of the taxonomy's prior relationships and add new.
* Enable "Custom Field"? - As checkbox, radio, select, input, or textarea
* "Custom Field" Configuration - You may create options formatted like "the-key|Supremely, Pretty Values" seperated by newlines.
	* Example configuration
`1
Two
3|Three
four|Four, and forty five
five-five|55`

**Compatibility & Reset**

* Export Settings – These are your current settings in a serialized format. Copy the contents to make a backup of your settings.
* Import Settings – Paste new serialized settings here to overwrite your current configuration.
* Remove Plugin Data on Deletion? - Delete all Custom Bulk/Quick Edit data and options from database on plugin deletion
* Reset to Defaults? – Check this box to reset options to their defaults

= API =

* Read the [Custom Bulk/Quick Edit API](https://github.com/michael-cannon/custom-bulkquick-edit/blob/master/API.md).

= Languages =

* Serbo-Croatian by [Borisa Djuraskovic](borisad@webhostinghub.com)

You can translate this plugin into your own language if it's not done so already. The localization file `custom-bulkquick-edit.pot` can be found in the `languages` folder of this plugin. After translation, please [send the localized file](http://aihr.us/contact-aihrus/) to the plugin author.

See the FAQ for further localization tips.

= Help Me, Help You =

Do [let me know](http://wordpress.org/support/plugin/custom-bulkquick-edit) how well you're able to use this plugin or not. 

This plugin grew out of the frustration of having to custom write this code for every client. It works best when the custom post types have already added columns to the edit screen via the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) filter.

[Example](https://aihrus.zendesk.com/entries/24800411-How-do-I-add-custom-columns-).

= Limitations =

Unless the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) is already configured, your theme's `functions.php` file will have to modified to add custom field columns. See "[How do I add custom columns to my edit page?](https://aihrus.zendesk.com/entries/24800411-How-do-I-add-custom-columns-to-my-edit-page-)" for help.

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

= Most Common Resolutions =

1. [How do I add custom columns to my edit page?](https://aihrus.zendesk.com/entries/24800411-How-do-I-add-custom-columns-to-my-edit-page-)
1. [Where can I find working samples?](https://aihrus.zendesk.com/entries/27667723-Where-can-I-find-working-samples-)
1. [How do you configure options?](https://aihrus.zendesk.com/entries/24911342-How-do-you-configure-options-)

= Still Stuck? =

Please visit the [Custom Bulk/Quick Edit Knowledge Base](https://aihrus.zendesk.com/categories/20112546-Custom-Bulk-Quick-Edit) for frequently asked questions, offering ideas, or getting support.


== Screenshots ==

1. Custom Bulk/Quick Edit Settings panel
2. TwentyTwelve theme with Posts Post Excerpts enabled
3. Posts Quick Edit with excerpts
4. Posts Bulk Edit with excerpts

== Changelog ==

See [Changelog](https://github.com/michael-cannon/custom-bulk-quick-edit/blob/master/CHANGELOG.md)


== Upgrade Notice ==

= 1.3.0 =

* Requires PHP 5.3+ [notice](https://aihrus.zendesk.com/entries/30678006-Testimonials-2-16-0-Requires-PHP-5-3-)

= 1.1.0 =

* Please review your settings as some option keys have changed. There's no auto-upgrade at this time.


== Beta Testers Needed ==

I really want Custom Bulk/Quick Edit and Custom Bulk/Quick Edit Premium to be the best WordPress plugins of their type. However, it's beyond me to do it alone.

I need beta testers to help with ensuring pending releases of Custom Bulk/Quick Edit and Custom Bulk/Quick Edit Premium are solid. This would benefit us all by helping reduce the number of releases and raise code quality.

[Please contact me directly](http://aihr.us/contact-aihrus/).

Beta testers benefit directly with latest versions, a free 1-site license for Custom Bulk/Quick Edit Premium, and personalized support assistance.

== TODO ==

See [TODO](https://github.com/michael-cannon/custom-bulkquick-edit/blob/master/TODO.md)
