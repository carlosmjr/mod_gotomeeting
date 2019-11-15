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
 * Backup activities required for backup of gotomeeting classes and content.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define all the backup steps that will be used by the backup_gotomeeting_activity_task
 *
 */
class backup_gotomeeting_activity_structure_step extends backup_activity_structure_step {
    /**
     * Function describes the structure of a backup file.
     *
     * @return string
     */
    protected function define_structure() {
        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $gotomeeting = new backup_nested_element('gotomeeting', array('id'), array(
            /*'insescod', 'class_id', */'name', 'intro', 'introformat',
            'gotomeeting_datetime', 'gotomeeting_datetime_end', 'class_timezone', 'timecreated', 'timemodified', 'duration',
            'presenter_id', 'lasteditorid',
            'class_status', 'gotomeeting_id', 'gotomeeting_joinurl', 'gotomeeting_license_id'));
        // Build the tree
        $event = new backup_nested_element('event');
        $event = new backup_nested_element('event', array('id'), array(
            'name', 'description', 'format', 'courseid', 'groupid', 'userid',
            'repeatid', 'modulename', 'instance', 'eventtype', 'timestart',
            'timeduration', 'visible', 'uuid', 'sequence', 'timemodified'));

        $gotomeeting->add_child($event);

        // Define sources
        $gotomeeting->set_source_table('gotomeeting', array('id' => backup::VAR_ACTIVITYID));
        $event->set_source_sql('SELECT * FROM {event} WHERE modulename = "gotomeeting" AND instance = ?',
                               array(backup::VAR_ACTIVITYID));

        // Define file annotations
        $gotomeeting->annotate_files('mod_gotomeeting', 'intro', null);
        // Return the root element (gotomeeting), wrapped into standard activity structure
        return $this->prepare_activity_structure($gotomeeting);
    }
}