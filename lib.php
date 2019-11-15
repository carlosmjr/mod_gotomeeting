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
 * Library of interface functions and constants for module gotomeeting
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/calendar/lib.php');
require_once('locallib.php');

// require_once(__DIR__ . '/vendor/autoload.php');
/**
 * Defines the features that are supported by gotomeeting.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function gotomeeting_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the gotomeeting into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $gotomeeting An object from the form in mod_form.php
 * @param object $mform
 *
 * @return int The id of the newly inserted gotomeeting record
 */
function gotomeeting_add_instance(stdClass $gotomeeting, mod_gotomeeting_mod_form $mform = null) {
    global $CFG, $DB, $USER, $PAGE;

    $coursecontext = context_course::instance($gotomeeting->course);

    $gotomeeting->timecreated = time();

    $userid = $USER->id;

    $gotomeeting->presenter_id = $userid;

    if (0 != ($gotomeeting->groupingid)) {
        $eventtype = 'group';
    } else if (1 == $gotomeeting->course) {
        $eventtype = 'site';
    } else {
        $eventtype = 'course';
    }

    $classduration = $gotomeeting->duration;
    $title = $gotomeeting->name;
    
    $gotomeetingdatetimestart = date('Y-m-d\TH:i:s\Z ', $gotomeeting->gotomeeting_datetime);
    $gotomeetingdatetimeend = date('Y-m-d\TH:i:s\Z ', strtotime('+' . $classduration . ' minutes', $gotomeeting->gotomeeting_datetime));

    $gotomeetingmoodle = mod_gotomeeting\gotomeeting_moodle::instance_by_license($gotomeeting->gotomeeting_license_id);
    $resmeeting = $gotomeetingmoodle->get_meeting();

    // create meeting
    $data = array('subject' => $title,
        'starttime' => $gotomeetingdatetimestart,
        'endtime' => $gotomeetingdatetimeend,
        'passwordrequired' => false,
        'conferencecallinfo' => 'VoIP',
        'meetingtype' => 'Scheduled',
        'timezonekey' => ""
     );

    $return = $resmeeting->createMeeting($data);

    if (isset($return[0]) && array_key_exists('joinURL', $return[0])) {
        
        $gotomeeting->class_status = "upcoming";
        
        $gotomeeting->gotomeeting_id = $return[0]['meetingid'];
        $gotomeeting->gotomeeting_joinurl = $return[0]['joinURL'];
        $gotomeeting->gotomeeting_datetime_end = strtotime('+' . $classduration . ' minutes', $gotomeeting->gotomeeting_datetime);
        $returnid = $DB->insert_record('gotomeeting', $gotomeeting);
        $event = new stdClass();
        $event->name        = format_string($gotomeeting->name);
        $event->description = format_module_intro('gotomeeting', $gotomeeting, $gotomeeting->coursemodule);
        $event->courseid    = $gotomeeting->course;
        $event->groupid     = $gotomeeting->groupingid;
        $event->userid      = $userid;
        $event->modulename  = 'gotomeeting';
        $event->instance    = $returnid;
        $event->eventtype   = $eventtype;
        $event->timestart   = $gotomeeting->gotomeeting_datetime;
        $event->timeduration = $gotomeeting->duration;
        calendar_event::create($event);

        $params = array(
            'objectid' => $returnid, // $gotomeetingclass_id,
                'relateduserid' => $USER->id,
                'courseid' => $gotomeeting->course,
                'context' => $coursecontext,
                'other' => array(
                        'error' => ''
                )
        );
        $event = \mod_gotomeeting\event\gotomeeting_classadd::create($params);
        $event->trigger();

        return $returnid;
    } else {

        $errormsg = get_string('error_license_gotomeeting', 'gotomeeting');
        $params = array(
            'objectid' => $gotomeeting->timecreated,
            'relateduserid' => $USER->id,
            'courseid' => $gotomeeting->course,
            'context' => $coursecontext,
            'other' => array(
            'error' => $errormsg
            )
        );
        $eventee = \mod_gotomeeting\event\gotomeeting_classadd::create($params);
        $eventee->trigger();
        // $url = "{$CFG->wwwroot}/mod/gotomeeting/error_message.php?message=".$errormsg."&courseid=".$gotomeeting->course;
        redirect($PAGE->url, $errormsg, null, \core\output\notification::NOTIFY_ERROR);
    }
}

/**
 * Updates an instance of the gotomeeting in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $gotomeeting An object from the form in mod_form.php
 *
 * @return boolean Success/Fail
 */
