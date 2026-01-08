function Verifica(){
var faz = 0;

if($("#nomeRemetente").val() == ""){
$("#nomeRemetente").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#ramoatividade").val() == ""){
$("#ramoatividade").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#jucesp").val() == ""){
$("#jucesp").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#datajucesp").val() == ""){
$("#datajucesp").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#cnpj").val() == ""){
$("#cnpj").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
/*if($("#inscricaoestadual").val() == ""){
$("#inscricaoestadual").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}*/
if($("#abertura").val() == ""){
	if($("#encerramento").val() == ""){
		$("#encerramento").css({"box-shadow":"-1px 0px 4px red"});  
		$("#abertura").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;
	}else{
		if($("#dataencerramento").val() == ""){
		$("#dataencerramento").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;	
	}else{
		$("#dataabertura").removeAttr("style");	
		$("#dataencerramento").removeAttr("style");	
		$("#encerramento").removeAttr("style");	
		$("#abertura").removeAttr("style");	
	}
	}	
}else{
	if($("#dataabertura").val() == ""){
		$("#dataabertura").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;	
	}else{
		$("#dataabertura").removeAttr("style");	
		$("#dataencerramento").removeAttr("style");	
		$("#encerramento").removeAttr("style");	
		$("#abertura").removeAttr("style");	
	}
}

//recebe o valor do radiobutton adocao
var ado = $("#adocao").prop("checked");
if(ado){
	if($("#dataadocao").val() == ""){
		$("#dataadocao").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;
	}
}else{
$("#dataadocao").removeAttr("style");	
}
//recebe o valor do radiobutton exclusao
var exclu = $("#exclusao").prop("checked");
if(exclu){
	if($("#dataexclusao").val() == ""){
		$("#dataexclusao").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;
	}
}else{
$("#dataexclusao").removeAttr("style");		
}
//recebe o valor do radiobutton exclusao
var publi = $("#publicidade").prop("checked");
if(publi){
	if($("#tipopublicidade").val() == ""){
		$("#tipopublicidade").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;
	}
	if($("#metrosquad").val() == ""){
		$("#metrosquad").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;
	}
}else{
$("#tipopublicidade").removeAttr("style");		
$("#metrosquad").removeAttr("style");		
}
//recebe o valor do radiobutton exclusao
var ocup = $("#ocupacao").prop("checked");
if(ocup){
	if($("#metrosquadocu").val() == ""){
		$("#metrosquadocu").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;
	}
	if($("#autorizacaoocu").val() == ""){
		$("#autorizacaoocu").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;
	}
	if($("#localocu").val() == ""){
		$("#localocu").css({"box-shadow":"-1px 0px 4px red"});  
		faz = 1;
	}
}else{
$("#metrosquadocu").removeAttr("style");		
$("#autorizacaoocu").removeAttr("style");		
$("#localocu").removeAttr("style");
}

if($("#cep").val() == ""){
$("#cep").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#endereco").val() == ""){
$("#endereco").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#numero").val() == ""){
$("#numero").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#bairro").val() == ""){
$("#bairro").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#cidade").val() == ""){
$("#cidade").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#estado").val() == ""){
$("#estado").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#nomesignatario").val() == ""){
$("#nomesignatario").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#endereco_signatario").val() == ""){
$("#endereco_signatario").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#numero_signatario").val() == ""){
$("#numero_signatario").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#rg_signatario").val() == ""){
$("#rg_signatario").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}
if($("#cpf_signatario").val() == ""){
$("#cpf_signatario").css({"box-shadow":"-1px 0px 4px red"});  
faz = 1;
}

if (faz == 1 ){
  alert("Faltam dados a serem preenchidos");
}else{

  $("#target").submit();
}


}
function ImprimiF () {
	var cod = $("#cod_aut").val();
	window.open("../includes/gerapdf_frente.php?cod=" + cod);
	alert("Após a impressao, colocar a mesma folha na impressora para ser impresso o verso do Alvara");
	document.getElementById('btnimprimiv').disabled = false;
}
function ImprimiV () {
	var cod = $("#cod_aut").val();
	window.open("../includes/gerapdf_verso.php?cod=" + cod);
	//window.location.assign("index.php");
}

function VerificaCPF(cpf){

  $.ajax({

    type: "POST",
    data: {cpf:cpf},
    url: "../includes/validaCPF.php",
    dataType: "html",
    success: function(result){
      if (result == "CPF Invalido"){

        alert(result);
        $("#" + id).val("");
      }

    }


  })
}

function VerificaCNPJ(cnpj){
  $.ajax({

    type: "POST",
    data: {cnpj:cnpj},
    url: "../includes/validaCNPJ.php",
    dataType: "html",
    success: function(result){
      if (result == "CNPJ Invalido"){

        alert(result);
        $("#" + id).val("");
      }

    }


  })
}


 function BuscaCep(cep){
  if (cep == ""){
    //alert("CEP não preenchido");
  }
    else{
    $.ajax({

      type: "POST",
      data: { cep:cep },
      url: "/acaoeinclusaosocial/includes/cep.php",
      dataType: "json",
      beforeSend: function(){
        $("#mapa").fadeOut("slow");
        $("#endereco").val("carregando...");
        $("#bairro").val("carregando...");
        $("#cidade").val("carregando...");
        
        $("#loading").html('<div id="progressbar"><div class="progress-label">Loading...</div></div>');
        $("#endereco").prop('disabled', true);
        $("#bairro").prop('disabled', true);
        $("#cidade").prop('disabled', true);
        },
      success: function(resultados){

         $("#progressbar" ).fadeOut("slow");
         var posiMapa = $("#mapa");
         var offset = posiMapa.offset();
         $("#mapa").fadeIn("slow");

        if(resultados == null){
          $("#show").html("");
              alert("CEP não encontrado");
          }else{

            //alert(resultados.logradouro);
            $("#endereco").val(resultados.logradouro);
            $("#bairro").val(resultados.bairro);
            $("#cidade").val(resultados.cidade);
            // $("#estado").val(resultados.uf);
            // $('#estado').selectpicker('refresh');

            $("#endereco").prop('disabled', false);
            $("#bairro").prop('disabled', false);
            $("#cidade").prop('disabled', false);

              $("#div_cep").attr("class", "form-line focused");
              $("#div_endereco").attr("class", "form-line focused");
              $("#div_bairro").attr("class", "form-line focused");

            var numero_end = $("#numero").val();
            if(numero_end == "" || (!$.isNumeric(numero_end)))
              var endereco = resultados.logradouro + ", " + resultados.cidade + ", " + resultados.uf ;
            else
              var endereco = resultados.logradouro + ", " + numero_end + ", " + resultados.cidade + ", " + resultados.uf ;

            //marca o endereco no mapa
            BuscaLocation(endereco);
          }
      },
      error: function( req, status, err ) {
        console.log(req);
      }
    });

  }
}

function PrepareAddress(rua, cidade, numero){
  if(rua == "" || cidade == "" ){
    //alert("");
  }else{
    var endereco = rua + ", " + cidade;
    if(numero != "")
      endereco += ", " + numero;

    BuscaLocation(endereco);
  }
}


function BuscaLocation(endereco){
  if(endereco == ""){
    //alert("Endereço não preenchido");
  }else{
    var geocoder = new google.maps.Geocoder();  
    geocoder.geocode({ 'address': endereco + ', Brasil', 'region': 'BR' }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                var latitude = results[0].geometry.location.lat();
                var longitude = results[0].geometry.location.lng();

                //$('#endereco').val(results[0].formatted_address);
                $('#txtLatitude').val(latitude);
                $('#txtLongitude').val(longitude);


                var location = new google.maps.LatLng(latitude, longitude);
                marker.setPosition(location);
                map.setCenter(location);
                map.setZoom(18);
                $("#mapa").show();
               // $("#mapa").fadeIn("slow");
            }
        }
    });
  }
  
}

