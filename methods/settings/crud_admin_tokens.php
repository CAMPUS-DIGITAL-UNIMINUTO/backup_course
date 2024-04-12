<?php
require_once('../../../../config.php');
require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/pagelib.php");
require_once("$CFG->libdir/blocklib.php");

class administrar_tokens{
    public function __construct(){   $this->ejecutar();   }  

    public  function ejecutar(){
        $datosJSON = file_get_contents("php://input");
        $_POST = json_decode($datosJSON);
        $idfunc = $_POST->key; 
        switch($idfunc){
            case 'Q01': 
                echo json_encode($this->lista_tokens());
            break;
            case 'D01': 
                echo json_encode($this->eliminar_token());
                break;
            case 'Q02': 
                echo json_encode($this->lista_tokens_activos());
            break;
            case 'U01': 
                echo json_encode($this->desactivar_token());
            break;
        }
    }
    /**
     * Listar tokens de la lista negra
     * @returns {array}
     */
    private function lista_tokens() {
        global $DB;
        $tokens = $DB->get_records('bc_black_list',array(), $sort='', $fields='*');
        return $tokens;
    }
    /*
     * Borrar tokens de la lista negra
     * @param {int} id
     * @param {string} url
     * @param {string} tok
     * @returns {Generator}
     */
    private function eliminar_token() {
        global $DB;
        $registro = (object)$_POST;
        $res = array();
        $res['bc_black_list_p'] = $DB->delete_records('bc_black_list',array('id'=>$registro->id));
        $res['bc_white_list_p'] = $DB->delete_records('bc_white_list',array('url'=>$registro->url));
        $res['bc_registro_pc_p'] = $DB->delete_records('bc_registro_pc',array('url_hijo'=>$registro->url));
        $tok = sha1('2017.UVD_TokeN_noDos');
        $url_actual = explode('/local/', $_SERVER['HTTP_REFERER']);
        $url = $registro->url.'/webservice/rest/server.php?wstoken='.$tok.'&wsfunction=local_remoter_register_node&moodlewsrestformat=json';
        $parametros = array('function'=>$registro->key,
                        'url' =>$url_actual[0],
                        'nombre' => '',
                        'token' => $registro->token,
                        /* 'ip' => $_SERVER['SERVER_ADDR'], */
                        'url_hijo' => $registro->url,
                        'edition_acti'=>'',
                        'estado' => '',
                        'server' => '',
                        'port' => '',
                        'username' => '',
                        'password' => '',
                        'startdate' => '',
                        'enddate8' => '',
                        'enddate16' => ''
                );

        $curl = new curl;
        $res['bc_registro_pc_h'] = json_decode($curl->post($url,$parametros));
        return $res;
    }
    
    /**
     * Listar tokens activos
     * @returns {array}
     */
    private function lista_tokens_activos() {
        global $DB;
        $tokens = $DB->get_records('bc_registro_pc',array('estado'=>1), $sort='', $fields='*');
        return $tokens;
    }
    
    
    /*
     * Desactivar tokens
     * @param {int} id
     * @param {string} url
     * @param {string} tok
     * @returns {Generator}
     */
    private function desactivar_token() {
        global $DB;
        $registro = (object)$_POST;
        $res = array();
        $tb_reg = $DB->get_record('bc_registro_pc',array('id'=>$registro->id));
        $tb_reg->estado = 0; // desactivar estado
        $res['bc_registro_pc_p'] = $DB->update_record('bc_registro_pc',$tb_reg);
        $tok = sha1('2017.UVD_TokeN_noDos');
        $url_actual = explode('/local/', $_SERVER['HTTP_REFERER']);
        $url = $tb_reg->url_hijo.'/webservice/rest/server.php?wstoken='.$tok.'&wsfunction=local_remoter_register_node&moodlewsrestformat=json';
        $parametros = array('function'=>$registro->key,
                        'url' =>$url_actual[0],
                        'nombre' => $tb_reg->nombre,
                        'token' => $tb_reg->token,
                        'url_hijo' =>$tb_reg->url_hijo,
                        'startdate'=>$tb_reg->startdate,
                        'enddate8'=>$tb_reg->enddate8,
                        'enddate16'=>$tb_reg->enddate16,
                        'estado' => $tb_reg->estado,
                        'edition_acti' => $tb_reg->edition,
                );

        $curl = new curl;
        $res['bc_registro_pc_h'] = json_decode($curl->post($url,$parametros));
        return $res;
    }
}
new administrar_tokens();  
