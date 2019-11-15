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

/*
 * Privacy Subsystem implementation for mod_gotomeeting.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
    [
        'jquery'
        , 'core/ajax'
        , 'mod_gotomeeting/jquery.dataTables'
        , 'core/notification'
    ]
    , function ($, ajax, dataTable, notification) {

        return {

            init : function() {

            }

            /*
             * Loads table list of updateable users.
             * Controls the definition of user to save it via ajax
             *
             * @param  {int} contextId Current context id.
             */
            , add_datatables_upusers: function (params) {

                courseid = params.course;

                M.util.js_pending('mod_gotomeeting_loading_users_updateables' + courseid);
                $.when(
                    ajax.call([
                        {
                            methodname: 'mod_gotomeeting_load_users_updateables',
                            args: {courseid: params.course}
                        }
                    ])[0]/*,
                    templates.render('tool_itop_tour_added/addedmodal', {})*/
                )
                .then(function(response/*, template*/) {
                    // console.log(response);
                    var data = response.map(function(s) {
                        if ( s.hasOwnProperty("id") ) {
                            s.DT_RowId = s.id;
                            delete s.id;
                        }
                          return s;
                    } );
                    // console.log(data);

                    var jqtable = $(params.selector);

                    jqtable.dataTable({
                        /**/
                        "lengthMenu": [ [params.length, (params.length * 2), (params.length * 4), -1], [params.length, (params.length * 2), (params.length * 4), M.str.mod_gotomeeting.datatables_all]],
                        'bAutoWidth': false,
                        'sPaginationType': 'full_numbers',
                        // 'ajax': {"url": "ajax/loaddata.php?id=" + params.course},
                        'data': response,
                        'fixedHeader': true,
                        'columns': [
                            {'data': "class" },
                            {'data': "name" },
                            {'data': "email" },
                            {'data': "entry" },
                            {'data': "end" },
                            {'data': "duration" },
                            {'data': "student", 'className': 'goto_student' }
                        ],
                        'oLanguage': {
                            'oAria': {
                                'sSortAscending': M.str.mod_gotomeeting.datatables_sortascending,
                                'sSortDescending': M.str.mod_gotomeeting.datatables_sortdescending,
                            },
                            'oPaginate': {
                                'sFirst': M.str.mod_gotomeeting.datatables_first,
                                'sLast': M.str.mod_gotomeeting.datatables_last,
                                'sNext': M.str.mod_gotomeeting.datatables_next,
                                'sPrevious': M.str.mod_gotomeeting.datatables_previous
                            },
                            'sEmptyTable': M.str.mod_gotomeeting.datatables_emptytable,
                            'sInfo': M.str.mod_gotomeeting.datatables_info,
                            'sInfoEmpty': M.str.mod_gotomeeting.datatables_infoempty,
                            'sInfoFiltered': M.str.mod_gotomeeting.datatables_infofiltered,
                            'sInfoThousands': M.str.langconfig.thousandssep,
                            'sLengthMenu': M.str.mod_gotomeeting.datatables_lengthmenu,
                            'sLoadingRecords': M.str.mod_gotomeeting.datatables_loadingrecords,
                            'sProcessing': M.str.mod_gotomeeting.datatables_processing,
                            'sSearch': M.str.mod_gotomeeting.datatables_search,
                            'sZeroRecords': M.str.mod_gotomeeting.datatables_zerorecords
                        },
                        "fnDrawCallback": function (settings) {
                            var api = this.api();
                            var totalRows = api.rows().data().length; // Get total rows of data
                            var rowPerPage = api.rows({ page: 'current' }).data().length; // Get total rows of data per page

                            var table = '#' + $(this).attr('id') + '_wrapper';

                            if (totalRows > rowPerPage) {
                                // Show pagination and "Show X Entries" drop down option
                                $(table).find('div.dataTables_paginate')[0].style.display = "block";
                                $(table).find('div.dataTables_length')[0].style.display = "block";
                            } else {
                                // Hide it
                                $(table).find('div.dataTables_paginate')[0].style.display = "none";
                                $(table).find('div.dataTables_length')[0].style.display = "none";
                            }

                            // Fill select for students

                            var select = '<select name="gotoselectuser">' + '<option value="0"></option>';
                            for (var option in params.optionsusers) {
                                select += '<option value="' + option + '">' + params.optionsusers[option] + '</option>';
                            }
                            select += '</select>';

                            jqtable.find('td.goto_student').each(function( index ) {
                                $userid = $( this ).text();
                                $( this ).html(select);
                                if ($userid){
                                    $( this ).find('select option[value="' + $userid + '"]').attr("selected",true);
                                }
                            });

                            jqtable.find('td.goto_student select').change(function( event ) {
                                var userid = $(this).val();
                                var attendaceid = $( this ).parents('tr:first').attr('id');

                                M.util.js_pending('mod_gotomeeting_updating_user' + attendaceid);
                                $.when(
                                    ajax.call([
                                        {
                                            methodname: 'mod_gotomeeting_update_user',
                                            args: {'attendaceid': attendaceid, 'userid': userid}
                                        }
                                    ])[0]
                                )
                                .then(function(response) {
                                    location.href = params.url;
                                })
                                .always(function() {
                                    M.util.js_complete('update_user' + attendaceid);
                                    return;
                                })
                                .fail(notification.exception);
                            });

                        },

                    });

                })
                .always(function() {
                    M.util.js_complete('mod_gotomeeting_loading_users_updateables' + courseid);
                    return;
                })
                .fail(notification.exception);

            }
        };
    }

);
