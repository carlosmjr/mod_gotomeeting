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
 * Page to present attendance report.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_gotomeeting\gotomeeting_moodle;

// define('GOTOMEETING_MAX_TABLE_SIZE', 10);
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->libdir.'/tablelib.php');
global $CFG, $USER, $OUTPUT, $PAGE;

// --------parameter needed---------
$id = required_param('id', PARAM_INT);   // course
$meetingid = required_param('meetingid', PARAM_INT);   // gotomeeting_class_id
$downloadattendence = optional_param('download', '', PARAM_RAW);

confirm_sesskey();
$sesskey = sesskey();
// $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
list($course, $cm) = get_course_and_cm_from_instance($meetingid, 'gotomeeting', $id);
$gotomeetingmodulecontext = context_module::instance($cm->id);

require_capability('mod/gotomeeting:view_attendance_report', $gotomeetingmodulecontext);

$gotomeetingmoodle = gotomeeting_moodle::instance_by_id($meetingid, true);
$resmeeting = $gotomeetingmoodle->get_meeting();
$gotomeetingid = $gotomeetingmoodle->get_gotomeeting_id();

// $gotomeetingclass = $DB->get_record('gotomeeting', array('id' => $meetingid), '*', MUST_EXIST);
require_course_login($course);

$coursecontext = context_course::instance($course->id);

$noerror = get_string('noerror', 'gotomeeting');
$params = array(
    'objectid' => $meetingid,
    'relateduserid' => $USER->id,
    'courseid' => $id,
    'context' => $coursecontext,
    'other' => array(
        'sesskey' => $sesskey,
        'error' => $noerror
    )
);
$event = \mod_gotomeeting\event\gotomeeting_classattendance::create($params);
$event->trigger();

$PAGE->set_url('/mod/gotomeeting/attendancereport.php',
        array('id' => $id, 'meetingid' => $meetingid, 'sesskey' => sesskey()));
$pagetitle = new stdClass();
$pagetitle->name = get_string('attendance_report', 'gotomeeting');
$PAGE->set_title(format_string($pagetitle->name));
$gotomeetingattendancereport = get_string('gotomeeting_attendancereport', 'gotomeeting');
$attreptitle = $gotomeetingattendancereport." ".$gotomeetingmoodle->get_name();
$PAGE->set_heading(format_string($attreptitle));
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('incourse');
$coursenumber = $course->id;

$output = $PAGE->get_renderer('mod_gotomeeting');

$idpaging = 'paging';
$perpage = gotomeeting_get_table_pagination($output, $idpaging);

try {
    $meetingattendance = $resmeeting->getAttendeesByMeeting($gotomeetingid);
} catch (\Exception $e) {
    // Probabbly never had any student
    $meetingattendance = array();
}

$meetingstarttime = $gotomeetingmoodle->get_gotomeeting_datetime();
$meetingendtime = $gotomeetingmoodle->get_gotomeeting_datetime_end();

foreach ($meetingattendance as $key => $value) {

    // $attend_join_time = strtotime(substr($value['joinTime'], 0, 10));
    $attendjointime = strtotime(substr($value['joinTime'], 0, 19));
    // $attend_leave_time = strtotime(substr($value['leaveTime'], 0, 10)); //strtotime($value['leaveTime'].' UTC');
    $attendleavetime = strtotime(substr($value['leaveTime'], 0, 19));

    if ($attendleavetime < ($meetingstarttime - 3600) || $attendjointime > ($meetingendtime + 3600)) {
        unset($meetingattendance[$key]);
    }

}

// Creation of table starts
$table = new flexible_table('attendenreport');
$table->define_columns(array('name', 'email', 'entry_time', 'exit_time', 'attended_minutes'));
$table->column_style_all('text-align', 'left');
$table->define_headers(array(get_string('attendee_name', 'gotomeeting'), get_string('attendee_email', 'gotomeeting'),
    get_string('entry_time', 'gotomeeting'), get_string('exit_time', 'gotomeeting'),
    get_string('attended_minutes', 'gotomeeting'),
   ));
$table->define_baseurl($PAGE->url);
$table->is_downloadable(true);
$table->download_buttons();
$table->show_download_buttons_at(array(TABLE_P_BOTTOM));
$table->sortable(false);
$table->pageable(true);

// Naming of the table download file
$gotomeetingattendencefile = get_string('attendence_file', 'gotomeeting');
$gotomeetingattendencefileheading = $gotomeetingattendencefile." ".$coursenumber;
$attendanceclass = get_string('gotomeeting_attendence_file', 'gotomeeting');
$gotomeetingatendncfilename = $attendanceclass.$coursenumber;
$table->is_downloading($downloadattendence, $gotomeetingatendncfilename,
    $gotomeetingattendencefileheading);
if (!$table->is_downloading()) {
    echo $OUTPUT->header();

    echo $output->tabs($course, 'gotomeeting_attendance', $cm);

    echo $output->table_pagination($perpage, $idpaging);
}
$table->setup();

