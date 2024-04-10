/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var script_jqu = document.createElement('script');

script_jqu.onload = function () {

    var script_confirm = document.createElement('script');
    
    script_confirm.onload = function () {
        //alert("Script loaded and script_confirm");
    };

    script_confirm.src = "../../js/base/jquery-confirm.js";
    document.getElementsByTagName('head')[0].appendChild(script_confirm);

    var script_search = document.createElement('script');
};

script_jqu.src = "../../../../lib/jquery/jquery-3.6.1.js";
document.getElementsByTagName('head')[0].appendChild(script_jqu);