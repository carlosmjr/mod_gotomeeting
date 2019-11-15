<?php

namespace GoToMeeting\DalPraS\OAuth2\Client\Resources;

use GoToMeeting\DalPraS\OAuth2\Client\Decorators\AccessTokenDecorator;
use GoToMeeting\DalPraS\OAuth2\Client\Provider\Exception\GotoMeetingProviderException;

class Meeting extends \GoToMeeting\DalPraS\OAuth2\Client\Resources\AuthenticatedResourceAbstract {

    /**
     * Get all Meetings.
     *
     * https://api.getgo.com/G2M/rest/v2/account/{accountKey}/Meetings?page=0&size=20
     *
     * @return array
     * [
     *    [
     *       "MeetingKey" => "string",
     *       "MeetingID" => "string",
     *       "organizerKey" => "string",
     *       "accountKey" => "string",
     *       "subject" => "string",
     *       "description" => "string",
     *       "times" => [[
     *           "startTime" => "2019-01-30T15:00:00Z",
     *           "endTime" => "2019-01-30T16:00:00Z"
     *       ]],
     *       "timeZone" => "string",
     *       "locale" => "en_US",
     *       "approvalType" => "string",
     *       "registrationUrl" => "string",
     *       "impromptu" => true,
     *       "isPasswordProtected" => true,
     *       "recurrenceType" => "string",
     *       "experienceType" => "string"
     *    ]
     * ]
     */
    public function getMeetings():array {
        $utcTimeZone = new \DateTimeZone('UTC');
        $body      = [
            'fromTime' => (new \DateTime('-3 years', $utcTimeZone))->format('Y-m-d\TH:i:s\Z'),
            'toTime'   => (new \DateTime('+3 years', $utcTimeZone))->format('Y-m-d\TH:i:s\Z'),
            'page'     => 0,
            'size'     => 100
        ];

        $url = $this->provider->domain . '/G2M/rest/v2/organizers/' . (new AccessTokenDecorator($this->accessToken))->getOrganizerKey() . '/Meetings';
        $url .= '?' . http_build_query($body, null, '&', \PHP_QUERY_RFC3986);
        $request  = $this->provider->getAuthenticatedRequest('GET', $url, $this->accessToken);
        return $this->provider->getParsedResponse($request)['_embedded']['Meetings'];
    }

    /**
     * Get upcoming Meetings.
     *
     * https://api.getgo.com/G2M/rest/v2/account/{accountKey}/Meetings?page=0&size=20
     *
     * @return array
     * [
     *    [
     *       "MeetingKey" => "string",
     *       "MeetingID" => "string",
     *       "organizerKey" => "string",
     *       "accountKey" => "string",
     *       "subject" => "string",
     *       "description" => "string",
     *       "times" => [[
     *           "startTime" => "2019-01-30T15:00:00Z",
     *           "endTime" => "2019-01-30T16:00:00Z"
     *       ]],
     *       "timeZone" => "string",
     *       "locale" => "en_US",
     *       "approvalType" => "string",
     *       "registrationUrl" => "string",
     *       "impromptu" => true,
     *       "isPasswordProtected" => true,
     *       "recurrenceType" => "string",
     *       "experienceType" => "string"
     *    ]
     * ]
     */
    public function getUpcoming():array {
        $utcTimeZone = new \DateTimeZone('UTC');
        $body      = [
            'fromTime' => (new \DateTime('now', $utcTimeZone))->format('Y-m-d\TH:i:s\Z'),
            'toTime'   => (new \DateTime('+3 year', $utcTimeZone))->format('Y-m-d\TH:i:s\Z'),
            'page'     => 0,
            'size'     => 100
        ];
        $url = $this->provider->domain . '/G2M/rest/v2/organizers/' . (new AccessTokenDecorator($this->accessToken))->getOrganizerKey() . '/Meetings';
        $url .= '?' . http_build_query($body, null, '&', \PHP_QUERY_RFC3986);
        $request  = $this->provider->getAuthenticatedRequest('GET', $url, $this->accessToken);
        return $this->provider->getParsedResponse($request)['_embedded']['Meetings'];
    }

