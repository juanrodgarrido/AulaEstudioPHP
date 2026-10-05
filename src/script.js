$(document).ready(function() {


const valoresURL = window.location.search; 
const parametros = new URLSearchParams(valoresURL) 

const nombreGuardado = parametros.get('nombre');
const apellido1 = parametros.get('apellido1');
const apellido2 = parametros.get('apellido2');
const dni = parametros.get('dni');
const email = parametros.get('email');

$('.nombreResultado').val(nombreGuardado);
$('.apellido1Resultado').val(apellido1)
$('.apellido2Resultado').val(apellido2)
$('.dniResultado').val(dni);
$('.emailResultado').val(email);


$(".btnform").click(function (e) {    


    let nombre = $("#nombre").val().trim();
    let apellido1 = $("#apellido1").val().trim();
    let apellido2 = $("#apellido2").val().trim();
    let email = $("#email").val().trim();

    if(nombre.length < 1 || apellido1.length < 1 || apellido2.length < 1 || email.length < 1){
        e.preventDefault();
        alert("Falta algún dato");
        return;
    }

    
    let dni = $("#dni").val().trim().toUpperCase();

    if(!calcularLetra(dni)){
        e.preventDefault();
        alert("Has introducido un DNI incorrecto");
        return;
    };
    
    



})

function calcularLetra(dni) {

    const letras = "TRWAGMYFPDXBNJZSQVHLCKE";

    let soloDNI = dni.substring(0, 8);
    let numeroDNI = parseInt(soloDNI);
    let letraUsuario = dni.charAt(8)

    if(isNaN(numeroDNI) || numeroDNI < 0 || numeroDNI > 99999999){
        return false;
    }

    const resto = numeroDNI % 23; 
    if(letras.charAt(resto) == letraUsuario){
        return true;
    }else{
        return false;
    }
    
}


})