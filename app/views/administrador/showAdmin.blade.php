@extends('master-admin')

@section('modals')

<div class="modal fade" id="criarAdministrador" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Novo Administrador</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="/admin/criarAdministrador" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="tipo" name="tipo" value="3">
                    </div>
                    <div id="div_nome"  class="form-group">
                        <label class="control-label" for="nome"><i id="icone_nome" class="fa"></i> Nome</label>
                        <input type="text" autocomplete="off" id="nome" name="nome" onblur="fcn_recarregaCores();" maxlength="50" class="form-control somenteLetras nomeObrigatorio">
                    </div>
                    <div id="div_sobrenome" class="form-group">
                        <label class="control-label" for="sobrenome"><i id="icone_sobrenome" class="fa"></i> Sobrenome</label>
                        <input type="text" autocomplete="off" id="sobrenome" name="sobrenome" onblur="fcn_recarregaCores();" maxlength="50" class="form-control somenteLetras sobrenomeObrigatorio">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="cargo"> Cargo</label>
                        <input type="text" autocomplete="off" id="cargo" name="cargo" maxlength="50" class="form-control" onblur="fcn_recarregaCores();" >
                    </div>
					<div id="div_codRegistro" class="form-group">
                        <label class="control-label" for="codRegistro"><i id="icone_codRegistro" class="fa"></i> Código de Registro</label>
                        <input type="text" autocomplete="off" id="codRegistro" name="codRegistro" maxlength="50" class="form-control somenteNumeros codRegistroObrigatorio" onblur="fcn_recarregaCores();" >
                    </div>
                    <div id="div_email" class="form-group">
                        <label class="control-label" for="email"><i id="icone_email" class="fa"></i> E-mail</label>
                        <input type="text" autocomplete="off" id="email" name="email" maxlength="50" class="form-control emailObrigatorio" onblur="fcn_recarregaCores();fcn_validaEmail(this);" >
                    </div>
                    <div id="div_senha" class="form-group">
						<label class="control-label" for="senha"><i id="icone_senha" class="fa"></i> Senha</label>
						
						<!-- Campo fake para enganar o autocomplete do chrome -->
						<input type="password" name="password" style="display:none;" >
						<!-- -->
						
						<input type="password" autocomplete="off" id="senha" name="password" maxlength="12" class="form-control senhaObrigatoria" onblur="fcn_recarregaCores();fcn_validaSenha(6, 12, this.value);" >
					</div>	
					<div id="div_confirmaSenha" class="form-group">
						<label class="control-label" for="confirmaSenha"><i id="icone_confirmaSenha" class="fa"></i> Confirmar Senha</label>
						<input type="password" autocomplete="off" id="confirmaSenha" name="confirmaSenha" maxlength="12" class="form-control confirmaSenhaObrigatoria" onblur="fcn_recarregaCores();fcn_validaConfirmaSenha(6, 12, this.value);" >
					</div>
                    <div class="form-group">
                        <label for="urlImagem" class="control-label">Imagem Perfil</label>
                        <input type="file" id="urlImagem" name="urlImagem" class="form-control" onblur="fcn_validaArquivo(this.form, this.form.urlImagem.value)">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-primary btn-salvar" value="Salvar">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
	
@endsection

@section('maincontent')

<section class="content-header">
    <h1>Gerenciar Administradores</h1>
    <ol class="breadcrumb">
	    <?php
	    	$aux2 = Session::get('bc');
	    ?>
    	@foreach($aux2 as $b)
        	<li><a href="{{$b['link']}}" >{{$b['nome']}}</a></li>
		@endforeach
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
			<table id="example" class="display" cellspacing="0" width="100%">
		        <thead>
		            <tr>
		                <th>Nome</th>
		                <th>Sobrenome</th>
		                <th>Login</th>
		                <th>E-mail</th>
		                <th>Código de Registro</th>
		                <th>Status</th>
		                <th><button class="btn btn-primary btn-md" style="border-radius: 50px;" data-toggle="modal" data-target="#criarAdministrador" ><i class="fa fa-plus"></i></button></th>
		            </tr>
		        </thead>
		 
		        <tfoot>
		            <tr>
		                <th>Nome</th>
		                <th>Sobrenome</th>
		                <th>Login</th>
		                <th>E-mail</th>
		                <th>Código de Registro</th>
		                <th>Status</th>
		                <th><button class="btn btn-primary btn-md" style="border-radius: 50px;" data-toggle="modal" data-target="#criarAdministrador" ><i class="fa fa-plus"></i></button></th>
		            </tr>
		        </tfoot>
		    </table>
        </div>
    </div>

