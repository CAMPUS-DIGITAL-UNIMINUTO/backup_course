<?php
require_once('../../../../config.php');
require_once("$CFG->libdir/filelib.php");

class create{
    
    public static function run(){
        $datosJSON = file_get_contents("php://input");
        $_POST = json_decode($datosJSON);
        $objCRE = new self();
        $idfunc = $_POST->key; 
        switch($idfunc){
            case 'C01': 
                echo json_encode($objCRE->saveToken());
            break;
            case 'R01': 
                echo json_encode($objCRE->listNodos());
            break;
            case 'U01': 
                echo json_encode($objCRE->updateToken($_POST->node_id));
            break;
            case 'D01': 
                echo json_encode($objCRE->deleteToken($_POST->node_id));
            break;
        }
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
        $url = $registro->node_domain.'/webservice/rest/server.php?wstoken='.$tok.'&wsfunction=local_remoter_register_node&moodlewsrestformat=json';
        $params = array('function'=>$registro->key,
                        'url'=>$url_actual[0],
                        'nombre' => '',
                        'token' => $registro->token,
                        'url_hijo'=>$registro->node_domain,
                        'startdate'=>$registro->startdate,
                        'enddate8'=>$registro->enddate8,
                        'enddate16'=>$registro->enddate16,
                        'estado'=>$registro->node_status,
                        'edition_acti'=>$registro->edition_acti
                    );
        $curl = new curl;
        $results = json_decode($curl->post($url,$params));
        if($results || !empty($results)){
            if (isset($results[0]) && isset($results[0]->ack) && $results[0]->ack == 1) {
                $registro_token = new stdClass();
                $registro_token->nombre = $registro->node_name;
                $registro_token->url_hijo = $registro->node_domain;
                $registro_token->startdate = $registro->startdate;
                $registro_token->enddate8= $registro->enddate8;
                $registro_token->enddate16 = $registro->enddate16;
                $registro_token->token = sha1($registro->token);    
                $registro_token->estado = $registro->node_status;
                $registro_token->edition = $registro->edition_acti;
                $registro_token->url_padre = $url_actual[0];
                $id_reg = $DB->insert_record('bc_registro_pc', $registro_token);
                $moodle_data = str_replace("\\",'/',$CFG->dirroot);
                $moodle_data = $moodle_data.'/local/backup_course/tmp/'.$id_reg.'/';
                return $results;
            }else{
                echo '$results: ';
                print_r($results);
            }

        }else echo 'Sin comunicación con el nodo '.$registro->node_domain;
        return $results;
    }
        
    /**
     * Listar todos los nodos
     * @returns {array}
     */
    private function listNodos(){
        global $DB;
        $options_tokens = $DB->get_records_sql("SELECT id, url_padre, nombre, token, url_hijo, estado, edition, DATE(FROM_UNIXTIME(startdate/ 1000)) AS startdate, DATE(FROM_UNIXTIME(enddate8/ 1000)) AS enddate8, DATE(FROM_UNIXTIME(enddate16/ 1000)) AS enddate16 FROM {bc_registro_pc}");
        return $options_tokens;
    }

    /*
     * Actualizar Tokens -> updateToken
     * Permite actualizar un token en el padre y el hijo
     * @params {int} $id
     * Retorna la verificacion de la actualización
     * return {objet};
     */
    private function updateToken($id)
    {
        global $DB;
        $registro = (object)$_POST;
        $registro_token = new stdClass();
        $tok = sha1('2017.UVD_TokeN_noDos');
        $url_actual = explode('/local/', $_SERVER['HTTP_REFERER']);
        $url = $this->formatHttp($registro->node_domain) . '/webservice/rest/server.php?wstoken=' . $tok . '&wsfunction=local_remoter_register_node&moodlewsrestformat=json';

        if (isset($registro->token)) {
            $registro_token->token = sha1($registro->token);
            $new_tok = $registro->token;
        } else $new_tok = '';
        $params = array(
            'function' => $registro->key,
            'url' => $this->formatHttp($url_actual[0]),
            'nombre' => $registro->node_name,
            'token' => $new_tok,
            'url_hijo' => $this->formatHttp($registro->node_domain),
            'startdate' => $registro->startdate,
            'enddate8' => $registro->enddate8,
            'enddate16' => $registro->enddate16,
            'estado' => $registro->node_status,
            'edition_acti' => $registro->edition_acti,
        );

        $curl = new curl;
        $results = json_decode($curl->post($url, $params));
        if (!empty($results) && array_key_exists('0', $results) && property_exists($results[0], 'ack')) {
            foreach ($results as $arr) {
                if ($arr->ack == 1) {
                    $registro_token->id = $id;
                    $registro_token->nombre = $registro->node_name;
                    $registro_token->url_hijo = $this->formatHttp($registro->node_domain);
                    $registro_token->startdate = $registro->startdate;
                    $registro_token->enddate8 = $registro->enddate8;
                    $registro_token->enddate16 = $registro->enddate16;
                    $registro_token->estado = $registro->node_status;
                    $registro_token->edition = $registro->edition_acti;
                    $registro_token->url_padre = $this->formatHttp($url_actual[0]);

                    $DB->update_record('bc_registro_pc', $registro_token);
                }
            }
        } else print_r($results);
        return $results;
    }


    /*
     * Eliminar Tokens -> deleteToken
     * return {array};
     */
    private function deleteToken($idnodo){
        global $DB, $CFG;
        $registro = (object)$_POST;
        $tok = sha1('2017.UVD_TokeN_noDos');
        $url_actual = explode('/local/', $_SERVER['HTTP_REFERER']);
        $url = $this->formatHttp($registro->node_domain).'/webservice/rest/server.php?wstoken='.$tok.'&wsfunction=local_remoter_register_node&moodlewsrestformat=json';
        $params = array('function'=>$registro->key,
                        'url' =>$this->formatHttp($url_actual[0]),
                        'nombre' => '',
                        'token' => $registro->node_token,
                        'url_hijo' => $this->formatHttp($registro->node_domain),
                        'edition_acti'=>'',
                        'estado' => '',
                        'startdate' => '',
                        'enddate8' => '',
                        'enddate16' => ''
                );


        $curl = new curl;
        $results = json_decode($curl->post($url,$params));
        if(!empty($results) ){
            foreach ($results as $arr) {
                if(property_exists($arr,'ack')){
                    if($arr->ack == 1 && $idnodo != 0)  $DB->delete_records('bc_registro_pc',array('id'=>$idnodo));
                }else print_r($results);
            }
        }
        return $results;
    }
    
}  
create::run();