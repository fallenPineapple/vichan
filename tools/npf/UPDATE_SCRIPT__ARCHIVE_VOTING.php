<?php



// require 'inc/config.php';
// require 'inc/config_instance.php';
require 'inc/functions.php';


global $config;

// Check so only ADMIN can run script
check_login(true);
if (!$mod || $mod['type'] != ADMIN)
    die("You need to be logged in as admin");



// Set timelimit to what it is for rebuild
@set_time_limit($config['mod']['rebuild_timelimit']);


$page['title'] = 'Updating Database - Voting on Archived Threads';


$step = isset($_GET['step']) ? round($_GET['step']) : 0;

switch($step)
{
    default:
    case 0:
        $page['body'] = '<p style="text-align:center">You are about to update the database to allow voting on archived threads.</p>';
        $page['body'] .= '<p style="text-align:center"><a href="?step=2">Click here to update database entries.</a></p>';
    break;
	case 2:
		$page['body'] = '<p style="text-align:center">Database have been updated.</p>';

		$sql_errors = "";
		$file_errors = "";


		// Update posts_* table to archive function
		// Get list of boards	
		$boards = listBoards();
		foreach ($boards as &$_board) {
			$query = sprintf('ALTER TABLE `archive_%s` ADD `votes` INT UNSIGNED NOT NULL AFTER `mod_archived`', $_board['uri']);
			query($query) or $sql_errors .= sprintf("<li>Add Archive Voting column to DB for %s<br/>", $_board['uri']) . db_error() . '</li>';
		}

		if (!empty($sql_errors))
		    $page['body'] .= '<div class="ban"><h2>SQL errors</h2><p>SQL errors were encountered when trying to update the database.</p><p>The errors encountered were:</p><ul>' . $sql_errors . '</ul></div>';
		if (!empty($file_errors))
		    $page['body'] .= '<div class="ban"><h2>File System errors</h2><p>File System errors were encountered when trying to create folders.</p><p>The errors encountered were:</p><ul>' . $file_errors . '</ul></div>';

		break;
}


echo Element('page.html', $page);

?>
<!-- There is probably a much better way to do this, but eh. -->
<link rel="stylesheet" type="text/css" href="stylesheets/style.css" />
