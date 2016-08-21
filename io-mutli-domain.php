<?php

/**
* Plugin Name: Iostudio Multi-domain Theme Switcher
* Plugin URI: http://iostudio.com
* Description: allows for determining the theme to use based on the server name of the call.
* Author: Micah Flatt and Justin Maurer
* Version: 1.0
* Author URI: http://iostudio.com
**/

// @TODO create options page to set site url and domains dynamically

/*******************
* IMPORTANT!!!
* SITEURL IS SET THROUGH wp-content/db.php
* PLEASE MAKE SURE THIS FILE IS PRESENT AND HAS THE FOLLOWING CONTENT

add_filter ( 'pre_option_home', 'io_multi_domain_siteurl' );
add_filter ( 'pre_option_siteurl', 'io_multi_domain_siteurl' );

function io_multi_domain_siteurl($url) {
	if($_SERVER['SERVER_NAME'] == 'americasfund.iostudio.dev') {
		return 'http://americasfund.iostudio.dev';
	} else return false;
}

CHANGING THE SITE URL
See http://codex.wordpress.org/Running_a_Development_Copy_of_WordPress
*******************/

// @TODO create options page to set blog titles and description dynamically

function io_multi_domain_variables()
{
	switch( $_SERVER['SERVER_NAME'])
	{
	    case ALT_DOMAIN:
	    	return array(
	    		'stylesheet' => 'amf',
	    		'blogname'   => 'America\'s Fund',
	    		'blogdescription' => 'Test Description',
	    		'term' => 'americas-fund'
	    		);
	        break;
	    default:
	        return array(
	        	'blogdescription' => 'Helping people....',
	        	'term' => 'semper-fi-fund'
	        	);
	        break;
	}
}
function io_multi_get_var($name,$default)
{
	$domain_vars = io_multi_domain_variables();
	if( !empty($domain_vars[$name]) )
	{
		return $domain_vars[$name];
	}
	return $default;
}

// SWITCH STYLESHEET
add_filter('stylesheet', 'io_mutli_domain_selection');
function io_mutli_domain_selection($style)
{
	return io_multi_get_var('stylesheet',$style);
}

// SWITCH BLOG NAME
// http://codex.wordpress.org/Template_Tags/bloginfo
// http://codex.wordpress.org/Option_Reference
add_filter('pre_option_blogname', 'io_multi_domain_blogname');
function io_multi_domain_blogname($name)
{
	return io_multi_get_var('blogname',$name);
}
// SWITCH BLOG DESCRIPTION
add_filter('pre_option_blogdescription','io_multi_domain_blogdescription');
function io_multi_domain_blogdescription($description) {
	return io_multi_get_var('blogdescription',$description);
}

/*******************
* LOAD CHILD PAGE
* ACCORDING TO CATEGORY
*******************/
// get site-specific child id. returns false if no children exist. useful in other places as well.
function site_child($pid) {

	$site_term = io_multi_get_var('term', null);  //added 'null' to prevent error. not sure about

	if ( has_term($site_term,'site_assign',$pid) ) {
		return false;
	}

	$args = array(
		'child_of' => $pid,
		'hierarchical' => 1,
	);
	// http://codex.wordpress.org/Function_Reference/get_pages
	$children = get_pages($args);

	foreach ($children as $child) {

		if ( has_term($site_term,'site_assign',$child) ) {
			return $child->ID;
		}
	}
	return false;
}

// modify the PAGE main query to get site-specific child page
function replace_content_with_child( $query ) {

	if ( $query->is_main_query() && !is_admin() && is_page() ) {
		if ($query->get('page_id') == get_option('page_on_front')){
			$id = get_option('page_on_front');
		} else {
			$id = get_queried_object_id();
		}

		if( $sitechild = site_child($id) ) {

			$query->query_vars['page_id'] = $sitechild;
		}
	}
}
add_action( 'pre_get_posts', 'replace_content_with_child' );