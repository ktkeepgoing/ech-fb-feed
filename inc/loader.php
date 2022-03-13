<?php
/**
 * loads in all files for this plugin
 */

$inc_filesArr = glob(plugin_dir_path(__FILE__). "*.php");

if (!empty($inc_filesArr)) {

	foreach ($inc_filesArr as $inc_file) {
		if (strpos($inc_file, 'loader.php') !== false) {
			continue;
		}
		if (strpos($inc_file, 'index.php') !== false) {
			continue;
		}

		require_once($inc_file);
		
	} // end foreach

} // end if




