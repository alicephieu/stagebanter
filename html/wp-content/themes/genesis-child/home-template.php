<?php
/* 
Template Name: Home
*/

//custom hooks below here...

remove_action('genesis_post_title', 'genesis_do_post_title');

genesis(); // <- everything important: make sure to include this. 