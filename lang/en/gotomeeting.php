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
 * English strings for gotomeeting
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'GoToMeeting Live Class';
$string['pluginname'] = 'GoToMeeting';
$string['modulenameplural'] = 'GoToMeeting Classes';
$string['modulename_help'] = 'The gotomeeting module enables you to schedule a class. You can schedule online classes, give high impact presentations and watch class recordings with a single click from inside of Moodle';
$string['gotomeetingname'] = 'Title';
$string['vc_class_timezone'] = 'Timezone';
$string['introeditor'] = 'Description';
$string['introeditor_help'] = 'Remember that it is important that the user when attached to a Meeting must use the same name, surname and email with which you registered in the Moodle platform. With this we ensure that the user is correctly displayed on attendance reports';
$string['introeditordefault'] = '<i>It is important that the user when joining a Meeting enter your full name and email with which you registered in the Moodle platform</i>';
$string['gotomeeting_duration'] = 'Duration (in Minutes)';
$string['gotomeetingname_help'] = 'Enter the class title';
$string['gotomeeting'] = 'gotomeeting';
$string['generalconfig'] = 'General configuration';
$string['presenter_id'] = 'Select teacher';
$string['webserviceurl'] = 'Webservice URL';
$string['webserviceurl_desc'] = 'This web service is used to interact with GoToMeeting server for scheduling classes.';
$string['access_key'] = 'Access Key';
$string['access_key_desc'] = 'This is required to authenticate user. We strongly recommend that you don\'t share or change these keys';
$string['secretacesskey'] = 'Consumer Key';
$string['secretacesskey_desc'] = 'This is required to authenticate user. We strongly recommend that you don\'t share or change these keys';
/*
$string['vc_language_xml'] = 'Language xml';
$string['vc_language_xml_desc'] = 'This allows you to choose from various supported languages of the virtual classroom. We strongly recommend that you don’t change this.';
*/
$string['explaingeneralconfig'] = 'API Credentials:- Required for authentication';
$string['discription'] = 'Description About Class';
$string['authemail'] = 'Authenticate Email';
$string['authemail_desc'] = 'This is required to authenticate user. We strongly recommend that you don\'t share or change these keys';
$string['authpassword'] = 'Authenticate Password';
$string['authpassword_desc'] = 'This is required to authenticate user. We strongly recommend that you don\'t share or change these keys';
$string['pluginadministration'] = 'GoToMeeting administration';

$string['select_organizer'] = 'Organizer';
$string['gotomeetingdatetimesetting'] = 'Set Timing of the Class.';
$string['gotomeeting_datetime'] = 'Date and time.';
$string['schedule_for_now'] = 'Schedule for right now';
$string['schedule_for_now_help'] = 'Check it if you want to schedule class for current time';
$string['gotomeetingclasssettings'] = 'Setting of GoToMeeting Class.';
$string['duration'] = 'Duration of class.';
$string['duration_error'] = 'Can only be number';
// $string['vc_language'] = 'Virtualclass language';
$string['audio'] = 'Audio';
$string['writing'] = 'Writing';
$string['record'] = 'Yes';
$string['dontrecord'] = 'No';
/*
$string['recording_option'] = 'Record this class';
$string['recordingtype'] = 'Recording Option';
*/
$string['duration_number'] = 'Must be a valid number';
$string['duration_req'] = 'Must enter a number';
/*
$string['scheduleforself'] = 'Schedule for Self';
$string['scheduleforself_help'] = 'Admin can update class to be schedule for himself';
*/
$string['duration_minutes'] = 'Minutes';
// ========================================error Msgs
$string['wrongtime'] = 'Cannot schedule class for past time';
$string['wrongduration'] = 'Duration should be between 30 minutes to 300 minutes';
$string['meetingdatetime_already_exists'] = 'A meeting already exists with that date and time';

