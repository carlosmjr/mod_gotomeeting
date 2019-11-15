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
 * Privacy Subsystem implementation for mod_gotomeeting.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_gotomeeting\privacy;

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem implementation for mod_gotomeeting.
 *
 * @copyright  2019 iTopTraining
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin currently implements the original plugin_provider interface.
    \core_privacy\local\request\plugin\provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   collection  $items  The collection to add metadata to.
     * @return  collection  The array of metadata
     */
    public static function get_metadata(collection $items) : collection {
        // The table 'gotomeeting' stores a record for each gotomeeting.
        // It does not contain user personal data, but data is returned from it for contextual requirements.

        // The table 'gotomeeting_licenses' contains data about connections with GoToMeeting.
        // It does not contain any user identifying data and does not need a mapping.

        // The 'gotomeeting_attendace_report' table stores a record for each student who entered a GoToMeeting room
        // It contains a userid which links to the Moodle user, complete name and email.
        $items->add_database_table('gotomeeting_attendace_report', [
                'attendee_name' => 'privacy:metadata:gotomeeting_attendace_report:attendee_name',
                'gotomeetingid' => 'privacy:metadata:gotomeeting_attendace_report:gotomeetingid',
                'join_time' => 'privacy:metadata:gotomeeting_attendace_report:join_time',
                'leave_time' => 'privacy:metadata:gotomeeting_attendace_report:leave_time',
                'duration' => 'privacy:metadata:gotomeeting_attendace_report:duration',
                'attendee_email' => 'privacy:metadata:gotomeeting_attendace_report:attendee_email',
                'meeting_start_time' => 'privacy:metadata:gotomeeting_attendace_report:meeting_start_time',
                'attempt' => 'privacy:metadata:gotomeeting_attendace_report:meeting_end_time',
            ], 'privacy:metadata:gotomeeting_attendace_report');

        return $items;
    }

    /**
     * Get the list of contexts where the specified user has attendance a GoToMeeting
     *
     * @param   int             $userid The user to search.
     * @return  contextlist     $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $resultset = new contextlist();

        // Users who attendance GoToMeeting.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {gotomeeting} gtm ON gtm.id = cm.instance
                  JOIN {gotomeeting_attendace_report} gtmar ON gtmar.gotomeetingid = gtm.gotomeeting_id
                 WHERE gtmar.userid = :userid";
        $params = ['contextlevel' => CONTEXT_MODULE, 'modname' => 'gotomeeting', 'userid' => $userid];
        $resultset->add_from_sql($sql, $params);

        return $resultset;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $params = [
            'cmid'    => $context->instanceid,
            'modname' => 'gotomeeting',
        ];

        // Users who attendance GoToMeeting.
        $sql = "SELECT qa.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {gotomeeting} gtm ON gtm.id = cm.instance
                  JOIN {gotomeeting_attendace_report} gtmar ON gtmar.gotomeetingid = gtm.gotomeeting_id
                 WHERE cm.id = :cmid";
        $userlist->add_from_sql('userid', $sql, $params);

    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (!count($contextlist)) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;
        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT gtm.*, c.id AS contextid
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {gotomeeting} gtm ON gtm.id = cm.instance
          --   LEFT JOIN {gotomeeting_attendace_report} gtmar ON gtmar.gotomeetingid = gtm.gotomeeting_id AND gtmar.userid = :userid
                 WHERE c.id {$contextsql}";

        $params = ['contextlevel' => CONTEXT_MODULE, 'modname' => 'gotomeeting', 'userid' => $userid];
        $params += $contextparams;

        // Fetch the individual gotomeetings.
        $gotomeetings = $DB->get_recordset_sql($sql, $params);
        $mappings = [];
        foreach ($gotomeetings as $gotomeeting) {
            $mappings[$gotomeeting->id] = $gotomeeting->contextid;
            $context = \context::instance_by_id($gotomeeting->contextid);

            $data = \core_privacy\local\request\helper::get_context_data($context, $user);
            writer::with_context($context)->export_data([], $data);
            \core_privacy\local\request\helper::export_context_files($context, $user);

        }
        $gotomeetings->close();

        if (!empty($mappings)) {
            // Store all attendace data for this meeting.
            static::export_attendace_data($userid, $mappings);
        }

    }

    /**
     * Store all information about all attendaces.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   array       $mappings A list of mappings from "moodle gotomeeting id" => contextid.
     * @return  array       Which gotomeetings had data written for them.
     */
    protected static function export_attendace_data(int $userid, array $mappings) {
        global $DB;

        // Find all of the attendaces.
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($mappings), SQL_PARAMS_NAMED);

        $sql = "SELECT gtmar.*, gtm.name AS roomname, gtm.id as gtmid, gtm.presenter_id
                  FROM {gotomeeting} gtm
            INNER JOIN {gotomeeting_attendace_report} gtmar ON gtmar.gotomeetingid = gtm.gotomeeting_id AND gtmar.userid = :userid
                 WHERE gtm.id {$insql}";

        $params = ['userid' => $userid];
        $params += $inparams;

        // Keep track of the attendaces which have data.
        $withdata = [];

        $attendaces = $DB->get_recordset_sql($sql, $params);

        foreach ($attendaces as $attendace) {
            // No need to take timestart into account as the user has some involvement already.
            // Ignore discussion timeend as it should not block access to user data.
            $withdata[$attendace->gtmid] = true;
            $context = \context::instance_by_id($mappings[$attendace->gtmid]);

            $attendacedata = (object) [
                'gotomeeting' => format_string($attendace->roomname, true),
                'fullname' => format_string($attendace->attendee_name, true),
                'email' => format_string($attendace->attendee_email, true),
                'join_time' => transform::datetime($attendace->join_time),
                'leave_time' => transform::datetime($attendace->leave_time),
                'creator_was_you' => transform::yesno($attendace->presenter_id == $userid),
            ];

            writer::with_context($context)->export_data(static::get_attendace_area($attendace), $attendacedata);
        }

        $attendaces->close();

        return $withdata;
    }

    /**
     * Get the discussion part of the subcontext.
     *
     * @param   \stdClass   $discussion The discussion
     * @return  array
     */
    protected static function get_attendace_area(\stdClass $attendace) : Array {
        $pathparts = [];

        $parts = [
            $attendace->id,
            $attendace->roomname,
        ];

        $discussionname = implode('-', $parts);

        $pathparts[] = get_string('attendance_report', 'mod_gotomeeting');
        $pathparts[] = $discussionname;

        return $pathparts;
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   \context                 $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            // Only gotomeeting module will be handled.
            return;
        }

        $cm = get_coursemodule_from_id('gotomeeting', $context->instanceid);
        if (!$cm) {
            // Only gotomeeting module will be handled.
            return;
        }

        $gotomeeting = $DB->get_record('gotomeeting', ['id' => $context->instanceid]);
        if (!$gotomeeting) {
            return;
        }
        $DB->delete_records('gotomeeting_attendace_report', ['gotomeetingid' => $gotomeeting->gotomeeting_id]);

    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();

        foreach ($contextlist as $context) {

            if ($context->contextlevel != CONTEXT_MODULE) {
                // Only gotomeeting module will be handled.
                continue;
            }

            $cm = get_coursemodule_from_id('gotomeeting', $context->instanceid);
            if (!$cm) {
                // Only gotomeeting module will be handled.
                continue;
            }

            $gotomeeting = $DB->get_record('gotomeeting', ['id' => $cm->instance]);
            if (!$gotomeeting) {
                return;
            }
            $DB->delete_records('gotomeeting_attendace_report',
                ['gotomeetingid' => $gotomeeting->gotomeeting_id, 'userid' => $user->id]);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_MODULE) {
            // Only gotomeeting module will be handled.
            return;
        }

        $cm = get_coursemodule_from_id('gotomeeting', $context->instanceid);
        if (!$cm) {
            // Only gotomeeting module will be handled.
            return;
        }

        $gotomeeting = $DB->get_record('gotomeeting', ['id' => $context->instanceid]);
        if (!$gotomeeting) {
            return;
        }

        $userids = $userlist->get_userids();

        foreach ($userids as $userid) {
            $DB->delete_records('gotomeeting_attendace_report',
                ['gotomeetingid' => $gotomeeting->gotomeeting_id, 'userid' => $userid]);
        }
    }

}
