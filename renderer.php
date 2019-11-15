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
 * The renderer for gotomeeting module.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The renderer for the gotomeeting module.
 *
 * @copyright  2019 iTopTraining
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_gotomeeting_renderer extends plugin_renderer_base {

    /**
     * Builds the common tabs
     *
     * @param object $course info data table of course.
     * @param string $selected The id of the selected tab
     * @param \cm_info $cm course module info
     * @return string containing html data.
     */
    public function tabs($course, $selected, $cm = null) {
        global $CFG, $DB;

        $tabs = array();
        $courseid = $course->id;

        if ($cm) {
            $modulecontext = context_module::instance($cm->id);
            if (has_capability('mod/gotomeeting:manage_classes', $modulecontext)) {
                $url = new moodle_url("$CFG->wwwroot/mod/gotomeeting/view.php", array('id' => $cm->id));
                $tabs[] = new tabobject('gotomeeting_view', $url, get_string('classviewdetail', 'gotomeeting'));
            }
            if (has_capability('mod/gotomeeting:view_attendance_report', $modulecontext)) {
                $url = new moodle_url("$CFG->wwwroot/mod/gotomeeting/attendancereport.php",
                    array('id' => $courseid, 'meetingid' => $cm->instance, 'sesskey' => sesskey()));
                $tabs[] = new tabobject('gotomeeting_attendance', $url, get_string('attendance_report', 'gotomeeting'));
            }
        }

        $coursecontext = context_course::instance($courseid);

        if (has_capability('mod/gotomeeting:addinstance', $coursecontext) && $courseid > 1) {
            $url = new moodle_url("$CFG->wwwroot/course/modedit.php",
                array('add' => 'gotomeeting', 'type' => '', 'course' => $courseid, 'section' => '0', 'return' => '0'));
            $tabs[] = new tabobject('gotomeeting_addinstance', $url, get_string('schedule_class', 'gotomeeting'));
        }

        if (has_capability('mod/gotomeeting:manage_classes', $coursecontext) && $courseid > 1) {
            $url = new moodle_url("$CFG->wwwroot/mod/gotomeeting/index.php", array('id' => $courseid, 'sesskey' => sesskey()));
            $tabs[] = new tabobject('gotomeeting_manage', $url, get_string('manage_classes', 'gotomeeting'));
        }

        if (has_capability('mod/gotomeeting:manage_licenses', $coursecontext)) {
            $url = new moodle_url("$CFG->wwwroot/mod/gotomeeting/manage_licenses.php",
                array('id' => $courseid, 'sesskey' => sesskey()));
            $tabs[] = new tabobject('gotomeeting_licenses', $url, get_string('manage_licenses', 'gotomeeting'));
        }

        if (has_capability('mod/gotomeeting:manage_users', $coursecontext)) {
            $sql = "SELECT gar.*
                  FROM {gotomeeting_attendace_report} gar
                  JOIN {gotomeeting} g ON g.gotomeeting_id = gar.gotomeetingid
                 WHERE g.course=:courseid AND (gar.userid IS NULL) AND gar.duration > 0";

            $gotomeetingsessions = $DB->get_records_sql($sql, array('courseid' => $courseid));

            if (count($gotomeetingsessions) > 0) {
                $url = new moodle_url('/mod/gotomeeting/update_users.php', array('id' => $courseid));
                $text = html_writer::tag('strong',
                    get_string('update_not_sync_users', 'gotomeeting') . ' ' . count($gotomeetingsessions));
                $tabs[] = new tabobject('gotomeeting_update_users', $url, $text);
            }
        }

        return print_tabs([$tabs], $selected, null, null, true);
    }

    /**
     * Build the button to manage icenses
     *
     * @param int $courseid if of the course.
     * @return  string
     *
     */
    public function link_licenses($courseid = 1) {
        global $OUTPUT;

        $button = html_writer::link(
            new moodle_url('/mod/gotomeeting/manage_licenses.php', ['id' => $courseid, 'sesskey' => sesskey()])
            , get_string('manage_licenses', 'gotomeeting'), ['class' => 'btn btn-primary']);
        return $OUTPUT->box($button, 'text-center');
    }

    /**
     * Build the button to form of add icense
     * @return  string
     *
     */
    public function link_add_license() {
        global $OUTPUT;

        $button = html_writer::link(
            new moodle_url('/mod/gotomeeting/addlicense.php')
            , get_string('add_license', 'gotomeeting'), ['class' => 'btn btn-primary']);
        return $OUTPUT->box($button, 'text-center');
    }

    /**
     * Get a filler icon for display in the actions column of a table.
     *
     * @param   string      $url            The URL for the icon.
     * @param   string      $icon           The icon identifier.
     * @param   string      $alt            The alt text for the icon.
     * @param   string      $iconcomponent  The icon component.
     * @param   array       $options        Display options.
     * @return  string
     */
    public function format_icon_link($url, $icon, $alt, $action = null, $iconcomponent = 'moodle', $options = array()) {

        return $this->action_icon(
            $url,
            new \pix_icon($icon, $alt, $iconcomponent, ['title' => $alt]),
            $action,
            $options
            );
    }

    /**
     * Get selector to paginate table.
     *
     * @param   int $selected       Element selected.
     * @param   string $name        Name of selector.
     * @return  string html selector
     */
    public function table_pagination($selected = null, $name = 'paging', $url = null) {
        if (!$url) {
            global $PAGE;
            $url = $PAGE->url;
        }

        $pagingoption = new single_select($PAGE->url, $name,
            array('5' => '5', '10' => '10', '15' => '15', '20' => '20'), $selected);

        $pagingoption->label = get_string('per_page_content', 'gotomeeting');
        return $this->render($pagingoption);
    }

    /**
     * adds js needed for updateable users.
     *
     * @param   int $courseid  of the course.
     * @param   int $tableid id of the html table.
     * @return  \moodle_page $page page where include css and js
     */
    public function add_js_update_users($courseid, $tableid, \moodle_page $page = null) {
        global $PAGE, $DB;
        if (!$page) {
            $page = $PAGE;
        }

        $page->requires->css('/mod/gotomeeting/css/jquery.dataTables.min.css');

        $context = context_course::instance($courseid);

        $studentroleid = $DB->get_field ( 'role', 'id', array (
            'shortname' => 'student'
        ) );
        $users = get_role_users ( $studentroleid, $context, false, 'u.id, ' . get_all_user_name_fields(true, 'u'), 'u.id ASC' );

        $options = array();
        foreach ($users as $user) {
            // $options[] = '{ label: "' . fullname($user) . '", value: "'.$user->id. '" }';
            $options[$user->id] = fullname($user);
        }

        $data = array();
        $data = [
            'selector' => '#' . $tableid
            , 'course' => $courseid
            , 'optionsusers' => $options
            , 'url' => $page->url->out()
            , 'urlajax' => (new moodle_url('/mod/gotomeeting/ajax/data.php'))->out()
        ];

        $page->requires->string_for_js('thousandssep', 'langconfig');
        $page->requires->strings_for_js(
            array(
                'datatables_sortascending',
                'datatables_sortdescending',
                'datatables_first',
                'datatables_last',
                'datatables_next',
                'datatables_previous',
                'datatables_emptytable',
                'datatables_info',
                'datatables_infoempty',
                'datatables_infofiltered',
                'datatables_lengthmenu',
                'datatables_loadingrecords',
                'datatables_processing',
                'datatables_search',
                'datatables_zerorecords',
                'datatables_all',
            ),
            'mod_gotomeeting');

            $page->requires->js_call_amd('mod_gotomeeting/gotomeeting', 'add_datatables_upusers', array($data));
    }

    /**
     * renders the table structure of updatable users.
     *
     * @param   int $tableid id of the html table.
     */
    public function print_update_users_table($tableid) {
        global $CFG, $DB, $OUTPUT;

        // obtain results

        // $columns = gotomeeting_get_headers();

        $data = array('columns' => gotomeeting_get_headers(), 'tabelement' => $tableid);
        return $this->render_from_template('mod_gotomeeting/update_users_table', $data);

        // return $output;

    }

}
