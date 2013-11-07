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
  if($("#Ta_tipo_plato_in_id option:selected").text()=='otros'){
    $(".agregar-campo").show();
    $("#va_otros").prop('disabled', false);
  }else{
    $(".agregar-campo").hide();
    $("#va_otros").prop('disabled', true);
  }
  $("#Ta_tipo_plato_in_id").change(function() {
    if($("#Ta_tipo_plato_in_id option:selected").text()=='otros'){
      $(".agregar-campo").show();
      $("#va_otros").prop('disabled', false);
    }else{
      $(".agregar-campo").hide();
      $("#va_otros").prop('disabled', true);
    }
  });
});

$(document).ready(function(){
$('#responsive-menu-button').sidr({
      name: 'sidr-main',
      source: '#navigation'
    });
var limpiar_modal =function(){
$('.control-group').removeClass('success');
  $('.control-group').removeClass('error');
  $('label').remove(":contains('OK!')");
  $('.error').remove();
};
$('#local').validate({
      rules: {
        va_horario: { required: true },
        va_rango_precio :{ required:true},
        ta_dia_in_id:{ required : true },
        pais : { required : true}, 
        departamento : { required : true},
        provincia : { required : true},
        distrito : { required : true},        
        va_direccion:{ required : true  }
           
      },
      messages:{
            va_razon_social:{
                required:"Por favor ingresar un horario"
                       },
            va_rango_precio:{
                required:"Por favor ingresa un rango de precio"
            },
            ta_dia_in_id: {
                required : "Por favor ingrese dia de atencion"
            },           
            va_pais : {
              required : "Por favor seleccione el pais"
            },       
            departamento :{
              required : "Por favor seleccione el departamento"                
            },
            provincia : {
              required : "Por favor seleccione la provincia"
            },
            distrito : {
              required : "Por favor seleccione el distrito"
            },
            va_direccion : {
              required : "Por favor ingrese la dirección "
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
$('#restaurante').validate({
      rules: {
        va_nombre: {
            required: true,
            minlength : 4
        },
        va_razon_social: {
              required: true ,
              minlength : 10
        },
        va_web :{
                   url:true},
       /* va_ruc:{
                required : true,
                number:true,
                rucReal:true            
            },*/
        
        Ta_tipo_comida_in_id:{
                required : true
              }       
      },
      messages:{
            va_nombre: {
                required:"Por favor ingresar el nombre del restaurante",
                minlength : "Minimo 4 caracteres"
            },
            va_razon_social:{
                required:"Por favor ingresar la razon social",
                minlength : "Minimo 10 caractertes"
            },
            va_web:{
                url:"Por favor ingresa una Url valida"
            },
           /* va_ruc: {
                required : "Por favor ingrese un Ruc",
                number : "Por favor ingresar solo numeros",
                rucReal:" Ingresa un Ruc valido "
            },*/
                
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
$('#platos').validate({
      rules: {
        va_nombre: {
            required: true,
            minlength : 3
        },
        tx_descripcion: {
              required: true           
        },      
        Ta_tipo_plato:{
          required : true
        }       
      },
      messages:{
        va_nombre: {
            required:"Por favor ingresar el nombre del plato",
            minlength : "Minimo 3 caracteres"
        },
        tx_descripcion:{
            required:"Por favor ingresar la descripcion"
        },                 
        Ta_tipo_plato :{
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

    $('#va_direccion').keyup(function () {
     var value = $(this).val();
    $('#address').val("");
     var d = $("#distrito option:selected").text();
     var p = $("#provincia option:selected").text();
     var pa = $("#pais option:selected").text();
     $("#address").val(value + ", " + d  + " , " + p + " , " + pa);
   }).keyup();

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
      var value = $("#va_direccion").val();   
     var d = $("#distrito option:selected").text();
     var p = $("#provincia option:selected").text();
     var pa = $("#pais option:selected").text();
     $("#address").val(value + ", " + d  + " , " + p + " , " + pa);
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
      var value = $("#va_direccion").val();   
     var d = $("#distrito option:selected").text();
     var p = $("#provincia option:selected").text();
     var pa = $("#pais option:selected").text();
     $("#address").val(value + ", " + d  + " , " + p + " , " + pa);
});

//mapa

var map;
 
function load_map() {
    var myLatlng = new google.maps.LatLng(-12.055345316962327, -77.04518530000001);
    var myOptions = {
        zoom: 15,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map($("#map_canvas").get(0), myOptions);
}
 
$('#search').on('click', function() {
  load_map();
  $("#address").val("");
     var value = $("#va_direccion").val();   
     var d = $("#distrito option:selected").text();
     var p = $("#provincia option:selected").text();
     var pa = $("#pais option:selected").text();
  $("#address").val(value + ", " + d  + " , " + p + " , " + pa);
  $('#mostrar_map').css("display","block");
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
        alert("La direccion es encontrado en google maps : " + status);
    }
}

$(".eli").on("click",function(){
	var id = $(this).attr('data-id');
	var nom =$(this).attr('name');
  $('#eli-user').modal('show');
  console.log(id);
	$('#verusuario').attr({'data-id':id});
	$('#verusuario').html("Estas seguro de eliminar al usuario " + nom + " ?");
});

$(".eli-com").on("click",function(){
  var id = $(this).attr('data-id');
  $('#eli-com').modal('show');
  console.log(id);
  $('#vercom').attr({'data-id':id});
  $('#vercom').html("Estas seguro de eliminar el comentario ?");
});

$(".eli-lo").on("click",function(){
  var id = $(this).attr('data-id');
  $('#eli-local').modal('show');
  console.log(id);
  $('#verlocal').attr({'data-id':id});
  $('#verlocal').html("Estas seguro de eliminar el local ?");
});
$(".eli-lo").on("click",function(){
  var id = $(this).attr('id');
  var es=$(this).attr('data-name');
  console.log(id , es);
  var request = $.ajax({
  url: "/plato/eliminar-plato?id="+id + "&estado=" + es,
  type: "POST",
  data: {id: id, estado:es}  
  });
  if (es=="activo"){
    $(this).attr('data-name','desactivo');
    $(this).html("");
    $(this).removeClass('btn btn-primary');
    $(this).addClass('btn btn-danger');
    $(this).append("<i class='icon-trash icon-white'></i> Desactivar");
  }else{
    $(this).attr('data-name','activo');
    $(this).html("");    
    $(this).removeClass('btn btn-danger');
    $(this).addClass('btn btn-primary');
    $(this).append("<i class='icon-trash icon-white'></i>Activar");
  } 
});
$(".eli-co-menu").on("click",function(){
  var id = $(this).attr('data-id');
  $('#eli-local').modal('show');
  console.log(id);
  $('#verlocal').attr({'data-id':id});
  $('#verlocal').html("Estas seguro de eliminar el local ?");
});

$('.check-plato').mousedown(function() {
    var id = $(this).attr('data-id');
    var est;
        if (!$(this).is(':checked')) {
          if (confirm("Desea Destacar el plato ?") ){
            var est="si";
            var request = $.ajax({
            url: "/plato/destaque-plato?id="+id + "&estado=" + est,
            type: "get"
           
                   });
            $(this).prop("checked", "checked");
            $("#" + id).addClass("success");
            $("#la" + id).removeClass().addClass("label label-success");
            $("#la" + id).html("");
            $("#la" + id).html("Destacado");
                 };
        }else{
          var est="no";
            var request = $.ajax({
            url: "/plato/destaque-plato?id="+id + "&estado=" + est,
            type: "get"
            
                   });
          $("#" + id).removeClass("success");
          $("#la" + id).removeClass().addClass("label label-important");
          $("#la" + id).html("");
            $("#la" + id).html("No Destacado");
              }
    }); 
$('.check_rest').mousedown(function() {
    var id = $(this).attr('data-id');
    console.log(id); 
    var est;
        if (!$(this).is(':checked')) {
          if (confirm("Desea Activar al Restaurante ?") ){
            var est="activo";
            var request = $.ajax({
            url: "/restaurante/index/cambiaestado?id="+id + "&estado=" + est,
            type: "get",
            data: {id: id , estado:est}
                   });
            $(this).prop("checked", "checked");
            $("#" + id).addClass("success");
            $("#la" + id).removeClass().addClass("label label-success");
            $("#la" + id).html("");
            $("#la" + id).html("activo");
              $("#" + id).closest('tr').remove();
                 };
        }else{
          var est="desactivo";
            var request = $.ajax({
            url: "/restaurante/index/cambiaestado?id="+id + "&estado=" + est,
            type: "get",
            data: {id: id , estado:est}
                   });
          $("#" + id).removeClass("success");
          $("#la" + id).removeClass().addClass("label label-important");
          $("#la" + id).html("");
          $("#la" + id).html("desactivo");
          $("#" + id).closest('tr').remove();
              }
    });
$('.check_rest_menu').mousedown(function() {
    var id = $(this).attr('data-id');
    console.log(id); 
    var est;
        if (!$(this).is(':checked')) {
          if (confirm("Desea Activar al Restaurante ?") ){
            var est="activo";
            var request = $.ajax({
            url: "/restaurante/index/cambiaestadomenu?id="+id +"&estado="+ est,
            type: "get",
            data: {id: id , estado:est}
                   });
            $(this).prop("checked", "checked");
            $("#" + id).addClass("success");
            $("#la" + id).removeClass().addClass("label label-success");
            $("#la" + id).html("");
            $("#la" + id).html("activo");
              //$("#" + id).closest('tr').remove();
                 };
        }else{
          var est="desactivo";
            var request = $.ajax({
            url: "/restaurante/index/cambiaestadomenu?id="+id + "&estado=" + est,
            type: "get",
            data: {id: id , estado:est}
                   });
          $("#" + id).removeClass("success");
          $("#la" + id).removeClass().addClass("label label-important");
          $("#la" + id).html("");
          $("#la" + id).html("desactivo");
          //$("#" + id).closest('tr').remove();
              }
    }); 
    
    
    
    $('.check_rest_registrorestaurante').mousedown(function() {
    var id = $(this).attr('data-id');
    console.log(id); 
    var est;
        if (!$(this).is(':checked')) {
          if (confirm("Desea Activar al registro ?") ){
            var est="activo";
            var request = $.ajax({
            url: "/restaurante/index/activarregistro?id="+id +"&estado="+ est,
            type: "get",
            data: {id: id , estado:est}
                   });
            $(this).prop("checked", "checked");
            $("#" + id).addClass("success");
            $("#la" + id).removeClass().addClass("label label-success");
            $("#la" + id).html("");
            $("#la" + id).html("activo");
              //$("#" + id).closest('tr').remove();
                 };
        }else{
         // var est="desactivo";
           // var request = $.ajax({
          //  url: "/restaurante/index/activarregistro?id="+id + "&estado=" + est,
          //  type: "get",
          //  data: {id: id , estado:est}
            //       });
          //$("#" + id).removeClass("success");
          //$("#la" + id).removeClass().addClass("label label-important");
          //$("#la" + id).html("");
         // $("#la" + id).html("desactivo");
          //$("#" + id).closest('tr').remove();
              }
    });
    
    
    
    
    

  $('.check').mousedown(function() {
  	var id = $(this).attr('data-id');
    console.log(id);
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
  $('.check-com').mousedown(function() {
    var id = $(this).attr('data-id');
    console.log(id);
     var est;
        if (!$(this).is(':checked')) {
          if (confirm("Desea Aprobar el comentario ?") ){
            var est="aprobado";
            var request = $.ajax({
            url: "/usuario/comentarios/cambiaestado?id="+id + "&estado=" + est,
            type: "get",
            data: {id: id , estado:est}
                   });
            $(this).prop("checked", "checked");
            $("#" + id).addClass("success");
            $("#la" + id).removeClass().addClass("label label-success");
            $("#la" + id).html("");
            $("#la" + id).html("aprobado");
                 };
        }else{
          var est="desaprobado";
            var request = $.ajax({
            url: "/usuario/comentarios/cambiaestado?id="+id + "&estado=" + est,
            type: "get",
            data: {id: id , estado:est}
                   });
          $("#" + id).removeClass("success");
          $("#la" + id).removeClass().addClass("label label-important");
          $("#la" + id).html("");
            $("#la" + id).html("desaprobado");
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
  $("#delete-local").on("click",function(){
    var user=$("#verlocal").attr("data-id");
    $("#" + user).closest('tr').remove();
    $('#eli-local').modal('hide');
    console.log(user);
    var request = $.ajax({
    url: "/local/index/eliminarlocal?id="+user,
    type: "POST",
    data: {id: user} 
    });
  });
  $("#delete-local-menu").on("click",function(){
    var menuG=$("#verlocal").attr("data-id");
    $("#" + menuG).closest('tr').remove();
    $('#eli-local').modal('hide');
    console.log(menuG);
    var request = $.ajax({
      url: "/restaurante/index/eliminarmenu?id="+menuG,
      type: "POST",
      data: {id: menuG} 
    });
  });
   $("#delete-registro-restaurantes").on("click",function(){
    var menuG=$("#verlocal").attr("data-id");
    $("#" + menuG).closest('tr').remove();
    $('#eli-local').modal('hide');
    console.log(menuG);
    var request = $.ajax({
      url: "/restaurante/index/eliminarregistro?id="+menuG,
      type: "GET",
      data: {id: menuG} 
    });
  });
  $("#delete-local-banner").on("click",function(){
    var menuG=$("#verlocal").attr("data-id");
    $("#" + menuG).closest('tr').remove();
    $('#eli-local').modal('hide');
    console.log(menuG);
    var request = $.ajax({
      url: "/restaurante/index/eliminarbanner?id="+menuG,
      type: "POST",
      data: {id: menuG} 
    });
  });
  $("#delete-comentario").on("click",function(){
    var user=$("#vercom").attr("data-id");
    $("#" + user).closest('tr').remove();
    $('#eli-com').modal('hide');
    console.log(user);
    var request = $.ajax({
    url: "/usuario/comentarios/eliminarcomentario?id="+user,
    type: "POST",
    data: {id: user} 
    });
  });
});
