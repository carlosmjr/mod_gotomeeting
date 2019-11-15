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
 * List of gotommeting classes
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_gotomeeting\gotomeeting_moodle;

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->libdir.'/tablelib.php');
global $DB, $CFG;

// set_time_limit(0);

// Parameter needed
$id = required_param('id', PARAM_INT);   // course
$download = optional_param('download', '', PARAM_RAW);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_course_login($course);

$coursecontext = context_course::instance($course->id);
require_capability('mod/gotomeeting:manage_classes', $coursecontext);

$PAGE->set_url('/mod/gotomeeting/index.php', array('id' => $id, 'sesskey' => sesskey()));
$pagetitle = new stdClass();
$pagetitle->name = get_string('manage_classes', 'gotomeeting');
$PAGE->set_title(format_string($pagetitle->name));
$PAGE->set_heading(format_string(get_string('gotomeeting_classes', 'gotomeeting')));
$PAGE->set_pagelayout('incourse');
$PAGE->set_context($coursecontext);
$coursenumber = $course->id;
$output = $PAGE->get_renderer('mod_gotomeeting');

$idpaging = 'paging';
$perpage = gotomeeting_get_table_pagination($output, $idpaging);

// This function get all the data regarding the gotomeeting class, including cm id's

$gotomeetings = get_all_instances_in_course('gotomeeting', $course);

$params = array(
    'objectid' => null,
    'relateduserid' => $USER->id,
    'courseid' => $course->id,
    'context' => $coursecontext,
    'other' => array(
        'error' => '',
        'sesskey' => sesskey()
    )
);
$event = \mod_gotomeeting\event\gotomeeting_classlisting::create($params);
$event->trigger();

// Creation of table starts
$table = new flexible_table('manageclasses');

$table->define_columns(array('name', 'date_time', 'presenter', 'status', 'manage', 'attendance_report'));

$statusheading = get_string('status', 'gotomeeting')." " .

    $output->format_icon_link($PAGE->url, 'i/reload', get_string('refresh_page', 'gotomeeting'));

// $table->column_style_all('text-align', 'left');
$table->define_headers(array(get_string('name', 'gotomeeting'),
    get_string('date_time', 'gotomeeting'), get_string('presenter', 'gotomeeting'),
    $statusheading, get_string('manage', 'gotomeeting'),
    get_string('attendance_report', 'gotomeeting')));
$table->define_baseurl($PAGE->url);
$table->is_downloadable(true);
$table->download_buttons();
$table->show_download_buttons_at(array(TABLE_P_BOTTOM));
$table->sortable(false);
$table->pageable(true);

// Naming of the table download file
$gotomeetingmanageclassesfile = get_string('manage_classes_file', 'gotomeeting');
$gotomeetingfileheading = $gotomeetingmanageclassesfile." ".$coursenumber;
$manageclasses = get_string('gotomeeting_classes_file', 'gotomeeting');
$gotomeetingmcfilename = $manageclasses.$coursenumber;
$table->is_downloading($download, $gotomeetingmcfilename, $gotomeetingfileheading);

$table->setup();

// Required here so that the $OUTPUT and the html renders only when the page is not downloading--
if (!$table->is_downloading()) {
    echo $OUTPUT->header();

    echo $output->tabs($course, 'gotomeeting_manage');

    if (! $gotomeetings) {
        notice(get_string('nogotomeetings', 'gotomeeting'),
            new moodle_url('/course/view.php', array('id' => $course->id)));
    }

    echo $output->table_pagination($perpage, $idpaging);
    // $perpage = gotomeeting_get_table_pagination($output, 'paging');
    $totalgotomeetingrecords = count($gotomeetings);
    $table->pagesize($perpage, $totalgotomeetingrecords);

    // Sorting array to get the newest record first
    rsort($gotomeetings);
    $startingindex = $table->get_page_start();

    // Slicing the array depending upon the page size choosen by the user
    $slice = array_slice($gotomeetings, $startingindex, $perpage);
}

