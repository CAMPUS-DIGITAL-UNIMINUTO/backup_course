<?php
require_once('../../../../config.php');
require_once("$CFG->libdir/filelib.php");

class create{
    
    public static function run(){
        $objCRE = new self();
        $idfunc = $_POST['key']; 
        switch($idfunc){
            case 'C01': 
                $resp = $objCRE->saveToken();
            case 'R01': 
                $resp = $objCRE->listNodos();
            break;
        }
        echo json_encode($resp);
        //redirect($FULLME);
    } 
           
    /*
     * Https to Http -> formatHttp
     * identifica si la cadena ingresada contiene HTTPS y lo convierte a HTTP
     * Retorna un string con la cadena formateada
     * return {string};
    */

    private function formatHttp($url)    {
        $url = trim($url);
        $url_expl = explode(":", $url);
        $url_https = $url_expl[0];
        $url_https = strtolower($url_https);
        if ($url_https == 'https') {
            $url_http = str_replace("s", "", $url_https);
            return $url_http . ":" . $url_expl[1];
        } else return $url;
    }

    /*
     * Create token -> saveToken
     * Se une al hijo y crea un token correspondiente
     * Retorna la respuesta exitosa o inválida
     * return {objet};
     */
    private function saveToken(){
        global $DB, $CFG;
        $registro = (object)$_POST; 
        
        $tok = sha1('2017.UVD_TokeN_noDos');
        $url_actual = explode('/local/', $_SERVER['HTTP_REFERER']);
        $url = $registro->data_child['node_domain'].'/webservice/rest/server.php?wstoken='.$tok.'&wsfunction=local_remoter_register_node&moodlewsrestformat=json';
        $params = array('function'=>$registro->key,
                        'url'=>$url_actual[0],
                        'nombre' => '',
                        'token' => $registro->token,
                        'ip'=>$_SERVER['SERVER_ADDR'],
                        'url_hijo'=>$registro->data_child['node_domain'],
                        'startdate'=>$registro->data_child['startdate'],
                        'enddate8'=>$registro->data_child['enddate8'],
                        'enddate16'=>$registro->data_child['enddate16'],
                        'estado'=>$registro->data_child['node_status'],
                        'edition_acti'=>$registro->data_child['edition_acti']
                    );
            
        $curl = new curl;
        $results = json_decode($curl->post($url,$params));
        if(!empty($results)){
            if(array_key_exists('0', $results) && $results[0]->ack == 1){
                $registro_token = new stdClass();
                $registro_token->nombre = $registro->data_child['node_name'];
                $registro_token->ip = $registro->data_child['node_ip'];
                $registro_token->url_hijo = $registro->data_child['node_domain'];
                $registro_token->startdate = $registro->data_child['startdate'];
                $registro_token->enddate8= $registro->data_child['enddate8'];
                $registro_token->enddate16 = $registro->data_child['enddate16'];
                $registro_token->token = sha1($registro->token);    
                $registro_token->estado = $registro->data_child['node_status'];
                $registro_token->edition = $registro->data_child['edition_acti'];
                $registro_token->url_padre = $url_actual[0];
                $id_reg = $DB->insert_record('bc_registro_pc', $registro_token);
                $moodle_data = str_replace("\\",'/',$CFG->dirroot);
                $moodle_data = $moodle_data.'/local/backup_course/tmp/'.$id_reg.'/';
                
            }else{
                echo '$results: ';
                print_r($results);
            }

        }
        return $results;
    }
        
    /**
     * Listar todos los nodos
     * @returns {array}
     */
    private function listNodos(){
        global $DB;
        return $DB->get_records('bc_registro_pc',array(), $sort='', $fields='*');
    }
    
}  
