$(document).ready(function(){

	$('.flexslider').flexslider({ animation:"fade" });

	var pasoactual = 0;
	window.caldo = '';
	window.plato = '';
	
	var miSetOut = setTimeout( entradahome , 2000 );
	
	//----------------animacion hover  redes sociales
	   $("#tes").hover(
		function () {
		$('.envoltura').animate({'margin-left':'-95'},100);
		},
		function () {
		$('.envoltura').animate({'margin-left':'-5'},'fast');
		}
		);
		
		$("#tes2").hover(
		function () {
		$('.envoltura2').animate({'margin-left':'-96'},100);
		},
		function () {
		$('.envoltura2').animate({'margin-left':'-5'},'fast');
		}
		);
	
/*===== Animacion Salida HOME =====*/
	function salidahome(){
		$('#carbonada').animate({'margin-top':'-300'},800,'easeOutCubic');
		$('#pack-pollo').animate({'margin-top':'-350'},800,'easeOutCubic')
		$('#pastel').animate({'margin-top':'-400'},800,'easeOutCubic');
		$('#cactus').animate({'margin-top':'-300'},800,'easeOutCubic');
		$('#condimentos').animate({'margin-left':'1830'},1000,'easeOutCubic');
		$('#pack-carne').animate({'margin-left':'1650'},1000,'easeOutCubic');
		$('#revista-1').animate({'margin-left':'-1000'},1000,'easeOutCubic');
		$('#salero').animate({'margin-left':'-1000'},1000,'easeOutCubic');
		$('#pack-verdura').animate({'margin-left':'1680'},1500,'easeOutCubic');
		$('#sopa').animate({'margin-left':'1850'},1000,'easeOutCubic');
		$('#pack-pescado').animate({'margin-left':'1145'},1500,'easeOutCubic');
		$('#mariscal').animate({'margin-left':'-1320'},1000,'easeOutCubic');
		$('#ribbon-home').fadeOut(300);
			$('#llamado').fadeOut(300);
			$('#btn-continua a').fadeOut(200);
	}


/*===== Animacion Entrada HOME =====*/
function ahora(){ miSetOut; }
function entradahome(){
	$('#carbonada').animate({'margin-top':'-30'},1000,'easeOutCubic');
	$('#pack-pollo').animate({'margin-top':'55'},1500,'easeOutCubic')
	$('#pastel').animate({'margin-top':'-20'},1000,'easeOutCubic');
	$('#cactus').animate({'margin-top':'0'},1500,'easeOutCubic');
	$('#condimentos').animate({'margin-left':'830'},1000,'easeOutCubic');
	$('#pack-carne').animate({'margin-left':'130'},1000,'easeOutCubic');
	$('#revista-1').animate({'margin-left':'-150'},1500,'easeOutCubic');
	$('#salero').animate({'margin-left':'-310'},1500,'easeOutCubic');
	$('#pack-verdura').animate({'margin-left':'725'},1500,'easeOutCubic');
	$('#sopa').delay(200).animate({'margin-left':'915'},1500,'easeOutCubic');
	$('#pack-pescado').animate({'margin-left':'79'},1500,'easeOutCubic');
	$('#mariscal').delay(150).animate({'margin-left':'-251'},1500,'easeOutCubic',function(){
		$('#ribbon-home').fadeIn(800);
		$('#llamado').delay(400).fadeIn(1000);
		$('#btn-continua a').delay(700).fadeIn(500,function(){
			juegue(); 
		});
	});
};
	
	function juegue(){
		$('#btn-continua a').animate({'margin-top':'95'},1200,'easeOutCubic',function(){
			$(this).animate({'margin-top':'80'},1200,'easeOutCubic',function(){
				juegue();
			});
		});
	}






/*===== Array Platos y Caldos =====*/
	var platosCarne = new Array( 'carbonada', 'estofado', 'pulpadecerdo' );
	var platosPollo = new Array( 'ajidegallina', 'lasanadepollo', 'piedepollo' );
	var platosVerdura = new Array( 'wok', 'guisoverdura', 'sopadezapallo', 'lomosaltado');
	var platosPescado = new Array( 'chupedeloco', 'bisque', 'caldecongrio' );
	var platosArray = new Array(platosCarne, platosPollo, platosVerdura, platosPescado);
	var platosSlug = new Array('carne', 'pollo', 'verdura', 'pescado');

	


/*===== Botones Platos por Caldo =====*/
	var i = 0;
	while (platosSlug[i]) {
		var ii = 0;
		while (platosArray[i][ii]) {
			//$('#plato-' + platosSlug[i] + '-' + platosArray[i][ii]).show();
			//$('#plato-' + platosSlug[i] + '-' + platosArray[i][ii]).css({opacity:0});
			clickPlato(platosArray[i][ii], platosSlug[i], platosArray[i]);
			ii++;
		}
		i++;
	}
	
	function clickPlato(plato, caldo, arreglo) {
		$('#' + plato + '-btn').click(function(){
			window.plato = plato;
			$('#roseton-tipo-' + caldo).fadeOut(1000);
			$('#plato-' + caldo + '-' + plato).animate({opacity:1},1000,function(){
				$('#btn-continua2').fadeIn(500);

			});
			$('#plato-' + caldo + '-' + plato).css('display','block');
			var pp = 0;
			while (arreglo[pp]) {
				if (arreglo[pp] != plato) {
					$('#plato-' + caldo + '-' + arreglo[pp]).animate({opacity:0},1000);
					$('#plato-' + caldo + '-' + arreglo[pp]).css('display','none');
				}
				pp++;
			}
		});
	}
	
/*===== Animacion Back Paso 2 =====*/
	$('#btn-paso2').click(function(){
		if (pasoactual > 2){
			pasoactual = 2;
			$('#paso-tres').animate({'margin-top':'-975'},1700,'easeOutCubic');  
			$('#btn-paso3').removeClass('bo');
			$('#btn-paso2').addClass('bo');
			animatres();
			if (window.caldo == 'carne') {
				animocarne(); 
			} else if (window.caldo == 'pollo') {
				animopollo();
			} else if(window.caldo == 'verdura'){
				animoverdura();
			} else if(window.caldo == 'pescado'){
				animopescado();
			}
		} else {
			alertas('Necesitas completar el paso previo antes de continuar');
		}
	});


/*===== Animacion Back Paso 3 =====*/
	$('#btn-paso3').click(function(){
		alertas('Necesitas completar el paso previo antes de continuar');
	});


/*===== Animacion Paso 3 =====*/
	$('#btn-continua2').click(function(){
		pasoactual = 3;
		$('#btn-paso2').removeClass('bo');
		$('#btn-paso3').addClass('bo');
		animatresback();
		animocarneback();
		animopolloback();
		animoverduraback();
		animopescadoback();
		iniciar();
		$('#paso-tres').animate({'margin-top':'-1950'},1700,'easeOutCubic');
	});
});