$string['namerequired'] = 'Title of the Class is required';
$string['licenserequired'] = 'License is required';
$string['errormsg'] = 'this is a schedule time error';
$string['error_in_update'] = 'There was error while updating Your class.<br />Please Try Again.';
$string['error_in_curl'] = 'Please enable curl extention in php.ini file.';
$string['error_in_langread'] = 'Unable to read Language Xml.';
$string['error_in_timeread'] = 'There are no records of time zones. To update go to Site Administration > Location > Update timezones';
$string['error_in_downloadrec'] = 'There is some error in downloading the Recording.';
$string['error_in_languagexml'] = 'Check your Settings. Unable to read Language Xml';
$string['error_in_timezonexml'] = 'Check your Settings. Unable to read Timezone Xml';
$string['deletefromgotomeeting'] = 'Deleted from GoToMeeting';
$string['unable_to_get_url'] = 'Url missing';
$string['parent_not_fould'] = 'Parent folder not found';
$string['recnotcreatedyet'] = 'Download Recording not available yet';
$string['error_license_gotomeeting'] = 'License is expired or have errors';
$string['message_error_view'] = 'Error';
// Help
$string['duration_help'] = 'Duration of the class should be in minutes. Minimum duration is 30 minutes and maximum is 300 minutes. You can extend duration of the class from with-in the virtual class-room';
// $string['vc_language_help'] = 'By default language in virtual class-room is En-US, you can change language by selecting language from dropdown menu';
// $string['scheduleforother_help'] = 'By default class schedules for admin. By mark check in checkbox, you can schedule the class for your teachers aswell by selecting teachers from dropdown menu';
$string['gotomeeting_datetime_help'] = 'Select the date and time for class. You can-not schedule class for past time. Don not add day-light saving time to this time';
// $string['recordingtype_help'] = 'By default class scheduled is recorded class, if you do not wants to record class then select "No" option provided';
$string['vc_class_timezone_help'] = 'Select the time-zone for which you want to schedule the class';
// View table
$string['classviewdetail'] = 'Details of class';
$string['presenter_name'] = 'Teacher ';
$string['teacher_you'] = 'You ';
$string['gotomeeting_start_time'] = 'Timing of Class ';
$string['join_class'] = 'Join Class';
$string['gotomeeting_class_timezone'] = 'Time-Zone ';
$string['status_of_class'] = 'Class Status ';
$string['language_name'] = 'Language in ClassRoom ';
$string['update_class'] = 'Edit Class';
$string['delete_class'] = 'Delete Class';
$string['schedule_class'] = 'Schedule Class';
$string['manage_classes'] = 'Manage Classes';
$string['manage_content'] = 'Manage Content';
$string['fetchdata_upgarde'] = 'Fetch Data Upgrade';
$string['launch_class'] = 'Launch Class';
$string['viewclassnotheld'] = 'Class not held';
$string['classnotheld'] = '';
$string['timezone_required'] = 'Timezone required';
// Manage class page
$string['week'] = 'Week';
$string['name'] = 'Class title';
$string['date_time'] = 'Date-Time';
$string['presenter'] = 'Presenter';
$string['status'] = 'Status';
$string['manage'] = 'Manage Class';
$string['links'] = 'Links';
$string['manage_classes_file'] = 'List Of Class For Course';
$string['gotomeeting_classes_file'] = 'gotomeeting_listing_for_course';
$string['per_page_classes'] = 'Classes Per Page ';
$string['refresh_page'] = 'Click here to get latest status';
$string['attendance_report'] = 'Attendance Report';
$string['nogotomeetings'] = 'No GoToMeeting class has been created in this course';
$string['attendencereport'] = 'Attendance Report';
$string['attendee_name'] = 'Attendee Name';
$string['attendee_email'] = 'Email in GoToMeeting';
$string['class'] = 'Class';
$string['name'] = 'Name';
$string['entry'] = 'Entry';
$string['exit'] = 'Exit';
$string['duration'] = 'Duration';
$string['student'] = 'Student';
$string['entry_time'] = 'Entry Time';
$string['exit_time'] = 'Exit Time';
$string['attended_minutes'] = 'Attended Time';
$string['attendence_file'] = 'Attendence List For Class';
$string['gotomeeting_attendence_file'] = 'gotomeeting_attendence_for_class';
$string['editconfirm'] = 'Are you sure to edit class';
$string['deleteconfirm'] = 'Are you sure to delete class';
$string['deleteconfirmcontent'] = 'Are you sure to delete';
$string['nocapability'] = 'Don\'t have capability';
$string['per_page_content'] = 'Content Per Page';
$string['uploaderror'] = 'Error in uploading Content';
$string['content_delete'] = 'Delete';
$string['subcontenterror'] = 'Delete inner content first';
$string['datatempered'] = 'Data changed';
$string['unable_to_delete'] = 'Problem in deleting content';
$string['unable_to_create'] = 'Problem in creating folder';
$string['no_delete_xml'] = 'No xml returned on deleting content';
$string['errorcrtingfolder'] = 'Error in creating folder';
$string['errorinfileupload'] = 'Error in Uploading File';
$string['folder_alrdy_exist'] = 'already exist at this level';
$string['foldernamestring'] = 'Folder name';
$string['error_in_fileext'] = 'Upload allowed file type';
$string['inprogress'] = 'Inprogress';
$string['available'] = 'Available';
$string['contentfail'] = 'Failed';
$string['notknown'] = 'Not known';
$string['nameheading'] = 'Name';
$string['deleteheading'] = 'Delete';
// $string['gotomeeting_content'] = 'GoToMeeting Content';
$string['gotomeeting_classes'] = 'GoToMeeting Classes';
$string['gotomeeting_class'] = 'GoToMeeting Class';
$string['gotomeeting_attendancereport'] = 'GoToMeeting attendance report for ';

