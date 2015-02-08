<?php
/**
 * Markdon Syntax
 *
 * @author     sonots
 * @license    http://www.gnu.org/licenses/gpl.html GPL v2
 * @link       http://lsx.sourceforge.jp/?Plugin%2Fmarkdown.inc.php
 * @version    $Id: markdown.inc.php,v 1.2 2007-02-24 16:28:39Z sonots $
 * @package    plugin
 */
ini_set('display_errors', 1);

require_once('plugin/Michelf/MarkdownExtra.inc.php');
use \Michelf\MarkdownExtra;

function plugin_markdown_convert()
{
    $args = func_get_args();
    $body = array_pop($args);
    $noskin = in_array("noskin", $args);
    global $vars;
    if (! (PKWK_READONLY > 0 or is_freeze($vars['page']) or plugin_markdown_is_edit_auth($vars['page']))) {
        $body = htmlspecialchars($body);
    }

    $body = MarkdownExtra::defaultTransform($body);

    if ($noskin) {
        pkwk_common_headers();
        print $body;
        exit;
    }
	return $body;
}

function plugin_markdown_is_edit_auth($page, $user = '')
{
    global $edit_auth, $edit_auth_pages, $auth_method_type;
    if (! $edit_auth) {
        return FALSE;
    }
    // Checked by:
    $target_str = '';
    if ($auth_method_type == 'pagename') {
        $target_str = $page; // Page name
    } else if ($auth_method_type == 'contents') {
        $target_str = join('', get_source($page)); // Its contents
    }

    foreach($edit_auth_pages as $regexp => $users) {
        if (preg_match($regexp, $target_str)) {
            if ($user == '' || in_array($user, explode(',', $users))) {
                return TRUE;
            }
        }
    }
    return FALSE;
}
?>