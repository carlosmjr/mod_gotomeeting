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
 * Redirects to the GoToMeeting url
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
global $CFG, $USER, $OUTPUT, $PAGE;

$meetingid = optional_param('meeting_id', 0, PARAM_INT); // gotomeeting ID
$cmid = optional_param('cm_id', 0, PARAM_INT); //
require_login();

$context = context_module::instance($cmid);

$gotomeeting = $DB->get_record('gotomeeting', array('gotomeeting_id' => $meetingid), '*', MUST_EXIST);

$coursecontext = context_course::instance($gotomeeting->course);

$params = array(
        'objectid' => $gotomeeting->id,
        'relateduserid' => $USER->id,
        'courseid' => $gotomeeting->course,
        'context' => $coursecontext,
        'other' => array(
                'error' => ''
        )
);

$event = \mod_gotomeeting\event\gotomeeting_classjoin::create($params);
$event->trigger();

if (!empty($gotomeeting->gotomeeting_joinurl)) {
    redirect($gotomeeting->gotomeeting_joinurl);
}