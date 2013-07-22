function replaceAll(c, b, a) {
    while (c.toString().indexOf(b) != -1) {
        c = c.toString().replace(b, a);
    }
    return c;
}
var puntaje = function(a, b) {
    $(a).raty({readOnly: true,score: b,starOff: "/img/t2.png",starOn: "/img/t1.png"});
};
function regresar(){
  $("#mapa-buscador").hide();
   $("#esconder").css("display", "block");
    $("#esconder2").css("display", "block");
}
function initSucursales() {
    var m;
    var p = document.getElementById("mapCont");
    var l = $("#mapCont").data("lat");
    var n = $("#mapCont").data("lng");
    var g = Array();
    var j = new google.maps.MarkerImage("/img/point.png", new google.maps.Size(26, 32), new google.maps.Point(0, 0));
    var e = new google.maps.MarkerImage("/img/point.png", new google.maps.Size(26, 32), new google.maps.Point(0, 0));
    var c = new google.maps.LatLng(l, n);
    var r = {center: c,zoom: 17,mapTypeId: google.maps.MapTypeId.ROADMAP,backgroundColor: "#ffffff",disableDefaultUI: true,navigationControl: true,navigationControlOptions: {position: google.maps.ControlPosition.TOP_RIGHT,style: google.maps.NavigationControlStyle.SMALL}};
    var b = new google.maps.Map(p, r);
    var q = Array();
    function o(u, t) {
        place = new google.maps.LatLng(u.lat, u.lng);
        var s = new google.maps.Marker({icon: t,position: place,map: b,title: u.title,zIndex: 100});
        i = u.index;
        (function(w, v) {
            google.maps.event.addListener(v, "click", function() {
                a(v, w);
            });
        })(i, s);
        return s;
    }
    function m(u) {
        var v;
        for (var t = 0; t < u.length; t++) {
            if (u[t].lat) {
                var s = o(u[t], j);
                g[t] = s;
            }
        }
    }
    $(".list-suc .ul-suc li a").each(function(t) {
        var u = $(this).data("lat");
        var s = $(this).data("lng");
        var v = $(this).find("h6").text();
        q[t] = new Object();
        q[t].lat = u;
        q[t].lng = s;
        q[t].title = v;
        q[t].index = t;
    });
    m(q);
    var k = {lat: l,lng: n,index: g.length};
    g.push(o(k, e));
    function f() {
        $(g).each(function() {
            this.setIcon(j);
        });
    }
    function a(s, t) {
        if (t == null) {
            f();
            s.setIcon(e);
            b.panTo(s.getPosition());
        } else {
            if (t == (g.length - 1)) {
                h();
            } else {
                $(".list-suc .ul-suc li a").eq(t).trigger("click");
            }
        }
    }
    function h() {
        $("h4.ubi-map span").html("");
        index = g.length - 1;
        a(g[index], null);
        $(".ubicancion").effect("highlight", {}, 1000);
        $(".list-suc .ul-suc li a.activo").removeClass("activo");
    }
    function d(v) {
        var w = $(this).data("lat");
        var u = $(this).data("lng");
        var t = $(".list-suc .ul-suc li a").index(this);
        var x = $(this).find(".phone span").text();
        var s = $(v.target);
        $(".list-suc .ul-suc li a.activo").removeClass("activo");
        if (s.is("span.close-banch")) {
            h();
        } else {
            $(this).addClass("activo");
            $("h4.ubi-map span").html(" : " + "<span class='co_l'>"+x+"</span>");
            a(g[t], null);
        }
        return false;
    }
    $(".list-suc .ul-suc li a").on("click", d);
}
$(document).ready(function() {
    $('input, textarea').placeholder();
    if ($.browser.mozilla) { $(".verlistado").css("padding-top","10px");};
    $(".agregar-coment-1").click(function(a) {
        a.preventDefault();
        if ($(".agregar-comentario-desc").is(":hidden")) {
            $(".agregar-comentario-desc").show("slow");
        } else {
            $(".agregar-comentario-desc").slideUp();
        }
    });
    $(".cover").mosaic({animation: "slide",anchor_y: "top",hover_y: "300px"});
    $(".subir").hide();
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $(".subir").fadeIn();
            $("#ver").fadeOut();
        } else {
            $(".subir").fadeOut();
            $("#ver").fadeIn();
        }
    });
    $(".subir a").click(function() {
        $("body,html").animate({scrollTop: 0}, 500);
        return false;
    });
    $("#ver").on("click", function(b) {
        b.preventDefault();
        var a = $(this).attr("href");
        switch (a) {
            case "#cate-home":
                offset = $(a).offset().top - 20;
                break;
            default:
                offset = $(a).offset().top;
                break;
        }
        $("html, body").animate({scrollTop: offset}, "slow");
    });
    $("#bubi #q").keyup(function() {
        if (($(this).val() != "") && ($("#bubi #fq").val() != "seleccione distrito")) {
            $("#buscarmap").removeClass("disabled").addClass("map");
            $("#buscarmap").attr("href", "#");
            $("#buscarmap").removeAttr("disabled");
            $("#buscarmap").fadeIn();
        }
        if ($(this).val() == "") {
            $("#buscarmap").hide();
        }
    }).keyup();
    $("#buscarmap").on("click", function() {
        var c = $("#bubi #q").val();
        var a = $("#bubi #fq").val();
        var b = urlJson + "/jsonmapasa?distrito=" + a + "&q=" + c;
        $("#map").remove();
        $("#subir-home").remove();
        $(".mensaje").remove();
        $(".mensaje2").remove();
        $("#mapa-buscador").append("<div id='map' style='height:800px;'></div>");
        $("#esconder").css("display", "none");
        $("#esconder2").css("display", "none");
        console.log(b);
        if (($("#bubi #q").val() != "") && ($("#bubi #fq").val() != "seleccione")) {
            $("#mapa-buscador").fadeIn();
            $("#search #q").attr("value",c);
            var d = $.getJSON(b, function(e) {
                console.log(e.response.numFound);
                if (e.response.numFound >= 1) {
                    map = new GMaps({el: "#map",zoom: 12,lat: -12.043333,lng: -77.028333});
                    $.each(e.response.docs, function(g, f) {
                        map.setCenter(f.latitud, f.longitud);
                        console.log(f);
                        var por = f.tx_descripcion;
                        var sms = por.substring(0, 50);
                        var anom = replaceAll(f.name, " ", "-");
                        var adis = replaceAll(f.distrito, " ", "-");
                        map.addMarker({lat: f.latitud,lng: f.longitud,icon: {size: new google.maps.Size(32, 37),url: "/img/icomap.png"},title: f.restaurante,infoWindow: {content: "<img src='/plato/general/" + f.va_imagen + "' class='img-mapa'>" + "<p class='restaurante-map'>" + "<a href=/plato/" + anom + "-" + f.id + ">" + f.restaurante + "</a></p>" + "<p class='plato-map'>" + f.name + "</p>" + "<p class='txt-map'>" + sms + "...</p>" + "<a class='a-map' href=/plato/" + anom + "-" + f.id + "> ver mas </a>"}});
                    });
                } else {
                    $("#mapa-buscador").hide();
                    $(".descrip-product").remove();
                    $("#subir-home").remove();
                    $(".content-left").css("height", "100px");
                    $(".mensaje").remove();
                    $(".mensaje2").remove();
                    $(".contenido-plato").css("background", "url(/img/back-detalle.png) repeat");
                    $(".contenido-plato").append('<p class="mensaje">"Lamentamos no haber encontrado lo que estabas buscando pero tenemos muchas mas opciones para ti.</p><p class="mensaje2">También tenemos una opción para que nos escribas si deseas registrar un nuevo plato.</p>');
                    $(".contenido-plato").append('<div class="recomendados-platos primer-home" id="subir-home" style="padding-bottom: 90px;"></div>');
                    $("#subir-home").append('<div class="sub" style="margin-top: 10px;margin-bottom: 15px;background: url(/img/img-resultados.png);width: 41%;padding: 0.9em 0px;"><span  style="padding-left: 10px;color:white;font-weight: bold;">Platos Destacados</span></div>');
                    $("#subir-home").append('<ul id="listajson"></ul>');
                    $.getJSON(urlJson + "/jsondesta", function(f) {
                        $.each(f, function(g, h) {
                            var nplato = replaceAll(h.va_nombre, " ", "-");
                            $("#listajson").append('<li><div class="plato_r"><div class="mosaic-block cover2"><div class="mosaic-overlay"><span>' + h.va_nombre + '</span><img src="/plato/destacado/' + h.va_imagen + '" class="img-plato"><img src="/img/mas.png" alt="" class="mas"></div><a href="/plato/' + nplato + "-" + h.in_id + '" class="mosaic-backdrop"><div class="details"><h4>' + h.va_nombre + '</h4><p class="title-details" style="font-weight: bold;">Descripción</p><p class="desc-plato" style="font-size:0.9em;">' + h.tx_descripcion + '</p></div></a></div><div class="foo"><p class="nom_res">' + h.restaurant_nombre + '</p><div class="pt"><p class="com">' + h.NumeroComentarios + ' <i class="icon-comment"></i></p><div class="punt"><div class="puntuaciones c' + h.Ta_puntaje_in_id + '"></div></div></div></div></div></li>');
                        });
                        $(".cover2").mosaic({animation: "slide",anchor_y: "top",hover_y: "300px"});
                    });
                }
            });
            d.fail(function(g, h, e) {
                var f = h + ", " + e;
                console.log(f);
                $("#mapa-buscador").hide();
                $(".descrip-product").remove();
                $("#subir-home").remove();
                $(".content-left").css("height", "100px");
                $(".mensaje").remove();
                $(".mensaje2").remove();
                $(".contenido-plato").css("background", "url(/img/back-detalle.png) repeat");
                $(".contenido-plato").append('<p class="mensaje">"Lamentamos no haber encontrado lo que estabas buscando pero tenemos muchas mas opciones para ti.</p><p class="mensaje2">También tenemos una opción para que nos escribas si deseas registrar un nuevo plato.</p>');
                $(".contenido-plato").append('<div class="recomendados-platos primer-home" id="subir-home" style="padding-bottom: 90px;"></div>');
                $("#subir-home").append('<div class="sub" style="margin-top: 10px;margin-bottom: 15px;background: url(/img/img-resultados.png);width: 41%;padding: 0.9em 0px;"><span  style="padding-left: 10px;color:white;font-weight: bold;">Platos Destacados</span></div>');
                $("#subir-home").append('<ul id="listajson"></ul>');
                $.getJSON(urlJson+"/jsondesta", function(f) {
                    $.each(f, function(g, h) {
                        var nplato = replaceAll(h.va_nombre, " ", "-");
                        $("#listajson").append('<li><div class="plato_r"><div class="mosaic-block cover2"><div class="mosaic-overlay"><span>' + h.va_nombre + '</span><img src="/plato/destacado/' + h.va_imagen + '" class="img-plato"><img src="/img/mas.png" alt="" class="mas"></div><a href="/plato/' + nplato + "-" + h.in_id + '" class="mosaic-backdrop"><div class="details"><h4>' + h.va_nombre + '</h4><p class="title-details" style="font-weight: bold;">Descripción</p><p class="desc-plato" style="font-size:0.9em;">' + h.tx_descripcion + '</p></div></a></div><div class="foo"><p class="nom_res">' + h.restaurant_nombre + '</p><div class="pt"><p class="com">' + h.NumeroComentarios + ' <i class="icon-comment"></i></p><div class="punt"><div class="puntuaciones c' + h.Ta_puntaje_in_id + '"></div></div></div></div></div></li>');
                    });
                    $(".cover2").mosaic({animation: "slide",anchor_y: "top",hover_y: "300px"});
                });
            });
        } else {
            $("#mapa-buscador").hide();
            alert("debe ingresar el plato");
        }
    });
});
$("#bubi #fq").change(function(a) {
    if (($("#bubi #q").val() != "") && ($("#bubi #fq").val() != "seleccione distrito")) {
        $("#buscarmap").removeClass("disabled").addClass("map");
        $("#buscarmap").attr("href", "#");
        $("#buscarmap").removeAttr("disabled");
        $("#buscarmap").fadeIn();
    }
    if ($(this).val() == "seleccione distrito") {
        $("#buscarmap").hide();
    }
});
