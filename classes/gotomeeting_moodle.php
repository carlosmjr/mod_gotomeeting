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
 * gotomeeting_moodle API
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_gotomeeting;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/vendor/autoload.php');

/**
 * Controller between GotoMeeting and Moodle
 *
 * @package    block_itop_learning_management
 * @copyright  2016 onwards Antonello Moro {http://antonellomoro.it}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gotomeeting_moodle
{
    private $provider;
    private $meeting;
    private $license;

    private $id;
    private $name;
    private $intro;
    private $introformat;
    private $gotomeetingdatetime;
    private $gotomeetingdatetimeend;
    private $classtimezone;
    private $timecreated;
    private $timemodified;
    private $duration;
    private $presenterid;
    private $lasteditorid;
    private $classstatus;
    private $gotomeetingid;
    private $gotomeetingjoinurl;
    private $gotomeetinglicenseid;

    /**
     * gotomeeting_moodle constructor.
     */
    public function __construct() {

    }

    /*
     * Loads object with provider, license and meeting
     */
    public static function instance_by_license($licenseid) {
        global $DB;

        $gotomeetingmoodle = new self();
        $gotomeetingmoodle->set_meeting($licenseid);

        return $gotomeetingmoodle;
    }

    /*
     * Loads object with gotomeeting data loaded
     */
    public static function instance_by_id($id, $loadmeeting = true) {

        $gotomeetingmoodle = new self();
        $gotomeetingmoodle->fetch($id);

        if ($loadmeeting) {
            $gotomeetingmoodle->set_meeting();
        }

        return $gotomeetingmoodle;
    }

    /**
     * Fetch the specified GoToMeeting into the current object.
     *
     * @param   int         $id         The ID of the gotomeeting to fetch.
     */
    public function fetch($id = null) {
        global $DB;

        if (!$id) {
            $id = $this->get_id();
        }

        if ($data = $DB->get_record('gotomeeting', array('id' => $id), '*')) {
            $this->reload_from_record($data);
        }
    }

    /**
     * Reload the GoToMeeting into the current object.
     *
     * @param   /stdClass    $record     The record to reload.
     * @param   boolean     $clean      Clean the values.
     * @return  void
     */
    protected function reload_from_record($record) {

        $this->id = $record->id;
        $this->name = $record->name;
        $this->intro = $record->intro;
        $this->introformat = $record->introformat;
        $this->gotomeetingdatetime = $record->gotomeeting_datetime;
        $this->gotomeetingdatetimeend = $record->gotomeeting_datetime_end;
        $this->classtimezone = $record->class_timezone;
        $this->timecreated = $record->timecreated;
        $this->timemodified = $record->timemodified;
        $this->duration = $record->duration;
        $this->presenterid = $record->presenter_id;
        $this->lasteditorid = $record->lasteditorid;
        $this->classstatus = $record->class_status;
        $this->gotomeetingid = $record->gotomeeting_id;
        $this->gotomeetingjoinurl = $record->gotomeeting_joinurl;
        $this->gotomeetinglicenseid = $record->gotomeeting_license_id;

    }

    /**
     * Prepare this course for saving to the database.
     *
     * @return  object
     */
    public function to_record() {
        return (object) array(
            'id' => $this->id,
            'name' => $this->name,
            'intro' => $this->intro,
            'introformat' => $this->introformat,
            'gotomeeting_datetime' => $this->gotomeetingdatetime,
            'gotomeeting_datetime_end' => $this->gotomeetingdatetimeend,
            'class_timezone' => $this->classtimezone,
            'timecreated' => $this->timecreated,
            'timemodified' => $this->timemodified,
            'duration' => $this->duration,
            'presenter_id' => $this->presenterid,
            'lasteditorid' => $this->lasteditorid,
            'class_status' => $this->classstatus,
            'gotomeeting_id' => $this->gotomeetingid,
            'gotomeeting_joinurl' => $this->gotomeetingjoinurl,
            'gotomeeting_license_id' => $this->$gotomeetinglicenseid,
        );
    }

    /**
     * The meeting class.
     *
     * @return  \GoToMeeting\DalPraS\OAuth2\Client\Resources\Meeting
     */
    public function get_meeting() {
        return $this->meeting;
    }

    /**
     * Set the meeting class with the given params.
     *
     * @param   int      $licenseid      The id of table gotomeeting_licenses.
     * @param   bool     $development    true not verify ssl
     * @return  $this
     */
    public function set_meeting($licenseid = null, $development = false) {
        global $DB;

        if ($licenseid) {
            $this->set_provider($licenseid, $development);
        }

        $provider = $this->get_provider();

        $token = unserialize($this->license->data);

        if ($token != '') {
            if ($token->hasExpired()) {
                try {
                    $newaccesstoken = $provider->getAccessToken('refresh_token', [
                        'refresh_token' => $token->getRefreshToken()
                    ]);

                    $data = serialize($newaccesstoken);

                    $this->license->data = $data;

                    $DB->update_record('gotomeeting_licenses', $this->license);

                    $token = $newaccesstoken;

                } catch (\Exception $e) {

                    $response = $e->getResponseBody();

                    $response = json_decode($response);

                    if ($response->error == 'invalid_grant') {
                        redirect('/mod/gotomeeting/auth.php?id=' . $this->license->id);
                        die;
                    }
                }
            }
        } else {
            redirect('/mod/gotomeeting/auth.php?id=' . $this->license->id);
            die;
        }

        $this->meeting = new \GoToMeeting\DalPraS\OAuth2\Client\Resources\Meeting($provider, $token);

        return $this;
    }

    /**
     * The provider meeting class.
     *
     * @return  \GoToMeeting\DalPraS\OAuth2\Client\Provider\GotoMeeting
     */
    public function get_provider() {
        if (!$this->provider) {
            $this->set_provider();
        }
        return $this->provider;
    }

    /**
     * Set the provider meeting class with the given params.
     *
     * @param   int      $licenseid      The id of table gotomeeting_licenses.
     * @param   bool     $development    true not verify ssl
     * @return  $this
     */
    public function set_provider($licenseid = null, $development = false) {
        global $CFG;

        if ($licenseid) {
            $this->set_license($licenseid);
        } else if (!$this->license) {
            $this->set_license($licenseid);
        }

        $this->provider = new \GoToMeeting\DalPraS\OAuth2\Client\Provider\GotoMeeting([
            // The client ID assigned to you by the provider
            'clientId' => $this->license->consumer_key,
            'clientSecret' => $this->license->consumer_secret,
            'redirectUri' => $CFG->wwwroot . '/mod/gotomeeting/auth.php?id=' . $this->license->id
        ], [
            // optional
            'httpClient' => new \GuzzleHttp\Client([
                // setup some options for using with localhost
                'verify' => $development ? false : true,
                // timeout connection
                'timeout' => 60
            ])
        ]);

        return $this;
    }

    /**
     * The GoToMeeting license.
     *
     * @return  \GoToMeeting\DalPraS\OAuth2\Client\Resources\Meeting
     */
    public function get_license() {
        return $this->license;
    }

    /**
     * Set data of GoToMeeting license
     * @param /stdclass|int $licenseid Id of gotomeeting_licenses, or database object
     * @return  $this
     */
    public function set_license($licenseid = null) {
        global $DB;

        if (!$licenseid) {
            $licenseid = $this->gotomeetinglicenseid;
        }

        if (is_object($licenseid)) {
            $this->license = clone $licenseid;
        } else {
            $this->license = $DB->get_record('gotomeeting_licenses', array('id' => $licenseid), '*');
        }

        return $this;
    }

    /**
     * Saves data into gotomeeting table
     * @param /stdclass $data databases object
     * @return  $this
     */
    public function save($data = null) {

        global $DB;

        if (!$data) {
            $data = $this->to_record();
        }

        if ($data->id) {
            $DB->update_record('gotomeeting', $data);
        } else {
            $this->id = $DB->insert_record('gotomeeting', $data);
        }

        return $this;

    }

    /**
     * The ID of the gotomeeting.
     *
     * @return  int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * The ID of the course of the gotomeeting.
     *
     * @return  int
     */
    public function get_course() {
        return $this->course;
    }

    /**
     * Set the course id of the gotomeeting.
     *
     * @param   int      $value      The new course id.
     * @return  $this
     */
    public function set_course($value) {
        $this->course = $value;

        return $this;
    }

    /**
     * The name of the gotomeeting.
     *
     * @return  string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the name of the gotomeeting.
     *
     * @param   string      $value      The new name.
     * @return  $this
     */
    public function set_name($value) {
        $this->name = clean_param($value, PARAM_TEXT);

        return $this;
    }

    /**
     * The intro of the gotomeeting.
     *
     * @return  string
     */
    public function get_intro() {
        return $this->intro;
    }

    /**
     * Set the intro of the gotomeeting.
     *
     * @param   string      $value      The new intro.
     * @return  $this
     */
    public function set_intro($value) {
        $this->intro = clean_param($value, PARAM_TEXT);

        return $this;
    }

    /**
     * The introformat of the gotomeeting.
     *
     * @return  string
     */
    public function get_introformat() {
        return $this->introformat;
    }

    /**
     * Set the introformat of the gotomeeting.
     *
     * @param   int      $value      The new introformat.
     * @return  $this
     */
    public function set_introformat($value) {
        $this->introformat = $value;

        return $this;
    }

    /**
     * The datetime of the gotomeeting.
     *
     * @return  int
     */
    public function get_gotomeeting_datetime() {
        return $this->gotomeetingdatetime;
    }

    /**
     * Set the datetime of the gotomeeting.
     *
     * @param   int      $value      The new datetime.
     * @return  $this
     */
    public function set_gotomeeting_datetime($value) {
        $this->gotomeetingdatetime = $value;

        return $this;
    }

    /**
     * The datetime end of the gotomeeting.
     *
     * @return  int
     */
    public function get_gotomeeting_datetime_end() {
        return $this->gotomeetingdatetimeend;
    }

    /**
     * Set the datetime end of the gotomeeting.
     *
     * @param   int      $value      The new datetime end.
     * @return  $this
     */
    public function set_gotomeeting_datetime_end($value) {
        $this->gotomeetingdatetimeend = $value;

        return $this;
    }

    /**
     * The timezone of the gotomeeting.
     *
     * @return  string
     */
    public function get_class_timezone() {
        return $this->classtimezone;
    }

    /**
     * Set the timezone of the gotomeeting.
     *
     * @param   string      $value      The new timezone.
     * @return  $this
     */
    public function set_class_timezone($value) {
        $this->classtimezone = $value;

        return $this;
    }

    /**
     * The time of creation of the gotomeeting.
     *
     * @return  int
     */
    public function get_timecreated() {
        return $this->timecreated;
    }

    /**
     * Set the timecreated of the gotomeeting.
     *
     * @param   int      $value      The new timecreated.
     * @return  $this
     */
    public function set_timecreated($value) {
        $this->timecreated = $value;

        return $this;
    }

    /**
     * The time of modification of the gotomeeting.
     *
     * @return  int
     */
    public function get_timemodified() {
        return $this->timemodified;
    }

    /**
     * Set the timemodified of the gotomeeting.
     *
     * @param   int      $value      The new timemodified.
     * @return  $this
     */
    public function set_timemodified($value) {
        $this->timemodified = $value;

        return $this;
    }

    /**
     * The duration (in minutes) of the gotomeeting.
     *
     * @return  int
     */
    public function get_duration() {
        return $this->duration;
    }

    /**
     * Set the duration of the gotomeeting.
     *
     * @param   int      $value      The new duration.
     * @return  $this
     */
    public function set_duration($value) {
        $this->duration = $value;

        return $this;
    }

    /**
     * The id of creator of the class of the gotomeeting.
     *
     * @return  int
     */
    public function get_presenter_id() {
        return $this->presenterid;
    }

    /**
     * Set the id of creator of the gotomeeting.
     *
     * @param   int      $value      The new id of creator.
     * @return  $this
     */
    public function set_presenter_id($value) {
        $this->presenterid = $value;

        return $this;
    }

    /**
     * The id of last editor of the class of the gotomeeting.
     *
     * @return  int
     */
    public function get_lasteditorid() {
        return $this->lasteditorid;
    }

    /**
     * Set the id of last editor of the gotomeeting.
     *
     * @param   int      $value      The new id of last editor.
     * @return  $this
     */
    public function set_lasteditorid($value) {
        $this->lasteditorid = $value;

        return $this;
    }

    /**
     * The status of the gotomeeting instance.
     *
     * @return  string
     */
    public function get_class_status() {
        return $this->classstatus;
    }

    /**
     * Set the status of the gotomeeting.
     *
     * @param   string      $value      The new status.
     * @return  $this
     */
    public function set_class_status($value) {
        $this->classstatus = $value;

        return $this;
    }

    /**
     * The id connector of the gotomeeting instance.
     *
     * @return  int
     */
    public function get_gotomeeting_id() {
        return $this->gotomeetingid;
    }

    /**
     * Set the id connector of the gotomeeting instance.
     *
     * @param   int      $value      The new id connector of the gotomeeting instance.
     * @return  $this
     */
    public function set_gotomeeting_id($value) {
        $this->gotomeetingid = $value;

        return $this;
    }

    /**
     * The url to connect to the gotomeeting instance.
     *
     * @return  string
     */
    public function get_gotomeeting_joinurl() {
        return $this->gotomeetingjoinurl;
    }

    /**
     * Set the url to connect to the gotomeeting instance.
     *
     * @param   string      $value      The new url.
     * @return  $this
     */
    public function set_gotomeeting_joinurl($value) {
        $this->gotomeetingjoinurl = $value;

        return $this;
    }

    /**
     * The id of the gotomeeting license.
     *
     * @return  string
     */
    public function get_gotomeeting_license_id() {
        return $this->$gotomeetinglicenseid;
    }

    /**
     * Set the id of the gotomeeting license.
     *
     * @param   int      $value      The new gotomeeting license id.
     * @return  $this
     */
    public function set_gotomeeting_license_id($value) {
        $this->$gotomeetinglicenseid = $value;

        return $this;
    }

}