// Setting up the table

$resmeetings = array();

foreach ($slice as $gotomeeting) {

    if (!isset($resmeetings[$gotomeeting->gotomeeting_license_id])) {
        $gotomeetingmoodle = gotomeeting_moodle::instance_by_license($gotomeeting->gotomeeting_license_id);
        $resmeetings[$gotomeeting->gotomeeting_license_id] = $gotomeetingmoodle->get_meeting();
    }
    $resmeeting = $resmeetings[$gotomeeting->gotomeeting_license_id];

    $userid = $gotomeeting->presenter_id;
    $userfirstname = $DB->get_field_select('user', 'firstname', 'id='.$userid);
    $usersecondname = $DB->get_field_select('user', 'lastname', 'id='.$userid);
    $presentername = $userfirstname." ".$usersecondname;
    // If recording is opted for
    $gotomeetingmodulecontext = context_module::instance($gotomeeting->coursemodule);
    $newgotomeeting = $DB->get_record('gotomeeting', array('id' => $gotomeeting->id));

    $title = html_writer::link( new moodle_url('/mod/gotomeeting/view.php',
            array('id' => $gotomeeting->coursemodule)), format_string($newgotomeeting->name, true));
    $starttime = date('d-m-Y H:i:s', $newgotomeeting->gotomeeting_datetime);

    try {

        $meeting = $resmeeting->getMeeting($gotomeeting->gotomeeting_id);
        $newgotomeeting->class_status = $meeting[0]['status'];

    } catch (\Exception $e) {

        $newgotomeeting->class_status = '?';

    }

    $modurl = new moodle_url("$CFG->wwwroot/course/mod.php", array('return' => true, 'sesskey' => sesskey()));

    $editgotomeeting = $output->format_icon_link(
        new moodle_url($modurl, array('update' => $gotomeeting->coursemodule))
        , 'i/edit'
        , get_string('edit')
        , new confirm_action(get_string('editconfirm', 'gotomeeting')));

    $deletegotomeeting = $output->format_icon_link(
        new moodle_url($modurl, array('delete' => $gotomeeting->coursemodule))
        , 'i/delete'
        , get_string('delete')
        ,  new confirm_action(get_string('deleteconfirm', 'gotomeeting')));

    if (($newgotomeeting->class_status != "expired") && ($newgotomeeting->class_status != "completed")
        && ($newgotomeeting->class_status != GOTOMEETING_DELETED_GOTOMEETING)) {
                $manageclass = $editgotomeeting . $deletegotomeeting;
    } else {
        $manageclass = $OUTPUT->render($deletegotomeeting);
    }
    $gotomeetingexpired = ($newgotomeeting->class_status != 'expired');
    $gotomeetingdeletedformgotomeeting = ($newgotomeeting->class_status != GOTOMEETING_DELETED_GOTOMEETING);
    $gotomeetingupcoming = ($newgotomeeting->class_status != 'upcoming');

    if ($gotomeetingexpired && $gotomeetingupcoming && $gotomeetingdeletedformgotomeeting) {
        if (has_capability('mod/gotomeeting:view_attendance_report', $gotomeetingmodulecontext)) {
            $attendencereport = html_writer::link(
                    new moodle_url("$CFG->wwwroot/mod/gotomeeting/attendancereport.php",
                array('id' => $id, 'meetingid' => $newgotomeeting->id, 'sesskey' => sesskey())),
                    get_string('attendencereport', 'gotomeeting'));
        } else {
            $attendencereport = get_string('nocapability', 'gotomeeting');
        }
    } else {
        $attendencereport = get_string('classnotheld', 'gotomeeting');
    }

    $table->add_data(array($title, $starttime, $presentername,
        $newgotomeeting->class_status, $manageclass, $attendencereport));
}
// $table->setup();
$table->finish_output();
echo $OUTPUT->footer();
