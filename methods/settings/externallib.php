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
require_once '../../config.php';
require_once($CFG->dirroot . '/lib/externallib.php');
require_once($CFG->dirroot . '/lib/completionlib.php');
require_once($CFG->dirroot . '/local/backup_course/methods/ws_access.php');

class local_backup_token_external extends external_api
{
    /* 
     * Find_token_parameters
     * Estructura de los parametros que recibe el WS 
     * Peticion del padre     
     */

    public static function find_token_parameters()
    {
        return new external_function_parameters(
            array(
                'function' => new external_value(PARAM_CLEAN, 'function'),
                'url' => new external_value(PARAM_CLEAN, 'url'),
                'nombre' => new external_value(PARAM_CLEAN, 'nombre'),
                'token' => new external_value(PARAM_CLEAN, 'token'),
                'url_hijo' => new external_value(PARAM_CLEAN, 'url_hijo'),
                'estado' => new external_value(PARAM_CLEAN, 'estado'),
                'edition_acti' => new external_value(PARAM_CLEAN, 'edition_acti'),
                'startdate' => new external_value(PARAM_CLEAN, 'startdate'),
                'enddate8' => new external_value(PARAM_CLEAN, 'enddate8'),
                'enddate16' => new external_value(PARAM_CLEAN, 'enddate16')
            )
        );
    }
    /*
     * Funcion para crear el token
     * @params {string} $func
     * @params {string} $url_padre
     * @params {string} $nombre
     * @params {string} $token
     * @params {string} $url_hijo
     * @params {int} $estado
     * Retorna la verificacion de la creación
     * return {objet}
     */
    public static function find_token($func, $url_padre, $nombre, $token, $url_hijo, $estado, $edition_acti, $startdate, $enddate8, $enddate16)
    {
        global $DB, $USER, $CFG;
        $obj = new self();
        $params = array(
            'function' => $func,
            'url' => $url_padre,
            'nombre' => $nombre,
            'token' => $token,
            'edition' => $edition_acti,
            'url_hijo' => $url_hijo,
            'startdate' => $startdate,
            'enddate8' => $enddate8,
            'enddate16' => $enddate16,
            'estado' => $estado
        );

        $permis = new ws_access();
        $rest = array('ack' => $permis->perms($params));


        return $rest;
    }
    /*
     * Estructura de la respuesta de WS
     */
    public static function find_token_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'ack'  => new external_value(PARAM_RAW, 'respuesta 1 o 0'),
                    'response'  => new external_value(PARAM_RAW, 'respuesta del server'),
                )
            )
        );
    }

    /* List Courses
     * Peticion del nodo
     * Estructura de los parametros que recibe el WS 
     */

    public static function find_courses_parameters()
    {
        return new external_function_parameters(
            array(
                'function' => new external_value(PARAM_CLEAN, 'function'),
                'search' => new external_value(PARAM_CLEAN, 'search'),
                'token' => new external_value(PARAM_CLEAN, 'token'),
                /* 'ip' => new external_value(PARAM_CLEAN, 'ip'), */
                'url_hijo' => new external_value(PARAM_CLEAN, 'url_hijo'),
                'estado' => new external_value(PARAM_CLEAN, 'estado'),
            )
        );
    }
    /*
     * Funcion para buscar la información general de los cursos en el padre
     * @params {string} $func
     * @params {string} $search
     * @params {string} $token
     * @params {string} $ip
     * @params {string} $url_hijo
     * @params {int} $estado
     * Retorna los cursos encontrados
     * return {objet}
     */
    public static function find_courses($func, $search, $token,/* $ip, */ $url_hijo, $estado)
    {
        //Function Q02
        global $DB;
        $params = array(
            'function' => $func,
            'search' => $search,
            'token' => $token,
            /* 'ip' => $ip,  */
            'url' => $url_hijo,
            'estado' => $estado
        );
        $permis = new ws_access();
        $rest = $permis->perms($params);

        if (!has_capability('moodle/course:viewhiddencourses', context_system::instance())) {
            return false;
        }
        return $rest;
    }
    /*
     * Estructura de la respuesta de WS
     */
    public static function find_courses_returns()
    {
        return
            new external_single_structure(
                array(
                    'ack'  => new external_value(PARAM_RAW, 'respuesta 1 o 0'),
                    'response'  => new external_value(PARAM_RAW, 'respuesta objet'),
                )
            );
    }

    /* 
     * List Activitys -> Traer section and activities
     * Estructura de los parametros que recibe el WS 
     * peticion del nodo
     */

    public static function find_coursesAct_parameters()
    {
        return new external_function_parameters(
            array(
                'function' => new external_value(PARAM_CLEAN, 'function'),
                'search' => new external_value(PARAM_CLEAN, 'search'),
                'token' => new external_value(PARAM_CLEAN, 'token'),
                /* 'ip' => new external_value(PARAM_CLEAN, 'ip'), */
                'url_hijo' => new external_value(PARAM_CLEAN, 'url_hijo'),
                'estado' => new external_value(PARAM_CLEAN, 'estado'),
                'id_nodo' => new external_value(PARAM_CLEAN, 'id_nodo'),
                'id_course' => new external_value(PARAM_CLEAN, 'id_course'),
            )
        );
    }

    /*
     * Funcion para buscar las secciones y actividades del seleccionado
     * @params {string} $func
     * @params {string} $search
     * @params {string} $token
     * @params {string} $ip
     * @params {string} $url_hijo
     * @params {int} $estado
     * @params {int} $id_nodo
     * @params {int} $id_course
     * Retorna las secciones y sus respectivas actividades
     * return {objet}
     */
    public static function find_coursesAct($func, $search, $token,/* $ip, */ $url_hijo, $estado, $id_nodo, $id_course)
    {
        global $DB, $CFG, $USER;

        $sections = $DB->get_records_sql("SELECT sect.* FROM {course_sections} sect WHERE sect.course = :token ", array('token' => $id_nodo));
        $errors = new stdClass();
        $errors->userid = 0;
        $errors->courseid = $id_course;
        $sect = array();
        $props = array();
        if ($DB->get_manager()->table_exists('act_obj_create')) {
            $ob_prop_query = $DB->get_records_sql("SELECT act.id, act.courseid_h, obj.id_como_p, obj.obj
                                                    FROM {act_activities_uvd} act 
                                                    LEFT JOIN {act_obj_create} obj ON obj.activitiesid = act.id
                                                    WHERE act.courseid_h = :token AND act.relationid > 0", array('token' => $id_nodo));

            if (!empty($ob_prop_query)) {
                foreach ($ob_prop_query as $key => $value) {
                    $obj_pro = json_decode($value->obj);
                    if (!empty($obj_pro)) {
                        foreach ($obj_pro as $k => $v) {
                            if ($k == 'urls') {
                                for ($i = 0; $i < count($v); $i++) {
                                    $urls = $DB->get_record_sql(
                                        "SELECT como.id, como.instance, como.section 
                                                                    FROM {course_modules} como 
                                                                    LEFT JOIN {modules} mo ON mo.id = como.module
                                                                    WHERE mo.name = 'url' AND como.course = :id_nodo AND como.instance = :instance LIMIT 1",
                                        array('id_nodo' => $id_nodo, 'instance' => $v[$i]->id)
                                    );
                                    if (!empty($urls)) {
                                        //$urls_pro = each($urls);
                                        //$urls_pro = $urls[0];
                                        $props[] = $urls->id;
                                    }
                                }
                            } else if ($k == 'rel_course_modules')
                                $props[] = $v->id;
                        }
                    }
                }
            }
        }

        foreach ($sections as $key => $value) {
            $value->activities = null;
            if (!empty($value->sequence)) {
                $ids_como = explode(',', $value->sequence);
                $acty = array();
                $activities = array();
                for ($i = 0; $i < count($ids_como); $i++) {
                    $propuestas = null;
                    if ($DB->get_manager()->table_exists('act_obj_create')) {
                        $propuestas = $DB->get_record_sql(
                            'SELECT o.* FROM {act_obj_create} o 
                                                            LEFT JOIN {act_activities_uvd} a ON a.id = o.activitiesid
                                                            WHERE o.id_como_p = :id_como_p AND a.relationid >0',
                            array('id_como_p' => $ids_como[$i],)
                        );
                        if (empty($propuestas)) {
                            $pos = array_search($ids_como[$i], $props);
                            if (is_int($pos)) {
                                $propuestas = 1;
                            }
                        }
                    }
                    if (empty($propuestas)) {

                        $name_module = $DB->get_records_sql('SELECT como.*, como.instance AS id_table, 
                                                                modu.name AS name_table
                                                                FROM {course_modules} como 
                                                                LEFT JOIN {modules} modu ON como.module = modu.id
                                                                WHERE como.course = :id_nodo AND como.id = :ids_como ', array('id_nodo' => $id_nodo, 'ids_como' => $ids_como[$i]));


                        foreach ($name_module as $k => $val) {
                            $activities['act'] = $DB->get_record($val->name_table, array('id' => $val->id_table, 'course' => $id_nodo));
                            $activities['table'] = $val->name_table;
                            $activities['como'] = $name_module;
                            $obj = new self();
                            //$info_acti = each($activities['act']);
                            $info_acti = array();
                            $info_acti['value'] = $activities['act'];
                            $activities['info'] = json_encode($obj->getTableActivities($val->name_table, $info_acti, $id_course, $url_hijo, $ids_como[$i], $id_nodo));
                            $acty[] =  $activities;
                        }
                        $value->activities = json_encode($acty);
                    }
                }
            }
            $sect[] = json_encode($value);
        }

        $sects = json_encode($sect);
        $resp = new stdClass();
        $resp->ack = 1;
        $resp->response = $sects;

        return $resp;
    }
    /*
     * Estructura de la respuesta de WS
     */
    public static function find_coursesAct_returns()
    {
        return //new external_multiple_structure(
            new external_single_structure(
                array(
                    'ack'  => new external_value(PARAM_INT, 'respuesta 1 o 0'),
                    'response'  => new external_value(PARAM_RAW, 'objeto de la info del curso'),
                )
            );
        //);
    }
    /* 
     * find_relation_parameters ->objet relation activities padre e hijo
     * Estructura de los parametros que recibe el WS 
     * peticion del nodo
     */

    public static function find_relation_parameters()
    {
        return new external_function_parameters(
            array(
                'function' => new external_value(PARAM_CLEAN, 'function'),
                'url' => new external_value(PARAM_CLEAN, 'url'),
                'token' => new external_value(PARAM_CLEAN, 'token'),
                /* 'ip' => new external_value(PARAM_CLEAN, 'ip'), */
                'url_padre' => new external_value(PARAM_CLEAN, 'url_padre'),
                'id_reg' => new external_value(PARAM_CLEAN, 'id_reg'),
                'id_nodo' => new external_value(PARAM_CLEAN, 'id_nodo'),
                'id_padre' => new external_value(PARAM_CLEAN, 'id_padre'),
                'estado' => new external_value(PARAM_CLEAN, 'estado'),
                'obj' => new external_value(PARAM_RAW, 'obj'),
                'id_user' => new external_value(PARAM_CLEAN, 'id_user')
            )
        );
    }
    /*
     * Funcion para crear la relacion de las secciones y actividades creadas en el nodo con las del padre
     * @params {string} $func
     * @params {string} $url_hijo
     * @params {string} $token
     * @params {string} $ip
     * @params {string} $url_padre
     * @params {int} $id_reg
     * @params {int} $id_nodo
     * @params {int} $id_padre
     * @params {int} $estado
     * @params {obj} $obj
     * @params {int} $id_user
     * Retorna la verificación de creación
     * return {objet}
     */
    public static function find_relation($func, $url_hijo, $token,/* $ip, */ $url_padre, $id_reg, $id_nodo, $id_padre, $estado, $obj, $id_user)
    {
        global $DB, $USER, $CFG;
        $params = array(
            'function' => $func,
            'url' => $url_hijo,
            'token' => $token,
            /* 'ip' => $ip,  */
            'url_padre' => $url_padre,
            'id_reg' => $id_reg,
            'id_nodo' => $id_nodo,
            'id_padre' => $id_padre,
            'estado' => $estado,
            'obj' => $obj,
            'id_user' => $id_user
        );

        $permis = new ws_access();
        $rest = $permis->perms($params);
        return $rest;
    }
    /*
     * Estructura de la respuesta de WS
     */
    public static function find_relation_returns()
    {
        return new external_single_structure(
            array(
                'ack'  => new external_value(PARAM_RAW, 'respuesta 1 o 0'),
                'response'  => new external_value(PARAM_RAW, 'respuesta del server'),
            )
        );
    }


    /* 
     * List Info Course
     * Peticion del nodo 
     * Estructura de los parametros que recibe el WS 
     */

    public static function find_bloques_parameters()
    {
        return new external_function_parameters(
            array(
                'id_course' => new external_value(PARAM_CLEAN, 'id_course')
            )
        );
    }
    /*
     * Funcion para buscar la toda la información del curso padre (bloques, calificaciones, grupos, agrupaciones)
     * @params {int} $id_course
     * Retorna objeto con la información del curso
     * return {objet}
     */
    public static function find_bloques($id_course)
    {
        $infoCourse = new stdClass();
        $infoCourse->id_course = $id_course;
        $params = array(
            'id_course' => $id_course,
            'infoCourse' => $infoCourse,
            'function' => 'Q04'
        );
        $permis = new ws_access();
        $rest = $permis->perms($params);

        $resp = new stdClass();
        $resp->ack = 1;
        $resp->response = json_encode($rest);
        return $resp;
    }
    /*
     * Estructura de la respuesta de WS
     */
    public static function find_bloques_returns()
    {
        return
            new external_single_structure(
                array(
                    'ack'  => new external_value(PARAM_RAW, 'respuesta 1 o 0'),
                    'response'  => new external_value(PARAM_RAW, 'respuesta objet'),
                )
            );
    }


    /* 
     * find_deleTem_parameters -> Eliminar archivos temporales
     * Estructura de los parametros que recibe el WS 
     * peticion del Padre 
     */

    public static function find_deleTem_parameters()
    {
        return new external_function_parameters(
            array(
                'name_arc_p' => new external_value(PARAM_CLEAN, 'name_arc_p'),
            )
        );
    }
    /*
     * Funcion para buscar y eliminar el archivo temporal para los scrom
     * @params {int} $name_arc_p
     * Retorna objeto con la verificacion
     * return {objet}
     */
    public static function find_deleTem($name_arc_p)
    {
        global $DB, $USER, $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $params = array('name_arc_p' => $name_arc_p);
        $resp = new stdClass();
        $archivos = $name_arc_p;
        if (file_exists($archivos)) {
            unlink($archivos);
            $resp->ack = 1;
        } else {
            $resp->ack = 0;
        }
        return $resp;
    }
    /*
     * Estructura de la respuesta de WS
     */
    public static function find_deleTem_returns()
    {
        return new external_single_structure(
            array(
                'ack'  => new external_value(PARAM_RAW, 'respuesta 1 o 0'),
            )
        );
    }


    /* Banco de preguntas del curso
     * Peticion del nodo
     * Estructura de los parametros que recibe el WS 
     */

    public static function find_BanckPreguntas_parameters()
    {
        return new external_function_parameters(
            array(
                'function' => new external_value(PARAM_CLEAN, 'function'),
                'id_course' => new external_value(PARAM_CLEAN, 'id_course'),
                'id_nodo' => new external_value(PARAM_CLEAN, 'id_nodo'),
            )
        );
    }
    /*
     * Funcion para el banco de preguntas del curso
     * @params {string} $func
     * @params {string} $search
     * @params {string} $token
     * @params {string} $ip
     * @params {string} $url_hijo
     * @params {int} $estado
     * Retorna categorias y preguntas encontradas
     * return {objet}
     */
    public static function find_BanckPreguntas($func, $id_course, $id_nodo)
    {
        $params = array(
            'function' => $func,
            'id_course' => $id_course,
            'id_nodo' => $id_nodo,
        );

        $permis = new ws_access();
        $rest = $permis->perms($params);
        $respuesta = new stdClass();
        $respuesta->ack = 1;
        $respuesta->response = $rest;
        if (!has_capability('moodle/course:viewhiddencourses', context_system::instance())) {
            return false;
        }
        return $respuesta;
    }
    /*
     * Estructura de la respuesta de WS
     */
    public static function find_BanckPreguntas_returns()
    {
        return new external_single_structure(
            array(
                'ack'  => new external_value(PARAM_RAW, 'respuesta 1 o 0'),
                'response'  => new external_value(PARAM_RAW, 'respuesta objet'),
            )
        );
    }


    public static function find_Rubrica_parameters()
    {
        return new external_function_parameters(
            array(
                'function' => new external_value(PARAM_CLEAN, 'function'),
                'id_course' => new external_value(PARAM_CLEAN, 'id_course'),
                'id_nodo' => new external_value(PARAM_CLEAN, 'id_nodo'),
            )
        );
    }


    public static function find_Rubrica($func, $id_course, $id_nodo)
    {
        $params = array(
            'function' => $func,
            'id_course' => $id_course,
            'id_nodo' => $id_nodo,
        );

        $permis = new ws_access();
        $rest = $permis->perms($params);
        $respuesta = new stdClass();
        $respuesta->ack = 1;
        $respuesta->response = $rest;

        return $respuesta;
    }

    public static function find_Rubrica_returns()
    {
        return new external_single_structure(
            array(
                'ack'  => new external_value(PARAM_RAW, 'respuesta 1 o 0'),
                'response'  => new external_value(PARAM_RAW, 'respuesta object'),
            )
        );
    }

    

    /*
     * Query para traer la información de cada actividad
     * retunr {arrray}
     */
    public function getTableActivities($typeActi, $info_acti, $id_course, $url_hijo = null, $id_como, $id_nodo)
    {
        global $DB, $CFG, $SESSION;
        $activities = array();

        switch ($typeActi) {
            case 'assign':
                $activities['assign_plugin_config'] = $DB->get_records('assign_plugin_config', array('assignment' => $info_acti['value']->id));
                $activities['context'] = $DB->get_records('context', array('instanceid' => $id_como, 'contextlevel' => 70));
                foreach ($activities['context'] as $val) {
                    $activities['grading_areas'] = $DB->get_records('grading_areas', array('contextid' => $val->id));
                }
                if (array_key_exists('grading_areas', $activities) && (is_array($activities['grading_areas']) || is_object($activities['grading_areas']))) {
                    foreach ($activities['grading_areas'] as $value) {
                        $activities['grading_definitions'] = $DB->get_records('grading_definitions', array('areaid' => $value->id));
                    }
                }

                if (array_key_exists('grading_definitions', $activities) && (is_array($activities['grading_definitions']) || is_object($activities['grading_definitions']))) {
                    foreach ($activities['grading_definitions'] as $key => $value) {
                        $activities['gradingform_rubric_criteria'][$key] = $DB->get_records('gradingform_rubric_criteria', array('definitionid' => $value->id));
                        foreach ($activities['gradingform_rubric_criteria'][$key] as $k => $v) {
                            $activities['gradingform_rubric_levels'][$k] = $DB->get_records('gradingform_rubric_levels', array('criterionid' => $v->id));
                        }
                    }
                }

                break;
            case 'attendance':
                $activities['attendance_statuses'] = $DB->get_records('attendance_statuses', array('attendanceid' => $info_acti['value']->id));
                $activities['attendance_sessions'] = $DB->get_records('attendance_sessions', array('attendanceid' => $info_acti['value']->id));
                break;
            case 'book':
                $activities['book_chapters'] = $DB->get_records('book_chapters', array('bookid' => $info_acti['value']->id));
                break;
            case 'certificate':
                $activities['certificate_issues'] = $DB->get_records('certificate_issues', array('certificateid' => $info_acti['value']->id));
                break;
            case 'chat':
                $activities = array();
                break;
            case 'choice':
                $activities['choice_options'] = $DB->get_records('choice_options', array('choiceid' => $info_acti['value']->id));
                break;
            case 'choicegroup':
                $activities['choicegroup_options'] = $DB->get_records('choicegroup_options', array('choicegroupid' => $info_acti['value']->id));
                break;
            case 'collaborate':
                $activities = array();
                break;

            case 'customcert':
                $activities = array();
                $context_como = context_module::instance($id_como);

                $customcert_templates =  $DB->get_record('customcert_templates', array('id' => $info_acti['value']->templateid));
                $activities['customcert_templates'] = (array) clone ($customcert_templates);

                $rel = $DB->get_record('bc_registro_pc', array('url_hijo' => $url_hijo));
                $activities['id_rel'] = $rel->id;
                $activities['customcert_archivos'] = array();
                $activities['url_customcert'] = array();
                $activities['customcert_pages'] = array();
                $activities['customcert_elements'] = array();
                $activities['customcert_pages'] = $DB->get_records('customcert_pages', array('templateid' => $customcert_templates->id));

                $customcert_pages = $activities['customcert_pages'];
                foreach ($customcert_pages as $k_e2 => $v_a2) {
                    $customcert_elements = $DB->get_records('customcert_elements', array('pageid' => $v_a2->id));
                    foreach ($customcert_elements as $k_e3 => $v_a3) {
                        if ($v_a3->data) {
                            $ob_data = json_decode($v_a3->data);
                            if (property_exists($ob_data, 'contextid') && property_exists($ob_data, 'filename')  && property_exists($ob_data, 'filearea')) {
                                $file = $DB->get_record('files', array(
                                    'contextid' => $ob_data->contextid, 'filename' => $ob_data->filename,
                                    'component' => 'mod_customcert', 'filearea' => $ob_data->filearea
                                ));
                                if ($file && !empty($file) && property_exists($file, 'contenthash')) {
                                    $archivo = $file->contenthash;
                                    $v_a3->customcert_archivo = $archivo;
                                    //$v_a3->customcert_files = $file;

                                    $cr1 = substr($archivo, 0, 2);
                                    $cr2 = substr($archivo, 2, 2);
                                    $name_archive = $archivo . '_' . $id_course . '_' . $rel->id;
                                    $moodle_data = $CFG->dataroot . '/filedir/' . $cr1 . '/' . $cr2 . '/' . $archivo;
                                    require_once 'folder_S3/controlador2_m.php';
                                    $s3 = new Controlador2_m();
                                    $v_a3->url_customcert = $s3->run('create', $moodle_data, $name_archive, $id_course);
                                }
                            }
                        }
                        $activities['customcert_elements'][$k_e3] = (array)$v_a3;
                    }
                }
                break;
            case 'data':
                $activities['data_fields'] = $DB->get_records('data_fields', array('dataid' => $info_acti['value']->id));
                break;
            case 'feedback':
                $activities['feedback_item'] = $DB->get_records('feedback_item', array('feedback' => $info_acti['value']->id));
                break;
            case 'folder':

                $array_url = array();

                $array_name_archive = array();

                $array_moodle_data = array();

                $array_files_dir = array();

                $array_files_fold = array();

                $array_hashed = array();

                require_once 'folder_S3/controlador2_m.php';
                $s3 = new Controlador2_m();

                $rel = $DB->get_record('bc_registro_pc', array('url_hijo' => $url_hijo));

                $id_course = $id_course;

                $contextIdFold = context_module::instance($id_como);

                $files_fold = $DB->get_records_select(
                    'files',
                    'contextid = :contextid AND component = :component AND filesize > 0',
                    array(
                        'contextid' => $contextIdFold->id,
                        'component' => 'mod_folder',
                        'filearea' => 'content'
                    )
                );

                array_push($array_files_fold, $files_fold);

                foreach ($files_fold as $kf => $vkf) {

                    $archivo = $vkf->contenthash;

                    $ext = pathinfo($vkf->filename, PATHINFO_EXTENSION);

                    $name_archive = "";

                    if (!empty($ext)) {

                        $timestamp = time();

                        $uniqueString = 'Unimin2023' . $timestamp;

                        $hashedValue = hash('sha256', $uniqueString);

                        array_push($array_hashed, $hashedValue);

                        $name_archive = $archivo . '_' . $hashedValue . '_' . $id_course . '_' . $rel->id . "." . $ext;

                        array_push($array_name_archive, $name_archive);
                    }

                    $cr1 = substr($archivo, 0, 2);
                    $cr2 = substr($archivo, 2, 2);

                    $file_dir = '/filedir/' . $cr1 . '/' . $cr2 . '/' . $archivo;

                    array_push($array_files_dir, $file_dir);

                    $moodle_data = $CFG->dataroot . '/filedir/' . $cr1 . '/' . $cr2 . '/' . $archivo;

                    array_push($array_moodle_data, $moodle_data);

                    array_push($array_url, $s3->run('create', $moodle_data, $name_archive, $id_course));
                }

                $activities['urls_file'] = json_encode($array_url);
                $activities['moodle_data_folder'] = json_encode($array_moodle_data);
                $activities['name_archive_folder'] = json_encode($array_name_archive);
                $activities['files_fold'] = json_encode($array_files_fold);
                $activities['hashed'] = json_encode($array_hashed);
                $activities['files_dir'] = json_encode($array_files_dir);
                $activities['rel'] = $rel->id;

                break;
            case 'forum':
                $activities['forum_discussions'] = $DB->get_records('forum_discussions', array('forum' => $info_acti['value']->id));
                foreach ($activities['forum_discussions'] as $key => $value) {
                    $activities['forum_posts'][$key] = $DB->get_records('forum_posts', array('discussion' => $value->id));
                }
                break;
            case 'game':
                $activities = array();
                break;
            case 'glossary':  //ojo
                $activities['glossary_entries'] = $DB->get_records('glossary_entries', array('glossaryid' => $info_acti['value']->id));
                break;
            case 'groupselect':
                $activities = array();
                break;
            case 'h5pactivity':

                $rel = $DB->get_record('bc_registro_pc', array('url_hijo' => $url_hijo));
                $activities['id_rel'] = $rel->id;
                $activities['id_h5pactivity'] = $info_acti['value']->id;

                if ($DB->get_manager()->table_exists('admin_english')) {
                    $activities['admin_english'] = $DB->get_records('admin_english', array('hvpid' => $info_acti['value']->id));
                }
                break;
            case 'hvp':
                $hvp_contents_libraries = $DB->get_records('hvp_contents_libraries', array('hvp_id' => $info_acti['value']->id));
                $json_content = json_decode($info_acti['value']->json_content);
                if (property_exists($json_content, 'presentation')) {
                    foreach ($json_content->presentation->slides as $key => $value) {
                        if (property_exists($value, 'elements')) {
                            for ($j = 0; $j < count($value->elements); $j++) {
                                $vers = explode(" ", $value->elements[$j]->action->library);
                                $num = explode(".", $vers[1]);
                                $li = $DB->get_record('hvp_libraries', array('machine_name' => $vers[0], 'major_version' => $num[0], 'minor_version' => $num[1]));
                                //$activities['hvp_libraries'][$value->elements[$j]->action->library] = $DB->get_record('hvp_libraries', array('machine_name' => $vers[0], 'major_version'=>$num[0], 'minor_version'=>$num[1]));
                                $activities['hvp_libraries'][$li->id] = $DB->get_record('hvp_libraries', array('machine_name' => $vers[0], 'major_version' => $num[0], 'minor_version' => $num[1]));
                            }
                        }
                    }
                }
                $main_library_id = $DB->get_record('hvp_libraries', array('id' => $info_acti['value']->main_library_id));
                $activities['main_library_id'] = $main_library_id;
                foreach ($hvp_contents_libraries as $key => $value) {
                    $activities['hvp_libraries'][$key] = $DB->get_record('hvp_libraries', array('id' => $value->library_id));
                }
                $activities['hvp_contents_libraries'] = $hvp_contents_libraries;
                $rel = $DB->get_record('bc_registro_pc', array('url_hijo' => $url_hijo));

                //$rel = each($rel);
                $activities['id_rel'] = $rel->id;
                $activities['id_hvp'] = $info_acti['value']->id;

                if ($DB->get_manager()->table_exists('admin_english')) {
                    $activities['admin_english'] = $DB->get_records('admin_english', array('hvpid' => $info_acti['value']->id));
                }

                break;
            case 'imscp':
                $activities = array();
                break;
            case 'journal':
                $activities = array();
                break;
            case 'label':
                $activities = array();
                break;
            case 'lesson':
                $activities['lesson_pages'] = $DB->get_records('lesson_pages', array('lessonid' => $info_acti['value']->id));
                $activities['lesson_answers'] = $DB->get_records('lesson_answers', array('lessonid' => $info_acti['value']->id));
                break;
            case 'lti':
                $activities['lti_types'] = $DB->get_record('lti_types', array('id' => $info_acti['value']->typeid));
                $activities['lti_types_config'] = $DB->get_records('lti_types_config', array('typeid' => $info_acti['value']->typeid));
                break;
            case 'page':
                $activities = array();
                break;
            case 'quiz':
                $question = array();
                $activities['quiz_sections'] = $DB->get_records('quiz_sections', array('quizid' => $info_acti['value']->id));
                $activities['quiz_feedback'] = $DB->get_records('quiz_feedback', array('quizid' => $info_acti['value']->id));

                $activities['context'] = $DB->get_records('context', array('instanceid' => $id_como, 'contextlevel' => 70));
                $activities['quiz_slots'] = $DB->get_records('quiz_slots', array('quizid' => $info_acti['value']->id));
                $question_references = array();
                $question_set_references = array();
                $question_bank_entries = array();
                $question_categories = array();
                $question_versions = array();

                foreach ($activities['quiz_slots'] as $key0 => $val) {
                    $question_references[$key0] = $DB->get_records('question_references', array('itemid' => $val->id));
                    foreach ($question_references[$key0] as $key2 => $val2) {
                        $arr_bank_entries = $DB->get_records('question_bank_entries', array('id' => $val2->questionbankentryid));
                        $question_bank_entries[$key2] = array_values($arr_bank_entries);
                        $question_categories[$key2] = $DB->get_records('question_categories', array('id' => $question_bank_entries[$key2][0]->questioncategoryid));

                        $vers = $DB->get_records('question_versions', array('questionbankentryid' => $question_bank_entries[$key2][0]->id));
                        $question_versions[$key2] = $vers;
                        $activities = array_merge($activities, $this->questions($vers));
                    }
                    $question_set_references[$key0] = $DB->get_records('question_set_references', array('itemid' => $val->id));
                    foreach ($question_set_references[$key0] as $key2 => $val2) {
                        $question_categories[$key2] = $DB->get_records('question_categories', array('contextid' => $val2->questionscontextid));
                        foreach ($question_categories[$key2] as $key30 => $val30) {
                            $question_bank_entries[$key30] = $DB->get_records('question_bank_entries', array('questioncategoryid' => $val30->id));
                            foreach ($question_bank_entries[$key30] as $key3 => $val3) {
                                $vers = $DB->get_records('question_versions', array('questionbankentryid' => $val3->id));
                                $question_versions[$key3] = $vers;
                                $activities = array_merge($activities, $this->questions($vers));
                            }
                        }
                    }
                }
                $activities['question_categories'] = $question_categories;
                $activities['question_bank_entries'] = $question_bank_entries;
                $activities['question_references'] = $question_references;
                $activities['question_set_references'] = $question_set_references;

                $activities['question_versions'] = $question_versions;

                break;
            case 'resource':
                $activities = array();
                break;
            case 'scorm':
                $archivo = $info_acti['value']->sha1hash;
                $activities['scoes'] = $DB->get_records_sql('SELECT * FROM {scorm_scoes} s WHERE s.scorm = :scorm', array('scorm' => $info_acti['value']->id));
                $scoes_data = $activities['scoes'];
                $activities['scoes_data'] = array();
                foreach ($scoes_data as $k_e => $v_a) {
                    $activities['scoes_data'][$k_e] = $DB->get_records('scorm_scoes_data', array('scoid' => $v_a->id));
                }
                /////////sftp///////////
                $rel = $DB->get_record('bc_registro_pc', array('url_hijo' => $url_hijo));
                //$rel = each($rel);
                $activities['id_rel'] = $rel->id;
                $cr1 = substr($archivo, 0, 2);
                $cr2 = substr($archivo, 2, 2);
                $name_archive = $archivo . '_' . $id_course . '_' . $rel->id;
                $moodle_data = $CFG->dataroot . '/filedir/' . $cr1 . '/' . $cr2 . '/' . $archivo;

                /////s3
                $name_archive = $archivo . '_' . $id_course . '_' . $rel->id . '.zip';
                $moodle_data = $CFG->dataroot . '/filedir/' . $cr1 . '/' . $cr2 . '/' . $archivo;
                require_once 'folder_S3/controlador2_m.php';
                $s3 = new Controlador2_m();
                $activities['url_scorm'] = $s3->run('create', $moodle_data, $name_archive, $id_course);
                break;
            case 'survey':
                $activities = array();
                break;
            case 'url':
                $activities = array();
                break;
            case 'wiki':
                $activities = array();
                break;
            case 'workshop':

                $activities['workshopallocation_scheduled'] = $DB->get_records('workshopallocation_scheduled', array('workshopid' => $info_acti['value']->id));
                $activities['workshopform_accumulative']    = $DB->get_records('workshopform_accumulative', array('workshopid' => $info_acti['value']->id));
                $activities['workshopform_comments']        = $DB->get_records('workshopform_comments', array('workshopid' => $info_acti['value']->id));
                $activities['workshopform_numerrors']       = $DB->get_records('workshopform_numerrors', array('workshopid' => $info_acti['value']->id));
                $activities['workshopform_numerrors_map']   = $DB->get_records('workshopform_numerrors_map', array('workshopid' => $info_acti['value']->id));
                $activities['workshopform_rubric']          = $DB->get_records('workshopform_rubric', array('workshopid' => $info_acti['value']->id));
                foreach ($activities['workshopform_rubric'] as $key => $val) {
                    $activities['workshopform_rubric_levels'][$key] = $DB->get_records('workshopform_rubric_levels', array('dimensionid' => $key));
                }
                $activities['workshopform_rubric_config']   = $DB->get_records('workshopform_rubric_config', array('workshopid' => $info_acti['value']->id));

                break;
        }
        return $activities;
    }


    private $_preguntas = array();


    public function questions($versions)
    {
        global $DB;
        $activities = array();
        foreach ($versions as $key4 => $value4) {
            $this->_preguntas[$key4] = $DB->get_records('question', array('id' => $value4->id));
            foreach ($this->_preguntas[$key4] as $k => $v) {

                $activities['question_answers'][$k] = $DB->get_records('question_answers', array('question' => $v->id));

                $activities['question_attempts'][$k] = $DB->get_records('question_attempts', array('questionid' => $v->id));
                $activities['question_multianswer'][$k] = $DB->get_records('question_multianswer', array('question' => $v->id));

                $activities['question_truefalse'][$k] = $DB->get_records('question_truefalse', array('question' => $v->id));

                $activities['qtype_ddimageortext'][$k] = $DB->get_records('qtype_ddimageortext', array('questionid' => $v->id));
                $activities['qtype_ddimageortext_drags'][$k] = $DB->get_records('qtype_ddimageortext_drags', array('questionid' => $v->id));
                $activities['qtype_ddimageortext_drops'][$k] = $DB->get_records('qtype_ddimageortext_drops', array('questionid' => $v->id));
                $activities['qtype_ddmarker'][$k] = $DB->get_records('qtype_ddmarker', array('questionid' => $v->id));
                $activities['qtype_ddmarker_drags'][$k] = $DB->get_records('qtype_ddmarker_drags', array('questionid' => $v->id));
                $activities['qtype_ddmarker_drops'][$k] = $DB->get_records('qtype_ddmarker_drops', array('questionid' => $v->id));
                $activities['qtype_essay_options'][$k] = $DB->get_records('qtype_essay_options', array('questionid' => $v->id));
                $activities['qtype_match_options'][$k] = $DB->get_records('qtype_match_options', array('questionid' => $v->id));
                $activities['qtype_match_subquestions'][$k] = $DB->get_records('qtype_match_subquestions', array('questionid' => $v->id));
                $activities['qtype_multichoice_options'][$k] = $DB->get_records('qtype_multichoice_options', array('questionid' => $v->id));
                $activities['qtype_randomsamatch_options'][$k] = $DB->get_records('qtype_randomsamatch_options', array('questionid' => $v->id));
                $activities['qtype_shortanswer_options'][$k] = $DB->get_records('qtype_shortanswer_options', array('questionid' => $v->id));
            }
        }
        $activities['question'] = $this->_preguntas;
        return $activities;
    }
}

