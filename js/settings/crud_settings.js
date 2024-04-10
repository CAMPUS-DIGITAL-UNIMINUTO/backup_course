class CRUD {
  constructor() {
    this.data = []; // Array para almacenar los objetos
  }

  validar_form() {
    $('.loader').css("display", "block");
    // Validar que todos los campos estén llenos
    var nombre = document.getElementById("nombre_hijo").value;
    var url    = document.getElementById("url_hijo").value;
    var start  = document.getElementById("startdate").value;
    var end8   = document.getElementById("enddate8").value;
    var end16  = document.getElementById("enddate16").value;
    var activo = document.getElementById("activo_tok").value;
    var edition= document.getElementById("edition_acti").value;

    if (nombre.trim() === '') {
      msjBC.error('Error','Debe diligenciar el nombre del Hijo');
      return;
    }else if (url.trim() === ''){
      msjBC.error('Error','Debe diligenciar la URL del Hijo');
      return;
    }else if (!isValidUrl(url)) {
      msjBC.error('Error','La URL ingresada no es válida');
      return;
    }else if (start.trim() === ''){
      msjBC.error('Error','Debe diligenciar la fecha inicial');
      return;
    }else if (end8.trim() === ''){
      msjBC.error('Error','Debe diligenciar la fecha de 8 semanas');
      return;
    }else if (end16.trim() === ''){
      msjBC.error('Error','Debe diligenciar la fecha de 16 semanas');
      return;
    }else{
        let datosFormulario = {
          node_name:    nombre,
          node_domain:  url,
          startdate:    start,
          enddate8:     end8,
          enddate16:    end16,
          node_status:  activo,
          edition_acti: edition,
        };
    }

    // Si todos los campos están llenos, enviar el formulario
    crud.guardar_formulario(datosFormulario);
  }

  guardar_formulario(datosFormulario) {
    let id_reg = document.getElementById("id_reg").value;
    if (id_reg.trim() === ''){
      datosFormulario['key'] = 'C01';
      datosFormulario['token'] = crud.create_tok();
    }else datosFormulario['key'] = 'U01';
      

    // Enviar los datos por AJAX a un archivo PHP
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../methods/settings/crud_settings_padre.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        // La solicitud se completó con éxito
        console.log(xhr.responseText);
      }
    };
    xhr.send(JSON.stringify(datosFormulario));
  }

  

  listar_hijos(){
    let datosFormulario = {'key': 'R01'}
    // Enviar los datos por AJAX a un archivo PHP
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../methods/settings/crud_settings_padre.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        // La solicitud se completó con éxito
        console.log('->',xhr.responseText);
        if(xhr.responseText.length > 2){
            var fjson = JSON.parse(xhr.responseText);
            arrReg = fjson;
            $.each(fjson, function(k,v){
                var Act = v['estado'] === '1' ? 'Activo' : 'Inactivo' ;
                var edit = v['edition'] === '1' ? 'Si' : 'No' ;
            $('#list_tokens_creados').append('<div class="tok_no_selec" id="item_list_'+v["id"]+'"> '+
                    '<div class="edit-item-hijo" id="token_'+v["id"]+'" onclick="funcList.selec_token('+"'"+v["id"]+"'"+')"></div>'+
                    '<div class="delete-item-hijo" id="token_'+v["id"]+'" onclick="funcList.eliminar_token('+v["id"]+')"></div>'+
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
        $('.loader').hide();

      }
    };
    xhr.send(JSON.stringify(datosFormulario));
  }

  // Función para crear un nuevo objeto
  create_tok(obj) {
    let long = parseInt(40);
    let caracteres = "abcdefghijkmnpqrtuvwxyzABCDEFGHIJKLMNPQRTUVWXYZ2346789";
    let contrasena = "";
    for (i=0; i<long; i++) contrasena += caracteres.charAt(Math.floor(Math.random()*caracteres.length));
    return contrasena;
  }

}

// Ejemplo de uso:
const crud = new CRUD();
crud.listar_hijos()
