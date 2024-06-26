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
 * @package    local_remote_backup_provider
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$capabilities = array(

    'local/backup_course:access' => array(
        'riskbitmask' => RISK_CONFIG | RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    )
);

/*
global $DB;
$dbman = $DB->get_manager();
    // Add a bc_registro_pc
    $table = new xmldb_table('bc_registro_pc'); 
    if($DB->get_manager()->table_exists('bc_registro_pc')){
        // Adding fields to table bc_registro_pc.
        $field = new xmldb_field('startdate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);//añadir el campo startdate
        }
        $field = new xmldb_field('enddate8', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);//añadir el campo enddate8
        }
        $field = new xmldb_field('enddate16', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);//añadir el campo enddate16
        }
    }
 * */
 