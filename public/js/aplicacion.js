var puntaje = function(item,p){
                 $(item).raty({
                                 readOnly: true,
                                  score: p,
                                  starOff: '/img/t2.png',
                                  starOn : '/img/t1.png'

                  });
              }
 function initSucursales(){

        var populateMap;
        var mapDiv = document.getElementById('mapCont');
      
        var lat = $("#mapCont").data("lat");
        var lng = $("#mapCont").data("lng");
        var markers = Array();
      
        // Defino Iconos
        var iconGray = new google.maps.MarkerImage(
        '/img/point.png',
        new google.maps.Size(26, 32), 
        new google.maps.Point(0, 0)
    );

        var iconRed = new google.maps.MarkerImage(
        '/img/point.png',
        new google.maps.Size(26, 32), 
        new google.maps.Point(0, 0)
    );
      
      
        var latlng = new google.maps.LatLng(lat, lng);
      
        var options = {
            center   : latlng,
            zoom: 17,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            backgroundColor: '#ffffff',
            disableDefaultUI: true,
            navigationControl: true,
            navigationControlOptions:{
                position : google.maps.ControlPosition.TOP_RIGHT,
                style    : google.maps.NavigationControlStyle.SMALL
            }
        };
      
        var map = new google.maps.Map(mapDiv, options);
        var lugares = Array();

      
      
        function createMarker(lugar, icono){
                  
            place=new google.maps.LatLng(lugar.lat,lugar.lng);

            var marker = new google.maps.Marker({
                icon: icono,
                position: place,
                map: map,
                title: lugar.title,           
                zIndex: 100          
            });
            i = lugar.index;
            (function(i, marker) {
                google.maps.event.addListener(marker, 'click', function() {
                    setActive(marker,i);
                });
            })(i, marker);
            return marker;
        }
         
        function populateMap(lugares){
            var infowindow;
            // Agregar todos los puntos al mapa
            for (var i = 0; i < lugares.length; i++) {
                // agregando un punto
                if(lugares[i].lat) {
                    var marker = createMarker(lugares[i],iconGray);
                    markers[i]=marker;

                }
            }  
        }
      
        $(".list-suc .ul-suc li a").each(function(index){
            var lat = $(this).data("lat");
            var lng = $(this).data("lng");
            var title = $(this).find("h6").text();
            lugares[index] = new Object();
            lugares[index].lat = lat;
            lugares[index].lng = lng;
            lugares[index].title = title;
            lugares[index].index = index;
        });      
      
        populateMap(lugares);
        var principal = {'lat':lat,'lng':lng,'index':markers.length};
      
        markers.push(createMarker(principal,iconRed));
      
        function clearMarkers(){
            $(markers).each(function(){
                this.setIcon(iconGray);
            });
        }
      
        function setActive(marker,index){
            if(index==null){
                clearMarkers();
                marker.setIcon(iconRed);
                map.panTo(marker.getPosition());
            } else {
                if(index==(markers.length - 1)){
                    closeBranch();
                } else {
                    $(".list-suc .ul-suc li a").eq(index).trigger('click');
                }
            }
        }
      
        function closeBranch(){
            $("h4.ubi-map span").html(""); 
            index = markers.length - 1;
            setActive(markers[index],null);
            $('.ubicancion').effect('highlight',{},1000); 
            $(".list-suc .ul-suc li a.activo").removeClass("activo");
        }
      
      
        function eventHandler(event){
            var lat = $(this).data("lat");
            var lng = $(this).data("lng");   
            var index = $(".list-suc .ul-suc li a").index(this);
            var title = $(this).find("h6").text();
            var $target = $(event.target);
            $(".list-suc .ul-suc li a.activo").removeClass("activo");
            if($target.is("span.close-banch")){
                // Mostrar Principal
                closeBranch();
            } else {
                // Mostrar sucursal  
                $(this).addClass("activo");
                $("h4.ubi-map span").html(" : "+title);
                setActive(markers[index],null);
            }
            return false;  
        }
      
        $(".list-suc .ul-suc li a").on('click',eventHandler);

    }
