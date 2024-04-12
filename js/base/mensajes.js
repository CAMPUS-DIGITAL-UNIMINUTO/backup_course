class mensajesBC {  
    noticia(tit, txt)       {  this.create_mjs(tit, txt,'#f39c12 ');  }  
  
    informacion(tit, txt)   {   this.create_mjs(tit, txt,'#3c8dbc '); }  
  
    ok(tit, txt)            {   this.create_mjs(tit, txt,'#00a65a');  }  
  
    error(tit, txt)         {  this.create_mjs(tit, txt,'#dd4b39');   }  
  
    create_mjs(tit, txt, col) {  
        let x = document.getElementById("snackbar");  
        if(x){  
            x.innerHTML = '<strong>'+tit+'</strong><br>'+txt;  
            x.className = "show";  
            x.style.backgroundColor = col;  
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);      
        }else{  
            throw new Error('Cree un div con id snackbar');  
        }  
    }  
}  
  
let msjBC = new mensajesBC();  


