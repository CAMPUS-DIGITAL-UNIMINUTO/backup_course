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
 * @package   local_backup_course
 * @copyright 2024 Daniela Sierra https://danielasierra.com.co
 * @license   https://www.uniminuto.edu/  
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */   
function xmldb_local_backup_course_upgrade($oldversion) {
    global $DB,$CFG;
    if ($oldversion < 2024040403) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('bc_balck_list'); 
        if ($dbman->table_exists($table)) {$dbman->drop_table($table);}// Eliminar tabla si existe.

        $table = new xmldb_table('bc_config_sftp'); 
        if ($dbman->table_exists($table)) {$dbman->drop_table($table);}// Eliminar tabla si existe.

        $table = new xmldb_table('bc_excepcions_errors'); 
        if ($dbman->table_exists($table)) {$dbman->drop_table($table);}// Eliminar tabla si existe.


        //////////////tabla con nombre corregido//////////
        $table = new xmldb_table('bc_black_list');

        // Adding fields to table black_list.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('token', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('estado', XMLDB_TYPE_CHAR, '2', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table black_list.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        if (!$dbman->table_exists($table)) {$dbman->create_table($table); }//Crear tabla si no existe
        upgrade_plugin_savepoint(true, 2024040403, 'local', 'backup_course');
        return true;
    }
}