$(document).ready(function(){

  $(".agregar-coment-1").click(function(e){
            e.preventDefault();
            if ($(".agregar-comentario-desc").is(":hidden")) {
                $(".agregar-comentario-desc").show("slow");
            } else {
                $(".agregar-comentario-desc").slideUp();
            }
        });
//comentarios validacion

var validar=function(elemento){
   $(elemento).validate({
        rules: {
            va_nombre: {
                required: true
            },
            va_email: {
                required: true,
                email : true           
            },
            tx_descripcion:{
                required : true                       
            }            
        },
        messages:{
            va_nombre: {
                required:"Por favor ingresar el nombre del plato"
            },
            tx_descripcion:{
                required:"Por favor ingresar una comentario"
            },                
            va_email :{
                required : "Por favor ingresar un email"                
            }
        },
        highlight: function(element) {
            $(element).closest('.control-group').removeClass('success').addClass('error');
        },
        success: function(element) {
            element
            .addClass('valid')
            .closest('.control-group').removeClass('error').addClass('success');
        }
        });
}
       

            $('.cover').mosaic({
                   animation    :    'slide',
                   anchor_y    :    'top',       
                    hover_y        : '300px'
                });           
             $(".subir").hide(); 
                $(window).scroll(function () {
                    if ($(this).scrollTop() > 100) { 
                        $('.subir').fadeIn(); 
                        $('#ver').fadeOut();
                    } else {
                        $('.subir').fadeOut(); 
                         $('#ver').fadeIn();
                    }
                });
                $('.subir a').click(function () {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 500); 
                    return false;
                });
                $('#ver').on("click", function(e) {
                  e.preventDefault();
                  var nextElement = $(this).attr('href');
                  switch(nextElement) {
                    case "#cate-home":
                      offset = $(nextElement).offset().top - 20;
                      break;
                    default:
                      offset = $(nextElement).offset().top;
                      break;
                  }

                  $('html, body').animate({
                    scrollTop: offset
                  }, 'slow');

                });
   //mostra link para buscar mapa             
      $('#bubi #q').keyup(function () {
      if(($(this).val() != "") &&  ($('#bubi #fq').val() != "" )){
      $('#buscarmap').removeClass("disabled").addClass('map');
      $('#buscarmap').attr("href","#");
      $('#buscarmap' ).removeAttr('disabled');
       $('#buscarmap').fadeIn();
      }   }).keyup();
  //buscar mapa       
  $("#buscarmap").on("click",function(){
        var plato=$('#bubi #q').val();
         var dis=$('#bubi #fq').val();
         var url = "http://192.168.1.38:8080/application/index/jsonmapasa?distrito=" + dis+ "&plato=" + plato;
        $("#map").remove();
        $("#subir-home").remove();
        $(".mensaje").remove();
        $(".mensaje2").remove();
        $("#mapa-buscador").append("<div id='map' style='height:800px;'></div>");
        $('#esconder').css("display","none");  
        console.log(url);
           if ( ($('#bubi #q').val() != "") && ($('#bubi #fq').val()!="") ){
             $("#mapa-buscador").fadeIn();
             var prueba=$.getJSON( url, function(data) {
                     console.log(data.response.numFound);
            if (data.response.numFound >= 1) {
               //var mcOptions = { maxZoom: 12};
                     map = new GMaps({
                       el: '#map',
                       zoom: 15,
                        lat: -12.043333,
                        lng: -77.028333
        //               markerClusterer: function(map) {
        //           return new MarkerClusterer(map,mcOptions);
        //         }
           });
        $.each( data.response.docs, function(i, marker) {
                 map.setCenter(marker.latitud,marker.longitud );
                  console.log(marker);
               map.addMarker({
                   lat: marker.latitud,
                   lng: marker.longitud,
                   icon : {
               size : new google.maps.Size(32, 37),
               url : "/img/icomap.png"
             },
                   title: marker.restaurante ,
                   infoWindow: {
                           content: marker.name + '</br> <a href=/platos/index/verplatos?id=' + marker.id +'&q=' + plato +'>ver plato </a>'
                         }

                 });
             });
      }else{
      $("#mapa-buscador").hide();
      $("#subir-home").remove();
      $(".mensaje").remove();
      $(".mensaje2").remove();
      $(".contenido-plato").append('<p class="mensaje">"Lamentamos no haber encontrado lo que estabas buscando pero tenemos muchas mas opciones para ti.</p><p class="mensaje2">También tenemos una opción para que nos escribas si deseas registrar un nuevo plato.</p>');
      $(".contenido-plato").append('<div class="recomendados-platos primer-home" id="subir-home" style="padding-bottom: 90px;"></div>');
       $("#subir-home").append ('<div class="sub" style="margin-top: 10px;margin-bottom: 15px;background: url(/img/img-resultados.png);width: 41%;padding: 0.9em 0px;"><span  style="padding-left: 10px;color:white;font-weight: bold;">Platos Destacados</span></div>');       
       $("#subir-home").append ('<ul id="listajson"></ul>');
       $.getJSON('http://192.168.1.38:8080/application/index/jsondesta',function(data){    
       $.each(data,function(i,val){
          $('#listajson').append('<li><div class="plato_r"><div class="mosaic-block cover2"><div class="mosaic-overlay"><span>' + val.va_nombre + '</span><img src="/imagenes/'+ val.va_imagen+'" class="img-plato"><img src="/img/mas.png" alt="" class="mas"></div><a href="/platos/index/verplatos?id='+val.in_id +'" class="mosaic-backdrop"><div class="details"><h4>'+ val.va_nombre +'</h4><p class="title-details" style="font-weight: bold;">Ingredientes</p><p class="desc-plato" style="font-size:0.9em;">'+ val.tx_descripcion+'</p></div></a></div><div class="foo"><p class="nom_res">'+val.restaurant_nombre+'</p><div class="pt"><p class="com">'+ val.NumeroComentarios+' <i class="icon-comment"></i></p><div class="punt"><div class="puntuaciones c'+val.Ta_puntaje_in_id+'"></div></div></div></div></div></li>');
      });          
    $('.cover2').mosaic({
                   animation    :    'slide',
                   anchor_y    :    'top',        //Vertical anchor position
                    hover_y        : '300px'
                });
      });     
      }
         });     
     prueba.fail(function( jqxhr, textStatus, error ) {
      var err = textStatus + ', ' + error;
      console.log(err); 
      $("#mapa-buscador").hide();
        $(".mensaje").remove();
        $(".mensaje2").remove();
      $(".contenido-plato").append('<p class="mensaje">"Lamentamos no haber encontrado lo que estabas buscando pero tenemos muchas mas opciones para ti.</p><p class="mensaje2">También tenemos una opción para que nos escribas si deseas registrar un nuevo plato.</p>');
    });
 }else{
 $("#mapa-buscador").hide();
alert("debe ingresar el plato");
 }  
   });



});