function AdicionaSocio () {
	$("#accordion").fadeOut("slow");	
	var nome = $("#nome_socio").val();
	var rg = $("#rg_socio").val();
	var ende = $("#ende_socio").val();
	var cpf = $("#cpf_socio").val();
	var nro = $("#nro_socio").val();
  if( nome == "" || rg == "" || ende == "" || cpf == "" || nro == ""){

    alert("Verifique se todos os campos referentes ao socio foram preenchidos");
  }
    else{
	var cod = $("#cod_aut").val();
  $.ajax({
    type: "POST",
    data: { nome:nome, rg:rg, ende:ende, cpf:cpf, nro:nro, cod:cod },
    url: "../includes/add_socio.php",
    dataType: "json",
    success: function(resultados){
       alert(resultados[0].nome);
		$("#nome_socio").val("");
		$("#rg_socio").val("");
		$("#ende_socio").val("");
		$("#cpf_socio").val("");
		$("#nro_socio").val("");
       	MontaSocio();
    }


  });
}
}

function EditaSocio (id) {
  $("#accordion").fadeOut("slow");  
  var nome = $("#nome_socio_" + id).val();
  var rg = $("#rg_socio_" + id).val();
  var ende = $("#ende_socio_" + id).val();
  var cpf = $("#cpf_socio_" + id).val();
  var nro = $("#nro_socio_" + id).val();
  if( nome == "" || rg == "" || ende == "" || cpf == "" || nro == ""){

    alert("Verifique se todos os campos referentes ao socio foram preenchidos");
  }
    else{
  
  $.ajax({
    type: "POST",
    data: { nome:nome, rg:rg, ende:ende, cpf:cpf, nro:nro },
    url: "../includes/edita_socio.php",
    dataType: "json",
    success: function(resultados){
       alert(resultados[0].nome);
    $("#nome_socio_" + id).val("");
    $("#rg_socio_" + id).val("");
    $("#ende_socio_" + id).val("");
    $("#cpf_socio_" + id).val("");
    $("#nro_socio_" + id).val("");
        MontaSocio();
    }


  });
}
}