</section>

@endsection

@section('scripts')
<script src="/plugins/datatables/dataTables.tableTools.js" type="text/javascript"></script>

<script>
	
	$.get('/admin/listarAdministradores', function(data) {
		console.log(data);
	});

	$('#example').DataTable( {
	  "ajax":"/admin/listarAdministradores" ,
	    "columns": [
	        { data: 'nome' },
	        { data: 'sobrenome' },
	        { data: 'login' },
	        { data: 'email' },
	        { data: 'codRegistro' },
	        { data: 'excluido' },
	        { data: 'action'}
	    ],

	    "scrollX": true,

	    "columnDefs": [ {
		      "targets": 6,
		      "orderable": false,
		      "searchable": false
		    } ],

	    "dataSrc": "",
	     dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "/swf/copy_csv_xls_pdf.swf"
        },

        responsive: true,

        language: {
		    "emptyTable":     "Nenhum registro disponível",
		    "info":           "Mostrando _START_ a _END_ de _TOTAL_ valores",
		    "infoEmpty":      "Mostrando 0 to 0 of 0 valores",
		    "infoFiltered":   "(Filtrado dentre _MAX_ valores)",
		    "infoPostFix":    "",
		    "thousands":      ",",
		    "lengthMenu":     "Mostrar _MENU_ valores",
		    "loadingRecords": "Carregando...",
		    "processing":     "Processando...",
		    "search":         "Pesquisa:",
		    "zeroRecords":    "Nenhum resultado encontrado",
		    "paginate": {
		        "first":      "Primeiro",
		        "last":       "Último",
		        "next":       "Próximo",
		        "previous":   "Anterior"
		    },
		    "aria": {
		        "sortAscending":  ": activate to sort column ascending",
		        "sortDescending": ": activate to sort column descending"
		    }
		}
    } );
</script>

<script> //Validações
$( ".somenteLetras" ).keyup(function() {
	//Não ativa função ao clicar tecla direção esquerda e direito, botão apagar e botão deletar
	if(event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 46 && event.keyCode != 8){
		var valor = $(this).val().replace(/[^a-zA-ZãÃáÁàÀâÂéÉèÈêÊíÍìÌîÎõÕóÓòÒôÔúÚùÙûÛÇç ]+/g,'');
		$(this).val(valor);
	}
});

	function confirmar(){
	    if(!(confirm("Deseja realmente apagar este aluno e todos os registros relacionados a ele?"))){
	        return false
	    }
	}

$( ".somenteNumeros" ).keyup(function() {
	//Não ativa função ao clicar tecla direção esquerda e direito, botão apagar e botão deletar
	if(event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 46 && event.keyCode != 8){
		var valor = $(this).val().replace(/[^0-9]+/g,'');
		$(this).val(valor);
	}
});

