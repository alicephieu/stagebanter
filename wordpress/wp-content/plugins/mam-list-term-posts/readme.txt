=== MAM List Term Posts Plugin ===
Contributors: Mac McDonald
Tags: terms, alphabetical, list
Requires at least: 3.4.
Tested up to: 3.4.2
Stable tag: 1.0
License: MIT

This plugin creates a shortcode to create an alphabetical list of Posts
grouped by the first letter of the title.

== Description ==

This plugin creates a shortcode to create an alphabetical list of Posts
with a given Taxonomy and Term grouped by the first letter of the title.

By default it lists posts that are in the 'uncategorized' category.

Posts are sorted on title, or a custom field if present, and grouped by
the first letter of the sort field.

Example: List all posts in the category 'state', 3 per row, using the
custom field named 'state-name' for the sort key, or title if 'state-name'
is not found:

    [mam_list_term_posts term_slug="state" title_field='state-name']

The shortcode provides these parameters:

    * tax_slug          The slug of the desired Taxonomy.
                        Default is 'category'.
    * term_slug         The slug of the desired Term.
                        Default is 'uncategorized'.
    * post_type         The type for the posts to list.
                        Default is 'posts'.
    * posts_per_row     The number of post titles to list in a row.
                        The provided CSS allows for 1, 2, 3, or 4 posts per row.
                        Default is 3.
    * posts_per_page    The number of post titles to show on a page.
                        Default is -1 to list all titles.
    * title_field       Name of a Custom Field to use instead of post_title
                        if the field exists for a given post.
                        Default is 'mltp_title'.


== Installation ==

1. Upload the MAM List Term Posts plugin to your site.
2. Activate it.
3. Add the shortcode to one or more Pages or Posts.

== Changelog ==

==== 1.0 ====
* Intial Release
