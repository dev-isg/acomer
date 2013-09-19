 $(document).ready(function(){
    initSucursales();
    $('#star').raty({
        target    : '#Ta_puntaje_in_id',
        targetType: 'number',
        starOff: '/img/t2.png',
        targetKeep:true,
        starOn : '/img/t1.png'
    });
    $("#side").height($("#main").height()); 
        //comentarios validacion

   $('#comentarios').validate({
        rules: {
            va_nombre: {
                required: true,
                minlength : 4,
                maxlength : 40
            },
            va_email: {
                required: true,
                email : true           
            },
            tx_descripcion:{
                required : true,
                 minlength : 15                      
            },
            Ta_puntaje_in_id:{
                required: true
            }       
        },
        messages:{
            va_nombre: {
                required:"Por favor ingresar el nombre del plato",
                minlength : "Por favor ingresar minimo 4 caracteres",
                maxlength : "Por favor ingresar un maximo de 40 cacteres"
            },
            tx_descripcion:{
                required:"Por favor ingresar una comentario",
                 minlength : "Por favor ingresar minimo 15 caracteres"
            },                
            va_email :{
                required : "Por favor ingresar un email"                
            },
            Ta_puntaje_in_id:{
                required:"De su puntuacion"
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

    $('.btn-comentarioDev').click(function(e){
        e.preventDefault();
        if($("#Ta_puntaje_in_id").val()==""){
            $('.error-votos').show();
            if($("#comentarios").valid()){
                
            }else{
                
            }
        }else{
            $('.error-votos').hide();
            if($("#comentarios").valid()){
                $("#comentarios").get(0).submit();
            }else{
                $("#comentarios").submit();
            }
        }
    });

    
    /*$('#comentarios').submit(function() {
        if($("#Ta_puntaje_in_id").val()==""){
            $('.error-votos').show();
        }else{

        }
    });*/
});