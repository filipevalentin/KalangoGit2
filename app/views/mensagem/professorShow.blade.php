@extends('master-prof')

<?php 
	function resposta($mensagem){
		if($mensagem->idRE != null){
			$re = $mensagem->emResposta;
 ?>
	<div id="anteriores" style="display: none;">
		<div class="">
			<div class="mailbox-read-info" style="padding:10px;">
				<h3>{{$re->titulo}}</h3>
				<h5>De: {{$re->usuarioOrigem->nome}} {{$re->usuarioOrigem->sobrenome}}<span class="mailbox-read-time pull-right">{{$re->usuarioOrigem->nome}} {{$re->usuarioOrigem->sobrenome}}<span class="mailbox-read-time pull-right">{{ date("d/m/Y h:i", strtotime(str_replace('-','/', $re->data)))}}</span></h5><hr>
			</div><!-- /.mailbox-read-info -->
			<!-- /.mailbox-controls -->
			<div class="mailbox-read-message" style="padding:10px;">
				{{$re->conteudo}}
			</div><!-- /.mailbox-read-message -->
		</div><hr>
	</div>

		<?php
			resposta($mensagem->emResposta);
		}
	} 
?>

@section('modals')

<div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title"><i class="fa fa-envelope-o"> </i> Nova Mensagem</h4>
			</div>
			<form action="/professor/mensagem/enviar" method="post">
				<div class="modal-body">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">Para:</span>
							<select class="form-control" name="idUsuarioDestino" id="idUsuarioDestino">
								<option disabled>Selecionar Aluno</option>
								@foreach(Auth::user()->professor->turmas as $turma)
									<option disabled>--Turma: {{$turma->nome}} --</option>
									@foreach($turma->alunos as $aluno)
									<option value="{{$aluno->id}}">{{$aluno->usuario->nome}}</option>
									@endforeach
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<input autocomplete="off" class="form-control tituloObrigatorio" type="text" id="titulo" name="titulo" placeholder="Titulo">
					</div>
					<div class="form-group">
						<textarea name="conteudo" id="email_message" class="form-control mensagemObrigatoria" placeholder="Mensagem" style="height: 120px;"></textarea>
					</div>
				</div>
				<div class="modal-footer clearfix">
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-envelope"></i> Enviar</button>
					<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-times"></i> Descartar</button>
				</div>
			</form>
		</div> 
	</div> 
</div>


<div class="modal fade" id="responder" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title"><i class="fa fa-envelope-o"> </i> Responder Mensagem</h4>
			</div>
			<form action="/professor/mensagem/responder" method="post">
				<div class="modal-body">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">Para:</span>
							<input class="form-control" type="text" id="nomeDestino" value="" disabled>
							<input type="hidden" id="idUsuarioDestino" name="idUsuarioDestino" value="">
						</div>
					</div>
					<div class="form-group">
						<input autocomplete="off" class="form-control tituloObrigatorio" type="text" id="titulo" name="titulo" placeholder="">
					</div>
					<div class="form-group">
						<textarea name="conteudo" id="email_message" class="form-control mensagemObrigatoria" placeholder="Mensagem" style="height: 120px;"></textarea>
					</div>
					<div class="form-group">
						<input name="idRE" type="hidden" id="idRE" class="form-control" style="height: 120px;">
					</div>
				</div>
				<div class="modal-footer clearfix">
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-envelope"></i> Enviar</button>
					<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-times"></i> Descartar</button>
				</div>
			</form>
		</div> 
	</div> 
