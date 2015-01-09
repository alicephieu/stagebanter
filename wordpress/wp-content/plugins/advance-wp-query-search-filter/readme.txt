=== Advance WP Query Search Filter ===
Contributors: TC.K
Donate link: http://9-sec.com/donation/
Tags: Search Filter, taxonoy, custom post type, custom meta field, taxonomy & meta field filter, advanced search
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 1.0.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Advance WP Query Search Filter let you search through post type, taxonomy and meta field. 

== Description ==

This plugin is an upgraded version of WP_Query Search Filter with added a lot of features. In nutshell Advance WP_Query Search Filter let your user perform more precisely search by filtering the search through post type, taxonomy and meta field. Ideal for website that have multiple post types, taxonomies and meta fields, eg property website, product website etc.  

**Plugin Features:**

* Admin are free to choose whether the search go through post type, taxonomy, meta field or even all of them.
* Multiple Search Form Supported.
* Search form now support checkbox,radio and dropdown fields.
* Using wp search template to disply the result.
* Admin can define how many result per page.
* Admin can sorting the result page by meta key and meta value.
* Using shortcode to display the search form.


If you have any problems with current plugin, please leave a
message on Forums Posts or goto [Here](http://9-sec.com/2013/03/advance-wp-query-search-filter/).


== Installation ==

1. Upload `advance-wqsf` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create your search form in the Advance WQSF.
4. Using `[awsqf-form id={form id}]` to display the form. 

== Frequently Asked Questions ==

= How can I styling the search form? =

You can simply refer the awqsf-style.css.css that come with the folder and alter it or override it at your theme css file.

= What if I want to display the search form in the template? =

Put this into `<?php echo do_shortcode("[awsqf-form id={form id}"); ?>` your template.

= What if I want to display the search form in the sidebar widget? =

Just insert the shortcodes like you inserted in the post content. eg. '[awsqf-form id=3299]`

= What if I don't want to display the title of the search form? =

Just giving `0` to `formtitle` atribute in the shortcode eg. '[awsqf-form id=3299 formtitle="0"]`


== Screenshots ==
1. Advance WP Query Search Filter setting page 1
2. Advance WP Query Search Filter setting page 2
3. Advance WP Query Search Filter search form in the content and sidebar


== Changelog ==


= 1.0 =
* First version.

= 1.0.1 =
* Fixed minor bugs

= 1.0.2 =
* Fixed front end jquery bugs (Thanks for muxahuk1214 pointed it out)

= 1.0.3 =
* Fixed Page Title bug

= 1.0.4 =
* Added more CSS classes for better customization 

= 1.0.5 =
* Fix Shortcode output markup problem

= 1.0.6 =
* Fix get_search_query and wp_title filter Undefined variable warning. 

= 1.0.7 =
* Fix Undefined variable warning. 
* Add new action hooks.

= 1.0.9 =
* Add new filter hooks in search form for taxonomy and custom meta field.

= 1.0.10 =
* Fix filter error on meta field output.
