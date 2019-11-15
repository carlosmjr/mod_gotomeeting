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
 * Forms used in gotomeeting module
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Class add_license_form
 *
 * @package    mod_gotomeeting
 * @copyright  2019 gotomeeting.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_license_form extends moodleform {

    /**
     * Form definiton.
     */
    public function definition() {
        global $DB, $USER, $CFG, $OUTPUT;

        $mform =& $this->_form;

        $mform->addElement('header', 'license', get_string('license', 'gotomeeting'));

        // nombre
        $mform->addElement ( 'text', 'name', get_string ( 'license_name', 'gotomeeting' ), 'maxlength=255, style="width: 40%"' );
        $mform->addRule ( 'name', null, 'required', null, 'license' );
        $mform->setType('name', PARAM_TEXT);

        // consumer key
        $mform->addElement ( 'text', 'consumer_key', get_string ( 'license_consumer_key', 'gotomeeting' ), 'maxlength=255, style="width: 40%"' );
        $mform->addRule ( 'consumer_key', null, 'required', null, 'license' );
        $mform->setType('consumer_key', PARAM_TEXT);

        // consumer secret
        $mform->addElement ( 'text', 'consumer_secret', get_string ( 'license_consumer_secret', 'gotomeeting' ), 'maxlength=255, style="width: 40%"' );
        $mform->addRule ( 'consumer_secret', null, 'required', null, 'license' );
        $mform->setType('consumer_secret', PARAM_TEXT);

        $mform->addElement ( 'hidden', 'id' );
        $mform->setType('id', PARAM_INT);

        $mform->addElement('html', $OUTPUT->notification(get_string ( 'info_license_keys', 'gotomeeting' ),

            \core\output\notification::NOTIFY_INFO));

        $link = $CFG->dirroot . '/mod/gotomeeting/auth.php';
        $mform->addElement('html', $OUTPUT->notification(
            get_string ( 'info_license_url', 'gotomeeting' ) . ' ' . html_writer::tag('b', $link)
            , \core\output\notification::NOTIFY_WARNING)
        );

        // buttons
        $this->add_action_buttons();

    }

}