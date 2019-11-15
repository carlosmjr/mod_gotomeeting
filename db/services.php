<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * GoToMeeting external functions and service definitions.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'mod_gotomeeting_load_users_updateables' => array(
        'classname'       => 'mod_gotomeeting_external',
        'methodname'      => 'users_updateables',
        'description'     => 'Return the users that need to be updated',
        'type'            => 'read',
        'capabilities'    => 'mod/gotomeeting:manage_users',
        'ajax'            => true,
    ),
    'mod_gotomeeting_update_user' => array(
        'classname'       => 'mod_gotomeeting_external',
        'methodname'      => 'update_user',
        'description'     => 'Udate information of syncronization between GoToMeeting and Moodle',
        'type'            => 'write',
        'capabilities'    => 'mod/gotomeeting:manage_users',
        'ajax'            => true,
    ),
);
