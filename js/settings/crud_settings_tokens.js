/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class admin_tokens {
    constructor() {this.obtener_tokens_bloqueados();}
    /**
     * Listar tokens de la lista negra
     * @returns {undefined}
     */
    obtener_tokens_bloqueados() {  
        document.querySelector('#loader').style.display = "block";  
        document.querySelector('#list_tokens_black').innerHTML = '';  
      
        let datosFormulario = {'key': 'Q01'};  
        fetch('../../methods/settings/crud_admin_tokens.php', {  
            method: 'POST',  
            headers: {   'Content-Type': 'application/json' },  
            body: JSON.stringify(datosFormulario)  
        })  
        .then(response => {  
            if (!response.ok) { throw new Error('Network response was not ok');}  
            return response.json();  
        })  
        .then(data => {  
            if(Object.keys(data).length > 0){  
                document.querySelector('#list_tokens_black').innerHTML = '<span class="lista_no_tokens" >' + data + '</span>';  
                if (typeof data == 'object') {  
                    document.querySelector('#list_tokens_black').innerHTML = '';  
                    for (let k in data) {
                        let v = data[k]  
                        document.querySelector('#list_tokens_black').insertAdjacentHTML('beforeend', '<div class="border rounded p-2">' +  
                            '<span class="lista_no_tokens" > URL: ' + v['url'] + '</span><br>' +  
                            '<span class="lista_no_tokens" > Token: ' + v['token'] + '</span><br>' +  
                            '<div title="Eliminar" class="delete-item-hijo border rounded " id="delete_' + v['id'] + '" onclick="admin_tok.eliminar_tokens_bloqueados(\'' + v['id'] + '\',\'' + v['url'] + '\',\'' + v['token'] + '\')"><i class="fa fa-trash-o"></div>' +  
                        '</div>');  
                    };  
                } else {  
                    document.querySelector('#list_tokens_black').innerHTML = '<span class="lista_no_tokens" >No hay tokens en lista negra</span>';  
                }  
            } else {  
                document.querySelector('#list_tokens_black').innerHTML = '<span class="lista_no_tokens" >No hay tokens bloqueados</span>';  
            }  
            admin_tok.obtener_tokens_activos();  
        })  
        .catch((error) => {  console.error('Error:', error);   })  
        .finally(() => {   document.querySelector('#loader').style.display = "none";   });  
    }  


    obtener_tokens_activos() {  
        document.querySelector('#loader').style.display = "block";  
        document.querySelector('#list_tokens_activos').innerHTML = '';  
      
        let datosFormulario = {'key': 'Q02'};  
        fetch('../../methods/settings/crud_admin_tokens.php', {  
            method: 'POST',  
            headers: {   'Content-Type': 'application/json'  },  
            body: JSON.stringify(datosFormulario)  
        })  
        .then(response => {  
            if (!response.ok) { throw new Error('Network response was not ok');}  
            return response.json();  
        })  
        .then(data => {  
            if(Object.keys(data).length > 0){  
                document.querySelector('#list_tokens_activos').innerHTML = '<span class="lista_no_tokens" >' + data + '</span>';  
                if (typeof data == 'object') {  
                    document.querySelector('#list_tokens_activos').innerHTML = '';  
                    for (let k in data) { 
                        let v = data[k]  
                        let edit = v['edition'] == 0? 'NO': 'SI';  
                        document.querySelector('#list_tokens_activos').insertAdjacentHTML('beforeend', '<div class="border rounded p-2">' +  
                            '<span class="lista_no_tokens" > Nombre: ' + v['nombre'] + '</span><br>' +  
                            '<span class="lista_no_tokens" > URL hijo: ' + v['url_hijo'] + '</span><br>' +  
                            '<span class="lista_no_tokens" > Estado: '+v['estado']+'</span><br>'+  
                            '<span class="lista_no_tokens" > Edición: '+edit+'</span>'+  
                            '<div title="Desactivar" class="block-item-hijo border rounded " id="edit_' + v['id'] + '" onclick="admin_tok.actualizar_tokens_activos(\'' + v['id'] + '\')"> <i class="fa fa-ban"> </div>' +  
                        '</div>');  
                    };  
                } else {  
                    document.querySelector('#list_tokens_activos').innerHTML = '<span class="lista_no_tokens" >No hay tokens activos</span>';  
                }  
            } else {  
                document.querySelector('#list_tokens_activos').innerHTML = '<span class="lista_no_tokens" >No hay tokens activos</span>';  
            }  
        })  
        .catch((error) => {   console.error('Error:', error);   })  
        .finally(() => {  document.querySelector('#loader').style.display = "none";  });  
    }  


    /*
     * Borrar tokens de la lista negra
     * @param {int} id
     * @param {string} url
     * @param {string} tok
     * @returns {Generator}
     */
    eliminar_tokens_bloqueados(id, url, tok){  
        document.querySelector('#loader').style.display = "block";  
        let datosFormulario = {key: 'D01', id: id, url: url, token:tok};  
      
        fetch('../../methods/settings/crud_admin_tokens.php', {  
            method: 'POST',  
            headers: {   'Content-Type': 'application/json'   },  
            body: JSON.stringify(datosFormulario)  
        })  
        .then(response => {  
            if (!response.ok) { throw new Error('Network response was not ok');}  
            return response.json();  
        })  
        .then(data => {  
            if (typeof data == 'object') {  
                let respu = data;  
                respu.bc_balck_list_p ? msjBC.ok('Eliminado','El token se elimininó de la black_list'): console.log('no se elimintó de la black_list del padre');  
                respu.bc_white_list_p ? msjBC.ok('Eliminado','El token se elimininó de la white_list'): console.log('no se elimintó de la white_list del padre');  
                respu.bc_registro_pc_p ? msjBC.ok('Eliminado','El token se elimininó de la bc_registro_pc_p'): console.log('no se elimintó de la bc_registro_pc del padre');  
                console.log('Respuesta del hijo bc_registro_pc', respu.bc_registro_pc_h);  
            } else{  
                console.log('revisa el network');  
            }  
            admin_tok.obtener_tokens_bloqueados();  
        })  
        .catch((error) => {  
            console.error('Error:', error);  
            msjBC.error('Error','Error al eliminar');  
        })  
        .finally(() => {   document.querySelector('#loader').style.display = "none";   });  
    }  

    
    
    
    /**
     * Listar tokens activos en el padre
     * @returns {undefined}
     */
    eliminar_tokens_bloqueados(id, url, tok){  
        document.querySelector('#loader').style.display = "block";  
        let datosFormulario = {key: 'D01', id: id, url: url, token:tok};  
      
        fetch('../../methods/settings/crud_admin_tokens.php', {  
            method: 'POST',  
            headers: {   'Content-Type': 'application/json'  },  
            body: JSON.stringify(datosFormulario)  
        })  
        .then(response => {  
            if (!response.ok) { throw new Error('Network response was not ok');}  
            return response.json();  
        })  
        .then(data => {  
            if (typeof data == 'object') {  
                let respu = data;  
                respu.bc_balck_list_p ? msjBC.ok('Eliminado','El token se elimininó de la black_list'): console.log('no se elimintó de la black_list del padre');  
                respu.bc_white_list_p ? msjBC.ok('Eliminado','El token se elimininó de la white_list'): console.log('no se elimintó de la white_list del padre');  
                respu.bc_registro_pc_p ? msjBC.ok('Eliminado','El token se elimininó de la bc_registro_pc_p'): console.log('no se elimintó de la bc_registro_pc del padre');  
                console.log('Respuesta del hijo bc_registro_pc', respu.bc_registro_pc_h);  
            } else{  
                console.log('revisa el network');  
            }  
            admin_tok.obtener_tokens_bloqueados();  
        })  
        .catch((error) => {  
            console.error('Error:', error);  
            msjBC.error('Error','Error al eliminar');  
        })  
        .finally(() => {  document.querySelector('#loader').style.display = "none";  });  
    }  

    /*
     * Borrar tokens de la lista negra
     * @param {int} id
     * @param {string} url
     * @param {string} tok
     * @returns {Generator}
     */
    actualizar_tokens_activos(id){  
        document.querySelector('#loader').style.display = "block";  
        let datosFormulario = {key: 'U01', id: id};  
      
        fetch('../../methods/settings/crud_admin_tokens.php', {  
            method: 'POST',  
            headers: {  'Content-Type': 'application/json'   },  
            body: JSON.stringify(datosFormulario)  
        })  
        .then(response => {  
            if (!response.ok) { throw new Error('Network response was not ok');}  
            return response.json();  
        })  
        .then(data => {  
            if (typeof data == 'object') {  
                let respu = data;  
                respu.bc_registro_pc_p ? msjBC.ok('Actualizado','El token se actualizó en bc_registro_pc del padre'): console.log('no se actualizó de la bc_registro_pc del padre');  
                console.log('Respuesta del hijo bc_registro_pc', respu.bc_registro_pc_h);  
            } else console.log('revisa el network');  
            admin_tok.obtener_tokens_activos();  
        })  
        .catch((error) => {  
            console.error('Error:', error);  
            msjBC.error('Error','Error al actualizar');  
        })  
        .finally(() => {  document.querySelector('#loader').style.display = "none";  });  
    }  

}

const admin_tok = new admin_tokens();

