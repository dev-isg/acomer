// requirejs.config({
//       paths: {
//         'jquery': 'lib/jquery-1.8.3.min',
//         'bootstrap': 'lib/bootstrap.min',
//         'validate': 'vendor/jquery.validate',
//         'gmap' : 'vendor/gmap3.min',
//         'upload' : 'vendor/AjaxUpload.2.0.min'
//       }
//     });


//require(['jquery','upload','bootstrap', 'validate','gmap'], function ($) {
//funciones de usuarios
$(document).ready(function(){
       
var limpiar_modal =function(){
$('.control-group').removeClass('success');
  $('.control-group').removeClass('error');
  $('label').remove(":contains('OK!')");
  $('.error').remove();
};
$('#restaurante').validate({
      rules: {
        va_nombre: {
            required: true
        },
        va_razon_social: {
              required: true
        },
        va_web :{
                   url:true},
        va_ruc:{
                required : true,
                rucReal:true            
            },
        Ta_tipo_comida_in_id:{
                required : true
              }       
      },
      messages:{
            va_nombre: {
                required:"Por favor ingresar el nombre del restaurante"
            },
            va_razon_social:{
                required:"Por favor ingresar la razon social"
            },
            va_web:{
                url:"Por favor ingresa una Url valida"
            },
            va_ruc: {
                required : "Por favor ingrese un Ruc",
                rucReal:" Ingresa un Ruc valido "
            },           
            Ta_tipo_comida_in_id :{
              required : "Por favor ingresar un tipo de plato"                
            }
        },
      highlight: function(element) {
        $(element).closest('.control-group').removeClass('success').addClass('error');
      },
      success: function(element) {
        element
        .text('OK!').addClass('valid')
        .closest('.control-group').removeClass('error').addClass('success');
      }
    });
var validar=function(elemento){
$(elemento).validate({
	    rules: {
	      va_nombre: {
	          required: true
	      },
	      va_apellidos: {
	   	        required: true
	      },
	      Ta_rol_in_id :{
	      required:true},
	      va_email: {
	        required: true,
	        email: true
	      },
	      va_contrasenia:{
                required : true,
                minlength:6             
            },
        va_contrasenia2:{
                required : true,
                equalTo: "#va_contrasenia",
                minlength:6               
            }
	    },
	    messages:{
            va_nombre: {
                required:"Por favor ingresar el nombre"
            },
            va_apellidos:{
                required:"Por favor ingresar el apellido"
            },
            va_email:{
                required:"Por favor ingresa un Email Valido"
            },
            va_contrasenia: {
                required : "Ingrese la clave",
                minlength:"Ingresa un password de 6 caracteres a mas"
            },
            va_contrasenia2: {
                required : "Repita la clave",            
                minlength:"Ingresa un password de 6 caracteres a mas",
                equalTo : "Ingrese el mismo valor de Clave"
            },
            Ta_rol_in_id :{
            	required : "Por favor ingresar un rol"            		
            }
        },
			highlight: function(element) {
				$(element).closest('.control-group').removeClass('success').addClass('error');
			},
			success: function(element) {
				element
				.text('OK!').addClass('valid')
				.closest('.control-group').removeClass('error').addClass('success');
			}
	  });

}
validar('#usuario');

$('#ta_restaurante_in_id').val(id_r);
 //llenado de combo
 $.getJSON('/usuario/index/jsonestado',function(data){
		$.each(data,function(i,val){
			$('#Ta_rol_in_id').append("<option value=" + val.in_id + " >" + val.va_nombre_rol + " </option>" );			
		})
});
 $.getJSON('/restaurante/index/jsoncomida',function(data){
    $.each(data,function(i,val){
      $('#Ta_tipo_comida_in_id').append("<option value=" + val.in_id + " >" + val.va_nombre_tipo + " </option>" );            
    })
});

$.getJSON('/restaurante/index/medio',function(data){ 
    $.each(data,function(i,val){
          $('#cmodal').append("<input type='checkbox' name='va_modalidad[]' id="+ val.in_id+" value="+ val.in_id+"> " + val.va_nombre + "</br>" ); 
          $('#comodal').append("<input type='checkbox' name='va_modalidad[]' id="+ val.in_id+" value="+ val.in_id+"> " + val.va_nombre + "</br>" );              
    });
});
$.getJSON('/local/index/jsonservicios',function(data){ 
    $.each(data,function(i,val){
          $('#servicio_local').append("<input type='checkbox' name='servicio_local[]' id="+ val.in_id+"> " + val.va_nombre + "</br>" );                     
    });
});

//llenado de combos
$("#pais").change(function(evento){
   var pais=parseInt($(this).val());
$.getJSON('/local/index/jsondepartamento',function(data){
    if(pais==1){
       $.each(data,function(i,val){
          $('#departamento').append( "<option value=" + val.in_iddep + " >" + val.ch_departamento + " </option>");                    
    });          
    }
});
});

  $("#departamento").change(function(evento){
   var dep=parseInt($(this).val());
   var url="/local/index/jsonprovincia?iddepa=" + dep;
 console.log(dep);
      $.getJSON(url,function(data){
         $("#provincia").empty();
          $("#distrito").empty();
          $("#provincia").append("<option value=''>Seleccione</option>");      
            $.each(data,function(i,val){         
                $('#provincia').append( "<option value=" + val.in_idprov + " >" + val.ch_provincia + " </option>");                                
                      });  
     });
});

  $("#provincia").change(function(evento){
 
   var dep=parseInt($('#departamento').val());
    var pro=parseInt($(this).val());
   var url="/local/index/jsondistrito?iddepa=" + dep + "&iddpro=" + pro
  console.log(pro);
      $.getJSON(url,function(data){
         $("#distrito").empty();
        $("#distrito").append("<option value=''>Seleccione</option>");
    $.each(data,function(i,val){  
          
                $('#distrito').append( "<option value=" + val.in_iddis + " >" + val.ch_distrito + " </option>");   
           
          });  
     });
});

//mapa

var map;
 
function load_map() {
    var myLatlng = new google.maps.LatLng(-12.055345316962327, -77.04518530000001);
    var myOptions = {
        zoom: 4,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map($("#map_canvas").get(0), myOptions);
}
 
$('#search').on('click', function() {
  load_map();
  $('#map_canvas').css("display","block");
    var address = $('#address').val();
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'address': address}, geocodeResult);
});
var infoWindow = null;
 
function openInfoWindow(marker) {
    var markerLatLng = marker.getPosition();
   $('#de_latitud').html("");
    $('#de_longitud').html("");
    var l= markerLatLng.lat();
    var lo = markerLatLng.lng();
   $('#de_latitud').val(l);
    $('#de_longitud').val(lo);

}
 
function geocodeResult(results, status) {
    // Verificamos el estatus
    if (status == 'OK') {
        // Si hay resultados encontrados, centramos y repintamos el mapa
        // esto para eliminar cualquier pin antes puesto
        var mapOptions = {
            center: results[0].geometry.location,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map($("#map_canvas").get(0), mapOptions);
        // fitBounds acercará el mapa con el zoom adecuado de acuerdo a lo buscado
        map.fitBounds(results[0].geometry.viewport);
        $('#de_latitud').val(results[0].geometry.location.lat());
      $('#de_longitud').val(results[0].geometry.location.lng());
        // Dibujamos un marcador con la ubicación del primer resultado obtenido
        var markerOptions = { position: results[0].geometry.location ,draggable: true}
        var marker = new google.maps.Marker(markerOptions);
        google.maps.event.addListener(marker, 'dragend', function(){ openInfoWindow(marker); });
        marker.setMap(map);
    } else {
        // En caso de no haber resultados o que haya ocurrido un error
        // lanzamos un mensaje con el error
        alert("Geocoding no tuvo éxito debido a: " + status);
    }
}

  //carga imagen
// $('#para').click(function(){
//       var btn_firma = $('#addImage'), interval;    
//       new AjaxUpload('#addImage', {
//         action: 'includes/uploadFile.php',onSubmit : function(file , ext){
//           if (! (ext && /^(jpg|png)$/.test(ext))){
//             alert('Sólo se permiten Imagenes .jpg o .png');
//             return false;
//           } else {
//             $('#loaderAjax').show();
//             btn_firma.text('Espere por favor');
//             this.disable();
//           }
//         },
//         onComplete: function(file, response){
     
//           btn_firma.text('Cambiar Imagen');          
//           respuesta = $.parseJSON(response);
//           if(respuesta.respuesta == 'done'){
//             $('#fotografia').removeAttr('scr');
//             $('#fotografia').attr('src','img/' + respuesta.fileName);
//             $('#loaderAjax').show();
//           }
//           else{
//             alert(respuesta.mensaje);
//           }
            
//           $('#loaderAjax').hide();  
//           this.enable();  
//         }
//     });
// });


//   $('#direccion_loc').keyup(function () {
//      var value = $(this).val();
//      var d = $("#distrito option:selected").text()
//      var p = $("#provincia option:selected").text()
//      var pa = $("#pais option:selected").text()
//      $("#address").val(value + ", " + d  + " , " + p + " , " + pa);
//    }).keyup();
//

$(".eli").on("click",function(){
	var id = $(this).attr('data-id');
	var nom =$(this).attr('name');
  $('#eli-user').modal('show');
  console.log(id);
	$('#verusuario').attr({'data-id':id});
	$('#verusuario').html("Estas seguro de eliminar al usuario " + nom + " ?");
});


$('.check_rest').mousedown(function() {
    var id = $(this).attr('id');
    console.log(id);
    console.log()
    var est;
        if (!$(this).is(':checked')) {
          if (confirm("Desea Activar al Restaurante ?") ){
            var est="activo";
            var request = $.ajax({
            url: "restaurante/index/cambiaestado?id="+id + "&estado=" + est,
            type: "get",
            data: {id: id , estado:est}
                   });
            $(this).prop("checked", "checked");
            $("#" + id).addClass("success");
            $("#la" + id).removeClass().addClass("label label-success");
            $("#la" + id).html("");
            $("#la" + id).html("activo");
                 };
        }else{
          var est="desactivo";
            var request = $.ajax({
            url: "restaurante/index/cambiaestado?id="+id + "&estado=" + est,
            type: "get",
            data: {id: id , estado:est}
                   });
          $("#" + id).removeClass("success");
          $("#la" + id).removeClass().addClass("label label-important");
          $("#la" + id).html("");
            $("#la" + id).html("desactivo");
              }
    }); 

  $('.check').mousedown(function() {
  	var id = $(this).attr('id');
    console.log(id);
    console.log()
    var est;
        if (!$(this).is(':checked')) {
        	if (confirm("Desea Activar al usuario ?") ){
            var est="activo";
            var request = $.ajax({
            url: "/usuario/index/cambiaestado?id="+id + "&estado=" + est,
            type: "get",
            data: {id: id , estado:est}
                   });
            $(this).prop("checked", "checked");
            $("#" + id).addClass("success");
            $("#la" + id).removeClass().addClass("label label-success");
            $("#la" + id).html("");
            $("#la" + id).html("activo");
                 };
        }else{
          var est="desactivo";
            var request = $.ajax({
            url: "/usuario/index/cambiaestado?id="+id + "&estado=" + est,
            type: "get",
            data: {id: id , estado:est}
                   });
        	$("#" + id).removeClass("success");
        	$("#la" + id).removeClass().addClass("label label-important");
          $("#la" + id).html("");
            $("#la" + id).html("desactivo");
              }
    });	

  $("#delete").on("click",function(){
	var user=$("#verusuario").attr("data-id");
	$("#" + user).closest('tr').remove();
	$('#eli-user').modal('hide');
	console.log(user);
  var request = $.ajax({
  url: "/usuario/index/eliminarusu?id="+user,
  type: "POST",
  data: {id: user} 
  });
});
});