function MontaSocio () {

	var cod = $("#cod_aut").val();

	$.ajax({
    type: "POST",
    data: { cod:cod },
    url: "../includes/busca_socio.php",
    dataType: "html",
    success: function(resultados){
    	if (resultados == false){
    		$("#socios").fadeOut("slow");
    	}else{
    		$("#socios").html("<label>S&oacute;cios j&aacute; acrescentados</label>");
    		$("#accordion").html(resultados);
    		//atualiza o accordion para exibir os novos socios
    		$("#accordion").accordion("refresh");
    		$("#accordion").fadeIn("slow");
    	}
    }


  });

	
}
function RemoveSocio (id) {
$("#accordion").fadeOut("slow");	
  $.ajax({
    type: "POST",
    data: { id:id },
    url: "../includes/remove_socio.php",
    dataType: "html",
    success: function(resultados){
       alert(resultados);
       MontaSocio();
    }


  });
}

function EditaSocio (id) {

$("#accordion").fadeOut("slow");  
  $.ajax({
    type: "POST",
    data: { id:id },
    url: "../includes/edita_socio.php",
    dataType: "html",
    success: function(resultados){
       alert(resultados);
       MontaSocio();
    }


  });
}



function ContaChar(char_digitados, id, id_span, tamanho){

var textarea = $("#" + id);
var tamanho_digitado = char_digitados.length;
if (tamanho_digitado == 0){
$("#" + id_span).fadeOut("slow");  

}else{
  $("#" + id_span).fadeIn("slow");  
$("#" + id_span).html("Caracteres Restantes: " + (tamanho - tamanho_digitado));
}
if (tamanho_digitado >= tamanho){
  //retira os caracteres a mais
  textarea[0].value = textarea[0].value.substr(0, tamanho);
  $("#" + id_span).html("Limite de caracteres atingido");
}
}

function HideContaChar(id){
$("#" + id).fadeOut("slow");

}


function Step(nro, t){
var passoant = 0;
var passopro = 0;
if (t == "a"){
passoant = nro + 1;
passopro = nro + 2;
//$( "#tabs" ).tabs("disable", nro);
}
else{
  passoant = nro - 1;
  passopro = nro + 1;
  
//$( "#tabs" ).tabs("disable", nro - 2 ); 
}

//$("#l" + passoant).attr('class', 'ui-state-default ui-corner-top ui-state-focus');
//$("#l" + passoant).attr('class', 'ui-state-default ui-corner-top ui-state-focus');
$("#l" + passoant).attr('aria-selected', 'false');
$("#l" + passoant).attr('aria-expanded', 'false');
$("#l" + passoant).attr('aria-disabled', 'true');
$("#l" + passoant).attr('class', 'ui-state-default ui-corner-top ui-state-disabled');
$("#l" + passoant).attr('role', 'presentation');
$("#l" + passoant).attr('tabindex', '-1');

$("#l" + passopro).attr('aria-selected', 'false');
$("#l" + passopro).attr('aria-expanded', 'false');
$("#l" + passopro).attr('aria-disabled', 'true');
$("#l" + passopro).attr('class', 'ui-state-default ui-corner-top ui-state-disabled');
$("#l" + passopro).attr('role', 'presentation');
$("#l" + passopro).attr('tabindex', '-1');

$("#l" + nro).attr('class', 'ui-state-default ui-corner-top ui-state-focus ui-tabs-active ui-state-active');

$("#tabs-" + passoant).attr('aria-hidden', 'true');
$("#tabs-" + passoant).attr('style', 'display:none');
$("#tabs-" + passoant).attr('tabindex', '-1');
$("#tabs-" + passoant).removeAttr('tabindex');
$("#tabs-" + nro).attr('aria-hidden', 'false');
$("#tabs-" + nro).attr('style', 'display:block');

//redimensiona o mapa, para que apareça dentro da tab
        google.maps.event.trigger( map, "resize" );
        //seta o marcador como centro do mapa
        map.setCenter(marker.getPosition());

}