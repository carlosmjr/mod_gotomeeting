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
 * Page called to create auth link.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_gotomeeting\gotomeeting_moodle;

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(__DIR__ . '/classes/vendor/autoload.php');

require_once(dirname(__FILE__).'/lib.php');
global $CFG, $USER, $OUTPUT, $PAGE;

$id = optional_param('id', 0, PARAM_INT); // license id
$state = optional_param('state', null, PARAM_RAW);

$context = context_user::instance($USER->id);

if (!has_capability ( 'mod/gotomeeting:manage_licenses', $context )) {
    error ('wrong_permission');
}

if (!empty($state)) {
    $license = $DB->get_record('gotomeeting_licenses', array('state' => $state, 'deleted' => 0));
} else if ($id > 0) {
    $license = $DB->get_record('gotomeeting_licenses', array('id' => $id, 'deleted' => 0));
}

if (empty($license)) {
    error (get_string('empty_license', 'gotomeeting'));
}

$gotomeetingmoodle = new gotomeeting_moodle();
$gotomeetingmoodle->set_license($license);
$provider = $gotomeetingmoodle->get_provider();

if (isset($license->data) && $license->data != '') {
    $token = unserialize($license->data);
}

$urllicenses = new moodle_url('/mod/gotomeeting/manage_licenses.php', ['id' => 1, 'sesskey' => sesskey()]);

if (isset($token)) {
    if ($token->hasExpired()) {
        try {
            $newaccesstoken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $token->getRefreshToken()
            ]);

            $data = serialize($newaccesstoken);
            $license->data = $data;

            $DB->update_record('gotomeeting_licenses', $license);

            $token = $newaccesstoken;

        } catch (\Exception $e) {

            $response = $e->getResponseBody();

            $response = json_decode($response);

            if ($response->error == 'invalid_grant') {
                $token = null;

                $license->data = '';

                $DB->update_record('gotomeeting_licenses', $license);
                // we need to renew it
            }
        }
    }
}

if (!isset($token)) {
    if ($code = optional_param('code', null, PARAM_RAW)) {

        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        // Use this to interact with an API on the users behalf

        $data = serialize($token);
        $license->data = $data;

        $DB->update_record('gotomeeting_licenses', $license);

        redirect($urllicenses, get_string('authetication_ok', 'gotomeeting', $license->name));

        die;

    } else {

        // If we don't have an authorization code then get one
        $authurl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();

        $license->state = $provider->getState();
        $DB->update_record('gotomeeting_licenses', $license);

        header('Location: '.$authurl);

        exit;

    }
}

redirect($urllicenses);