// =========================log text=============
$string['eventgotomeetingaddclass'] = 'Add Class method';
$string['eventaddclassdesc'] = "User with id '%s' addded the class for id '%s' belonging to the user with id '%s', the course module id '%s'.Error detail:'%s'";
$string['eventgotomeetingdeleteclass'] = 'Delete Class method';
$string['eventdeleteclassdesc'] = "User with id '%s' deleted the class with id '%s' belonging to the user with id '%s', the course module id '%s'.Error detail:'%s'";
$string['eventgotomeetingupdateclass'] = 'Update Class method';
$string['eventaddclassdesc'] = "User with id '%s' added/updated the class with id '%s' belonging to the user with id '%s', the course module id '%s'.Error detail:'%s'";
$string['eventviewclassdesc'] = "User with id '%s' has viewed the class with id '%s' belonging to the user with id '%s', the course module id '%s'";
$string['eventgotomeetingclassviewed'] = 'Gotomeeting Class Viewed';
$string['eventgotomeetingattendanceviewed'] = 'Gotomeeting Attendance Report Viewed';
$string['eventclassattendancedesc'] = "User with id '%s' has viewed the atttendance classid '%s' belonging to the user
with id '%s', course module id '%s',Error detail:'%s'";
$string['eventgotomeetingcontnetviewed'] = 'Gotomeeting Content Viewed';
$string['eventcontentdesc'] = "User with id '%s' has viewed the content for course '%s' belonging to the user
with id '%s', course module id '%s',Error detail:'%s'";
$string['eventgotomeetingcontnetadded'] = 'Gotomeeting Content Added';
$string['eventcontentadddesc'] = "User with id '%s' has added the content id '%s' belonging to the user
with id '%s', course module id '%s',Error detail:'%s'";
$string['eventgotomeetingcontentdelete'] = 'Gotomeeting Content Deleted';
$string['eventcontentdeletedesc'] = "User with id '%s' has deleted the content id '%s' belonging to the user
with id '%s', course module id '%s',Error detail:'%s'";
$string['eventgotomeetingfolderadded'] = 'Gotomeeting Folder Added';
$string['eventfolderadddesc'] = "User with id '%s' has added the folder for '%s' belonging to the user
with id '%s', course module id '%s',Error detail:'%s'";
$string['eventgotomeetingclassesviewed'] = 'Gotomeeting Classes Viewed ';
$string['eventclassesvieweddesc'] = "User with id '%s' has viewed all classes belonging to the course with id '%s',Error detail:'%s'";
$string['eventviewsessiondesc'] = "User with id '%s' has viewed the class with sessioncode '%s' belonging to the user with id '%s', the course module id '%s'.Error detail:'%s'";
$string['noerror'] = 'No';
$string['eventgotomeetingjoinclass'] = 'Join Class method';
$string['eventjoinclassdesc'] = "User with id '%s' has joined the class for id '%s' belonging to the user with id '%s', the course module id '%s'";