    /**
     * Get info for a single Meeting by passing the Meeting id or
     * in GotoMeeting's terms MeetingKey.
     *
     * @param int $MeetingKey
     * @return array
     * [
     *   "MeetingKey" => 0,
     *   "MeetingID" => "string",
     *   "subject" => "string",
     *   "description" => "string",
     *   "organizerKey" => 0,
     *   "organizerEmail" => "string",
     *   "organizerName" => "string",
     *   "times" => [[
     *       "startTime" => "2019-01-30T15:00:00Z",
     *       "endTime" => "2019-01-30T16:00:00Z"
     *   ]],
     *   "registrationUrl" => "string",
     *   "inSession" => true,
     *   "impromptu" => true,
     *   "type" => "string",
     *   "timeZone" => "string",
     *   "numberOfRegistrants" => 0,
     *   "registrationLimit" => 0,
     *   "locale" => "en_US",
     *   "accountKey" => "string",
     *   "recurrencePeriod" => "string",
     *   "experienceType" => "string",
     *   "isPasswordProtected" => true
     * ]
     */
    public function getMeeting($MeetingKey):array {
        $url = $this->provider->domain . '/G2M/rest/meetings/' . $MeetingKey;
        $request  = $this->provider->getAuthenticatedRequest('GET', $url, $this->accessToken);
        return $this->provider->getParsedResponse($request);
    }

    /**
     * Create a new Meeting.
     * Return the the MeetingKey.
     *
     * @param array $body
     * [
     *      "subject" => "subject",
     *      "description" => "description",
     *      "times" => [[
     *          "startTime" => "2019-02-20T09:00:00Z",
     *          "endTime" => "2019-02-20T10:00:00Z"
     *      ]],
     *      "timeZone" => "Europe/Rome",
     *      "type" => "single_session",
     *      "isPasswordProtected" => false,
     *      "recordingAssetKey" => "string",
     *      "isOndemand" => false,
     *      "experienceType" => "CLASSIC"
     * ]
     *
     * @return array
     * [
     *   "MeetingKey": "string"
     * ]
     */
    public function createMeeting(array $body = []) : array {
        $url = $this->provider->domain . '/G2M/rest/meetings';
        $request  = $this->provider->getAuthenticatedRequest('POST', $url, $this->accessToken, [
            'body' => json_encode($body)
        ]);
        return $this->provider->getParsedResponse($request);
    }

    /**
     * Update an existing Meeting.
     *
     * @param string $MeetingKey
     * @param array $body
     * [
     *     "subject" => "subject",
     *     "description" => "description",
     *     "times" => [[
     *         "startTime" => "2019-01-30T09:00:00Z",
     *         "endTime" => "2019-01-30T10:00:00Z"
     *     ]],
     *     "timeZone" => "Europe/Rome",
     *     "locale" => "it_IT"
     * ]
     *
     * @return array
     */
    public function updateMeeting($MeetingKey, array $body = []) {
        $url = $this->provider->domain . '/G2M/rest/meetings/' . $MeetingKey;
        $request  = $this->provider->getAuthenticatedRequest('PUT', $url, $this->accessToken, [
            'body' => json_encode($body)
        ]);
        return $this->provider->getParsedResponse($request);
    }

    /**
     * Delete a Meeting.
     * https://api.getgo.com/G2M/rest/v2/organizers/{organizerKey}/Meetings/{MeetingKey}?sendCancellationEmails=false
     *
     * @param string $MeetingKey
     * @return void
     */
    public function deleteMeeting($MeetingKey) {
        $url = $this->provider->domain . '/G2M/rest/meetings/' . $MeetingKey;
        $request  = $this->provider->getAuthenticatedRequest('DELETE', $url, $this->accessToken);
        return $this->provider->getParsedResponse($request);
    }

    public function getAttendeesByMeeting($MeetingKey):array {
        $url = $this->provider->domain . '/G2M/rest/meetings/' . $MeetingKey . '/attendees';
        $request  = $this->provider->getAuthenticatedRequest('GET', $url, $this->accessToken);
        return $this->provider->getParsedResponse($request);
    }

    public function startMeeting($MeetingKey):array {
        $url = $this->provider->domain . '/G2M/rest/meetings/' . $MeetingKey . '/start';
        $request  = $this->provider->getAuthenticatedRequest('GET', $url, $this->accessToken);
        return $this->provider->getParsedResponse($request);
    }

}

