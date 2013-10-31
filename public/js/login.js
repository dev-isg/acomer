$(document).ready(function(){
  $(".recContrasenia").click(function(e){
    e.preventDefault();
    $('#modalSession').modal('hide');
    $('#modalRecuperar').modal('show');
  });
  $(".recRegistro").click(function(e){
    e.preventDefault();
    $('#modalSession').modal('hide');
    $('#modalRegister').modal('show');
  })
  $(".go-back").click(function(e){
    e.preventDefault();
    $('#modalRecuperar').modal('hide');
    $('#modalSession').modal('show');
  });

  $("#RecuperarCo").validate({
    rules: {
      va_contrasena: {
        required: true,
        minlength: 6
      },
      verificar_contrasena: {
        required: true,
        equalTo: '#RecuperarCo #inputPassword'
      }
    },
    messages: {
      va_contrasena: {
        required: 'Por favor ingrese una contraseña',
        minlength: 'Ingrese 6 caracteres como mínimo'
      },
      verificar_contrasena:{
        required: 'Por favor ingrese una contraseña',
        equalTo: 'Por favor ingrese la misma contraseña',
        minlength: 'Ingrese 6 caracteres como mínimo'
      }
    },
    submitHandler: function(){
      var urlLogin = urlJson;
      var contraOne = $("#RecuperarCo #inputPassword").val();
      var contraTwo = $("#RecuperarCo #verificar_contrasena").val();

      $(".btnUpPass").addClass("disabled");
      $('.btnUpPass').attr("disabled", "disabled");
      $(".btnUpPass").val("Enviando...");

      var dataToquen = $("#modalRecuperarContr").attr('data-token');
      $.ajax({
        type:"POST",
        url: urlLogin+'/cambio-contrasena', 
        data: {va_contrasena: contraOne, verificar_contrasena: contraTwo, value: dataToquen},          
        success: function(data){          
          $("#RecuperarCo #inputPassword").val("");
          $("#RecuperarCo #verificar_contrasena").val("");
          $(".btnUpPass").removeClass("disabled");
          $(".btnUpPass").removeAttr("disabled");
          $(".btnUpPass").val("Enviar");

          $("#modalRecuperarContr").modal('hide');
          $(".alert-errorM").remove();
          $('#modalRecuperarObsoleto').modal('show');
          var htmlAlert = '<div class="alert alert-success alert-font alertEmailF" style="margin-bottom:10px;">'+data.menssage+'</div>';
          $(".recuperarConO").prepend(htmlAlert);
        }
      });

    }
  });

  $("#CambiarPass").validate({
    rules: {
      va_email: {
        required: true,
        email: true
      }
    },
    errorElement: "em",
    messages: {
      va_email: {
        required: "Ingrese correo electronico", 
        email: "Ingrese correo válido"
      }
    },
    submitHandler: function(){
      var urlLogin = urlJson;
      var emailRe = $("#CambiarPass #va_email").val();
      //btnEmailPass
      $(".btnEmailPass").addClass("disabled");
      $('.btnEmailPass').attr("disabled", "disabled");
      $(".btnEmailPass").val("Enviando...");
      $.ajax({
        type:"POST",
        url: urlLogin+'/cambio', 
        data: {va_email: emailRe},          
        success: function(data){
          $(".alertEmailF").remove();
          var htmlAlert = '<div class="alert alert-success alert-font alertEmailF" style="margin-bottom:10px;">'+data.menssage+'</div>';
          $(".modalAlertEmail").prepend(htmlAlert);
          $("#CambiarPass #va_email").val("");
          $(".btnEmailPass").removeClass("disabled");
          $(".btnEmailPass").removeAttr("disabled");
          $(".btnEmailPass").val("Enviar");
        }
      });
    }
  });
//auth//authenticate
	$("#LoginUser").validate({
    rules: {
        va_email: {
          required: true,
          email: true
        },
        va_contrasena: {
          required: true,          
        }
      },
      errorElement: "em",
      messages: {
        va_email: {
          required: "Ingrese correo electronico", 
          email: "Ingrese correo válido"
        }, 
        va_contrasena: {
          required: "Ingrese una contraseña"
        }
      },
      submitHandler: function() {
        var urlLogin = urlJson;
        var dato1 = $("#LoginUser #va_email").val();
        var dato2 = $("#LoginUser #inputPassword").val();
        $(".btnLoginIn").addClass("disabled");
        $('.btnLoginIn').attr("disabled", "disabled");
        $(".btnLoginIn").val("Enviando...");

        $.ajax({
          type:"POST",
          url: urlLogin+'/validar', 
          data: {va_email: dato1, va_contrasena: dato2},          
          success: function(data){
            $(".alert-se").remove();
            if(data.success === false){
              var htmlAlert = '<div class="alert alert-error alert-font">'+data.menssage+'</div>';
              $(".modalAlert").prepend(htmlAlert);
              $(".btnLoginIn").removeClass("disabled");
              $(".btnLoginIn").removeAttr("disabled");
              $(".btnLoginIn").val("Enviar");
            }else{
              $("#LoginUser #va_email").val("");
              $("#LoginUser #va_contrasena").val("");
              location.reload();
            }
          }
        });
      }
    });

    $("#RegistroUser").validate({
      rules: {
        va_nombre_cliente: {
          required: true
        },
        va_email: {
          required: true,
          email: true
        },
        va_contrasena: {
          required: true,
          minlength: 6
        },
        verificar_contrasena:{
          required:true,
          equalTo: '#RegistroUser #va_contrasena',
          minlength: 6
        }
      },
      errorElement: "em",
      messages: {
        va_nombre_cliente: "Por favor ingrese su nombre completo", 
        va_email: {
          required: "Por favor ingrese su correo electrónico",
          email: "Por favor ingrese un correo válido"
        },
        va_contrasena: {
          required: 'Por favor ingrese una contraseña',
          minlength: 'Ingrese 6 caracteres como mínimo'
        },
        verificar_contrasena: {
          required: 'Por favor ingrese una contraseña',
          equalTo: "Por favor ingrese la misma contraseña",
          minlength: 'Ingrese 6 caracteres como mínimo'
        }
      },
      submitHandler: function() {
        var urlLogin = urlJson;
        var datoForm = $("#RegistroUser").serialize();
        $(".btnRegisU").addClass("disabled");
        $('.btnRegisU').attr("disabled", "disabled");
        $(".btnRegisU").val("Enviando...");

        var dataToquen = $("#modalRecuperarContr").attr('data-token');
        $.ajax({
          type:"POST",
          url: urlLogin+'/registrarse', 
          data: datoForm,          
          success: function(data){
            if(data.success === true){
              $(".btnRegisU").removeClass("disabled");
              $(".btnRegisU").removeAttr("disabled");
              $(".btnRegisU").val("Registrarse");
              $("#RegistroUser")[0].reset();
              $("#modalRegister").modal('hide');
              var htmlAlert = '<p>'+data.menssage['bienvenido']+'</p><div class="alert alert-success alert-font">'+data.menssage['saludo']+'</div>';
              $(".recuperarConR").prepend(htmlAlert);
              $('#modalRecuperarRegistro').modal('show');
            }else{
              $(".btnRegisU").removeClass("disabled");
              $(".btnRegisU").removeAttr("disabled");
              $(".btnRegisU").val("Registrarse");

              var htmlAlert = '<div class="alert alert-error alert-font" style="padding:8px;">'+data.menssage+'</div>';
              $("#modalRegister .decs-f").prepend(htmlAlert);

            }        

          }
        });
      }
    });

});