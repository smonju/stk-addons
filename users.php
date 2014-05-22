<?php
/**
 * Copyright 2009      Lucas Baudin <xapantu@gmail.com>
 *           2012-2014 Stephen Just <stephenjust@users.sf.net>
 *           2014      Daniel Butum <danibutum at gmail dot com>
 * This file is part of stkaddons
 *
 * stkaddons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * stkaddons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with stkaddons.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once(__DIR__ . DIRECTORY_SEPARATOR . "config.php");
AccessControl::setLevel('basicPage');

// set current user if not defined
$_GET['user'] = (isset($_GET['user'])) ? $_GET['user'] : $_SESSION['user'];
$action = (isset($_GET['action'])) ? $_GET['action'] : null;

$tpl = new StkTemplate('two-pane.tpl');
$tpl->assign('title', htmlspecialchars(_('SuperTuxKart Add-ons')) . ' | ' . htmlspecialchars(_('Users')));
$panel = array(
    'left'   => '',
    'status' => '',
    'right'  => ''
);

// handle user actions, set user feedback
$status = '';
switch ($action)
{
    case 'password':
        $user = User::getFromUserName($_GET['user']);
        if ($_SESSION['user'] !== $_GET['user']
            && !$_SESSION['role']['manage' . $user->getUserRole() . 's']
        )
        {
            $status = '<span class="error">' . htmlspecialchars(
                    _('You do not have the necessary permissions to perform this action.')
                ) . '</span><br />';
            break;
        }
        try
        {
            $user->setPass($_POST['oldPass'], $_POST['newPass'], $_POST['newPass2']);
            $status = htmlspecialchars(_('Your password has been changed successfully.'));
        }
        catch(UserException $e)
        {
            $status = '<span class="error">' . $e->getMessage() . '</span><br />';
        }
        break;
    case 'config':
        $user = User::getFromUserName($_GET['user']);
        if ($_SESSION['user'] !== $_GET['user']
            && !$_SESSION['role']['manage' . $user->getUserRole() . 's']
        )
        {
            $status = '<span class="error">' . htmlspecialchars(
                    _('You do not have the necessary permissions to perform this action.')
                ) . '</span><br />';
            break;
        }
        $available = (isset($_POST['available'])) ? $_POST['available'] : null;
        $range = (isset($_POST['range'])) ? $_POST['range'] : null;

        if ($user->setConfig($available, $range))
        {
            $status = 'Saved configuration.';
        }
        else
        {
            $status = 'An error occured';
        }
        break;
    default:
        break;
}
$panel['status'] = $status;

// get all users from the database, create links
$users = User::getAllData();
$templateUsers = array();
$templateUsers[] = array(
    'url'   => "users.php?user={$_SESSION['user']}",
    'label' => '<img class="icon"  src="image/user.png" />' . htmlspecialchars(_('Me')),
    'class' => 'user-list menu-item'
);
foreach ($users as $user)
{
    // Make sure that the user is active, or the viewer has permission to
    // manage this type of user
    if ($_SESSION['role']['manage' . $user['role'] . 's']
        || $user['active'] == 1
    )
    {
        $class = 'user-list menu-item';
        if ($user["active"] == 0)
        {
            $class .= ' unavailable';
        }
        $templateUsers[] = array(
            'url'   => "users.php?user={$user['user']}",
            'label' => '<img class="icon"  src="image/user.png" />' . htmlspecialchars($user['user']),
            'class' => $class
        );
    }
}

// left panel
$left_tpl = new StkTemplate('url-list-panel.tpl');
$left_tpl->assign('items', $templateUsers);
$panel['left'] = (string)$left_tpl;

// right panel
ob_start();
include(ROOT_PATH . 'users-panel.php');
$panel['right'] = ob_get_clean();

// output the view
$tpl->assign('panel', $panel);
echo $tpl;