// =========================manage licenses=============
$string['manage_licenses'] = 'Manage Licenses';
$string['license_name'] = 'Name';
$string['license_email'] = 'Email';
$string['license_password'] = 'Password';
$string['license_consumer_key'] = 'Consumer Key';
$string['add_license'] = 'Add License';
$string['license_editconfirm'] = 'Are you sure to edit license';
$string['license_deleteconfirm'] = 'Are you sure to delete license';
$string['license'] = 'License';
$string['donthave_permission'] = "Sorry, but you do not currently have permissions to do that";
$string['donthave_licenses_registered'] = 'You have no registered license, you must register any order to create a meeting';

// =========================buttons=============
$string['continue_btn'] = 'Continue';

// =========================selects lists=============
$string['select_license'] = 'Select a license';
$string['select_vctimezone'] = 'Select an timezone';

$string['update_users'] = 'Update attendance report data';
$string['update_not_sync_users'] = 'Update records without associated user';
$string['license_consumer_secret'] = 'Consumer secret';
$string['info_license_keys'] = 'To get the license data: <br>
Go to the "development center" of LogMeIn<br>
Create a new App if you don\'t have or need<br>
Select the App you want where you can see the data you need in "Keys"';
$string['info_license_url'] = 'Remember that in the Application URL field, you must enter the next URL: ';

$string['not_found'] = 'The GotoMeeting session was not found in the associated account';
$string['authetication_ok'] = 'Correct authentication with the GoToMeeting license {$a}';
$string['empty_license'] = 'License not found';
$string['expired_session'] = 'The selected session has expired';
$string['gotomeeting:addinstance'] = 'Add a GoToMeeting session';
$string['gotomeeting:view_attendance_report'] = 'View GoToMeeting attendance report';
$string['gotomeeting:gotomeeting_download_rec'] = 'GoToMeeting Recording can be downloaded';
$string['gotomeeting:gotomeeting_view_rec'] = 'GoToMeeting Recording can be seen';
$string['gotomeeting:start_meeting'] = 'start session in GoToMeeting';
$string['gotomeeting:manage_licenses'] = 'Manage licenses in GoToMeeting';
$string['gotomeeting:manage_classes'] = 'Manage sessions in GoToMeeting';
$string['gotomeeting:manage_users'] = 'Manage users in GoToMeeting';
$string['privacy:metadata:gotomeeting_attendace_report:attendee_name'] = 'Full name given from the user in GoToMeeting';
$string['privacy:metadata:gotomeeting_attendace_report:gotomeetingid'] = 'Identifier of GoToMeeting room';
$string['privacy:metadata:gotomeeting_attendace_report:join_time'] = 'The time that the the user joined';
$string['privacy:metadata:gotomeeting_attendace_report:leave_time'] = 'The time that the the user leave';
$string['privacy:metadata:gotomeeting_attendace_report:duration'] = 'Duration in minutes that the user was in';
$string['privacy:metadata:gotomeeting_attendace_report:attendee_email'] = 'Email given from the user in GoToMeeting';
$string['privacy:metadata:gotomeeting_attendace_report:meeting_start_time'] = 'The time that the GotoMeeting started';
$string['privacy:metadata:gotomeeting_attendace_report:meeting_end_time'] = 'The time that the GotoMeeting ended';
$string['privacy:metadata:gotomeeting_attendace_report'] = 'Detail of times of each user sessions in GoToMeeting';

$string['datatables_sortascending'] = ': activate to sort column ascending';
$string['datatables_sortdescending'] = ': activate to sort column descending';
$string['datatables_first'] = 'First';
$string['datatables_last'] = 'Last';
$string['datatables_next'] = 'Next';
$string['datatables_previous'] = 'Previous';
$string['datatables_emptytable'] = 'No data available in table';
$string['datatables_info'] = 'Showing _START_ to _END_ of _TOTAL_ entries';
$string['datatables_infoempty'] = 'Showing 0 to 0 of 0 entries';
$string['datatables_infofiltered'] = '(filtered from _MAX_ total entries)';
$string['datatables_lengthmenu'] = 'Show _MENU_ entries';
$string['datatables_loadingrecords'] = 'Loading...';
$string['datatables_processing'] = 'Processing...';
$string['datatables_search'] = 'Search:';
$string['datatables_zerorecords'] = 'No matching records found';
$string['datatables_all'] = 'All';