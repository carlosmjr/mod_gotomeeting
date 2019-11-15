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
 * The main gotomeeting configuration form
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
define('GOTOMEETING_ALLOWED_DIFFRENCE', 300);
define('GOTOMEETING_MINIMUM_DURATION', 30);
define('GOTOMEETING_MAXIMUM_DURATION', 300);
global $CFG;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once('locallib.php');
// require_once('lib.php');
require_once($CFG->dirroot.'/lib/dml/moodle_database.php');

/**
 * The main gotomeeting configuration class.
 *
 * Module instance settings form. This class inherits the moodleform_mod class to
 * create the moodle form for gotomeeting.
 * @author     Pablo
 */
class mod_gotomeeting_mod_form extends moodleform_mod {

    private $licenses;

    /*
     * Constructor
     */
    public function __construct($current, $section, $cm, $course) {
        global $DB;

        $this->licenses = $DB->get_records('gotomeeting_licenses', array('deleted' => 0));

        parent::__construct($current, $section, $cm, $course);
    }

    /**
     * Defines the structure for gotomeeting mod_form.
     */
    public function definition() {
        /* @var $COURSE type */
        global $CFG, $PAGE, $COURSE, $USER, $DB;
        $mform = $this->_form;
        $context = context_course::instance($COURSE->id);

        if (count($this->licenses) == 0) {
            $mform->addElement('html', \html_writer::tag('h4', get_string('donthave_licenses_registered', 'gotomeeting')) );

            $output = $PAGE->get_renderer('mod_gotomeeting');
            $mform->addElement('html', $output->link_add_license() );

            // -------------------------------------------------------------------------------
            // add standard elements, common to all modules
            // $this->standard_coursemodule_elements();
            // -------------------------------------------------------------------------------
            // add standard buttons, common to all modules
            // $this->add_action_buttons();
        } else {
            // -------------------------------------------------------------------------------
            // Adding the "general" fieldset, where all the common settings are showed
            $mform->addElement('header', 'general', get_string('general', 'form'));
            // Adding the standard "name" field
            $mform->addElement('text', 'name', get_string('gotomeetingname', 'gotomeeting'), array('size' => '64'));
            if (!empty($CFG->formatstringstriptags)) {
                $mform->setType('name', PARAM_TEXT);
            } else {
                $mform->setType('name', PARAM_RAW);
            }

            $mform->addElement('hidden', 'lasteditorid', "");
            $mform->setType('lasteditorid', PARAM_INT);
            $mform->addElement('hidden', 'id');
            $mform->setType('id', PARAM_INT);
            $mform->addRule('name', null, 'required', null, 'client');
            $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
            $mform->addHelpButton('name', 'gotomeetingname', 'gotomeeting');
            // Adding the standard "intro" and "introformat" fields
            // $this->standard_intro_elements(get_string("introeditordefault", "gotomeeting"));

            $mform->addElement(
                'editor'
                , 'introeditor'
                , get_string('moduleintro')
                , array('rows' => 10)
                , array('noclean' => true, 'context' => $context, 'subdirs' => true, 'maxfiles' => 10)
            );
            $mform->setType('introeditor', PARAM_RAW);
            $mform->addHelpButton('introeditor', 'introeditor', 'gotomeeting');

            $licenses = array();
            $licenses[0] = get_string('select_license', 'gotomeeting');
            foreach ($this->licenses as $value) {
                $licenses[$value->id] = $value->name;
            }
            $mform->addElement('select', 'gotomeeting_license_id',
                    get_string('license', 'gotomeeting'), $licenses);
            $mform->addRule('gotomeeting_license_id', null, 'required', null, 'client');
            // -------------------------------------------------------------------------------
            // Adding the rest of gotomeeting settings, spreeading all them into this fieldset
            // or adding more fieldsets ('header' elements) if needed for better logic
            $mform->addElement('header', 'gotomeetingdatetimesetting',
                    get_string('gotomeetingdatetimesetting', 'gotomeeting'));
            $vctime = gotomeeting_timezone();
            $gotomeetingtimezoneselect = $mform->addElement('select', 'gotomeeting_timezone',
                    get_string('vc_class_timezone', 'gotomeeting'), $vctime);
            if (isset($_COOKIE['gotomeeting_vctimezone'])) {
                $gotomeetingvctimezonecookie = $_COOKIE['gotomeeting_vctimezone'];
                $gotomeetingtimezoneselect->setSelected($gotomeetingvctimezonecookie);
            }
            $mform->addHelpButton('gotomeeting_timezone', 'vc_class_timezone', 'gotomeeting');

            // $mform->addElement('hidden', 'timenow', time());
            // $mform->setType('timenow', PARAM_INT);
            $year = date('Y');
            $dtoption = array(
                'startyear' => $year,
                'stopyear'  => $year + 3,
                'timezone'  => 99,
                'applydst'  => true,
                'step'      => 1,
                'optional' => false
            );
            $mform->addelement('date_time_selector', 'gotomeeting_datetime',
                    get_string('gotomeeting_datetime', 'gotomeeting'), $dtoption);
            $mform->addHelpButton('gotomeeting_datetime', 'gotomeeting_datetime', 'gotomeeting');
            // $mform->disabledif ('gotomeeting_datetime', 'schedule_for_now', 'checked');
            $mform->addElement('text', 'duration', get_string('gotomeeting_duration', 'gotomeeting'));
            $mform->setType('duration', PARAM_INT);
            $mform->addRule('duration', get_string('duration_req', 'gotomeeting'),
                    'required', null, 'client', true);
            $mform->addRule('duration', get_string('duration_number', 'gotomeeting'),
                    'numeric', null, 'client');
            $mform->setDefault('duration', 30);
            $mform->addHelpButton('duration', 'duration', 'gotomeeting');

            // -------------------------------------------------------------------------------
            // add standard elements, common to all modules
            $this->standard_coursemodule_elements();
            // -------------------------------------------------------------------------------
            // add standard buttons, common to all modules
            $this->add_action_buttons();

            // $this->set_data(['introeditor' => ['text' => 'Default text!' , 'format' => FORMAT_HTML], 'name' => 'xx']);

        }

    }

