/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ver_actualizaciones {
    constructor() {this.obtener_actualizaciones();}
    /**
     * Listar nodos actualizados
     * @returns {undefined}
     */
    obtener_actualizaciones() {  
        document.querySelector('.loader').style.display = "block";  
        document.querySelector('#list_updates_set').innerHTML = '';  
      
        let datosFormulario = {'key': 'Q01'};  
        fetch('../../methods/settings/class_admin_updates.php', {  
            method: 'POST',  
            headers: {   'Content-Type': 'application/json'   },  
            body: JSON.stringify(datosFormulario)  
        })  
        .then(response => {  
            if (!response.ok) { throw new Error('Network response was not ok');}  
            return response.json();  
        })  
        .then(data => {  
            if(Object.keys(data).length > 0){   
                document.querySelector('#list_updates_set').innerHTML = '<span class="lista_no_updates" >' + data + '</span>';  
                if (typeof data == 'object') {  
                    document.querySelector('#list_updates_set').innerHTML = '';  
                    for (var k in data) {
                        let v = data[k] 
                        document.querySelector('#list_updates_set').insertAdjacentHTML('beforeend', '<tr id="datos_nodos-' + v['id'] + '" onclick="v_actualizaciones.obtener_lista_cursos_actualizados(' + v['id'] + ')">' +  
                            '<td style="color: #000;"> ' + v['nombre'] + '</td>' +  
                            '<td style="color: #000;"> ' + v['url_hijo'] + '</td>' +  
                        '</tr>');  
                    };  
                } else  document.querySelector('#list_updates_set').innerHTML = '<span class="lista_no_updates" >No hay updates enviadas</span>';  
                
            } else  document.querySelector('#list_updates_set').innerHTML = '<span class="lista_no_updates" >No hay nodos con actualizaciones enviadas</span>';  
        })  
        .catch((error) => {  console.error('Error:', error);   })  
        .finally(() => {   document.querySelector('.loader').style.display = "none";   });  
    }  

       
    
    /**
     * Listar cursos updates en hijos
     * @returns {undefined}
     */
    obtener_lista_cursos_actualizados(id) {  
        document.querySelector('.loader').style.display = "block";  
        document.querySelector('#list_updates_activos').innerHTML = '';  
      
        let datosFormulario = {'key': 'Q02', 'id_nodo': id};  
        fetch('../../methods/settings/class_admin_updates.php', {  
            method: 'POST',  
            headers: {   'Content-Type': 'application/json'   },  
            body: JSON.stringify(datosFormulario)  
        })  
        .then(response => {  
            if (!response.ok) { throw new Error('Network response was not ok');}  
            return response.json();  
        })  
        .then(data => {  
            if(Object.keys(data).length > 0){    
                document.querySelector('#list_updates_activos').innerHTML = '<span class="lista_no_updates" >' + data + '</span>';  
                if (typeof data == 'object') {  
                    document.querySelector('#list_updates_activos').innerHTML = '';  
                    for (var k in data) {
                        let v = data[k]  
                        document.querySelector('#list_updates_activos').insertAdjacentHTML('beforeend', '<tr id="datos_list_course-' + v['id_course_sp'] + '" onclick="v_actualizaciones.obtener_lista_items_actualizados(' + v['id_course_sp'] + ', '+id+')">' +  
                            '<td class="lista_no_updates" style="color: #000;">' + v['fullname'] + '</td>' +  
                            '<td class="lista_no_updates" style="color: #000;">' + v['shortname'] + '</td>' +  
                        '</tr>');  
                    };  
                } else {  
                    document.querySelector('#list_updates_activos').innerHTML = '<span class="lista_no_updates" >No hay updates activos</span>';  
                }  
            } else {  
                document.querySelector('#list_updates_activos').innerHTML = '<span class="lista_no_updates" >No hay updates activos</span>';  
            }  
        })  
        .catch((error) => { console.error('Error:', error);  })  
        .finally(() => {  document.querySelector('.loader').style.display = "none";   });  
    }  

    /*
     * Borrar updates de la lista negra
     * @param {int} id
     * @param {string} url
     * @param {string} tok
     * @returns {Generator}
     */
    obtener_lista_items_actualizados(id_course, id_nodo){  
        document.querySelector('.loader').style.display = "block";  
      
        let datosFormulario = {'key': 'Q03', 'id_nodo': id_nodo, 'id_course': id_course};  
        fetch('../../methods/settings/class_admin_updates.php', {  
            method: 'POST',  
            headers: {  'Content-Type': 'application/json'  },  
            body: JSON.stringify(datosFormulario)  
        })  
        .then(response => {  
            if (!response.ok) { throw new Error('Network response was not ok');}  
            return response.json();  
        })  
        .then(data => {  
            if(Object.keys(data).length > 0){ 
                document.querySelector('#list_updates_items').innerHTML = '<span class="lista_no_updates" >' + data + '</span>';  
                if (typeof data == 'object') {  
                    document.querySelector('#list_updates_items').innerHTML = '';  
                    for (var k in data) {
                        let v = data[k]   
                        document.querySelector('#list_updates_items').insertAdjacentHTML('beforeend', '<tr id="datos_list_items_course-' + v['id'] + '" onclick="">' +  
                            '<td style="width: 20%; float: left; color: #000;">' + v['type_act'] + '</td>' +  
                            '<td style="width: 20%; float: left; color: #000;">' + v['time_update_date'] + '</td>' +  
                            '<td style="width: 40%; float: left; color: #000;">' + v['email'] + '</td>' +  
                            '<td style="width: 20%; float: left; color: #000;" data-toggle="collapse" data-target="#contenDataPlantilla-'+v['id']+'"><i class="fa fa-fw fa-eye"></i> Ver objeto</td>' +  
                        '</tr>'+  
                        '<tr class="collapse" id="contenDataPlantilla-'+v['id']+'"><td style="width: 100%;">'+  
                            '<textarea style="width: 100%;">' + v['obj_act'] + '</textarea>' +  
                        '</td></tr>');  
                    };  
                } else  document.querySelector('#list_updates_items').innerHTML = '<span class="lista_no_updates" >No hay updates activos</span>';  
            } else  document.querySelector('#list_updates_items').innerHTML = '<span class="lista_no_updates" >No hay updates activos</span>';  
        })  
        .catch((error) => {  console.error('Error:', error);   })  
        .finally(() => {   document.querySelector('.loader').style.display = "none";  });  
    }  
}

const v_actualizaciones = new ver_actualizaciones();