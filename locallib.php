<?php
// This file is part of gotomeeting Moodle Plugin - https://itoptraining.com/
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
 * Internal library of functions for module gotomeeting
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('GOTOMEETING_MAX_TABLE_SIZE', 10);
define('GOTOMEETING_DELETED_GOTOMEETING', 'Deleted from GoToMeeting');

/**
 * This function generates teachers list that is displayed for
 * admin if he wants to schedule class for another teacher in gotomeeting mod_form.
 *
 * @param integer $courseid id of the course for which class is scheduled.
 *
 * @return string the teacherlist created.
 */
function gotomeeting_getteacherdetail($courseid) {
    global $CFG, $DB;
    $sql = "SELECT u.id, u.username FROM ".$CFG->prefix."course c ";
    $sql .= "JOIN ".$CFG->prefix."context ct ON c.id = ct.instanceid ";
    $sql .= "JOIN ".$CFG->prefix."role_assignments ra ON ra.contextid = ct.id ";
    $sql .= "JOIN ".$CFG->prefix."user u ON u.id = ra.userid ";
    $sql .= "JOIN ".$CFG->prefix."role r ON r.id = ra.roleid ";
    $sql .= "WHERE (archetype ='editingteacher' OR name ='teacher') AND c.id = $courseid";
    $teacherlist = $DB->get_records_sql($sql);
    return $teacherlist;
}

/**
 * Generates the list of timezones in gotomeeting mod_form.
 *
 * @return array $vctimezone the virtual classroom timezones list.
 */
function gotomeeting_timezone() {
    // TODO:keep this in setting.php
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($dbman->table_exists('timezone')) {
        $sql = "SELECT distinct name FROM mdl_timezone ORDER BY name ASC";
        $timezonerecords = $DB->get_records_sql($sql);

        if (!empty($timezonerecords)) {
            $vctimezone = array();
            $vctimezone['select'] = get_string('select_vctimezone', 'gotomeeting');
            foreach ($timezonerecords as $value) {
                $vctimezone[(string)$value->name] = (string)$value->name;
            }
            return $vctimezone;
        } else {
            // an error happened
            print_error('error_in_timeread', 'gotomeeting');
            return false;
        }
    } else {
        return core_date::get_list_of_timezones();
    }
}

/**
 * Generates the class time from unixtimestamp according to particular timezone
 * selected by the user while scheduling class.
 *
 * @param int $timestamp the unix timestamp
 * @param string $timezonerequired the timezone for which class is scheduled
 *
 * @return integer $class_time the virtual classroom time.
 */
function gotomeeting_converttime($timestamp, $timezonerequired) {
    $systemtimezone = date_default_timezone_get();
    $st = $timestamp;
    date_default_timezone_set($timezonerequired);
    $classtime = date('Y-m-d\TH:i:s\Z', $st);
    date_default_timezone_set($systemtimezone);
    return $classtime;
}

/**
 * Gets the pagination by url, cookie or default.
 *
 * @param string $idpagination the identifier for the element of pagination
 *
 * @return integer $class_time the virtual classroom time.
 */
function gotomeeting_get_table_pagination($output, $idpagination = 'paging') {
    // TODO: Class for tables

    $paging = optional_param($idpagination, '', PARAM_INT);

    // Setting paging as cookie in order to have paging when page number is changed
    if (!empty($paging)) {
        setcookie('gotomeeting_managecookie', $paging, time() + (86400 * 7 * 365));
        $perpage = $paging;
    } else if (isset($_COOKIE['gotomeeting_managecookie'])) {
        $perpage = $_COOKIE['gotomeeting_managecookie'];
    } else {
        $perpage = GOTOMEETING_MAX_TABLE_SIZE;
    }

    echo $output->table_pagination($perpage);

    return $perpage;
}

/**
 * Gets the headers of users updateable table.
 *
 * @return array string literals.
 */
function gotomeeting_get_headers() {

    return array(
        get_string('class', 'gotomeeting'),
        get_string('name', 'gotomeeting'),
        get_string('email'),
        get_string('entry', 'gotomeeting'),
        get_string('exit', 'gotomeeting'),
        get_string('duration', 'gotomeeting'),
        get_string('student', 'gotomeeting'),
    );

}

/**
 * Gets the columns of users updateable table.
 *
 * @return array string literals.
 */
function gotomeeting_get_columns() {

            return array(
            'class',
            'name',
            'email',
            'entry',
            'end',
            'duration',
            'student'
            );
}

