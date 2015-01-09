<?php
/*
Plugin Name: MAM List Term Posts
Description: This plugin adds a shortcode to alphabetically list posts assigned a term.
Version: 1.0
Author: Mac McDonald
Author URI: http://wordpress.mcdspot.com
*/

//Catch anyone trying to directly acess the plugin - which isn't allowed
if (!function_exists('add_action')) {
   // Silence is golden.
   echo '';
   exit;
}

//Check if the the class already exists
if (!class_exists("MamListTermPosts")) {
   class MamListTermPosts {

      private $_VersionNumber = "1.0";
      private $_MamLoadTermPostsURL = "http://wordpress.mcdspot.com/";
      public $table_alias;
      private $mam_filter_fields, $mam_filter_join, $mam_filter_where, $mam_filter_orderby;

      /**
       * Basic constructor for the MamListTermPosts Class
       */
      public function __construct() {
         $this->table_alias = 'mam_' . rand();
      }

      /*
       * Shortcode to list posts that are assigned a given term in a taxonomy.
       * By default it lists posts that are in the 'uncategorized' category.
       *
       * Posts are sorted on title, or a custom field if present, and grouped by
       * the first letter of the sort field.
       *
       * Example: List all posts in the category 'state', 3 per row, using the
       *    custom field named 'state-name' for the sort key:
       *
       *    [mam_list_term_posts term_slug="state" title_field='state-name']
       *
       * Version 4.1
       *
      */
      public function mam_list_term_posts_func($atts) {
         extract( shortcode_atts( array(
            'tax_slug' => 'category',
            'term_slug' => 'uncategorized',
            'post_type' => 'post',
            'posts_per_row' => 3,            // Should be 1, 2, 3, or 4
            'posts_per_page' => -1,
            'title_field' => 'mltp_title',
         ), $atts ) );

         global $wp_query,$post;

         $taxobject = get_taxonomy($tax_slug);
         if( !$taxobject )
            return "<h2>Sorry, no taxonomy found for taxonomy='{$tax_slug}'!</h2>";
         $tax_name = $taxobject->labels->name;

         // Get the slug term id
         $args = array(
            'slug' => $term_slug,
            'hide_empty' => true,
            'hierarchical' => true,
         );
         $termarray = get_terms($tax_slug,$args);
         if ( !$termarray )
            return "<h2>Sorry, no term found for taxonomy='{$tax_name}' term='{$term_slug}'!</h2>";
         $term = $termarray[0];

         // Use filters to modify the query to sort on a Custom Field if present.
         $this->mam_alter_query_sort($title_field, $this->table_alias);

         $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
         $args = array (
            'posts_per_page' => $posts_per_page,
            'post_type' => $post_type,
            'orderby' => 'title',
            'order' => 'ASC',
            'paged' => $paged,
            'category__in' => array($term->term_id),
         );
         $temp_query = clone $wp_query;
         $wp_query = new WP_QUERY($args);
         // print_r('<p>REQUEST:');print_r($wp_query->request);print_r('</p>');

         // If the query is paged, check for letters split across pages.
         $continued_from_msg = $continued_on_msg = '';
         if ($wp_query->max_num_pages > 1) $this->mam_check_for_split_letters($continued_from_msg, $continued_on_msg, $args);

         // Clear query filters set by mam_alter_query_sort
         $this->mam_filter_fields = $this->mam_filter__global_join = $this->mam_filter__global_orderby = '';

         ob_start();
         if ( have_posts() ) {
            $in_this_row = 0;
            while ( have_posts() ) {
               the_post();
               $title = ( $title_field ) ? $post->sort_key : apply_filters('the_title', get_the_title());
               $first_letter = strtoupper(substr($title,0,1));
               if ($first_letter != $curr_letter) {
                  if (++$post_count > 1) {
                     $this->mam_end_prev_letter();
                  }
                  $this->mam_start_new_letter($first_letter, $posts_per_row, $continued_from_msg);
                  $continued_from_msg = '';
                  $curr_letter = $first_letter;
                  $in_this_row = 0;
               }
               if (++$in_this_row > $posts_per_row) {
                  $this->mam_end_prev_row();
                  $this->mam_start_new_row($posts_per_row);
                  $in_this_row = 1;  // Account for this first post
               } ?>
               <div class="mltp-title-cell mltp-title-cell-<?php echo $posts_per_row; ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php echo $title; ?></a></div>
            <?php }
            $this->mam_end_prev_letter();
            if( $continued_on_msg ) echo $continued_on_msg;
            ?>
            <div class="navigation">
               <div class="alignleft"><?php next_posts_link('&laquo; Higher Letters') ?></div>
               <div class="alignright"><?php previous_posts_link('Lower Letters &raquo;') ?></div>
            </div>
            <?php $wp_query = clone $temp_query;
            wp_reset_query();
         } else {
            echo "<h2>Sorry, no $post_type entries were found for taxonomy='{$tax_name}' term='{$term->name}'!</h2>";
         }
         $results = ob_get_clean();
         return $results;
      }

      private function mam_end_prev_letter() {
         $this->mam_end_prev_row();
         echo "</div><!-- End of mltp-letter-group -->\n";
         echo "<div class='mltp-clear'></div>\n";
      }
      private function mam_start_new_letter($letter, $posts_per_row, $continued_from_msg) {
         echo "<div class='mltp-letter-group'>\n";
         echo "\t<div class='mltp-letter-cell-row'><div class='mltp-letter-cell'>$letter</div>$continued_from_msg</div>\n";
         echo "\t\t<div class='mltp-clear'></div>\n";
         $this->mam_start_new_row($posts_per_row);
      }
      private function mam_end_prev_row() {
         echo "\t</div><!-- End mltp-row-cells -->\n";
      }
      private function mam_start_new_row($posts_per_row) {
         global $in_this_row;
         $in_this_row = 0;
         echo "\t<div class='mltp-row-cells mltp-row-cells-{$posts_per_row}'>\n";
      }
      private function mam_alter_query_sort($cf_name,$table_alias) {
         global $wpdb;
         $this->mam_filter_fields = ", IFNULL($table_alias.meta_value, $wpdb->posts.post_title) as sort_key";
         $this->mam_filter_join = " LEFT OUTER JOIN $wpdb->postmeta $table_alias ON ($wpdb->posts.ID = $table_alias.post_id AND $table_alias.meta_key = '$cf_name')";
         $this->mam_filter_orderby = " UPPER(sort_key) ASC, UPPER($wpdb->posts.post_title) ASC";
         // print_r('<p>TABLE ALIAS:');print_r($this->table_alias);print_r('</p>');
      }

      public function mam_posts_fields ($fields) {
         // Make sure there is a leading comma
         if ($this->mam_filter_fields) $fields .= (preg_match('/^(\s+)?,/',$this->mam_filter_fields)) ? $this->mam_filter_fields : ", $this->mam_filter_fields";
         return $fields;
      }
      public function mam_posts_join ($join) {
         if ($this->mam_filter_join) $join .= " $this->mam_filter_join";
         return $join;
      }
      public function mam_posts_where ($where) {
         if ($this->mam_filter_where) $where .= " $this->mam_filter_where";
         return $where;
      }
      public function mam_posts_orderby ($orderby) {
         if ($this->mam_filter_orderby) $orderby = $this->mam_filter_orderby;
         return $orderby;
      }

      private function mam_check_for_split_letters(&$continued_from_msg, &$continued_on_msg, $args) {
         global $wp_query, $wpdb;

         $this_page = $wp_query->query['paged'];
         $max_pages = $wp_query->max_num_pages;
         $posts_this_page = sizeof($wp_query->posts);
         $first_letter = strtoupper(substr($wp_query->posts[0]->sort_key,0,1));
         $last_letter = strtoupper(substr($wp_query->posts[$posts_this_page - 1]->sort_key,0,1));

         // If we are on any page other than 1, check for letter continued from.
         if ($this_page > 1) {
            // Create new query to get just one post that precedes this page.
            $query = $wp_query->request;
            if ( preg_match('/limit (\d+), *(\d+)/i', $query, $matches)) {
               $new_limit = $matches[1] - 1;
               $new_query = str_replace($matches[0],"LIMIT $new_limit, 1", $query);
               $prior_post = $wpdb->get_row($new_query);
               $prior_letter = strtoupper(substr($prior_post->sort_key,0,1));
               if ($prior_letter == $first_letter) {
                  $continued_from_msg = "<span class='mltp-continued-from'> (letter $prior_letter continued from prior page) </span>";
               }
            }
         }

         // If we are on any page other than the last one, check for letter continued on.
         if ($this_page < $max_pages) {
            // Create new query to get just one post that follows this page.
            $query = $wp_query->request;
            if ( preg_match('/limit (\d+), *(\d+)/i', $query, $matches)) {
               $new_limit = $matches[1] + $posts_this_page;
               $new_query = str_replace($matches[0],"LIMIT $new_limit, 1", $query);
               $next_post = $wpdb->get_row($new_query);
               $next_letter = strtoupper(substr($next_post->sort_key,0,1));
               if ($next_letter == $last_letter) {
                  $continued_on_msg = "<div class='mltp-continued-on'> (letter $next_letter continued on next page) </div>";
               }
            }
         }
      }

      /**
      * Enqueue plugin style-file
      */
      public function mam_add_my_stylesheet() {
         // Respects SSL, Style.css is relative to the current file
         wp_register_style( 'mam-style', plugins_url('style.css', __FILE__) );
         wp_enqueue_style( 'mam-style' );
      }


   }
}

if (class_exists("MamListTermPosts")) {
   $mamListTermPosts = new MamListTermPosts();
}

if (isset($mamListTermPosts)) {

   // print_r('<p>TABLE ALIAS:');print_r($mamListTermPosts->table_alias);print_r('</p>');

   // Actions
   add_action( 'wp_enqueue_scripts', array(&$mamListTermPosts,'mam_add_my_stylesheet') );


   // Filters
   add_filter('posts_fields', array(&$mamListTermPosts, 'mam_posts_fields'));
   add_filter('posts_join', array(&$mamListTermPosts, 'mam_posts_join'));
   add_filter('posts_where', array(&$mamListTermPosts, 'mam_posts_where'));
   add_filter('posts_orderby', array(&$mamListTermPosts, 'mam_posts_orderby'));

   //Shortcodes
   //add_shortcode - http://codex.wordpress.org/Function_Reference/add_shortcode
   add_shortcode( 'mam_list_term_posts', array(&$mamListTermPosts, 'mam_list_term_posts_func') );
}

?>
