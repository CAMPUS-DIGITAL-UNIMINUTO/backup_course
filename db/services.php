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

$services = array(
     'Token_UVD' => array(
          'functions' => array(
               'local_remoter_register_node'

          ),
          'restrictedusers' => 0,
          'enabled' => 1,
          'timecreated' => time(),
          'shortname' => 'token',
     ),

);

$functions = array(


     'local_remoter_register_node' => array(
          'classname' => 'local_backup_token_external',
          'methodname' => 'find_token',
          'classpath' => 'local/backup_course/methods/settings/externallib.php',
          'description' => 'Nodos creados en la tabla de registro.',
          'type' => 'read',
          'services' => array('token'),
     )



);
