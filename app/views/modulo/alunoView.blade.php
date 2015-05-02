@extends('master')


@section('content-header')
	<!-- Content Header (Page header) -->
    <h1>
	        Aulas
	    </h1>
	    <ol class="breadcrumb">
	        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	        <li class="active">Meus Cursos</li>
			<li class="active">Aulas</li>
	    </ol>
@stop

@section('modals')
	<div class="modal fade" id="video" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">Vídeo</h4>
                </div>
                <div class="modal-body">
                    <video class="center-block" width="320" height="240" controls style="max-width:100%; display: block; height:auto;">
                      <source src="" type="video/mp4">
                      Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('maincontent')

	<section class="content">
		<h2 class="page-header">Inglês - Teens 1</h2>
		<div class="row">
			<div class="col-md-12">
				<div class="box box-solid">
		            <div class="box-header">
		                <h3 class="box-title">Conteúdos</h3>
		            </div><!-- /.box-header -->
		            <div class="box-body">
		                <div class="box-group" id="accordion">
		                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
							
							@foreach ( ($modulo->aulas) as $aula)
								<div class="panel box box-success">
			                        <div class="box-header">
			                            <h4 class="box-title">
			                                <a data-toggle="collapse" data-parent="#accordion" {{'href="#'.$aula->id.'"'}}>
			                                    {{$aula->titulo}}
			                                </a>
			                            </h4>
			                        </div>
			                        <div id={{'"'.$aula->id.'"'}} class="panel-collapse collapse">
			                            <div class="box-body">
			                        
			                                	@foreach ($aula->materialApoio as $material)
				                                	<div class="alert alert-success alert-dismissable" style="min-height: 55px;">
				                                        <i class="fa  fa-check-circle" style="left: -15px; top: 7px;"></i>
				                                        <p style="float:left;">{{$material->nome}}</p>
				                                        <div class="box-tools pull-right">
                                                        	@if($material->tipo == 1)
	                                                            <a href="/Viewer#/{{$material->url}}"><button class="btn btn-primary btn-xs"><i class="fa fa-file-pdf-o"></i></button></a>
	                                                        @elseif($material->tipo == 2)
	                                                            <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#video" data-url="{{$material->url}}"><i class="fa fa-film"></i></button>
	                                                        @elseif($material->tipo == 3)
	                                                            <a href="{{$material->url}}" target="_blank"><button class="btn btn-primary btn-xs"><i class="fa fa-external-link"></i></button></a>
	                                                        @endif
                                                        </div>
				                                    </div>
			                                	@endforeach

			                                	@foreach ($aula->atividades()->where('status','=','1')->get() as $atividade)
			                                		<div class="alert bg-primary alert-dismissable" style="min-height: 55px;">
				                                        <i class="fa  fa-check-circle" style="left: -15px; top: 7px;"></i>
				                                        <p style="float:left;">{{$atividade->nome}}</p>
				                                        <div class="box-tools pull-right">
				                                        	
                                                        	<a href="/aluno/atividade/{{$atividade->id}}"><button class="btn btn-primary btn-xs"><i class="fa fa-external-link"></i></button></a>
                                                        </div>
				                                    </div>
			                                	@endforeach


			                            </div>
			                        </div>
			                    </div>
							@endforeach

		                </div>
		            </div><!-- /.box-body -->
		        </div>
		    </div>

			
		</div>
	</section>

@endsection