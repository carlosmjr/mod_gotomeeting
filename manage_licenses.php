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
 * List of GoToMeting licenses
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->libdir.'/tablelib.php');

global $DB, $CFG, $PAGE;

// Parameter needed
$id = required_param('id', PARAM_INT);   // Course
confirm_sesskey();

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course->id);
$coursecontext = context_course::instance($course->id);

$PAGE->set_url('/mod/gotomeeting/manage_licenses.php',
        array('id' => $id, 'sesskey' => sesskey()));
$pagetitle = format_string(get_string('manage_licenses', 'gotomeeting'));
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('incourse');

// Creation of table starts
$table = new flexible_table('managelicenses');
$table->define_columns(array('name', /* 'email', 'password', */ 'consumer_key', 'manage'));
$table->column_style_all('text-align', 'left');
$table->define_headers(array(get_string('license_name', 'gotomeeting'),
        // get_string('license_email', 'gotomeeting'), get_string('license_password', 'gotomeeting'),
        get_string('license_consumer_key', 'gotomeeting'),
        get_string('manage', 'gotomeeting')
));
$table->define_baseurl($PAGE->url);
$table->is_downloadable(false);
// $table->download_buttons();
// $table->show_download_buttons_at(array(TABLE_P_BOTTOM));
$table->sortable(false);
$table->pageable(true);

echo $OUTPUT->header();

$output = $PAGE->get_renderer('mod_gotomeeting');
echo $output->tabs($course, 'gotomeeting_licenses');

if (has_capability('mod/gotomeeting:manage_licenses', $coursecontext)) {
    $gotomeetinglicenses = $DB->get_records('gotomeeting_licenses', array('deleted' => 0));

    $table->setup();

    foreach ($gotomeetinglicenses as $license) {

        $editurl = new moodle_url("$CFG->wwwroot/mod/gotomeeting/addlicense.php",
            array('edit' => true, 'id' => $license->id, 'courseid' => $course->id, 'sesskey' => sesskey()));
        $editgotomeeting = $output->format_icon_link(
            $editurl
            , 'i/edit'
            , get_string('edit')
            , new confirm_action(get_string('editconfirm', 'gotomeeting')));

        $deleteurl = new moodle_url("$CFG->wwwroot/mod/gotomeeting/addlicense.php",
            array('rem' => true, 'id' => $license->id, 'courseid' => $course->id, 'sesskey' => sesskey()));
        $deletegotomeeting = $output->format_icon_link(
            $deleteurl
            , 'i/delete'
            , get_string('delete')
            , new confirm_action(get_string('license_deleteconfirm', 'gotomeeting')));

        $manageclass = $editgotomeeting . $deletegotomeeting;

        $table->add_data(array($license->name, $license->consumer_key, $manageclass));
    }

    $table->setup();
    $table->finish_output();

    echo $output->link_add_license();

} else {
    print_error('donthave_permission', 'gotomeeting');
}

echo $OUTPUT->footer();