$(".btn-salvar").click(function(event){
	
	var obrigatorioPendente = 0;
	
	if($(".nomeObrigatorio").val() == ""){
		obrigatorioPendente = 1;
		$( "#div_nome" ).removeClass("has-success");
		$( "#icone_nome" ).removeClass("fa-check");
		$( "#div_nome" ).addClass("has-error");
		$( "#icone_nome" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_nome" ).removeClass("has-error");
		$( "#icone_nome" ).removeClass("fa-times-circle-o");
		$( "#div_nome" ).addClass("has-success");
		$( "#icone_nome" ).addClass("fa-check");
	}
	
	if($(".sobrenomeObrigatorio").val() == ""){
		obrigatorioPendente = 1;
		$( "#div_sobrenome" ).removeClass("has-success");
		$( "#icone_sobrenome" ).removeClass("fa-check");
		$( "#div_sobrenome" ).addClass("has-error");
		$( "#icone_sobrenome" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_sobrenome" ).removeClass("has-error");
		$( "#icone_sobrenome" ).removeClass("fa-times-circle-o");
		$( "#div_sobrenome" ).addClass("has-success");
		$( "#icone_sobrenome" ).addClass("fa-check");
	}
	
	if($(".codRegistroObrigatorio").val() == ""){
		obrigatorioPendente = 1;
		$( "#div_codRegistro" ).removeClass("has-success");
		$( "#icone_codRegistro" ).removeClass("fa-check");
		$( "#div_codRegistro" ).addClass("has-error");
		$( "#icone_codRegistro" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_codRegistro" ).removeClass("has-error");
		$( "#icone_codRegistro" ).removeClass("fa-times-circle-o");
		$( "#div_codRegistro" ).addClass("has-success");
		$( "#icone_codRegistro" ).addClass("fa-check");
	}
	
	if($(".emailObrigatorio").val() == ""){
		obrigatorioPendente = 1;
		$( "#div_email" ).removeClass("has-success");
		$( "#icone_email" ).removeClass("fa-check");
		$( "#div_email" ).addClass("has-error");
		$( "#icone_email" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_email" ).removeClass("has-error");
		$( "#icone_email" ).removeClass("fa-times-circle-o");
		$( "#div_email" ).addClass("has-success");
		$( "#icone_email" ).addClass("fa-check");
	}
	
	if($(".senhaObrigatoria").val() == ""){
		obrigatorioPendente = 1;
		$( "#div_senha" ).removeClass("has-success");
		$( "#icone_senha" ).removeClass("fa-check");
		$( "#div_senha" ).addClass("has-error");
		$( "#icone_senha" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_senha" ).removeClass("has-error");
		$( "#icone_senha" ).removeClass("fa-times-circle-o");
		$( "#div_senha" ).addClass("has-success");
		$( "#icone_senha" ).addClass("fa-check");
	}
	
	if($(".confirmaSenhaObrigatoria").val() == ""){
		obrigatorioPendente = 1;
		$( "#div_confirmaSenha" ).removeClass("has-success");
		$( "#icone_confirmaSenha" ).removeClass("fa-check");
		$( "#div_confirmaSenha" ).addClass("has-error");
		$( "#icone_confirmaSenha" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_confirmaSenha" ).removeClass("has-error");
		$( "#icone_confirmaSenha" ).removeClass("fa-times-circle-o");
		$( "#div_confirmaSenha" ).addClass("has-success");
		$( "#icone_confirmaSenha" ).addClass("fa-check");
	}
	
	if(obrigatorioPendente == 1){
		alert("É necessário preencher todos os campos obrigatórios!");
		return false;
	}
	
	if(document.getElementById('senha').value != document.getElementById('confirmaSenha').value){
				
		$( "#div_senha" ).removeClass("has-success");
		$( "#icone_senha" ).removeClass("fa-check");
		$( "#div_senha" ).addClass("has-error");
		$( "#icone_senha" ).addClass("fa-times-circle-o");
		$( "#div_confirmaSenha" ).removeClass("has-success");
		$( "#icone_confirmaSenha" ).removeClass("fa-check");
		$( "#div_confirmaSenha" ).addClass("has-error");
		$( "#icone_confirmaSenha" ).addClass("fa-times-circle-o");
		
		alert("O campo Confirmar Senha está diferente do campo Senha!");
		return false;
	}
	
})

function fcn_recarregaCores(){
	
	if($(".nomeObrigatorio").val() == ""){
		$( "#div_nome" ).removeClass("has-success");
		$( "#icone_nome" ).removeClass("fa-check");
		$( "#div_nome" ).addClass("has-error");
		$( "#icone_nome" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_nome" ).removeClass("has-error");
		$( "#icone_nome" ).removeClass("fa-times-circle-o");
		$( "#div_nome" ).addClass("has-success");
		$( "#icone_nome" ).addClass("fa-check");
	}
	
	if($(".sobrenomeObrigatorio").val() == ""){
		$( "#div_sobrenome" ).removeClass("has-success");
		$( "#icone_sobrenome" ).removeClass("fa-check");
		$( "#div_sobrenome" ).addClass("has-error");
		$( "#icone_sobrenome" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_sobrenome" ).removeClass("has-error");
		$( "#icone_sobrenome" ).removeClass("fa-times-circle-o");
		$( "#div_sobrenome" ).addClass("has-success");
		$( "#icone_sobrenome" ).addClass("fa-check");
	}
	
	if($(".codRegistroObrigatorio").val() == ""){
		$( "#div_codRegistro" ).removeClass("has-success");
		$( "#icone_codRegistro" ).removeClass("fa-check");
		$( "#div_codRegistro" ).addClass("has-error");
		$( "#icone_codRegistro" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_codRegistro" ).removeClass("has-error");
		$( "#icone_codRegistro" ).removeClass("fa-times-circle-o");
		$( "#div_codRegistro" ).addClass("has-success");
		$( "#icone_codRegistro" ).addClass("fa-check");
	}
	
	if($(".emailObrigatorio").val() == ""){
		$( "#div_email" ).removeClass("has-success");
		$( "#icone_email" ).removeClass("fa-check");
		$( "#div_email" ).addClass("has-error");
		$( "#icone_email" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_email" ).removeClass("has-error");
		$( "#icone_email" ).removeClass("fa-times-circle-o");
		$( "#div_email" ).addClass("has-success");
		$( "#icone_email" ).addClass("fa-check");
	}
	
	if($(".senhaObrigatoria").val() == ""){
		$( "#div_senha" ).removeClass("has-success");
		$( "#icone_senha" ).removeClass("fa-check");
		$( "#div_senha" ).addClass("has-error");
		$( "#icone_senha" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_senha" ).removeClass("has-error");
		$( "#icone_senha" ).removeClass("fa-times-circle-o");
		$( "#div_senha" ).addClass("has-success");
		$( "#icone_senha" ).addClass("fa-check");
	}
	
	if($(".confirmaSenhaObrigatoria").val() == ""){
		$( "#div_confirmaSenha" ).removeClass("has-success");
		$( "#icone_confirmaSenha" ).removeClass("fa-check");
		$( "#div_confirmaSenha" ).addClass("has-error");
		$( "#icone_confirmaSenha" ).addClass("fa-times-circle-o");
	}else{
		$( "#div_confirmaSenha" ).removeClass("has-error");
		$( "#icone_confirmaSenha" ).removeClass("fa-times-circle-o");
		$( "#div_confirmaSenha" ).addClass("has-success");
		$( "#icone_confirmaSenha" ).addClass("fa-check");
	}
				
}

function fcn_validaEmail(pstr_email){
	if (pstr_email.value != '') {	
		er = /^[a-zA-Z0-9][a-zA-Z0-9\._-]+@([a-zA-Z0-9\._-]+\.)[a-zA-Z-0-9]{2}/;
		if (!er.exec(pstr_email.value)) {
			
			$( "#div_email" ).removeClass("has-success");
			$( "#icone_email" ).removeClass("fa-check");
			$( "#div_email" ).addClass("has-error");
			$( "#icone_email" ).addClass("fa-times-circle-o");
			
			pstr_email.focus();
			alert("É necessário o preenchimento de um endereço de e-mail válido.");
			return false;
		}
	}
}

function fcn_validaSenha(minimo, maximo, pstr_valor){
	
	var senha = document.getElementById('senha').value;
	var str = pstr_valor;
	
	if(senha != ""){
	
		if (senha.length > maximo) {
			document.getElementById('senha').value = senha.substring(0, maximo);
		} else {
			
			if(senha.length < minimo){
				
				$( "#div_senha" ).removeClass("has-success");
				$( "#icone_senha" ).removeClass("fa-check");
				$( "#div_senha" ).addClass("has-error");
				$( "#icone_senha" ).addClass("fa-times-circle-o");
				
				alert("A senha deve ter entre 6 e 12 caracteres.");
				document.getElementById('senha').focus();
				return false;
			}
			
		}
		
		//Caracteres especiais
		if (!(
				str.indexOf('"') > 0 || str.indexOf('!') > 0 || str.indexOf('@') > 0 || str.indexOf('#') > 0 || str.indexOf('$') > 0 || 
				str.indexOf('%') > 0 || str.indexOf('¨') > 0 || str.indexOf('&') > 0 || str.indexOf('*') > 0 ||	str.indexOf('(') > 0 || 
				str.indexOf(')') > 0 || str.indexOf('-') > 0 ||	str.indexOf('_') > 0 || str.indexOf('=') > 0 || str.indexOf('+') > 0 || 
				str.indexOf('¹') > 0 || str.indexOf('²') > 0 || str.indexOf('³') > 0 || str.indexOf('£') > 0 || str.indexOf('¢') > 0 || 
				str.indexOf('¬') > 0 || str.indexOf(',') > 0 || str.indexOf('.') > 0 || str.indexOf(';') > 0 || str.indexOf('/') > 0 || 
				str.indexOf('<') > 0 || str.indexOf('>') > 0 || str.indexOf(':') > 0 || str.indexOf('?') > 0 || str.indexOf('~') > 0 || 
				str.indexOf('^') > 0 || str.indexOf(']') > 0 || str.indexOf('}') > 0 || str.indexOf('{') > 0 || str.indexOf('[') > 0 || 
				str.indexOf('º') > 0 || str.indexOf('ª') > 0 || str.indexOf('§') > 0 || str.indexOf('*') > 0 || str.indexOf('°') > 0
			)) {
			
			$( "#div_senha" ).removeClass("has-success");
			$( "#icone_senha" ).removeClass("fa-check");
			$( "#div_senha" ).addClass("has-error");
			$( "#icone_senha" ).addClass("fa-times-circle-o");
			
			alert("A senha deve ter pelo menos 1 caracter especial.");
			document.getElementById('senha').focus();
			return false;
		}
		
	}
	
}

function fcn_validaConfirmaSenha(minimo, maximo, pstr_valor){
		
	var senha = document.getElementById('confirmaSenha').value;
	var str = pstr_valor;
	
	if(senha != ""){
	
		if (senha.length > maximo) {
			document.getElementById('confirmaSenha').value = senha.substring(0, maximo);
		} else {
			
			if(senha.length < minimo){
				
				$( "#div_confirmaSenha" ).removeClass("has-success");
				$( "#icone_confirmaSenha" ).removeClass("fa-check");
				$( "#div_confirmaSenha" ).addClass("has-error");
				$( "#icone_confirmaSenha" ).addClass("fa-times-circle-o");
				
				alert("A senha deve ter entre 6 e 12 caracteres.");
				document.getElementById('confirmaSenha').focus();
				return false;
			}
			
		}
		
		//Caracteres especiais
		if (!(
				str.indexOf('"') > 0 || str.indexOf('!') > 0 || str.indexOf('@') > 0 || str.indexOf('#') > 0 || str.indexOf('$') > 0 || 
				str.indexOf('%') > 0 || str.indexOf('¨') > 0 || str.indexOf('&') > 0 || str.indexOf('*') > 0 ||	str.indexOf('(') > 0 || 
				str.indexOf(')') > 0 || str.indexOf('-') > 0 ||	str.indexOf('_') > 0 || str.indexOf('=') > 0 || str.indexOf('+') > 0 || 
				str.indexOf('¹') > 0 || str.indexOf('²') > 0 || str.indexOf('³') > 0 || str.indexOf('£') > 0 || str.indexOf('¢') > 0 || 
				str.indexOf('¬') > 0 || str.indexOf(',') > 0 || str.indexOf('.') > 0 || str.indexOf(';') > 0 || str.indexOf('/') > 0 || 
				str.indexOf('<') > 0 || str.indexOf('>') > 0 || str.indexOf(':') > 0 || str.indexOf('?') > 0 || str.indexOf('~') > 0 || 
				str.indexOf('^') > 0 || str.indexOf(']') > 0 || str.indexOf('}') > 0 || str.indexOf('{') > 0 || str.indexOf('[') > 0 || 
				str.indexOf('º') > 0 || str.indexOf('ª') > 0 || str.indexOf('§') > 0 || str.indexOf('*') > 0 || str.indexOf('°') > 0
			)) {
			
			$( "#div_confirmaSenha" ).removeClass("has-success");
			$( "#icone_confirmaSenha" ).removeClass("fa-check");
			$( "#div_confirmaSenha" ).addClass("has-error");
			$( "#icone_confirmaSenha" ).addClass("fa-times-circle-o");
			
			alert("A senha deve ter pelo menos 1 caracter especial.");
			document.getElementById('confirmaSenha').focus();
			return false;
		}
		
	}
	
}

function fcn_validaArquivo(formulario, arquivo) { 
   
	if(arquivo != ""){
		extensoes_permitidas = new Array(".jpg", ".png", ".jpeg"); 
		meuerro = ""; 
		 
		extensao = (arquivo.substring(arquivo.lastIndexOf("."))).toLowerCase(); 
		permitida = false; 
		for (var i = 0; i < extensoes_permitidas.length; i++) { 
			if (extensoes_permitidas[i] == extensao) { 
				permitida = true; 
				break; 
			} 
		} 
		
		if (permitida == false) { 
			alert("Verifique a extensão do arquivo anexado. \n\nAs extensões permitidas são: " + extensoes_permitidas.join()); 
			document.getElementById('urlImagem').value = "";
			return 1;
		}
		 
		return 0; 
	}
} 
</script>

@endsection