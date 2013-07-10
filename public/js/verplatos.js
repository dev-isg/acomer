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
validar('#comentarios');            

});