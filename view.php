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
 * This page is the entry page into the gotomeeting UI.
 * Displays information about a particular instance of gotomeeting
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('GOTOMEETING_HEIGHT', 786);
define('GOTOMEETING_WIDTH', 1024);
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
global $CFG, $USER, $OUTPUT, $PAGE;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID
// gotomeeting instance ID - it should be named as the first character of the module
$w  = optional_param('w', 0, PARAM_INT);
if ($id) {
    $cm         = get_coursemodule_from_id('gotomeeting', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $gotomeeting  = $DB->get_record('gotomeeting', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($w) {
    $gotomeeting  = $DB->get_record('gotomeeting', array('id' => $w), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $gotomeeting->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('gotomeeting', $gotomeeting->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$params = array(
    'objectid' => $gotomeeting->id,
    'relateduserid' => $USER->id,
    'courseid' => $gotomeeting->course,
    'context' => $context,
    'other' => array(
        'error' => ''
    )
);
$event = \mod_gotomeeting\event\gotomeeting_classdetail::create($params);
$event->trigger();

// Print the page header

$PAGE->set_url('/mod/gotomeeting/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($gotomeeting->name));
$pagetitle = get_string('gotomeeting_class', 'gotomeeting');
$pagetitlename = $pagetitle." ".$gotomeeting->name;
$PAGE->set_heading(format_string($pagetitlename));
$PAGE->set_context($context);

$canaddinstance = has_capability('mod/gotomeeting:addinstance', $context);
$canstartmeeting = has_capability('mod/gotomeeting:start_meeting', $context);

// Print output
echo $OUTPUT->header();

$output = $PAGE->get_renderer('mod_gotomeeting');
echo $output->tabs($course, 'gotomeeting_view', $cm);

// Get details of the class

$gotomeetingmoodle = mod_gotomeeting\gotomeeting_moodle::instance_by_license($gotomeeting->gotomeeting_license_id);
$resmeeting = $gotomeetingmoodle->get_meeting();

try {
    $meeting = $resmeeting->getMeeting($gotomeeting->gotomeeting_id);
} catch (\Exception $e) {
    print_error(get_string('not_found', 'gotomeeting'));
}

// Actual description table starts form here

$viewtable = new html_table();

$viewtable->id = 'gotomeeting_tbl_view';
$viewtable->head = array($gotomeeting->name);
$viewtable->headspan = array(2, 1);

$presenterid = $gotomeeting->presenter_id;
$status = $meeting[0]['status'];

if ($gotomeeting->class_status != $status) {
    $updates = new stdClass(); // just enough data for updating the submission
    $updates->id = $gotomeeting->id;
    $updates->class_status = $status;
    $DB->update_record('gotomeeting', $updates);
}

$timezone = $gotomeeting->class_timezone;
$starttime = date('d-m-Y H:i:s', strtotime(substr($meeting[0]['startTime'], 0, 19).' '.$timezone));

if ($presenterid == $USER->id) {
    $presenternamedisplay = get_string('teacher_you', 'gotomeeting');
} else {
    $user = $DB->get_record('user', ['id' => $presenterid]);
    $presenternamedisplay = fullname($user); // $userfirstname." ".$usersecondname;
}

$viewtable->data = array(
    array(get_string('presenter_name', 'gotomeeting'), $presenternamedisplay),

    array(get_string('status_of_class', 'gotomeeting'), $status),

    array(get_string('gotomeeting_start_time', 'gotomeeting'), $starttime),

    array(get_string('gotomeeting_class_timezone', 'gotomeeting'), $timezone),
    array(get_string('gotomeeting_duration', 'gotomeeting'), $meeting[0]['duration'])
);
echo html_writer::table($viewtable);

// Row to make button visible
$buttonrow = new html_table_row();

$statusmsg = ltrim(rtrim($status));

if ($presenterid != $USER->id ) {
    $attendeeurl = new moodle_url("$CFG->wwwroot/mod/gotomeeting/joinmeeting.php",
                array('meeting_id' => $gotomeeting->gotomeeting_id, 'cm_id' => $cm->id, 'sesskey' => sesskey()));

    if (!empty($attendeeurl)) {
        $classlink = $attendeeurl;
        $gotomeetinglinkname = get_string('join_class', 'gotomeeting');
    } else if (!empty ($errormsg)) {
        $classlink = '';
    }
    if (!empty($classlink)) {
        $classurl = new moodle_url($classlink);
        $action = new popup_action('click', $classurl, "class_name",
                array('height' => GOTOMEETING_HEIGHT, 'width' => GOTOMEETING_WIDTH));
        $join = $OUTPUT->action_link($classurl, $gotomeetinglinkname, $action,
                array('title' => get_string('modulename', 'gotomeeting')));
    } else {
        $join = get_string('unable_to_get_url', 'gotomeeting');
    }
}

if ($statusmsg == 'INACTIVE') {

    if ($presenterid == $USER->id || $canstartmeeting) {
        $presenterurl = new moodle_url("$CFG->wwwroot/mod/gotomeeting/startmeeting.php",
                                        array('meeting_id' => $gotomeeting->gotomeeting_id, 'cm_id' => $cm->id, 'sesskey' => sesskey()));
        if (!empty($presenterurl)) {
            $classlink = $presenterurl;
            $gotomeetinglinkname = get_string('launch_class', 'gotomeeting');
        } else {
            $classlink = '';
        }

        // Code to update/edit the class
        if ($canaddinstance) {
            $update = html_writer::link(new moodle_url("$CFG->wwwroot/course/mod.php",
                array('update' => $cm->id, 'return' => true, 'sesskey' => sesskey())),
                get_string('update_class', 'gotomeeting'));
            $updatecell = new html_table_cell($update);
        }

        // Code to delete the class
        $deleteclass = html_writer::link(
            new moodle_url("$CFG->wwwroot/course/mod.php",
            array('delete' => $cm->id, 'return' => true, 'sesskey' => sesskey())),
            get_string('delete_class', 'gotomeeting'));
        $deletecell = new html_table_cell($deleteclass);

    }
    if (!empty($classlink)) {
        $classurl = new moodle_url($classlink);
        $action = new popup_action('click', $classurl, "class_name",
                array('height' => GOTOMEETING_HEIGHT, 'width' => GOTOMEETING_WIDTH));
        $join = $OUTPUT->action_link($classurl, $gotomeetinglinkname, $action,
                array('title' => get_string('modulename', 'gotomeeting')));
    } else {
        $join = get_string('unable_to_get_url', 'gotomeeting');
    }

} else {
    $notheldcell = new html_table_cell(get_string('viewclassnotheld', 'gotomeeting'));
}

$joincell = new html_table_cell($join);
$joincell->attributes['class'] = 'gotomeeting_actions';

if ($canstartmeeting) {
    $separatorcell = new html_table_cell('|');

    $deleteclass = html_writer::link(
        new moodle_url("$CFG->wwwroot/course/mod.php",
            array('delete' => $cm->id, 'return' => true, 'sesskey' => sesskey())),
        get_string('delete_class', 'gotomeeting'));
    $deletecell = new html_table_cell($deleteclass);
}

$array = array();
if ($statusmsg == 'INACTIVE' || $statusmsg == 'ACTIVE') {
    if (isset($joincell)) {
        array_push($array, $joincell);
    }
    if (isset($separatorcell)) {
        array_push($array, $separatorcell);
    }
    if (isset($updatecell)) {
        array_push($array, $updatecell);
    }
    if (isset($deletecell)) {
        array_push($array, $deletecell);
    }

} else if ($statusmsg == GOTOMEETING_DELETED_GOTOMEETING) {
    if (isset($deletecell)) {
        array_push($array, $deletecell);
    }
} else {
    if (isset($notheldcell)) {
        array_push($array, $notheldcell);
    }
    if (isset($separatorcell)) {
        array_push($array, $separatorcell);
    }
    if (isset($deletecell)) {
        array_push($array, $deletecell);
    }

}
$buttonrow->cells = $array;

$buttontable = new html_table();
$buttontable->id = 'gotomeeting_tbl_actions';
$buttontable->data = array($buttonrow);
echo html_writer::table($buttontable);
if ($gotomeeting->intro) {
    // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->heading(get_string('discription', 'gotomeeting'));
    echo $OUTPUT->box(format_module_intro('gotomeeting', $gotomeeting, $cm->id), 'generalbox mod_introbox', 'gotomeetingintro');
}

echo $OUTPUT->footer();
