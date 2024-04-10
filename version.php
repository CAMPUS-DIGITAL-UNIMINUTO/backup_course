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

$plugin->component = 'local_backup_course';
$plugin->cron      = 300;
$plugin->maturity  = MATURITY_ALPHA;
$plugin->requires  = 2022112800;
$plugin->version   = 2024040100;
$plugin->release = 'For 4.1.7';
