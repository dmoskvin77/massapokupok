<?php
/**
 * Handles logins, logouts, and password reset requests.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (isset($_GET['action']))
	define('FORUM_QUIET_VISIT', 1);

if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');

require FORUM_ROOT.'include/commonlogin.php';

($hook = get_hook('li_start')) ? eval($hook) : null;

// Load the login.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/login.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$errors = array();

// Login
if (isset($_POST['req_username']) && isset($_POST['req_password']) &&  empty($action))
{
	$form_username = forum_trim($_POST['req_username']);
	$form_password = forum_trim($_POST['req_password']);
	$save_pass = isset($_POST['save_pass']);

	($hook = get_hook('li_login_form_submitted')) ? eval($hook) : null;

	// Get user info matching login attempt
	$query = array(
		'SELECT'	=> 'u.id, u.group_id, u.password, u.salt',
		'FROM'		=> 'users AS u'
	);

	if (in_array($db_type, array('mysql', 'mysqli', 'mysql_innodb', 'mysqli_innodb')))
		$query['WHERE'] = 'email=\''.$forum_db->escape($form_username).'\'';
	else
		$query['WHERE'] = 'LOWER(email)=LOWER(\''.$forum_db->escape($form_username).'\')';

	($hook = get_hook('li_login_qr_get_login_data')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	list($user_id, $group_id, $db_password_hash, $salt) = $forum_db->fetch_row($result);

	$authorized = false;
	if (!empty($db_password_hash))
	{
		$form_password_hash = md5(md5($form_password).sha1($form_password));
		if ($db_password_hash == $form_password_hash || $db_password_hash == $form_password)
			$authorized = true;

	}

	($hook = get_hook('li_login_pre_auth_message')) ? eval($hook) : null;

	if (!$authorized)
		$errors[] = sprintf($lang_login['Wrong user/pass']);

	// Did everything go according to plan?
	if (empty($errors))
	{
		// Update the status if this is the first time the user logged in
		if ($group_id == FORUM_UNVERIFIED)
		{
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'group_id='.$forum_config['o_default_user_group'],
				'WHERE'		=> 'id='.$user_id
			);

			($hook = get_hook('li_login_qr_update_user_group')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);

			// Remove cache file with forum stats
			if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			{
				require FORUM_ROOT.'include/cache.php';
			}

			clean_stats_cache();
		}

		// Remove this user's guest entry from the online list
		$query = array(
			'DELETE'	=> 'online',
			'WHERE'		=> 'ident=\''.$forum_db->escape(get_remote_address()).'\''
		);

		($hook = get_hook('li_login_qr_delete_online_user')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		$expire = ($save_pass) ? time() + 1209600 : time() + $forum_config['o_timeout_visit'];
		forum_setcookie($cookie_name, base64_encode($user_id.'|'.$form_password_hash.'|'.$expire.'|'.sha1($salt.$form_password_hash.forum_hash($expire, $salt))), $expire);

		($hook = get_hook('li_login_pre_redirect')) ? eval($hook) : null;

		redirect(forum_htmlencode($_POST['redirect_url']).((substr_count($_POST['redirect_url'], '?') == 1) ? '&amp;' : '?').'login=1', $lang_login['Login redirect']);
	}
}
// Logout
else if ($action == 'out')
{
	if ($forum_user['is_guest'] || !isset($_GET['id']) || $_GET['id'] != $forum_user['id'])
	{
		header('Location: '.forum_link($forum_url['index']));
		exit;
	}

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('logout'.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('li_logout_selected')) ? eval($hook) : null;

	// Remove user from "users online" list.
	$query = array(
		'DELETE'	=> 'online',
		'WHERE'		=> 'user_id='.$forum_user['id']
	);

	($hook = get_hook('li_logout_qr_delete_online_user')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Update last_visit (make sure there's something to update it with)
	if (isset($forum_user['logged']))
	{
		$query = array(
			'UPDATE'	=> 'users',
			'SET'		=> 'last_visit='.$forum_user['logged'],
			'WHERE'		=> 'id='.$forum_user['id']
		);

		($hook = get_hook('li_logout_qr_update_last_visit')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);
	}

	$expire = time() + 1209600;
	forum_setcookie($cookie_name, base64_encode('1|'.random_key(8, false, true).'|'.$expire.'|'.random_key(8, false, true)), $expire);

	// Reset tracked topics
	set_tracked_topics(null);

	($hook = get_hook('li_logout_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['index']), $lang_login['Logout redirect']);
}

if (!$forum_user['is_guest'])
	header('Location: '.forum_link($forum_url['index']));

// Setup form
header( 'Location: /userlogin', true, 303 );
exit();

$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['login']);

$forum_page['hidden_fields'] = array(
	'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
	'redirect_url'	=> '<input type="hidden" name="redirect_url" value="'.forum_htmlencode($forum_user['prev_url']).'" />',
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	sprintf($lang_login['Login info'], $forum_config['o_board_title'])
);

($hook = get_hook('li_login_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE', 'login');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();

($hook = get_hook('li_login_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo sprintf($lang_login['Login info'], $forum_config['o_board_title']) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="content-head">
			<p class="hn"><?php printf($lang_login['Login options'], '<a href="'.forum_link($forum_url['register']).'">'.$lang_login['register'].'</a>', '<a href="'.forum_link($forum_url['request_password']).'">'.$lang_login['Obtain pass'].'</a>') ?></p>
		</div>
<?php

	// If there were any errors, show them
	if (!empty($errors))
	{
		$forum_page['errors'] = array();
	foreach ($errors as $cur_error)
			$forum_page['errors'][] = '<li class="warn"><span>'.$cur_error.'</span></li>';

		($hook = get_hook('li_pre_login_errors')) ? eval($hook) : null;

?>
		<div class="ct-box error-box">
			<h2 class="warn hn"><?php echo $lang_login['Login errors'] ?></h2>
			<ul class="error-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['errors'])."\n" ?>
			</ul>
		</div>
<?php

	}

?>
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php echo $lang_common['Required warn'] ?></p>
		</div>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('li_login_pre_login_group')) ? eval($hook) : null; ?>
			<div class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
<?php ($hook = get_hook('li_login_pre_username')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_login['Username'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_username" value="<?php if (isset($_POST['req_username'])) echo forum_htmlencode($_POST['req_username']); ?>" size="35" maxlength="25" required spellcheck="false" /></span>
					</div>
				</div>
<?php ($hook = get_hook('li_login_pre_pass')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_login['Password'] ?></span></label><br />
						<span class="fld-input"><input type="password" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_password" value="<?php if (isset($_POST['req_password'])) echo forum_htmlencode($_POST['req_password']); ?>" size="35" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('li_login_pre_remember_me_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="save_pass" value="1"<?php if (isset($_POST['save_pass'])) echo ' checked="checked"'; ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_login['Remember me'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('li_login_pre_group_end')) ? eval($hook) : null; ?>
			</div>
<?php ($hook = get_hook('li_login_group_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="login" value="<?php echo $lang_login['Login'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('li_end')) ? eval($hook) : null;

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';
