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
 * Defines backup_gotomeeting_activity_task class.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Because it exists (must)
require_once($CFG->dirroot . '/mod/gotomeeting/backup/moodle2/backup_gotomeeting_stepslib.php');
// Because it exists (optional)
require_once($CFG->dirroot . '/mod/gotomeeting/backup/moodle2/backup_gotomeeting_settingslib.php');

/**
 * class used to backup activity related to gotomeeting.
 *
 * gotomeeting backup task that provides all the settings and steps
 * to perform one complete backup of the activity.
 *
 * @author     Pablo
 */
class backup_gotomeeting_activity_task extends backup_activity_task {
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
        $this->add_step(new backup_gotomeeting_activity_structure_step('gotomeeting_structure', 'gotomeeting.xml'));
    }
    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     *
     * @param string $content
     */
    static public function encode_content_links($content) {
         global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of gotomeeting
        $search = "/(".$base."\/mod\/gotomeeting\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@GOTOMEETINGINDEX*$2@$', $content);

        // Link to gotomeeting view by moduleid
        $search = "/(".$base."\/mod\/gotomeeting\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@GOTOMEETINGVIEWBYID*$2@$', $content);

        // Link to gotomeeting content by moduleid
        $search = "/(".$base."\/mod\/gotomeeting\/content.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@GOTOMEETINGCONTENT*$2@$', $content);
        return $content;
    }
}