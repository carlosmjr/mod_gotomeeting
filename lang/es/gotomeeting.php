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
 * Spanish strings for gotomeeting
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

$string['modulename'] = 'GoToMeeting Clase Online';
$string['pluginname'] = 'GoToMeeting';
$string['modulenameplural'] = 'GoToMeeting Clases';
$string['modulename_help'] = 'El módulo de GoToMeeting permite programar una clase. Puede programar las clases en línea, hacer presentaciones de alto impacto con un solo clic desde el propio Moodle';
$string['gotomeetingname'] = 'Titulo';
$string['vc_class_timezone'] = 'Zona horaria';
$string['introeditor'] = 'descripción';
$string['introeditor_help'] = 'Recordar que es importante que el usuario cuando se une a un Meeting debe usar el mismo nombre, apellidos e email con el que se registro en la plataforma Moodle. Con esto nos aseguramos de que se muestre correctamente el usuario en los reportes de asistencia';
$string['introeditordefault'] = '<i>Es importante que el usuario al momento de unirse a un Meeting ingrese su nombre, apellidos e email con el que se registro en la plataforma Moodle</i>';
$string['gotomeeting_duration'] = 'Duración (en Minutos)';
$string['gotomeetingname_help'] = 'Ingrese el titulo de la clase';
$string['gotomeeting'] = 'gotomeeting';
$string['generalconfig'] = 'Configuración General';
$string['presenter_id'] = 'Elegir profesor';
$string['webserviceurl'] = 'Webservice URL';
$string['webserviceurl_desc'] = 'Este servicio web se utiliza para interactuar con el servidor de GoToMeeting para la planificación de las clases';
$string['access_key'] = 'Access Key';
$string['access_key_desc'] = 'Esto es necesario para autenticar el usuario. Es muy recomendable que usted no comparta o cambie estas claves';
$string['secretacesskey'] = 'Consumer Key';
$string['secretacesskey_desc'] = 'Esto es necesario para autenticar el usuario. Es muy recomendable que usted no comparta o cambie estas claves';
/*
$string['vc_language_xml'] = 'Language xml';
$string['vc_language_xml_desc'] = 'This allows you to choose from various supported languages of the virtual classroom. We strongly recommend that you don’t change this.';
*/
$string['explaingeneralconfig'] = 'API Credenciales:- Requeridas para Autenticación';
$string['discription'] = 'Descripción de la Clase';
$string['authemail'] = 'Email de Autenticación';
$string['authemail_desc'] = 'Esto es necesario para autenticar el usuario. Es muy recomendable que usted no comparta o cambie estas claves';
$string['authpassword'] = 'Contraseña de Autenticación';
$string['authpassword_desc'] = 'Esto es necesario para autenticar el usuario. Es muy recomendable que usted no comparta o cambie estas claves';
$string['pluginadministration'] = 'Administración de GoToMeeting';

$string['select_organizer'] = 'Elegir Organizador';
$string['gotomeetingdatetimesetting'] = 'Establezca la hora de la clase';
$string['gotomeeting_datetime'] = 'Fecha y hora.';
$string['schedule_for_now'] = 'Programar para ahora mismo';
$string['schedule_for_now_help'] = 'Elija aquí si quiere programar la clase para la hora actual';
$string['gotomeetingclasssettings'] = 'Configuración de la clase gotomeeting.';
// $string['duration'] = 'Duration of class.';
$string['duration_error'] = 'Can only be number';
// $string['vc_language'] = 'Idioma de la clase virtual';
$string['audio'] = 'Audio';
$string['writing'] = 'Writing';
$string['record'] = 'Sí';
$string['dontrecord'] = 'No';
/*
$string['recording_option'] = 'Grabar esta clase';
$string['recordingtype'] = 'Opciones de Grabación';
*/
$string['duration_number'] = 'Must be a valid number';
$string['duration_req'] = 'Must enter a number';
/*
$string['scheduleforself'] = 'Schedule for Self';
$string['scheduleforself_help'] = 'Admin can update class to be schedule for himself';
*/
$string['duration_minutes'] = 'Minutos';
// ========================================error Msgs
$string['wrongtime'] = 'No se puede programar la clase para una hora pasada';
$string['wrongduration'] = 'La duración debe estar entre 30 minutos y 300 minutos';
$string['meetingdatetime_already_exists'] = 'Ya existe una reunión programada con esa fecha y hora';

