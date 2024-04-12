<?php
require_once('../../../../config.php');
require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/pagelib.php");
require_once("$CFG->libdir/blocklib.php");

class admin_actualizaciones{

    public function __construct(){   $this->ejecutar();   }  
      
    public function ejecutar(){ 
        $idfunc = $_POST['key']; 
        switch($idfunc){
            case 'Q01': 
                $resp = $this->lista_nodos();
                break;
            case 'Q02': 
                $resp = $this->lista_cursos_actualizados();
                break;
            case 'Q03': 
                $resp = $this->lista_items_actualizados();
                break;
        }
        echo json_encode($resp);
    }
    /**
     * Listar updates de la lista negra
     * @returns {array}
     */
    private function lista_nodos() {
        global $DB;
        $actualizaciones = $DB->get_records_sql('SELECT DISTINCT r.* 
                                            FROM {updates_nodos} un 
                                            LEFT JOIN {bc_registro_pc} r ON r.id = un.id_nodo_rel
                                            WHERE r.url_hijo IS NOT NULL');
        if(!empty($actualizaciones)){
            foreach ($actualizaciones as $key => $value) {
                
            }
        }
        return $actualizaciones;
    }
        
    /**
     * Listar cursos actualizados del nodo
     * @returns {array}
     */
    private function lista_cursos_actualizados() {
        global $DB;
        $registro = (object)$_POST;
        $actualizaciones = $DB->get_records_sql('SELECT DISTINCT uc.id_course_sp, c.fullname, c.shortname, r.url_hijo
                                        FROM {bc_registro_pc} r  
                                        LEFT JOIN {updates_nodos} un ON r.id = un.id_nodo_rel
                                        LEFT JOIN {updates_log} ul ON ul.id = un.id_log
                                        LEFT JOIN {updates_courses} uc ON ul.id_update = uc.id
                                        LEFT JOIN {user} u ON u.id = ul.id_user
                                        LEFT JOIN {course} c ON c.id = uc.id_course_sp 
                                        WHERE r.id = :id
                                        ORDER BY c.fullname DESC',
                                    array('id'=>$registro->id_nodo));
        return $actualizaciones;
    }
    
    
    /**
     * Listar items actualizados del nodo del curso
     * @returns {array}
     */
    private function lista_items_actualizados() {
        global $DB;
        $registro = (object)$_POST;
        $actualizaciones = $DB->get_records_sql('SELECT DISTINCT un.id, uc.id_course_sp, uc.id_act_sp, uc.type_act, uc.obj_act, FROM_UNIXTIME(ul.time_update) AS time_update_date,
                                        u.email, c.fullname, c.shortname, r.url_hijo
                                        FROM {bc_registro_pc} r  
                                        LEFT JOIN {updates_nodos} un ON r.id = un.id_nodo_rel
                                        LEFT JOIN {updates_log} ul ON ul.id = un.id_log
                                        LEFT JOIN {updates_courses} uc ON ul.id_update = uc.id
                                        LEFT JOIN {user} u ON u.id = ul.id_user
                                        LEFT JOIN {course} c ON c.id = uc.id_course_sp 
                                        WHERE r.id = :id AND c.id = :course
                                        ORDER BY ul.time_update DESC',
                                    array('id'=>$registro->id_nodo, 'course'=>$registro->id_course));
        return $actualizaciones;
    }
    
}
new admin_actualizaciones();  
