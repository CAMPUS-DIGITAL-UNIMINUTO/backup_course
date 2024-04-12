/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class llamar_js {
    constructor() {this.ejecutar();}
    /**
     * llamar scripts de conficto con moodle
     * @returns {undefined}
     */
    ejecutar() { 
        let script_jqu = document.createElement('script');
        script_jqu.onload = function () {
            let script_confirm = document.createElement('script');
            
            script_confirm.onload = function () {
                //alert("Script loaded and script_confirm");
            };

            script_confirm.src = "../../js/base/jquery-confirm.js";
            document.getElementsByTagName('head')[0].appendChild(script_confirm);

            let script_search = document.createElement('script');
        };

        script_jqu.src = "../../../../lib/jquery/jquery-3.6.1.js";
        document.getElementsByTagName('head')[0].appendChild(script_jqu);
    }
}
let llamajs = new llamar_js();