</div>
@endsection
@section('maincontent')
	<section class="content-header no-margin">
		<h1 class="text-center">
			Caixa de Mensagens
		</h1>
	</section>

	<section class="content">

		<div class="mailbox row">
			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-body">
						<div class="row">
							<div class="col-md-3 col-sm-4">

								<div class="box-header">
									<h3 class="box-title">Ler Mensagem</h3>
								</div>

								<a class="btn btn-block btn-primary" data-toggle="modal" data-target="#compose-modal"><i class="fa fa-pencil"></i>Nova Mensagem</a>

								<div style="margin-top: 15px;">
									<ul class="nav nav-pills nav-stacked">
										<li class="header">Pastas</li>
										<?php global $count; 
											$inbox= Mensagem::where('idUsuarioDestino', '=', Auth::user()->id)->get(); 
										?> 
										<?php $inbox->each(function($mensagem){
											global $count;
											if($mensagem->lida!=1){
												$count++;
											}
										}) ?>
										@if($count==0)
										<li class=""><a href="/professor/mensagens/entrada"><i class="fa fa-inbox"></i> Entrada</a></li>
										@else
										<li class=""><a href="/professor/mensagens/entrada"><i class="fa fa-inbox"></i> Entrada ({{$count}})</a></li>
										@endif
										<li class=""><a href="/professor/mensagens/enviados"><i class="fa fa-mail-forward"></i> Enviados</a></li>
									</ul>
								</div>
							</div> 
							<div class="col-md-9 col-sm-8">
								<div class="mailbox-read-info" style="padding:10px;">
									<h3>{{$mensagem->titulo}}</h3>
									<h5>De: {{$mensagem->usuarioOrigem->nome}} {{$mensagem->usuarioOrigem->sobrenome}}<span class="mailbox-read-time pull-right">{{ date('d/m/Y h:i', strtotime(str_replace('-','/', $mensagem->data)))}}</span></h5><hr>
								</div><!-- /.mailbox-read-info -->
								<!-- /.mailbox-controls -->
								<div class="mailbox-read-message" style="padding:10px;">
									{{$mensagem->conteudo}}
								</div><!-- /.mailbox-read-message -->
								<br><hr><!-- /.box-body -->

								@if($mensagem->idRE != null)
									<div class="mensagensAnteriores">
									<h4 style="padding-left:10px; cursor:pointer;">Mensagens anteriores</h4><hr>
										<?php resposta($mensagem) ?>
									</div>
								@endif
								<!-- /.box-footer -->
								<div class="box-footer">
									<div class="pull-right">
										<button class="btn btn-default" data-toggle="modal" data-target="#responder" data-idRE="{{$mensagem->id}}" data-idUsuarioDestino="{{$mensagem->idUsuarioOrigem}}" data-nomeDestino="{{User::find($mensagem->idUsuarioOrigem)->nome}}" data-titulo="RE: {{$mensagem->titulo}}"><i class="fa fa-reply" ></i> Responder</button>
									</div>
								</div><!-- /.box-footer -->
							</div>
						</div> 
					</div> 
				</div> 
			</div> 
		</div>

	</section>


@endsection



@section('scripts')
<script>
	$('div.mensagensAnteriores>h4').on('click', function(event) {
		$('div#anteriores').slideToggle();
	});

	$('textarea').wysihtml5({
        "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
        "useLineBreaks": false,
        "emphasis": true, //Italics, bold, etc. Default true
        "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
        "html": false, //Button which allows you to edit the generated HTML. Default false
        "link": false, //Button to insert a link. Default true
        "image": false, //Button to insert an image. Default true,
        "color": true //Button to change color of font  
    });

    $('#responder').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var idUsuarioDestino = button.data('idusuariodestino');
        var nomeDestino = button.data('nomedestino');
        var idRE = button.data('idre');
        var titulo = button.data('titulo');

        var modal = $(this);

        modal.find('#idUsuarioDestino').val(idUsuarioDestino);
        modal.find('#idRE').val(idRE);
        modal.find('#titulo').val(titulo);
        modal.find('#nomeDestino').val(nomeDestino);
    });

</script>

<script>
	
	$(".btn-enviar").click(function(event){
			
		if($(".alunoObrigatorio").val() == ""){
			alert("É necessário preencher o Professor que deverá receber a mensagem!");
			$(".alunoObrigatorio").focus();
			return false;
		} 
		
		if($(".tituloObrigatorio").val() == ""){
			alert("É necessário preencher o Título da Mensagem!");
			$(".tituloObrigatorio").focus();
			return false;
		}

		if($(".mensagemObrigatoria").val() == ""){
			alert("É necessário preencher a Mensagem!");
			$(".mensagemObrigatoria").focus();
			return false;
		}	

		
		
	})
	
	$(".btn-enviarResposta").click(function(event){
			
		if($(".tituloRespostaObrigatorio").val() == ""){
			alert("É necessário preencher o Título da Mensagem!");
			$(".tituloRespostaObrigatorio").focus();
			return false;
		}

		if($(".mensagemRespostaObrigatoria").val() == ""){
			alert("É necessário preencher a Mensagem!");
			$(".mensagemRespostaObrigatoria").focus();
			return false;
		}	

		
		
	})
			
</script>

@endsection
