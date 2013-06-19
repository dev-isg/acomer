requirejs.config({
      paths: {
        'jquery': 'lib/jquery-1.8.3.min',
        'bootstrap': 'lib/bootstrap.min',
        'validate': 'vendor/jquery.validate',
        'gmap' : 'vendor/gmap3.min',
        'upload' : 'vendor/AjaxUpload.2.0.min'
      }
    });


require(['jquery','upload','bootstrap', 'validate','gmap'], function ($) {
//funciones de usuarios

       

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
var cerrar_mod = function(mod,form){
    $(mod).modal('hide');
    $(form)[0].reset();
    limpiar_modal();
 }


//utilizando funciones 
 validar('#usuario');
 
 // $('#cerrar_insert_usuario').on('click', function(){
 // 	cerrar_mod('#ins-usuario','#ingreso-form');
 // });
 
 // $('#cerrar_update_usuario').on('click', function(){
 // 	cerrar_mod('#mod-usuario','#modificar-form');
 // });
 $('#cerrar_rol').on('click', function(){
 	cerrar_mod('#ins-rol','#form_rol');
 });
 // $('#cerrar_insert_rest').on('click', function(){
 //  cerrar_mod('#ing-restaurante','#ingrest-form');
 // });
//json para rol
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
// $.getJSON('especialidad.json',function(data){
//    $.each(data,function(i,val){
//      $('#esp_rol').append("<option value=" + val.id + " >" + val.nombre + " </option>" );            
//    })
//});
//
// $.getJSON('mod.json',function(data){
//    $.each(data,function(i,val){
//      $('#check-mod').append( "<input type='checkbox' id=" + val.id + " >" + val.nombre+ "</br>");            
//    })
//});

//mapa

$.getJSON('/local/index/jsondepartamento',function(data){
    $.each(data,function(i,val){
          $('#departamento').append( "<option value=" + val.in_iddep + " >" + val.ch_departamento + " </option>"); 
                   
    });

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


  $("#departamento").change(function(evento){
   var dep=parseInt($(this).val());
   var url="/local/index/jsonprovincia?iddepa=" + dep;
 console.log(dep);
      $.getJSON(url,function(data){
         $("#provincia").empty();
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
    // Obtenemos la dirección y la asignamos a una variable
    var address = $('#address').val();
    // Creamos el Objeto Geocoder
    var geocoder = new google.maps.Geocoder();

    // Hacemos la petición indicando la dirección e invocamos la función
    // geocodeResult enviando todo el resultado obtenido
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

$('#para').click(function(){
  //carga imagen

      var btn_firma = $('#addImage'), interval;    
      new AjaxUpload('#addImage', {
        action: 'includes/uploadFile.php',onSubmit : function(file , ext){
          if (! (ext && /^(jpg|png)$/.test(ext))){
            alert('Sólo se permiten Imagenes .jpg o .png');
            return false;
          } else {
            $('#loaderAjax').show();
            btn_firma.text('Espere por favor');
            this.disable();
          }
        },
        onComplete: function(file, response){
     
          btn_firma.text('Cambiar Imagen');          
          respuesta = $.parseJSON(response);
          if(respuesta.respuesta == 'done'){
            $('#fotografia').removeAttr('scr');
            $('#fotografia').attr('src','img/' + respuesta.fileName);
            $('#loaderAjax').show();
          }
          else{
            alert(respuesta.mensaje);
          }
            
          $('#loaderAjax').hide();  
          this.enable();  
        }
    });
});

//json para restaurante
//$.getJSON('rest.json', function(data) {
// var key =1;
// $.each(data, function(key, val) {
//     key=key+1;   
//
//    var nombre =val.nombre;
//    var demo2 = nombre.replace(' ','');
//       $('#table-res').append(
//       "<tr id="+ val.id + "><td>" + key + "</td><td><img src='img/"+ val.imagen + "' class='list-img img-polaroid'/></td><td><a href=''> " + val.nombre + 
//            " </a></td><td>" + val.razon +
//             "</td><td>" + val.web +
//             "</td><td>" + val.ruc +
//             "</td><td><a data-id=" + val.id  + " class='modificar btn btn-info' ><i class='icon-edit icon-white'></i></a> " +
//             "<a href='#' name="+ demo2 + " class='eli-resta btn btn-danger' data-id="+ val.id +" ><i class='icon-trash icon-white'></i></a> "+
//             "<a data-id=" + val.id  + " class='listar btn btn-primary' ><i class='icon-tasks icon-white'></i></a> " +
//             "</td></tr>"
//      );
//   
//      });
//
//  $(".eli-resta").on("click",function(){
//  var id = $(this).attr('data-id');
//  var nom =$(this).attr("name");
//  $('#eli-rest').modal('show');
//  $('#verestaurante').attr({'data-id':id});
//  $('#verestaurante').html("Esta seguro de Eliminar el restaurante " + nom + " ?");
// });
//   $('#direccion_loc').keyup(function () {
//      var value = $(this).val();
//      var d = $("#distrito option:selected").text()
//      var p = $("#provincia option:selected").text()
//      var pa = $("#pais option:selected").text()
//      $("#address").val(value + ", " + d  + " , " + p + " , " + pa);
//    }).keyup();
//
//  $('.listar').on("click",function(){
//      $('#local').modal('show');
//   
//});

      
  //});
//json para usuarios - operaciones para usuarios
// $.getJSON('/usuario/index/jsonlistar',{format:"json"}, function(data) {
//  var key =1;
//  var estado; 
  // $.each(data, function(key, val) {
  // 	 key=key+1;   
  //        console.log(data);
         
   //        	if(val.en_estado=="activo"){
  	// estado="Activo";
  	//    $('#table').append(
   //  	 "<tr id="+val.in_id+"><td><input id=" + val.in_id + " type='checkbox' class='check' name='checkname' checked='checked'>"+
   //          "</td><td>" + key + "</td><td> <span id=la"+ val.in_id  + " class='label label-success'>" + estado +
   //          "</span></td><td>" + val.va_nombre + 
   //          "</td><td>" + val.va_apellidos +
   //           "</td><td>" + val.va_email +
   //           "</td><td>" + val.va_nombre_rol +
   //           "</td><td><a data-id=" + val.in_id  + " class='modificar btn btn-info' ><i class='icon-edit icon-white'></i></a> " +
   //           "<a href='#' name="+ val.va_nombre +" class='eli btn btn-danger' data-id="+ val.in_id +" ><i class='icon-trash icon-white'></i></a>"+
   //           "</td></tr>"
   //  	);
   //    }else{
   //   	estado="Inactivo";
   //   	 $('#table').append(
   //  	 "<tr id="+val.in_id+"><td><input id=" + val.in_id + " type='checkbox' class='check' name='checkname' >"+
   //          "</td><td>" + key + "</td><td> <span id=la"+ val.in_id +" class='label label-important' >" + estado +
   //          "</span></td><td>" + val.va_nombre + 
   //          "</td><td>" + val.va_apellidos +
   //           "</td><td>" + val.va_email +
   //           "</td><td>" + val.va_nombre_rol +
   //           "</td><td><a data-id=" + val.in_id  + "  class='modificar btn btn-info' data-toggle='modal'><i class='icon-edit icon-white'></i></a> " +
   //           "<a href='#' name=" + val.va_nombre + " class='eli btn btn-danger' data-id="+ val.in_id +" ><i class='icon-trash icon-white'></i></a>"+
   //           "</td></tr>"
   //  	);
   //   };
   	
  //});

//$('.modificar').on("click",function(){	


  // var id_unica = $(this).attr('data-id');
  // var url= "usuario/index/getusuarioid?id=" + id_unica;
  // $.getJSON(url, function(data) {             
  // var posicion_en_array;
	 // $.each(data, function (i, val) {
  //               if (val) {
  //                   if (val.in_id == id_unica) {
  //                       posicion_en_array = i;
  //                       $('#modificar-form #nombre').val(val.va_nombre);
  //                       $('#modificar-form #apellido').val(val.va_apellidos);
  //                       $('#modificar-form #email').val(val.va_email);
  //                       $('#modificar-form #pass').val(val.va_contrasenia);
  //                       $("#modificar-form #rol option[value=" +  val.Ta_rol_in_id + "]").prop("selected",true);										
                      
  //                   }
  //               }
  //           });
  //  });
  //  $('#mod-usuario').modal('show');

   
//});

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


//});
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
