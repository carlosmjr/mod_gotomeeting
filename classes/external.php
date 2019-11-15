<?php
// This file is part of Moodle - http://moodle.org/
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
 * Gotomeeting external API
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/gotomeeting/locallib.php');

/**
 * Quiz external functions
 *
 * @package    mod_gotomeeting
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class mod_gotomeeting_external extends external_api {

    /**
     * Return the users that need to be updated
     *
     * @return string json
     */
    public static function users_updateables($courseid) {
        global $DB;

        $params = self::validate_parameters(self::users_updateables_parameters(), [
            'courseid'    => $courseid
        ]);

        $context = context_course::instance($courseid);
        require_capability('mod/gotomeeting:manage_users', $context);

        $sql = "SELECT gar.id, g.name as class
                    , CASE WHEN u.firstname IS NULL THEN concat(gar.attendee_name, ' ()') ELSE concat(gar.attendee_name, ' (', u.firstname , ' ' , u.lastname ,')' ) END as name
                    , gar.attendee_email as email, gar.duration as duration
                    , from_unixtime(gar.join_time) as entry, from_unixtime(gar.leave_time) as end, u.id as student
                FROM {gotomeeting_attendace_report} gar
                JOIN {gotomeeting} g ON g.gotomeeting_id = gar.gotomeetingid
                LEFT JOIN {user} u ON u.id=gar.userid
                WHERE g.course=:courseid AND (gar.userid IS NULL OR gar.updated=1) AND gar.duration > 0";

        $gotomeetingsessions = $DB->get_records_sql($sql, array('courseid' => $params['courseid']));

        $columns = gotomeeting_get_columns();

        $result = [];

        foreach ($gotomeetingsessions as $session) {
            $data = [];
            $data['id'] = $session->id;

            foreach ($columns as $column) {
                $resultcolumn = str_replace("\t", '', $session->$column);
                // $output .= '"'. $column .'":' . '"' . $resultcolumn . '",';
                $data[$column] = $resultcolumn;
            }
            $result[] = $data;

        }

        return $result;
    }

    /**
     * Describes the parameters for users_updateables.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function users_updateables_parameters() {
        return new external_function_parameters (
            array(
                'courseid' => new external_value(PARAM_INT, 'course id')
            )
        );
    }

    /**
     * Describes the users_updateables return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function users_updateables_returns() {

        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Module GoToMeeting ID.'),
                    'class' => new external_value(PARAM_TEXT, 'Class name.'),
                    'name' => new external_value(PARAM_TEXT, 'User name.'),
                    'email' => new external_value(PARAM_TEXT, 'User email.'),
                    'entry' => new external_value(PARAM_TEXT, 'User entry time.'),
                    'end' => new external_value(PARAM_TEXT, 'User end time.'),
                    'duration' => new external_value(PARAM_INT, 'Duration in minutes.'),
                    'student' => new external_value(PARAM_TEXT, 'Userid in moodle.'),
                )
            )
        );
    }

    /**
     * Udate information of syncronization between GoToMeeting and Moodle
     *
     * @return string json
     */
    public static function update_user($attendaceid, $userid) {
        global $DB;

        $warnings = array();
        $status = false;

        if ($userid === 0) {
            $userid = null;
        }

        $newrecord = $DB->get_record('gotomeeting_attendace_report', array('id' => $attendaceid));

        if ($newrecord && ($userid === null || $DB->record_exists('user', ['id' => $userid]))) {
            $courseid = $DB->get_field('gotomeeting', 'course', ['gotomeeting_id' => $newrecord->gotomeetingid]);
            $context = context_course::instance($courseid);
            require_capability('mod/gotomeeting:manage_users', $context);

            $newrecord->userid = $userid;
            $newrecord->updated = 1;
            $status = $DB->update_record('gotomeeting_attendace_report', $newrecord);
        } else {
            $warnings['Attendace not found'];
        }
        $result = array();
        $result['status'] = $status;
        $result['warnings'] = $warnings;
        return $result;

    }

    /**
     * Describes the parameters for update_user.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function update_user_parameters() {
        return new external_function_parameters (
            array(
                'attendaceid' => new external_value(PARAM_INT, 'GoToMeeting attendee id'),
                'userid' => new external_value(PARAM_INT, 'Moodle user id')
            )
        );
    }

    /**
     * Describes the update_user return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function update_user_returns() {

        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings(),
            )
        );
    }

}
