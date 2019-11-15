<?php
// This file is part of gotomeeting
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
 * Restore activities required for backup of gotomeeting classes and content.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Because it exists (must)
require_once($CFG->dirroot . '/mod/gotomeeting/backup/moodle2/restore_gotomeeting_stepslib.php');

/**
 * This provides all the settings and steps to perform one complete restore of the activity.
 *
 * @author     Jerónimo Cernuda
 */
class restore_gotomeeting_activity_task extends restore_activity_task {
    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }
    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // gotomeeting only has one structure step
        $this->add_step(new restore_gotomeeting_activity_structure_step('gotomeeting_structure', 'gotomeeting.xml'));
    }
    /**
     * Define (add) contents for this activity.
     *
     * @return string $contents.
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('gotomeeting', array('intro'), 'gotomeeting');

        return $contents;
    }
    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('GOTOMEETINGVIEWBYID', '/mod/gotomeeting/view.php?id=$1',
                                           'course_module');
        $rules[] = new restore_decode_rule('GOTOMEETINGINDEX', '/mod/gotomeeting/index.php?id=$1',
                                           'course');
        $rules[] = new restore_decode_rule('GOTOMEETINGCONTENT', '/mod/gotomeeting/content.php?id=$1',
                                           'course');

        return $rules;
    }
    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * gotomeeting logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();
        $rules[] = new restore_log_rule('gotomeeting', 'add', 'view.php?id={course_module}', '{gotomeeting}');
        $rules[] = new restore_log_rule('gotomeeting', 'update',
                                        'view.php?id={course_module}', '{gotomeeting}');
        $rules[] = new restore_log_rule('gotomeeting', 'view', 'view.php?id={course_module}', '{gotomeeting}');
        return $rules;
    }
    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        // Fix old wrong uses (missing extension)
        $rules[] = new restore_log_rule('gotomeeting', 'view all', 'index?id={course}', null,
                                        null, null, 'index.php?id={course}');
        $rules[] = new restore_log_rule('gotomeeting', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}