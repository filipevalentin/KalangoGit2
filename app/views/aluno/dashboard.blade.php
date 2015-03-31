@extends('master')

@section('modals')
	
@endsection

@section('maincontent')

<section class="content-header">
    <h1>Dashboard</h1>
    <ol class="breadcrumb">
        <li><a href="#" ><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Widgets</li>
    </ol>
</section>

<section class="content">

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
        @foreach($turmas as $turma)
            <li class="active"><a href="#{{$turma->id}}" data-toggle="tab">{{$turma->nome}}</a></li>
        @endforeach
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="{{$turma->id}}">
            @foreach($turmas as $turma)

                <!-- Dados Gerais -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="box box-solid" style="background: #B6703D; color: black;">
                            <div class="box-header">
                                <h3 class="box-title" style="color: white;">Meus Desempenho</h3>
                            </div> 
                            <div class="box-body" style="background: rgb(236, 236, 236)">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3 center" style="text-align: center">
                                        <span class="profile-picture">
                                            <img class="editable img-responsive" alt="Alex's Avatar" id="avatar2" style="max-height: 200px;" src="/{{Auth::user()->urlImagem}}">
                                        </span>
                                    </div>
                                    <div class="col-sm-9">
                                        <ul style="padding:0px; list-style: none;">
                                            <li style="font-size: x-large;font-weight: 600;">{{Auth::user()->nome}} {{Auth::user()->sobrenome}}</li>
                                            <li style="font-size: large;font-weight: 500;">{{Auth::user()->aluno->turmas->first()->nome}}</li>
                                            <li style="font-size: large;font-weight: 500;">Ranking: Turma: {{$turma->meuRanking['turma']+1}}º   Módulo: {{$turma->meuRanking['modulo']+1}}º</li>
                                            <li style="font-size: large;font-weight: 500;">Medalha:
                                                <small class="pull-right">90%</small>
                                                <div class="progress" style="margin-top:5px; height: 20px;background: white;">
                                                    <div class="progress-bar" style="width: 90%;background-color: silver;"><!-- Cores: bronze: darkgoldenrod, prata: silver, ouro: gold -->
                                                    {{$turma->pivot->pontuacao}} Pontos
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>   
                                </div>
                            </div> 
                        </div> 
                    </div>
                    <div class="col-lg-6">
                        <div class="box box-solid " style="background: #3F74D3; color: black;">
                            <div class="box-header">
                                <h3 class="box-title" style="color: white;">Estatísticas</h3>
                            </div> 
                            <div class="box-body" style="background: rgb(236, 236, 236)">
                                <div class="row">
                                    <div class="col-xs-4 center">
                                        <p> Respostas Corretas </p>
                                        <input type="text" class="knob" data-fgColor="green" data-bgColor="white" data-inputColor="black" data-width="96" data-height="96" data-skin="tron" value="{{$turma->acertos}}" data-max="{{$turma->questoes->count()}}" data-readOnly="true">
                                    </div>
                                    <div class="col-xs-4 center">
                                        <p> Respostas Erradas </p>
                                        <input type="text" class="knob" data-fgColor="tomato" data-bgColor="white" data-inputColor="black" data-width="96" data-height="96" data-skin="tron" value="{{$turma->erros}}" data-max="{{$turma->questoes->count()}}" data-readOnly="true">
                                    </div>
                                    <div class="col-xs-4 center">
                                        <p> Respostas Total </p>
                                        <input type="text" class="knob" data-fgColor="orange" data-bgColor="white" data-inputColor="black" data-width="96" data-height="96" data-skin="tron" value="{{$turma->questoes->count()}}" data-max="{{$turma->questoes->count()}}" data-readOnly="true">
                                    </div>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>

                <!-- Tópicos -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="box box-solid " style="background: limegreen; color: black;">
                            <div class="box-header">
                                <h3 class="box-title" style="color: white;">Tópicos</h3>
                            </div> 
                            <div class="box-body" style="background: rgb(236, 236, 236)">
                                <ul style="padding:0px; list-style: none;">
                                @foreach($turma->topicos as $topico)
                                    <li style="font-size:larger; font-weight: 500;">{{$topico->nome}}:
                                        <small class="pull-right">90%</small>
                                        <div class="progress" style="margin-top:5px; height: 15px;background: white;">
                                            <div class="progress-bar" style="width: 30%;background-color: gold; line-height: 15px;"> <!-- Cores: bronze: darkgoldenrod, prata: silver, ouro: gold -->
                                            {{$topico->pontos}} pontos
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                                Conteudo não dinamico abaixo
                                    <li style="font-size:larger; font-weight: 500;">Adjetivos:
                                        <small class="pull-right">90%</small>
                                        <div class="progress" style="margin-top:5px; height: 15px;background: white;">
                                            <div class="progress-bar" style="width: 70%;background-color: darkgoldenrod;"> <!-- Cores: bronze: darkgoldenrod, prata: silver, ouro: gold -->
                                            </div>
                                        </div>
                                    </li>
                                    <li style="font-size:larger; font-weight: 500;">Present Perfect:
                                        <small class="pull-right">90%</small>
                                        <div class="progress" style="margin-top:5px; height: 15px;background: white;">
                                            <div class="progress-bar" style="width: 50%;background-color: gold;"> <!-- Cores: bronze: darkgoldenrod, prata: silver, ouro: gold -->
                                            </div>
                                        </div>
                                    </li>
                                    <li style="font-size:larger; font-weight: 500;">Vocabulário:
                                        <small class="pull-right">90%</small>
                                        <div class="progress" style="margin-top:5px; height: 15px;background: white;">
                                            <div class="progress-bar" style="width: 86%;background-color: silver;"> <!-- Cores: bronze: darkgoldenrod, prata: silver, ouro: gold -->
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div> 
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="box box-solid " style="background: darkgrey; color: black;">
                            <div class="box-header">
                                <h3 class="box-title" style="color: white;">Top Tópicos</h3>
                            </div> 
                            <div class="box-body" style="background: rgb(236, 236, 236)">

                                <div class="row">
                                    <div class=" bg-green" style="margin: 10px 20px" >
                                        <span class="" style="font-size: -webkit-xxx-large;float: left;padding: 10px 20px 10px 20px;height: 100%;"><i class="fa fa-thumbs-o-up"></i></span>
                                        <div class="">
                                            <span class="" style="display: block;"><?php if($turma->topTopicos['melhor'] != null)echo $turma->topTopicos['melhor']->nome; ?></span>
                                            <span class=""><?php if($turma->topTopicos['melhor'] != null) echo $turma->topTopicos['melhor']->pontos; ?> pontos</span>
                                            <small class="pull-right" style="display: block;">90%</small>
                                            <div class="progress" style="height: 15px;background: rgb(0, 134, 73);">
                                                <div class="progress-bar" style="width: 50%;background-color: white;">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                               Parabéns!
                                            </span>
                                        </div><!-- /.info-box-content -->
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="bg-red" style="margin: 10px 20px">
                                        <span class="" style="font-size: -webkit-xxx-large;float: left;padding: 10px 20px 10px 20px;height: 100%;"><i class="fa fa-thumbs-o-down"></i></span>
                                        <div class="">
                                            <span class="" style="display: block;"><?php if($turma->topTopicos['pior'] != null)echo $turma->topTopicos['pior']->nome; ?></span>
                                            <span class=""><?php if($turma->topTopicos['pior'] != null) echo $turma->topTopicos['pior']->pontos; ?> pontos</span>
                                            <small class="pull-right" style="display: block;">90%</small>
                                            <div class="progress" style="height: 15px;background: rgb(0, 134, 73);">
                                                <div class="progress-bar" style="width: 10%;background-color: white;">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                Que tal revisar esse tópico?
                                            </span>
                                        </div><!-- /.info-box-content -->
                                    </div>
                                </div>
                                   
                            </div> 
                        </div>
                    </div>
                </div>

                <!-- Rankings -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Ranking: {{$turma->modulo->curso->nome}}-{{$turma->modulo->nome}}</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body no-padding">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th>#</th>
                                            <th>Nome</th>
                                            <th>Turma</th>
                                            <th>Pontuação</th>
                                            <th>Medalha</th>
                                        </tr>
                                        <?php $count=1; ?>
                                        @foreach($turma->rankingModulo as $aluno)
                                        <tr>
                                            <td>{{$count++}}º</td>
                                            <td>{{$aluno->usuario->nome}} {{$aluno->usuario->sobrenome}}</td>
                                            <td>{{Turma::find($aluno->pivot->idTurma)->nome}}</td>
                                            <td>{{$aluno->pivot->pontuacao}}</td>
                                            <td>Medalha</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div><!-- /.box-body -->
                        </div>
                    </div>

                    <div class="col-lg-6">
                         <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Ranking Turma</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body no-padding">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th>#</th>
                                            <th>Nome</th>
                                            <th>Pontuação</th>
                                            <th>Medalha</th>
                                        </tr>
                                        <?php $count=1; ?>
                                        @foreach($turma->rankingTurma as $aluno)
                                        <tr>
                                            <td>{{$count++}}º</td>
                                            <td>{{$aluno->usuario->nome}} {{$aluno->usuario->sobrenome}}</td>
                                            <td>{{$aluno->pivot->pontuacao}}</td>
                                            <td>Medalha</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                </div>

            @endforeach
            </div> 
        </div> 
    </div>

</section>

@endsection

@section('scripts')

<script>
	$(".knob").knob();
</script>

@endsection