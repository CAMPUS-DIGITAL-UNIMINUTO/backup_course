class CRUD {
  constructor() {

  }

  validar_form(llam = 0, up=0) {
    $('.loader').css("display", "block");
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
    let enddate8 = new Date(end8);
    let enddate16 = new Date(end16);

    if (nombre.trim() === '') {
      msjBC.error('Error','Debe diligenciar el nombre del Hijo');
      $('.loader').hide(); return;
      return;
    }else if (url.trim() === ''){
      msjBC.error('Error','Debe diligenciar la URL del Hijo');
      $('.loader').hide(); return;
    }else if (!this.isValidUrl(url)) {
      msjBC.error('Error','La URL ingresada no es válida');
      $('.loader').hide(); return;
    }else if (start.trim() === ''){
      msjBC.error('Error','Debe diligenciar la fecha inicial');
      $('.loader').hide(); return;
    }else if (end8.trim() === ''){
      msjBC.error('Error','Debe diligenciar la fecha de 8 semanas');
      $('.loader').hide(); return;
    }else if (end16.trim() === ''){
      msjBC.error('Error','Debe diligenciar la fecha de 16 semanas');
      $('.loader').hide(); return;
    }else if (!(startdate < enddate8 && enddate16 > enddate8)) {
      msjBC.error('Error','La fecha de la semana 8 debe ser mayor que la inicial y menor a la semana 16');
      $('.loader').hide(); return;
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
          datosFormulario['token'] = this.create_tok();
          this.guardar_formulario(datosFormulario, up);
        }else if (llam==0) this.alert_Confirmar()
        else this.guardar_formulario(datosFormulario, up);
    }

  }

  guardar_formulario(datosFormulario, up) {
    let id_reg = document.getElementById("id_reg").value;
    if (id_reg.trim() === ''){
      datosFormulario['key'] = 'C01';
    }else {
      datosFormulario['key'] = 'U01';
      datosFormulario['node_id'] = document.getElementById('id_reg').value
      if (up==1)datosFormulario['token'] = crud.create_tok();
    }
    // Enviar los datos por AJAX a un archivo PHP
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../methods/settings/crud_settings_padre.php", true);
    //xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        // La solicitud se completó con éxito
        if (xhr.responseText.includes('Sin comunicaci')){
          msjBC.error('Error', xhr.responseText +'<br>Verifique que tenga los servicios web de moodle activos y no tenga bloqueas http');
        }else{
          let fjson = JSON.parse(xhr.responseText);
          if (fjson[0] && fjson[0]['ack']){
            if (fjson[0]['ack'] == 1 && id_reg.trim()=='' ) msjBC.ok('Creado', 'Se creó el hijo');
            else if(fjson[0]['ack'] == 1) msjBC.ok('Actualizado', fjson[0]['response']);
            else msjBC.error('Error', fjson[0]['response']);
          }
          crud.listar_hijos();
        }
      }
      $('.loader').hide();
    };
    xhr.send(JSON.stringify(datosFormulario));
  }

  

  listar_hijos(){
    $('.loader').css("display", "block");
    $('#list_tokens_creados').empty();
    $('#id_reg').val(''); 
    let datosFormulario = {'key': 'R01'}
    // Enviar los datos por AJAX a un archivo PHP
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../methods/settings/crud_settings_padre.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        // La solicitud se completó con éxito
        if(xhr.responseText.length > 2){
            let fjson = JSON.parse(xhr.responseText);
            $.each(fjson, function(k,v){
                let Act = v['estado'] === '1' ? 'Activo' : 'Inactivo' ;
                let edit = v['edition'] === '1' ? 'Si' : 'No' ;
            $('#list_tokens_creados').append('<div class="tok_no_selec" id="item_list_'+v["id"]+'"> '+
                    '<div class="edit-item-hijo" id="token_'+v["id"]+'" onclick="crud.selec_token('+"'"+v["id"]+"'"+')"></div>'+
                    '<div class="delete-item-hijo" id="token_'+v["id"]+'" onclick="crud.eliminar_token('+v["id"]+')"></div>'+
                    'Nombre: '+v['nombre']+' <br> '+
                    'Token: '+v['token']+' <br> '+
                    'URL: '+v['url_hijo'] +' <br> '+
                    'Fecha inicio del curso: '+v['startdate'] +' <br> '+
                    'Fecha fin de 8 semanas: '+v['enddate8'] +' <br> '+
                    'Fecha fin de 16 semanas: '+v['enddate16'] +' <br> '+
                    'Estado: '+Act +' <br> '+
                    'Edición de actividades: '+edit +' <br> '+
                '</div>');
            });                   
        }else{
            $('#list_tokens_creados').append('<span class="lista_no_tokens" >NO hay registros de Moodle hijos creados</span>');
            
        }
        admin_tok.get_tokens_black();
        $('.loader').hide();

      }
    };
    xhr.send(JSON.stringify(datosFormulario));
  }


  selec_token(id){
    var contenido = document.getElementById('item_list_'+id).innerHTML;
    var partes = contenido.split(' <br> ');
    var nombre = partes[0].split('Nombre: ');
    nombre = nombre[1];
    var url = partes[2].split('URL: ');
    url = url[1]; 

    document.getElementById('id_reg').value = id;
    document.getElementById("nombre_hijo").value = nombre;
    document.getElementById('url_hijo').value = url;
    
    var startdate = partes[3].split('Fecha inicio del curso: ');
    document.getElementById("startdate").value = startdate[1];
    var enddate8 = partes[4].split('Fecha fin de 8 semanas: ');
    document.getElementById("enddate8").value = enddate8[1]
    var enddate16 = partes[5].split('Fecha fin de 16 semanas: ');
    document.getElementById("enddate16").value= enddate16[1]
    
    var estado = partes[6].split('Estado: ');
    document.getElementById('activo_tok').value = estado[1] == "Activo"? 1:0;
    var edition = partes[7].split('Edición de actividades: ');
    document.getElementById('edition_acti').value = edition[1] == "Si"? 1:0;
      
  }


  deletToken(id){
    $('.loader').css("display", "block");
    var contenido = document.getElementById('item_list_'+id).innerHTML;
    var partes = contenido.split(' <br> ');
    var url = partes[2].split('URL: ');
    var tok = partes[1].split('Token: ');
    let datosFormulario = {'key'        : 'D01', 
                           'node_id'    : id,
                           'node_token' :tok[1],
                           'node_domain':url[1]}

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../methods/settings/crud_settings_padre.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        let fjson = JSON.parse(xhr.responseText);
        if (fjson[0] && fjson[0]['ack']){
          if (fjson[0]['ack'] == 1) msjBC.ok('Eliminado', fjson[0]['response']);
          else msjBC.error('Error', fjson[0]['response']);
        }
        crud.listar_hijos();
      }
    };
    xhr.send(JSON.stringify(datosFormulario));
  }

  
  alert_Confirmar (){
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
        content: '¿Desea Eliminar el registro?',
        icon: 'fa fa-question',
        theme: 'modern',
        closeIcon: true,
        animation: 'scale',
        type: 'red',
        buttons: {
            deleteToken: {
                text: 'Eliminar',
                btnClass: 'btn-dark',
                action: function(){
                    crud.deletToken(id);   
                }
            },

            cancel: {
                btnClass: 'btn-red',
                text: 'Cancelar'
            }
        }
    }); 

  }


  isValidUrl(url) {
    var urlPattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return urlPattern.test(url);
  }

  // Función para crear un nuevo objeto
  create_tok(obj) {
    let long = parseInt(40);
    let caracteres = "abcdefghijkmnpqrtuvwxyzABCDEFGHIJKLMNPQRTUVWXYZ2346789";
    let contrasena = "";
    for (let i=0; i<long; i++) contrasena += caracteres.charAt(Math.floor(Math.random()*caracteres.length));
    return contrasena;
  }

}

const crud = new CRUD();
crud.listar_hijos()