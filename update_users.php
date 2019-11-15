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
 * List of users reference that need to e updated
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");

global $CFG, $DB;

$id = required_param('id', PARAM_INT); // Courseid

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

if (empty($course)) {
    redirect($CFG->wwwroot);
}

require_login($course->id);

$context = context_course::instance( $course->id);
$output = $PAGE->get_renderer('mod_gotomeeting');
$tableid = 'resultstable';

require_capability('mod/gotomeeting:manage_users', $context);

$title = get_string('update_users', 'gotomeeting');

// 'PAGE' settings
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/mod/gotomeeting/update_users.php', array('id' => $id));
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);

$PAGE->navbar->ignore_active();
$PAGE->navbar->add( $course->fullname, new moodle_url('/course/view.php', array('id' => $course->id)));
$PAGE->navbar->add( get_string('gotomeeting', 'gotomeeting'), new moodle_url('/mod/gotomeeting/index.php', array('id' => $id)));
$PAGE->navbar->add( $title , null);

$output->add_js_update_users($course->id, $tableid);

echo $OUTPUT->header();

echo $output->tabs($course, 'gotomeeting_update_users');

// gotomeeting_print_detail_results();
echo $output->print_update_users_table($tableid);

echo $OUTPUT->footer();
