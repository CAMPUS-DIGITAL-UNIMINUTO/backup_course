<?php
require_once('../../../../config.php');

/// no guest autologin
require_login(0, false);
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/backup_course/template/settings/view_hijos.php'); 
$PAGE->set_pagelayout('admin');
$PAGE->requires->css('/local/backup_course/css/base/tostadas.css');
$PAGE->requires->css('/local/backup_course/css/base/jquery-confirm.css');
$PAGE->requires->css('/local/backup_course/css/base/style.css');
$PAGE->requires->jquery();
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/backup_course/js/base/llamados.js') );

$title = get_string('crear_hijo', 'local_backup_course');
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
?>

<div id="snackbar"></div>

<div class="app-content  flex-column-fluid ">
    <div id="header_list_tokens" class="app-container  container-fluid ">
        <div class="loader" id="loader"></div>
        <div class="container mt-5">
          <!-- ini tabs -->
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="hijos-tab" data-toggle="tab" href="#hijos" role="tab" aria-controls="hijos" aria-selected="true" onclick="crud.listar_hijos()">Hijos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="tokens-tab" data-toggle="tab" href="#tokens" role="tab" aria-controls="tokens" aria-selected="false" onclick="admin_tok.obtener_tokens_bloqueados()">Tokens</a>
            </li>
          </ul>
          <!-- fin tabs -->

          <div class="tab-content" id="myTabContent">
            <!-- ini tab hijos -->
            <div class="tab-pane fade show active " id="hijos" role="tabpanel" aria-labelledby="hijos-tab">
                <div class="row">
                    <!-- ini crud hijos -->
                    <div id="form_hijos" class="col-md-6" style="border-right: 1px solid #dbdbdb;"> 
                        <form><br>
                          <div class="form-group row">
                            <input type="hidden" class="form-control" id="id_reg" >
                            <label for="nombre_hijo" class="col-md-6">Digite el nombre del Moodle hijo</label>
                            <input type="text" class="form-control col-md-5" id="nombre_hijo" placeholder="Hijo 1">
                          </div>
                          <div class="form-group row">
                            <label for="url_hijo" class="col-md-6">Digite la URL del Moodle hijo</label>
                            <input type="text" class="form-control col-md-5" id="url_hijo" placeholder="URL">
                            <small id="url_hijoHelp" class="form-text text-muted col-md-12">http://aulas.uvd.edu/mdl_pruebas</small>
                          </div>
                          <div class="form-group row">
                            <label for="startdate" class="col-md-6">Seleccione la fecha de inicio</label>
                            <input type="date" id="startdate"  name="startdate" class="sm-form-control custom-select col-md-5">
                          </div>
                          <div class="form-group row">
                            <label for="enddate8" class="col-md-6">Seleccione la fecha fin de 8 semanas</label>
                            <input type="date" id="enddate8"  name="enddate8"  class="sm-form-control custom-select col-md-5">
                          </div>
                          <div class="form-group row">
                            <label for="enddate16" class="col-md-6">Seleccione la fecha fin de 16 Semanas</label>
                            <input type="date" id="enddate16"  name="enddate16"  class="sm-form-control custom-select col-md-5">
                          </div>
                          <div class="form-group row">
                            <label for="activo_tok" class="col-md-6">Seleccione si el token estará activo</label>
                            <select id="activo_tok" class="custom-select col-md-5">
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                          </div>
                          <div class="form-group row">
                            <label for="edition_acti" class="col-md-6">Seleccione si desea permitir la edición de actividades</label>
                            <select id="edition_acti" class="custom-select col-md-5">
                                <option value="1" selected>Editar</option>
                                <option value="0">No editar</option>
                            </select>
                          </div>
                          <div class="btn btn-primary" onclick="crud.validar_form()">Guardar</div>
                          <div class="btn btn-secondary" onclick="crud.limpiar_form()">Limpiar</div>
                        </form>
                    </div>
                    <!-- fin crud hijos -->
                    <!-- ini list hijos -->
                    <div class="col-md-6"> <br>
                        <h5>Lista de hijos</h5><hr>
                        <div id="list_tokens_creados" ></div>
                    </div>
                    <!-- fin list hijos -->
                </div>
            </div>
            <!-- fin tab hijos -->
            <!-- ini tab tokens -->
            <div class="tab-pane fade" id="tokens" role="tabpanel" aria-labelledby="tokens-tab">
              <div class="row">
                <div class="col-md-6">
                    <div class="header_list_tokens"><br>
                        <h5>Lista de Tokens Bloqueados</h5><hr>
                        <div id="list_tokens_black"> </div>
                    </div>
                </div>
                <div class="col-md-6">    
                    <div class="header_list_tokens"><br>
                        <h5>Lista de Tokens Activos</h5><hr>
                        <div id="list_tokens_activos"> </div>
                    </div>
                </div>
            </div>
            <!-- fin tab tokens -->
          </div>
        </div>
            
            
    </div>  

</div>

<?php
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/backup_course/js/settings/crud_settings_hijos.js') );
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/backup_course/js/settings/crud_settings_tokens.js') );
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/backup_course/js/base/mensajes.js') );
echo $OUTPUT->footer();



