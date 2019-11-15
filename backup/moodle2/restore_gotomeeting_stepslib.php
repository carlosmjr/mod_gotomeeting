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
 * Define all the restore steps.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one gotomeeting activity
 */
class restore_gotomeeting_activity_structure_step extends restore_activity_structure_step {
    /**
     * Define the structure for restoring gotomeeting.
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('gotomeeting', '/activity/gotomeeting');
        $paths[] = new restore_path_element('event', '/activity/gotomeeting/event');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }
    /**
     * Processing gotomeeting classes.
     *
     * @param string $data
     */
    protected function process_gotomeeting($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the gotomeeting record
        $newitemid = $DB->insert_record('gotomeeting', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }
    /**
     * Processing events related to gotomeeting.
     *
     * @param string $data
     */
    protected function process_event($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->instance = $this->get_new_parentid('gotomeeting');

        $newitemid = $DB->insert_record('event', $data);
        $this->set_mapping('event', $oldid, $newitemid);
    }

    /**
     * Executing activities.
     */
    protected function after_execute() {
        // Add gotomeeting related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_gotomeeting', 'intro', null);
    }
}