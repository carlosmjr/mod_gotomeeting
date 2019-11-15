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
 * Page to add license.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->libdir.'/filelib.php');
require_once('forms.php');
require_once('lib.php');

global $SESSION;

$rem = optional_param ( 'rem', null, PARAM_RAW );
$edit = optional_param ( 'edit', null, PARAM_RAW );
$id = optional_param ( 'id', null, PARAM_INT );
$courseid = optional_param ( 'courseid', null, PARAM_INT );

if (!empty($courseid)) {
    $SESSION->courseid = $courseid;
} else {
    if (isset($SESSION->courseid) && !empty($SESSION->courseid)) {
        $courseid = $SESSION->courseid;
    }
}

require_login();
$context = context_user::instance($USER->id);
$PAGE->set_url("/mod/gotomeeting/addlicense.php?courseid=$courseid");
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$url = "{$CFG->wwwroot}/mod/gotomeeting/manage_licenses.php?id=$courseid&sesskey=".sesskey();
$pageurl = "{$CFG->wwwroot}/mod/gotomeeting/addlicense.php?courseid=$courseid";

$title = get_string('manage_licenses', 'gotomeeting');

$PAGE->navbar->ignore_active();
$PAGE->navbar->add( $title, $url);

$PAGE->set_title($title);
$PAGE->set_heading( $title);
$PAGE->set_cacheable( true);

if (has_capability ( 'mod/gotomeeting:manage_licenses', $context )) {

    // Delete record.
    if ($rem) {
        $data = array('id' => $id, 'deleted' => 1);
        $DB->update_record('gotomeeting_licenses', $data);
        redirect($url);
    } else {
        $mform = new add_license_form();
        // Cancelled.
        if ($mform->is_cancelled()) {
            redirect($url);
        }
        if ($data = $mform->get_data()) {
            // Update
            if ($data->id) {
                $DB->update_record('gotomeeting_licenses', $data);
            } else { // Insert.
                $data->deleted = 0;
                $data->id = $DB->insert_record('gotomeeting_licenses', $data);
            }

            // Creates the object to connect for data if needed
            $test = \mod_gotomeeting\gotomeeting_moodle::instance_by_license($data->id);

            redirect($url);
        }

        // Edit record.
        if ($edit) {

            $getlicense = $DB->get_record ( 'gotomeeting_licenses', array('id' => $id, 'deleted' => 0));

            $mform = new add_license_form(null, array('id' => $id));
            $mform->set_data( $getlicense );
        }

        echo $OUTPUT->header();

        $mform->display();

        echo $OUTPUT->footer();
    }

} else {
    redirect($CFG->wwwroot);
}