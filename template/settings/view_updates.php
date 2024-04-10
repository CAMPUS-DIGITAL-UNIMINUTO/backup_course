<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../../../../config.php');

$context = context_user::instance($USER->id, MUST_EXIST);
$PAGE->set_context($context);
$PAGE->requires->css('/local/backup_course/css/base/style.css');
$PAGE->requires->css('/local/backup_course/css/base/tostadas.css');
$PAGE->requires->css('/local/backup_course/css/base/jquery-confirm.css');
$PAGE->requires->jquery();
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/backup_course/js/base/llamados.js') );
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/backup_course/js/base/mensajes.js') );
echo '<div id="snackbar"></div>';
$url = new moodle_url($FULLME);
$PAGE->set_url($url); 
$PAGE->set_heading($USER->id);
$PAGE->set_pagelayout('report');
require_login(0,false);
echo $OUTPUT->header();
?>
<div class="loader"></div>
 <br>
<div class="row" id="contenListCourse_import">
            <div class="col-md-6" id="lista_cate_act" >
                <div class="box box-info box_content_cate">
                    <div class="box-header with-border">
                        <h4 class="box-title">Lista de Nodos actualizados</h4>
                        <h6>Seleccione un nodo</h6>
                    </div>
                    <div class="box-body" style="border: solid 1px #022f94; border-top: 3px solid #022f94;  border-radius: 8px;">
                        <table class="table no-margin" style="margin-bottom:0;">
                            <thead>
                                <tr> 
                                    <th>Nombre</th> 
                                    <th>URL Hijos</th> 
                                </tr>
                            </thead>
                        </table>
                        <div class="table-responsive">
                            <table class="table no-margin">
                                
                                <tbody id="list_updates_set"> </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6" id="content_prin_course" >
                <div class="box box-info box_content_cate">
                    <div class="box-header with-border">
                        <h4 class="box-title" id="title_course_list">Lista de cursos del nodo</h4>
                        <h6>Seleccione un curso</h6>
                    </div>
                    <div class="box-body" style="border: solid 1px #045ab3; border-top: 3px solid #045ab3;  border-radius: 8px;">
                        <table class="table no-margin" style="margin-bottom:0;">
                            <thead>
                                <tr> 
                                    <th>Curso</th>
                                    <th>Alfa</th> 
                                </tr>
                            </thead>
                        </table>
                        <div class="table-responsive">
                            <table class="table no-margin">
                                <tbody id="list_updates_activos"> </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12"><br></div>
            
            <div class="col-md-12" id="content_items_course" >
                <div class="box box-info box_content_cate">
                    <div class="box-header with-border">
                        <h3 class="box-title" id="title_items_course_list">Items actualizados del curso </h3>
                    </div>
                    <div class="box-body" style="border: solid 1px #1c84f1; border-top: 3px solid #1c84f1;  border-radius: 8px;">
                        <table class="table no-margin" style="margin-bottom:0;">
                            <thead>
                                <tr> 
                                    <th style="width: 20%; float: left;">Item</th>
                                    <th style="width: 20%; float: left;">Fecha</th>
                                    <th style="width: 40%; float: left;">User</th>
                                    <th style="width: 20%; float: left;">Ver</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="table-responsive">
                            <table class="table no-margin">
                                <tbody id="list_updates_items"> </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style type="text/css">
            /* Estilos para la tabla */
            .table-responsive {
                max-height: 40vh; 
                position: relative; 
                scrollbar-width: thin;
                scrollbar-color: #6a737b #f8f9fa;
            }

        </style>
<?php
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/backup_course/js/settings/view_updates.js') );
echo $OUTPUT->footer();