$string['namerequired'] = 'Title of the Class is required';
$string['licenserequired'] = 'Licencia es obligatorio';
$string['errormsg'] = 'this is a schedule time error';
$string['error_in_update'] = 'There was error while updating Your class.<br />Please Try Again.';
$string['error_in_curl'] = 'Please enable curl extention in php.ini file.';
$string['error_in_langread'] = 'Unable to read Language Xml.';
$string['error_in_timeread'] = 'No existen registros de zonas horarias. Para actualizarlos valla a Administración del Sitio > Ubicación > Actualizar zonas horarias';
$string['error_in_downloadrec'] = 'There is some error in downloading the Recording.';
$string['error_in_languagexml'] = 'Check your Settings. Unable to read Language Xml';
$string['error_in_timezonexml'] = 'Check your Settings. Unable to read Timezone Xml';
$string['deletefromgotomeeting'] = 'Deleted from GoToMeeting';
$string['unable_to_get_url'] = 'Url missing';
$string['parent_not_fould'] = 'Parent folder not found';
$string['recnotcreatedyet'] = 'Download Recording not available yet';
$string['error_license_gotomeeting'] = 'Hay errores en los datos de la licencia o esta caducada';
$string['message_error_view'] = 'Error';
// ==================================================================help
$string['duration_help'] = 'La duración de la clase debe ser en minutos. La duración mínima es de 30 minutos y la máxima de 300 minutos. Usted puede aumentar la duración de la clase desde dentro de la sala de clase virtual';
// $string['vc_language_help'] = 'Por defecto el idioma en las aulas virtuales es el inglés de Estados Unidos (En-US), pero puede cambiarlo eligiendo del menú desplegable.';
// $string['scheduleforother_help'] = 'Por defecto la clase se programa para el admin. Si Usted marca la casilla de verificación, usted puede programar la clase para sus profesores eligiendo estos profesores del menú desplegable';
$string['gotomeeting_datetime_help'] = 'Seleccione la fecha y la hora de la clase. No puede programar una clase para una fecha ya pasada. No tenga en cuenta los ajustes de verano en esta hora.';
// $string['recordingtype_help'] = 'Por defecto la clase programada es clase grabada. Si usted no quiere grabar una clase entonces debe elegir la opción "No"';
$string['vc_class_timezone_help'] = 'Elija la Zona-Horaria en la que desea programar esta clase';
// ==========================view table
$string['classviewdetail'] = 'Resumen de la clase';
$string['presenter_name'] = 'Profesor ';
$string['teacher_you'] = 'Tú ';
$string['gotomeeting_start_time'] = 'Horario de Clase ';
$string['join_class'] = 'Unirse a Clase';
$string['gotomeeting_class_timezone'] = 'Zona Horaria ';
$string['status_of_class'] = 'Estado de Clase ';
$string['language_name'] = 'Language in ClassRoom ';
$string['update_class'] = 'Editar Clase';
$string['delete_class'] = 'Borrar Clase';
$string['schedule_class'] = 'Programar Clase';
$string['manage_classes'] = 'Gestionar Clases';
$string['manage_content'] = 'Manage Content';
$string['fetchdata_upgarde'] = 'Fetch Data Upgrade';
$string['launch_class'] = 'Empezar Clase';
$string['viewclassnotheld'] = 'La clase no se celebró';
$string['classnotheld'] = '';
$string['timezone_required'] = 'Timezone required';
// =========================manage class page=============
$string['week'] = 'Semana';
$string['name'] = 'Titulo';
$string['date_time'] = 'Fecha-Hora';
$string['presenter'] = 'Presentador';
$string['status'] = 'Estado';
$string['manage'] = 'Acciones';
$string['links'] = 'Enlaces';
$string['manage_classes_file'] = 'List Of Class For Course';
$string['gotomeeting_classes_file'] = 'gotomeeting_listing_for_course';
$string['per_page_classes'] = 'Clases Por Página ';
$string['refresh_page'] = 'Click here to get latest status';
$string['attendance_report'] = 'Informe de Asistencia';
$string['nogotomeetings'] = 'No se ha creado ningún GoToMeeting para este curso';
$string['attendencereport'] = 'Informe de Asistencia';
$string['attendee_name'] = 'Nombre del Asistente';
$string['attendee_email'] = 'Email en GoToMeeting';
$string['class'] = 'Clase';
$string['name'] = 'Nombre';
$string['entry'] = 'Entrada';
$string['exit'] = 'Salida';
$string['duration'] = 'Duración';
$string['student'] = 'Alumno';
$string['entry_time'] = 'Tiempo de Entrada';
$string['exit_time'] = 'Tiempo de Salida';
$string['attended_minutes'] = 'Tiempo de Permanencia';
$string['attendence_file'] = 'Attendence List For Class';
$string['gotomeeting_attendence_file'] = 'gotomeeting_attendence_for_class';
$string['editconfirm'] = 'Seguro desea editar la clase';
$string['deleteconfirm'] = 'Seguro desea eliminar la clase';
$string['deleteconfirmcontent'] = 'Seguro desea eliminar';
$string['nocapability'] = 'Don\'t have capability';
$string['per_page_content'] = 'Registros por página';
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
$string['gotomeeting_classes'] = 'Clases GoToMeeting';
$string['gotomeeting_class'] = 'Clase GoToMeeting';
$string['gotomeeting_attendancereport'] = 'GoToMeeting reporte de asistencia para ';

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
$string['eventclassesvieweddesc'] = "El usuario con id '%s' ha visto todas las sesiones pertenecientes al curso '%s', detalle de error:'%s'";
$string['eventviewsessiondesc'] = "User with id '%s' has viewed the class with sessioncode '%s' belonging to the user with id '%s', the course module id '%s'.Error detail:'%s'";
$string['noerror'] = 'No';
$string['eventgotomeetingjoinclass'] = 'Join Class method';
$string['eventjoinclassdesc'] = "User with id '%s' has joined the class for id '%s' belonging to the user with id '%s', the course module id '%s'";

