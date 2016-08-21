# WordPress URL-based Theme Switcher

Introduction
============
This is a WordPress plugin designed to allow a single WordPress installation to power multiple domains. It was originally written for the sffaf project, so currently, all domain settings are specific to that setup. The todo list includes creating an options page to dynamically configure these settings. The theme to be used on the secondary domain is defined in the `stylesheet` setting in the main plugin file.

db.php
======
In addition to installing the plugin, you must ensure that there is a file called db.php in the wp-content/ folder and that it has the following contents (where SECONDARYDOMAIN is the domain of the secondary URL to be used): 
	add_filter ( 'pre_option_home', 'io_multi_domain_siteurl' );
	add_filter ( 'pre_option_siteurl', 'io_multi_domain_siteurl' );

	function io_multi_domain_siteurl($url) {
		if($_SERVER['SERVER_NAME'] == 'SECONDARYDOMAIN') {
			return 'http://SECONDARYDOMAIN';
		} else return false;
	}

Content Replace
===============
In addition to theme switching, the plugin will attempt to replace page content with a child page, if the child has the term (category) specific to the site being viewed. For example, if the user visit the americasfund.com domain, the plugin will look for a child page with the `term` 'americas-fund' as defined in the main plugin file. It will then replace the content of the page begin viewed with the child page content.