if (!array_key_exists('int_error_code', $meetingattendance)) {

    foreach ($meetingattendance as $value) {

        if (!$DB->record_exists(
            'gotomeeting_attendace_report'
            , array(
                'gotomeetingid' => $gotomeetingid
                , 'join_time' => strtotime($value['joinTime'].' UTC')
                , 'leave_time' => strtotime($value['leaveTime'].' UTC')
                , 'attendee_name' => $value['attendeeName']
                , 'attendee_email' => $value['attendeeEmail']
                , 'duration' => $value['duration']
        ))) {
            // save attendance on DB
            $data = array('attendee_name' => $value['attendeeName'],
                'gotomeetingid' => $gotomeetingid,
                'join_time' => strtotime($value['joinTime'].' UTC'),
                'leave_time' => strtotime($value['leaveTime'].' UTC'),
                'duration' => $value['duration'],
                'attendee_email' => $value['attendeeEmail'],
                'meeting_start_time' => strtotime($value['startTime']),
                'meeting_end_time' => strtotime($value['endTime']),
                'updated' => 0
            );

            $newitemid = $DB->insert_record('gotomeeting_attendace_report', $data);
        }

        $name = (string)$value['attendeeName'];

        $actualentrytime = date('d-m-Y H:i:s', strtotime($value['joinTime'].' UTC'));
        $actualexittime = date('d-m-Y H:i:s', strtotime($value['leaveTime'].' UTC'));

        $attendedminutes = (string)$value['duration']. " ".get_string('duration_minutes', 'gotomeeting');
        // $table->add_data(array($name, $actualentrytime, $actualexittime, $attendedminutes));
    }

    $attendancereport = $DB->get_records('gotomeeting_attendace_report', array('gotomeetingid' => $gotomeetingid, 'userid' => null));
    foreach ($attendancereport as $value) {
        // Searching for moodle user giving a attendee email
        $user = $DB->get_record('user', array('email' => $value->attendee_email));
        if ($user) {
            $value->userid = $user->id;
            $DB->update_record('gotomeeting_attendace_report', $value);
        }
    }

    $attendancereport = $DB->get_records('gotomeeting_attendace_report', array('gotomeetingid' => $gotomeetingid, 'userid' => null));
    foreach ($attendancereport as $value) {
        // Searching for moodle user giving a attendee name
        $sql = "SELECT u.id FROM mdl_user as u
                JOIN {role_assignments} ra on ra.userid=u.id
                WHERE ra.contextid ={$coursecontext->id} AND CONCAT(TRIM(u.firstname), ' ', TRIM(u.lastname)) LIKE '%" . trim($value->attendee_name) . "%' ";
        $user = $DB->get_record_sql($sql);
        if ($user) {
            $value->userid = $user->id;
            $DB->update_record('gotomeeting_attendace_report', $value);
        }
    }

    $attendancereport = $DB->get_records('gotomeeting_attendace_report', array('gotomeetingid' => $gotomeetingid, 'userid' => null));
    foreach ($attendancereport as $value) {
        // Searching for moodle user giving a attendee name
        $likename = preg_replace('//', '%', preg_replace('/\s/', '', $value->attendee_name));
        $sql = "SELECT u.id FROM mdl_user as u
                JOIN {role_assignments} ra on ra.userid=u.id
                WHERE ra.contextid ={$coursecontext->id} AND CONCAT(TRIM(u.firstname), ' ', TRIM(u.lastname)) LIKE '" . $likename . "' ";
        $user = $DB->get_record_sql($sql);
        if ($user) {
            $value->userid = $user->id;
             $DB->update_record('gotomeeting_attendace_report', $value);
        }
    }

}

$sql = "SELECT gar.id, g.name, gar.attendee_name , gar.attendee_email AS email, gar.duration, gar.join_time,
               gar.leave_time, CONCAT(u.firstname, ' ', u.lastname) AS student
          FROM {gotomeeting_attendace_report} gar
          JOIN {gotomeeting} g ON g.gotomeeting_id = gar.gotomeetingid
          LEFT JOIN {user} u ON u.id=gar.userid
         WHERE gar.gotomeetingid=:gotomeetingid";

$records = $DB->get_records_sql($sql, array('gotomeetingid' => $gotomeetingid));

$table->pagesize($perpage, count($records));

$records = array_slice($records, $table->get_page_start(), $perpage);

foreach ($records as $record) {
    $name = $record->attendee_name . " (" . $record->student .")";
    $attendeeemail = $record->email;

    $actualentrytime = date('d-m-Y H:i:s', $record->join_time);
    $actualexittime = date('d-m-Y H:i:s', $record->leave_time);

    $attendedminutes = $record->duration. " ".get_string('duration_minutes', 'gotomeeting');

    $table->add_data(array($name, $attendeeemail, $actualentrytime, $actualexittime, $attendedminutes));

}

// $table->setup();
  $table->finish_output();
echo $OUTPUT->footer();