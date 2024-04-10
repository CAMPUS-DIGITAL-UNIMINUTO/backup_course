<?php
class ws_access
{
    /*
     * Run the class -> perms
     * @params -> array(url_padre,token,ip,estado)
     * return {int};
     */
    public function perms($param)    {
        $obj = new self();
        $idfunc = $param['function'];
        switch ($idfunc) {
            case 'C01':
                $resp = $obj->creNodo($param);
                break;
            case 'U01':
                $resp = $obj->updNodo($param);
                break;
            case 'D01':
                $resp = $obj->delNodo($param);
                break;
        }

        return $resp;
           
    private function creNodo($params)    {
        global $DB, $CFG;
        $query_bl_wl = new query_bl_wl();
        $resp = $query_bl_wl->run($params);
        if ($resp->response == 'Puede crear') {
            $registro_token = new stdClass();
            $registro_token->nombre = 'Padre';
            $registro_token->url_hijo = $params['url_hijo'];
            $registro_token->token = sha1($params['token']);
            $registro_token->estado = $params['estado'];
            $registro_token->edition = $params['edition'];
            $registro_token->url_padre = $params['url'];
            $registro_token->startdate = $params['startdate'];
            $registro_token->enddate8 = $params['enddate8'];
            $registro_token->enddate16 = $params['enddate16'];
            $DB->insert_record('bc_registro_pc', $registro_token);
        }
        return $resp;
    }

    /*
     * Update search -> updNodo
     * Actualizar el token en el nodo
     * @params -> array(url_padre,token,ip,estado)
     * return {objet};
     */
    private function updNodo($params)    {
        global $DB;
        $query_bl_wl = new query_bl_wl();
        $resp = $query_bl_wl->run($params);
        $id_reg = $query_bl_wl->QRY_RBC($params['url'], $params['nombre']);
        if ($resp->response == 'Ya existe en la blanca' && !empty($id_reg)) {
            $registro_token = new stdClass();
            $registro_token->id = $id_reg;
            $registro_token->nombre = 'Padre';
            $registro_token->url_hijo = $params['url_hijo'];
            $registro_token->startdate = $params['startdate'];
            $registro_token->enddate8 = $params['enddate8'];
            $registro_token->enddate16 = $params['enddate16'];
            $registro_token->estado = $params['estado'];
            $registro_token->edition = $params['edition'];
            if (!empty($params['token'])) {
                $registro_token->token = sha1($params['token']);
            }
            $DB->update_record('bc_registro_pc', $registro_token);
            $registro_token->id = $query_bl_wl->QRY_URLWL($params['url']);
            $registro_token->url = $params['url'];
            $query_bl_wl->UPD_WL($registro_token);
            $resp->ack = 1;
            $resp->response = 'Nodo Actualizado';
        } else if ($resp->response == 'Ya existe en la blanca') {
            $resp->ack = 0;
            $resp->response = 'NO se actualizó';
        }
        return $resp;
    }

    /*
     * Delete Padre -> delNodo
     * Eliminar token en el nodo
     * @params -> array(url_padre,token,ip,estado)
     * return {objet};
     */
    private function delNodo($params)    {
        global $DB;
        $query_bl_wl = new query_bl_wl();
        $resp = $query_bl_wl->run($params);
        $id_reg = $query_bl_wl->QRY_RBC($params['url'], $params['token']);
        if ($resp->response == 'Ya existe en la blanca' && !empty($id_reg)) {
            $query_bl_wl->DEL_WL($query_bl_wl->QRY_URLWL($params['url']));
            $DB->delete_records('bc_registro_pc', array('id' => $id_reg));
            $DB->delete_records('bc_rel_padre_hijo', array('registroid' => $id_reg));
            $resp->ack = 1;
            $resp->response = 'Se eliminó el hijo';
        } else {
            $resp->ack = 0;
            $resp->response = 'No es posible eliminar el nodo';
        }
        return $resp;
    }
}