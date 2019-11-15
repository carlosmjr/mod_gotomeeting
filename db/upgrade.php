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
 * This file keeps track of upgrades to the gotomeeting module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package     mod_gotomeeting
 * @copyright   www.itoptraining.com
 * @author      info@itoptraining.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute gotomeeting upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_gotomeeting_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager();

    if ($oldversion < 2018010401) {

        $sql = "TRUNCATE TABLE {$CFG->prefix}gotomeeting_attendace_report";

        $DB->execute($sql);

        upgrade_plugin_savepoint(true, 2018010401, 'mod', 'gotomeeting');
    }
    if ($oldversion < 2018012300) {

        $table = new xmldb_table('gotomeeting_attendace_report');

        $field = new xmldb_field('updated', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2018012300, 'mod', 'gotomeeting');
    }

    if ($oldversion < 2019020400) {

        $table = new xmldb_table('gotomeeting_licenses');

        $field = new xmldb_field('data', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, '', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2019020400, 'mod', 'gotomeeting');
    }

    if ($oldversion < 2019020401) {

        $table = new xmldb_table('gotomeeting_licenses');

        $field = new xmldb_field('consumer_secret', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2019020401, 'mod', 'gotomeeting');
    }

    if ($oldversion < 2019020402) {

        $table = new xmldb_table('gotomeeting_licenses');

        $field = new xmldb_field('state', XMLDB_TYPE_CHAR, '255', null, false, null, '', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2019020402, 'mod', 'gotomeeting');
    }

    if ($oldversion < 2019020403) {

        $table = new xmldb_table('gotomeeting_licenses');

        $field = new xmldb_field('oauth', XMLDB_TYPE_INTEGER, '2', true, true, null, '1', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2019020403, 'mod', 'gotomeeting');
    }

    if ($oldversion < 2019102900) {

        $table = new xmldb_table('gotomeeting');
        $deletefields = ['insescod', 'class_id', 'vc_language', 'recording', 'recording_link', 'view_recording_link', 'recording_link_status'];
        gotomeeting_drop_fields($table, $deletefields, $dbman);

        $table = new xmldb_table('gotomeeting_licenses');
        $deletefields = ['oauth', 'email', 'password'];
        gotomeeting_drop_fields($table, $deletefields, $dbman);

        $deletetable = new xmldb_table('gotomeeting_content');
        if ($dbman->table_exists($deletetable)) {
            $dbman->drop_table($deletetable);
        }

        upgrade_plugin_savepoint(true, 2019102900, 'mod', 'gotomeeting');
    }

    return true;
}

/**
 * Drops the fields passed into the table
 *
 * @param   xmldb_table     $table  The table.
 * @param   array     $fields  array of fields to delete.
 * @param   array     $dbman  db manager (optional).
 */
function gotomeeting_drop_fields(xmldb_table $table, $fields, $dbman = null) {

    if (!is_array($fields)) {
        $fields = array($fields);
    }
    if (!$dbman) {
        global $DB;
        $dbman = $DB->get_manager();
    }

    foreach ($fields as $deletefield) {
        $field = new xmldb_field($deletefield);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
    }

}
