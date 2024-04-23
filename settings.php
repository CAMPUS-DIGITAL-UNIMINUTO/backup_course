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
if ($hassiteconfig) {
    //gernerar el token para moodle y el web service
    global $DB;
    $tok = '2017.UVD_TokeN_noDos';
    
    $tb_tok = $DB->get_records_sql("SELECT id FROM {external_tokens} WHERE token = :token ",array('token'=>sha1($tok))); //saber si ya existe el token
    $tb_reg = $DB->get_records_sql('SELECT * FROM {bc_registro_pc} ');//saber si hay registros 
    $ext_ser1 = $DB->get_record_sql("SELECT id FROM {external_services} WHERE name = :name LIMIT 1",array('name'=>"Token_UVD"));// saber si ya existe el servicio web
    if(empty($tb_reg)&& empty($tb_tok)){
            $registro_token = new stdClass();
            $registro_token->token = sha1($tok);
            $registro_token->tokentype = 0;
            $registro_token->userid = 2;
            $registro_token->externalserviceid = $ext_ser1->id;
            $registro_token->id = null;
            $registro_token->contextid = 1;
            $registro_token->creatorid = 2;
            $registro_token->iprestriction = null;
            $registro_token->validuntil = 0;
            $registro_token->timecreated = time();
            $registro_token->lastaccess = null;
            $DB->insert_record('external_tokens', $registro_token); //Crear token
        }
        
    ////////////////////////////iniciar configuración del plugin/////////////////////////////////
    $ADMIN->add('localplugins', new admin_category('local_backup_course', new lang_string('pluginname', 'local_backup_course')));

    if(empty($tb_reg)){//solo si no hay registros
        $settings = new admin_settingpage('hijo_padre', 'Rol de instancia');
        
        $settings->add(new admin_setting_configselect('local_backup_course/instancia',
            new lang_string('instancia', 'local_backup_course'),
            new lang_string('instancia_desc', 'local_backup_course'), 
            0, array('Padre','Hijo')));
        $ADMIN->add('local_backup_course',$settings );
        
    }
    
/////////////////////////////Configuración cuando es Padre//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (empty(get_config('local_backup_course', 'instancia') )) {///padre

        $ir = new admin_externalpage('crear_hijo', get_string('crear_hijo', 'local_backup_course'), $CFG->wwwroot.'/local/backup_course/template/settings/view_hijos.php');
        $ADMIN->add('local_backup_course',$ir ); //Crear enlace para ir a ver la administración de hijos y tokens
        
        $ir = new admin_externalpage('view_updates', get_string('view_updates', 'local_backup_course'), $CFG->wwwroot.'/local/backup_course/template/settings/view_updates.php');
        $ADMIN->add('local_backup_course',$ir ); //Crear enlace para ir a ver la administración de actualizaciones
    }


    //Guardar el el registro la config de hijo
    if (!empty(get_config('local_backup_course', 'instancia')) ) {//hijo
        if(empty($tb_reg)){
            $registro_pc = new stdClass();
            $registro_pc->nombre = 'Soy Hijo';
            $registro_pc->url_hijo = 'soy_hijo';
            $registro_pc->token = sha1($tok);    
            $registro_pc->estado = 1;
            $registro_pc->edition = 0;
            $registro_pc->url_padre = 'url_padre';
            $registro_pc->startdate = 0;
            $registro_pc->enddate8 = 0;
            $registro_pc->enddate16 = 0;
            $DB->insert_record('bc_registro_pc', $registro_pc);
        }

    }
}