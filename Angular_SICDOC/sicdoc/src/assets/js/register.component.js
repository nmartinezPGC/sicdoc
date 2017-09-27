// Funciones de Validacion de Campos
function validCampo() {
  alert('Estoy Evaluando el Campo NMA: ');
}

$(document).ready(function(){
    $("#btn1").click(function(){
        $("p").append(" <b>Appended text</b>.");
    });

    $("#btn2").click(function(){
        $("ol").append("<li>Appended item</li>");
    });
});