    /**
     * Validates the data input from various input elements.
     *
     * @param string $data
     * @param string $files
     *
     * @return string $errors
     */
    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);
        if (empty($data['name'])) {
            $errors['name'] = get_string('namerequired', 'gotomeeting');
        }
        if (empty($data['gotomeeting_license_id'])) {
            $errors['gotomeeting_license_id'] = get_string('licenserequired', 'gotomeeting');
        }
        if ($data['gotomeeting_timezone'] == 'select') {
            $errors['gotomeeting_timezone'] = get_string('timezone_required', 'gotomeeting');
        }
        if (!$data['id'] && $data['gotomeeting_datetime'] < time() - 300) {
            $errors['gotomeeting_datetime'] = get_string('wrongtime', 'gotomeeting');
        }

        $gotomeetingdatetimeend = strtotime('+' . $data['duration'] . ' minutes', $data['gotomeeting_datetime']);
        $sql = "SELECT * FROM {gotomeeting}
                WHERE gotomeeting_license_id = :gotomeeting_license_id
                    AND id != :id
                    AND (gotomeeting_datetime between :gotomeeting_datetime1 AND :gotomeetingdatetimeend1
                        OR gotomeeting_datetime_end between :gotomeeting_datetime2 AND :gotomeetingdatetimeend2);";

        $recordexist = $DB->get_record_sql($sql, [
            'gotomeeting_license_id' => $data['gotomeeting_license_id'],
            'id' => $data['id'],
            'gotomeeting_datetime1' => $data['gotomeeting_datetime'],
            'gotomeeting_datetime2' => $data['gotomeeting_datetime'],
            'gotomeetingdatetimeend1' => $gotomeetingdatetimeend,
            'gotomeetingdatetimeend2' => $gotomeetingdatetimeend,
        ]);

        if ($recordexist) {
            $errors['gotomeeting_datetime'] = get_string('meetingdatetime_already_exists', 'gotomeeting');
        }

        $gotomeetingdurationmaxcheck = GOTOMEETING_MAXIMUM_DURATION < $data['duration'];
        $gotomeetingdurationmincheck = $data['duration'] < GOTOMEETING_MINIMUM_DURATION;
        if ($gotomeetingdurationmaxcheck || $gotomeetingdurationmincheck) {
            $errors['duration'] = get_string('wrongduration', 'gotomeeting');
        }
        /*
        if (isset($data['vc_language'])) {
            $vc_languagecookie = $data['vc_language'];
            setcookie('gotomeeting_vclanguage', $vc_languagecookie, time()+(86400 * 365));//86400  = `1 day
        }
        */
        $vctimezonecookie = $data['gotomeeting_timezone'];
        setcookie('gotomeeting_vctimezone', $vctimezonecookie, time() + YEARSECS);
        return $errors;
    }

    /**
     * Defines the data.
     */
    public function definition_after_data() {

        if (count($this->licenses) > 0) {
            parent::definition_after_data();

            if (empty($this->current->id)) {
                $mform = &$this->_form;
                $introtext = $mform->getElementValue('introeditor');
                $introtext['text'] = get_string("introeditordefault", "gotomeeting");
                $mform->getElement('introeditor')->setValue($introtext);

            }
        }

    }
}