function gotomeeting_update_instance($gotomeeting) {
    global $CFG, $DB, $USER;

    $id = optional_param('update', 0, PARAM_INT); // course_module ID

    $cm           = get_coursemodule_from_id('gotomeeting', $id, 0, false, MUST_EXIST);
    // $course       = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $gotomeetingrecord = $DB->get_record('gotomeeting', array('id' => $cm->instance), '*', MUST_EXIST);

    // $class_id = $gotomeeting->class_id;
    $gotomeeting->lasteditorid = $USER->id;

    $gotomeetingmoodle = mod_gotomeeting\gotomeeting_moodle::instance_by_license($gotomeeting->gotomeeting_license_id);
    $resmeeting = $gotomeetingmoodle->get_meeting();

    $meeting = $resmeeting->getMeeting($gotomeetingrecord->gotomeeting_id);

    $status = $meeting[0]['status'];

    $classstatus = ltrim(rtrim($status));

    if (($classstatus) != 'expired') {

        $gotomeeting->timemodified = time();
        $gotomeeting->id = $gotomeeting->instance;

        $userid = $DB->get_field('gotomeeting', 'presenter_id', array('id' => $gotomeeting->id));
        
        $gotomeeting->presenter_id = $userid;

        if (0 != ($gotomeeting->groupingid)) {
            $eventtype = 'group';
        } else if (1 == $gotomeeting->course) {
            $eventtype = 'site';
        } else {
            $eventtype = 'course';
        }

        $classduration = $gotomeeting->duration;
        $title = $gotomeeting->name;
        
        $gotomeetingdatetimestart = date('Y-m-d\TH:i:s\Z ', $gotomeeting->gotomeeting_datetime);
        $gotomeetingdatetimeend = date('Y-m-d\TH:i:s\Z ', strtotime('+' . $classduration . ' minutes', $gotomeeting->gotomeeting_datetime));

        $errormsg = "";
        
        // update meeting
        $data = array('subject' => $title,
            'starttime' => $gotomeetingdatetimestart,
            'endtime' => $gotomeetingdatetimeend,
            'passwordrequired' => false,
            'conferencecallinfo' => 'VoIP',
            'meetingtype' => 'Scheduled', // 'immediate',
            'timezonekey' => ''
        );

        $updateresult = $resmeeting->updateMeeting($gotomeetingrecord->gotomeeting_id, $data);

        $gotomeeting->class_timezone = $gotomeeting->gotomeeting_timezone;
        $DB->update_record('gotomeeting', $gotomeeting);
        $event = new stdClass();
        $event->id = $DB->get_field('event', 'id',
                array('modulename' => 'gotomeeting', 'instance' => $gotomeeting->id));

        if ($event->id) {
            $event->name        = format_string($gotomeeting->name);
            $event->description = format_module_intro('gotomeeting', $gotomeeting, $gotomeeting->coursemodule);
            $event->courseid    = $gotomeeting->course;
            $event->groupid     = $gotomeeting->groupingid;
            $event->userid      = $userid;
            $event->modulename  = 'gotomeeting';
            $event->eventtype   = $eventtype;
            $event->timestart   = $gotomeeting->gotomeeting_datetime;
            $event->timeduration = $gotomeeting->duration;
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event);
            return true;
        } else {
                print_error($errormsg);
        }

    } else {
        print_error(get_string('expired_session', 'gotomeeting'));
    }
}
/**
 * Removes an instance of the gotomeeting from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function gotomeeting_delete_instance($id) {
    global $DB, $CFG;

    if (! $gotomeeting = $DB->get_record('gotomeeting', array('id' => $id))) {
        return false;
    }
    // Delete any dependent records here #
    if (! $events = $DB->get_records('event',
            array('modulename' => 'gotomeeting', 'instance' => $gotomeeting->id))) {
        return false;
    }
    foreach ($events as $event) {
        $event = calendar_event::load($event);
        $event->delete();
    }
    if (! $DB->delete_records('gotomeeting', array('id' => $gotomeeting->id))) {
        return false;
    }

    $gotomeetingmoodle = mod_gotomeeting\gotomeeting_moodle::instance_by_license($gotomeeting->gotomeeting_license_id);
    $resmeeting = $gotomeetingmoodle->get_meeting();

    $meetingdeleted = $resmeeting->deleteMeeting($gotomeeting->gotomeeting_id);

    return true;
}

/**
 * Removes an instance of the gotomeeting from the course when course is deleted
 *
 * Called by moodle itself to delete the activities regarding the
 * gotomeeting in the course.
 *
 * @param int $course Id of the module instance
 * @param string $feedback feedback of the process.
 * @return boolean Success/Failure
 */
function gotomeeting_delete_course($course, $feedback=true) {
    return true;
}
/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function gotomeeting_cron () {
    return true;
}

