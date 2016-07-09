Primary Category Select
=============================

Adds a Primary Category metabox with a Select box to Posts. The Primary Category select box allows you to select a Primary Category for a selected category on that post. Once a Primary Category is selected it also changes the Post's permalink if the category is included in the permalink.

To check if there is a Primary Category for a post you can use `DldPrimaryCategory::has_primary_category( $post_id )`

To get the Primary Category ID on the Website you can use either of these options:
* `get_post_meta( $post_id, 'dld_primary_category_select', true )`
* `DldPrimaryCategory::get_primary_category( $post_id )`

Once you have the Primary Category ID you can get that Category by using `get_term( $primary_category_id )`. With `get_term()` you will have the name, slug, and taxonomy for that Primary Category. With that information you can display the Primary Category however you need on the page.