// =========================manage licenses=============
$string['manage_licenses'] = 'Gestionar Licencias';
$string['license_name'] = 'Nombre';
$string['license_email'] = 'Email';
$string['license_password'] = 'Contraseña';
$string['license_consumer_key'] = 'Consumer Key';
$string['add_license'] = 'Añadir Licencia';
$string['license_editconfirm'] = 'Seguro desea editar la licencia';
$string['license_deleteconfirm'] = 'Seguro desea eliminar la licencia';
$string['license'] = 'Licencia';
$string['donthave_permission'] = 'Lo sentimos, pero por el momento no tiene permiso para hacer eso';
$string['donthave_licenses_registered'] = 'No tienes ninguna licencia registrada, debes registrar alguna para poder crear una reunión';

// =========================buttons=============
$string['continue_btn'] = 'Continuar';

// =========================selects lists=============
$string['select_license'] = 'Seleccione una licencia';
$string['select_vctimezone'] = 'Seleccione una zona horaria';

$string['update_users'] = 'Actualizar datos de usuario';
$string['update_not_sync_users'] = 'Actualizar registros sin usuario asociado';
$string['license_consumer_secret'] = 'Consumer secret';
$string['info_license_keys'] = 'Para conseguir los datos de licencia:<br>
* Vaya al "centro de desarrollo" de LogMeIn<br>
* Cree una nueva App si no tiene o necesita<br>
* Seleccione la App que desee y podrá ver los datos que necesite en "Keys"';
$string['info_license_url'] = 'Recuerde que en el campo Application URL, debe introducir la URL: ';

$string['not_found'] = 'No se ha encontrado la sesión de GotoMeeting en la cuenta asociada';
$string['authetication_ok'] = 'Autenticación correcta con la licencia de GoToMeeting {$a}';
$string['empty_license'] = 'Licencia no encontrada';
$string['expired_session'] = 'La sesión seleccionada ha expirado';
$string['gotomeeting:addinstance'] = 'Añadir una sesión GoToMeeting';
$string['gotomeeting:view_attendance_report'] = 'Ver informe GoToMeeting de asistencia';
$string['gotomeeting:gotomeeting_download_rec'] = 'Puede descargarse la grabación GoToMeeting';
$string['gotomeeting:gotomeeting_view_rec'] = 'Puede ver la grabación GoToMeeting';
$string['gotomeeting:start_meeting'] = 'Iniciar sesión en GoToMeeting';
$string['gotomeeting:manage_licenses'] = 'Gestionar licencias en GoToMeeting';
$string['gotomeeting:manage_classes'] = 'Gestionar sesiones en GoToMeeting';
$string['gotomeeting:manage_users'] = 'Gestioar usuarios en GoToMeeting';
$string['privacy:metadata:gotomeeting_attendace_report:attendee_name'] = 'Nombre completo del usuario en GoToMeeting';
$string['privacy:metadata:gotomeeting_attendace_report:gotomeetingid'] = 'Identificador de la sala de GoToMeeting';
$string['privacy:metadata:gotomeeting_attendace_report:join_time'] = 'Hora a la que el usuario se unió';
$string['privacy:metadata:gotomeeting_attendace_report:leave_time'] = 'Hora a la que el usuario salió';
$string['privacy:metadata:gotomeeting_attendace_report:duration'] = 'Duración en minutos que estuvo el usuario en la sala';
$string['privacy:metadata:gotomeeting_attendace_report:attendee_email'] = 'Email del usuario en GoToMeeting';
$string['privacy:metadata:gotomeeting_attendace_report:meeting_start_time'] = 'Hora a la que la sala comenzó';
$string['privacy:metadata:gotomeeting_attendace_report:meeting_end_time'] = 'Hora a la que la sala terminó';
$string['privacy:metadata:gotomeeting_attendace_report'] = 'Detalle de tiempos de usuarios para los usuario en GoToMeeting';

$string['datatables_sortascending'] = ': activar orden ascendente';
$string['datatables_sortdescending'] = ': activar orden descendente';
$string['datatables_first'] = 'Primero';
$string['datatables_last'] = 'Último';
$string['datatables_next'] = 'Siguiente';
$string['datatables_previous'] = 'Previo';
$string['datatables_emptytable'] = 'No se encontraron datos';
$string['datatables_info'] = 'Mostrando desde el _START_ al _END_ de _TOTAL_ entradas';
$string['datatables_infoempty'] = 'Mostrando desde el 0 al 0 de 0 entradas';
$string['datatables_infofiltered'] = '(filtrando de _MAX_ entradas)';
$string['datatables_lengthmenu'] = 'Mostrar _MENU_ entradas';
$string['datatables_loadingrecords'] = 'Cargando...';
$string['datatables_processing'] = 'Procesando...';
$string['datatables_search'] = 'Buscar:';
$string['datatables_zerorecords'] = 'No se ha encontrado nada';
$string['datatables_all'] = 'Todos';
