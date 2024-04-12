class CRUD {
  constructor() {this.listar_hijos();}

  validar_form(llam = 0, up=0) {
    document.querySelector('.loader').style.display = 'block'; 
    let datosFormulario = {}
    // Validar que todos los campos estén llenos
    let nombre = document.getElementById("nombre_hijo").value;
    let url    = document.getElementById("url_hijo").value;
    let start  = document.getElementById("startdate").value;
    let end8   = document.getElementById("enddate8").value;
    let end16  = document.getElementById("enddate16").value;
    let activo = document.getElementById("activo_tok").value;
    let edition= document.getElementById("edition_acti").value;

    let startdate = new Date(start);
    let enddate8  = new Date(end8);
    let enddate16 = new Date(end16);

    if (nombre.trim() === '') {
      msjBC.error('Error','Debe diligenciar el nombre del Hijo');
      document.querySelector('.loader').style.display = 'none';  return;
    }else if (url.trim() === ''){
      msjBC.error('Error','Debe diligenciar la URL del Hijo');
      document.querySelector('.loader').style.display = 'none';  return;
    }else if (!this.url_valida(url)) {
      msjBC.error('Error','La URL ingresada no es válida');
      document.querySelector('.loader').style.display = 'none';  return;
    }else if (start.trim() === ''){
      msjBC.error('Error','Debe diligenciar la fecha inicial');
      document.querySelector('.loader').style.display = 'none';  return;
    }else if (end8.trim() === ''){
      msjBC.error('Error','Debe diligenciar la fecha de 8 semanas');
      document.querySelector('.loader').style.display = 'none';  return;
    }else if (end16.trim() === ''){
      msjBC.error('Error','Debe diligenciar la fecha de 16 semanas');
      document.querySelector('.loader').style.display = 'none';  return;
    }else if (!(startdate < enddate8 && enddate16 > enddate8)) {
      msjBC.error('Error','La fecha de la semana 8 debe ser mayor que la inicial y menor a la semana 16');
      document.querySelector('.loader').style.display = 'none';  return;
    }else{
        datosFormulario = {
          node_name:    nombre,
          node_domain:  url,
          startdate:    Date.parse(start),
          enddate8:     Date.parse(end8),
          enddate16:    Date.parse(end16),
          node_status:  activo,
          edition_acti: edition
        };
        let id_reg = document.getElementById("id_reg").value;
        if (id_reg.trim() === ''){
          datosFormulario['token'] = this.crear_token();
          this.guardar_formulario(datosFormulario, up);
        }else if (llam==0) this.confirmar_alerta()
        else this.guardar_formulario(datosFormulario, up);
    }

  }

  guardar_formulario(datosFormulario, up) {  
    let id_reg = document.getElementById("id_reg").value;  
    if (id_reg.trim() === '') datosFormulario['key'] = 'C01';  
    else {  
      datosFormulario['key'] = 'U01';  
      datosFormulario['node_id'] = document.getElementById('id_reg').value  
      if (up==1) datosFormulario['token'] = this.crear_token();  
    }  
    // Enviar los datos por AJAX a un archivo PHP  
    fetch("../../methods/settings/crud_settings_padre.php", {  
      method: 'POST',  
      headers: { 'Content-Type': 'application/json'  },  
      body: JSON.stringify(datosFormulario)  
    })  
    .then(response => {  
      if (!response.ok) { throw new Error('Network response was not ok');}  
      return response.json();  
    })  
    .then(data => {  
      // La solicitud se completó con éxito  
      if (data.includes('Sin comunicaci')){  
        msjBC.error('Error', data +'<br>Verifique que tenga los servicios web de moodle activos y no tenga bloqueas http');  
      } else {  
        if (data[0] && data[0]['ack']){  
          if (data[0]['ack'] == 1 && id_reg.trim()=='' ) msjBC.ok('Creado', 'Se creó el hijo');  
          else if(data[0]['ack'] == 1) msjBC.ok('Actualizado', data[0]['response']);  
          else msjBC.error('Error', data[0]['response']);  
        }  
        this.listar_hijos();  
      }  
      document.querySelector('.loader').style.display = 'none';  
    })  
    .catch((error) => {  console.error('Error:', error);      });  
  }  
 

  listar_hijos(){  
    document.querySelector('.loader').style.display = "block";  
    document.getElementById('list_tokens_creados').innerHTML = '';  
    document.getElementById('id_reg').value = '';   
    let datosFormulario = {'key': 'R01'}  
    // Enviar los datos por AJAX a un archivo PHP  
    fetch("../../methods/settings/crud_settings_padre.php", {  
      method: 'POST',  
      headers: {   'Content-Type': 'application/json'   },  
      body: JSON.stringify(datosFormulario)  
    })  
    .then(response => {  
      if (!response.ok) { throw new Error('Network response was not ok');}  
      return response.json();  
    })  
    .then(data => {  
      // La solicitud se completó con éxito  
      if(Object.keys(data).length > 0){  
        //data.forEach((v, k) => { 
        for (var k in data) {
          let v = data[k]
          let Act = v['estado'] === '1' ? 'Activo' : 'Inactivo' ;  
          let edit = v['edition'] === '1' ? 'Si' : 'No' ;  
          document.getElementById('list_tokens_creados').insertAdjacentHTML('beforeend', '<div class="border rounded p-2" id="item_list_'+v["id"]+'"> '+  
                  '<div class="edit-item-hijo border rounded " id="token_'+v["id"]+'" onclick="crud.selec_token('+"'"+v["id"]+"'"+')"><i class="fa fa-pencil"></i></div>'+  
                  '<div class="delete-item-hijo border rounded " id="token_'+v["id"]+'" onclick="crud.eliminar_token('+v["id"]+')"><i class="fa fa-trash-o"></i></div>'+  
                  'Nombre: '+v['nombre']+' <br> '+  
                  'Token: '+v['token']+' <br> '+  
                  'URL: '+v['url_hijo'] +' <br> '+  
                  'Fecha inicio del curso: '+v['startdate'] +' <br> '+  
                  'Fecha fin de 8 semanas: '+v['enddate8'] +' <br> '+  
                  'Fecha fin de 16 semanas: '+v['enddate16'] +' <br> '+  
                  'Estado: '+Act +' <br> '+  
                  'Edición de actividades: '+edit +' <br> '+  
              '</div>');  
        };                     
      }else document.getElementById('list_tokens_creados').insertAdjacentHTML('beforeend', '<span class="lista_no_tokens" >NO hay registros de Moodle hijos creados</span>');  
      admin_tok.obtener_tokens_bloqueados();  
      document.querySelector('.loader').style.display = 'none';  
    })  
    .catch((error) => {   console.error('Error:', error);  });  
  }  



  selec_token(id){
    let contenido = document.getElementById('item_list_'+id).innerHTML;
    let partes = contenido.split(' <br> ');
    let nombre = partes[0].split('Nombre: ');
    let url = partes[2].split('URL: ');

    document.getElementById('id_reg').value = id;
    document.getElementById("nombre_hijo").value = nombre[1];
    document.getElementById('url_hijo').value = url[1];
    
    let startdate = partes[3].split('Fecha inicio del curso: ');
    document.getElementById("startdate").value = startdate[1];
    let enddate8 = partes[4].split('Fecha fin de 8 semanas: ');
    document.getElementById("enddate8").value  = enddate8[1]
    let enddate16 = partes[5].split('Fecha fin de 16 semanas: ');
    document.getElementById("enddate16").value = enddate16[1]
    
    let estado = partes[6].split('Estado: ');
    document.getElementById('activo_tok').value = estado[1] == "Activo"? 1:0;
    let edition = partes[7].split('Edición de actividades: ');
    document.getElementById('edition_acti').value = edition[1] == "Si"? 1:0;
      
  }


  eliminar_token(id){  
    document.querySelector('.loader').style.display = "block";  
    let contenido = document.getElementById('item_list_'+id).innerHTML;  
    let partes = contenido.split(' <br> ');  
    let url = partes[2].split('URL: ');  
    let tok = partes[1].split('Token: ');  
    let datosFormulario = {'key'        :'D01',   
                           'node_id'    :id,  
                           'node_token' :tok[1],  
                           'node_domain':url[1]}  
  
    fetch("../../methods/settings/crud_settings_padre.php", {  
      method: 'POST',  
      headers: {   'Content-Type': 'application/json'  },  
      body: JSON.stringify(datosFormulario)  
    })  
    .then(response => {  
      if (!response.ok) { throw new Error('Network response was not ok');}  
      return response.json();  
    })  
    .then(data => {  
      let fjson = data;  
      if (fjson[0] && fjson[0]['ack']){  
        if (fjson[0]['ack'] == 1) msjBC.ok('Eliminado', fjson[0]['response']);  
        else msjBC.error('Error', fjson[0]['response']);  
      }  
      this.listar_hijos();  
      document.querySelector('.loader').style.display = "none";  
    })  
    .catch((error) => {   console.error('Error:', error);   });  
  }  


  
  confirmar_alerta (){
    $.confirm({
      title: 'ACTUALIZAR NODO',
      content: '¿Desea generar un nuevo Token para este hijo?',
      icon: 'fa fa-question',
      theme: 'modern',
      closeIcon: true,
      animation: 'scale',
      type: 'blue',
      buttons: {
        upTokenyes: {
            text: 'SI',
            btnClass: 'btn-dark',
            action: function(){   crud.validar_form(1,1);         }
        },
        upTokenno:{
            text: 'NO',
            btnClass: 'btn-dark',
            action: function(){   crud.validar_form(1);           }                           
        },
        cancel: {
            btnClass: 'btn-red',
            text: 'Cancelar'
        }
      }
    }); 

  }


  eliminar_token (id){
    $.confirm({
      title: 'ELIMINAR',
      content: 'Al eliminar el nodo se perderá toda relación con el mismo (Actualizaciones, actividades propuestas, ETC.)'+
               '<br><br>Tenga presente que las actividades propuestas en el editor de cursos pueden dejar de funcionar si no se crea un nodo con la misma URL. '+
               '<br> ¿Está seguro de eliminar este nodo?',
      icon: 'fa fa-question',
      theme: 'modern',
      closeIcon: true,
      animation: 'scale',
      type: 'red',
      buttons: {
        deleteToken: {
            text: 'Eliminar',
            btnClass: 'btn-dark',
            action: function(){ crud.eliminar_token(id);    }
        },

        cancel: {
            btnClass: 'btn-red',
            text: 'Cancelar'
        }
      }
    }); 

  }


  url_valida(url) {
    let urlPattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return urlPattern.test(url);
  }

  // Función para crear un nuevo objeto
  crear_token(obj) {
    let long = parseInt(40);
    let caracteres = "abcdefghijkmnpqrtuvwxyzABCDEFGHIJKLMNPQRTUVWXYZ2346789";
    let contrasena = "";
    for (let i=0; i<long; i++) contrasena += caracteres.charAt(Math.floor(Math.random()*caracteres.length));
    return contrasena;
  }

}

const crud = new CRUD();

