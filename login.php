<?php
/**
 * Copyright 2009      Lucas Baudin <xapantu@gmail.com>
 *           2011-2014 Stephen Just <stephenjust@users.sourceforge.net>
 *           2014-2015 Daniel Butum <danibutum at gmail dot com>
 * This file is part of stk-addons.
 *
 * stk-addons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * stk-addons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with stk-addons. If not, see <http://www.gnu.org/licenses/>.
 */
require_once(__DIR__ . DIRECTORY_SEPARATOR . "config.php");

// define possibly undefined variables
$username = isset($_POST['username']) ? $_POST['username'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;

// set default redirect url from where the user was
$return_to_url = $safe_url = ROOT_LOCATION;
if (isset($_POST["return_to"]))
{
    $return_to_url = $_POST["return_to"];
}
else if (isset($_GET["return_to"]))
{
    // already decoded
    $return_to_url = $_GET["return_to"];
}
// prevent foreign domain
if (!Util::str_starts_with($return_to_url, ROOT_LOCATION))
{
    // silently fall back to safe url
    $return_to_url = $safe_url;
}

$tpl = StkTemplate::get('login.tpl')
    ->assignTitle(_h("Login"))
    ->addBootstrapValidatorLibrary();

// Prepare forms
$login_form = [
    'display'     => true,
    'username'    => ['min' => User::MIN_USERNAME, 'max' => User::MAX_USERNAME, 'value' => h($username)],
    'password'    => ['min' => User::MIN_PASSWORD, 'max' => User::MAX_PASSWORD],
    'return_to'   => $return_to_url,
    'form_action' => URL::rewriteFromConfig('login.php?action=submit'),
    'links'       => [
        'register'       => StkTemplate::makeHTMLHyperLink('register.php', _h('Sign up here.')),
        'reset_password' => StkTemplate::makeHTMLHyperLink('password-reset.php', _h('(forgot password)'), true, false)
    ]
];

switch ($action)
{
    case 'logout':
        $login_form['display'] = false;

        User::logout();

        if (User::isLoggedIn())
        {
            $tpl->assign('errors', _h('Failed to logout.'));
        }
        else
        {
            Util::redirectTo($safe_url);
        }

        break;

    case 'submit':
        $login_form['display'] = false;

        $errors = "";
        try
        {
            User::login($username, $password);
        }
        catch (UserException $e)
        {
            $login_form['display'] = true;
            $errors = $e->getMessage();
        }

        if (User::isLoggedIn())
        {
            Util::redirectTo($return_to_url);
        }
        else
        {
            $login_form['display'] = true;
            $tpl->assign('errors', $errors);
        }

        break;

    default:
        if (User::isLoggedIn())
        {
            $login_form['display'] = false;
            $tpl->setMetaRefresh($safe_url, 5);

            $conf = _h('You are already logged in.');
            $conf .= sprintf(' Click <a href="%s">here</a> if you do not automatically redirect.', $safe_url);
            $tpl->assign('success', $conf);
        }
        break;
}

$tpl->assign('login', $login_form);
echo $tpl;
