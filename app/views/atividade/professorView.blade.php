@extends('master-prof')

@section('modals')


@endsection

@section('maincontent')
<section class="content-header">
    <h1>Visualizar Exercícios</h1>
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

            <h2 class="page-header">Inglês - Teens - Módulo 1 - Aula 2</h2>

            <div class="col-md-8">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">{{$atividades->nome}}</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="box-group" id="accordion">
                        <?php $aux = 1;
                            $questoes = $atividades->questoes->sortBy('id');
                        ?>
                            
                            @if($questoes->count() == 0)

                            <div class="callout callout-danger">
                                <h4>Nenhuma questão cadastrada</h4>
                            </div>
                                
                            @endif

                            @foreach($questoes as $questao)
                                @if($questao->tipo == "1") <!-- multipla escolha -->

                                <div class="panel box box-primary">
                                    <div class="box-header">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#ME{{$questao->id}}" class="">Questão {{$aux++}}</a>
                                        </h4>
                                        <small class="badge pull-right bg-green" style="margin: 0px 76px 0px 5px;"><?php if($questao->topico->nome != null) echo $questao->topico->nome ?></small>
		                                        	<small class="badge pull-right bg-red" style="margin: 0px 0px 0px 5px;"><?php if($questao->pontos != null) echo $questao->pontos ?> pontos</small>
                                    </div>
                                    <div id="ME{{$questao->id}}" class="panel-collapse collapse">
                                        <div class="box-body">
                                            <div class="row" style="margin:0px;">
                                                <div class="box text-center">
                                                    <div class="box-header">
                                                        <h3 class="box-title center" style="float: none;">{{$questao->enunciado}}</h3>
                                                    </div>
                                                    <div class="box-body">
                                                    <?php $categoria = (string)($questao->categoria);?>

                                                    @if(substr($categoria, 0, 1)==2)
                                                        <img src="/{{$questao->urlMidia}}" class="img-responsive center" alt="Responsive image" style="display: initial; max-height: 300px;">
                                                    @elseif(substr($categoria, 0, 1)==3)
                                                        <audio id="audio" controls="controls" style="display:none;">  
                                                            <source src="/{{$questao->urlMidia}}" />
                                                        </audio>
                                                        <div id="playParent" class="row">
                                                            <div class="small-box bg-aqua" style="width: 100px; margin:auto; font-size: -webkit-xxx-large;">
                                                                <i id="play" class="ion ion-play" style=""></i>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    </div>
                                                    
                                                    <div class="row" style="width: 98%; margin: auto;">

                                                        @if(substr($categoria, 1)==2)
                                                            <div class="col-md-6" style="padding-bottom: 5px;">
                                                                <button class="btn btn-block btn-flat bg-aqua" data-alternativa="A" style="border-radius: 16px;">
                                                                    <img src="/{{$questao->alternativaA}}" class="img-responsive center" alt="Responsive image" style="display: initial; max-height: 100px;">
                                                                </button>
                                                                <button class="btn btn-block btn-flat bg-red" style="border-radius: 16px;">
                                                                     <img src="/{{$questao->alternativaB}}" class="img-responsive center" alt="Responsive image" style="display: initial; max-height: 100px;">
                                                                </button>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button class="btn btn-block btn-flat bg-green" style="border-radius: 16px;">
                                                                     <img src="/{{$questao->alternativaC}}" class="img-responsive center" alt="Responsive image" style="display: initial; max-height: 100px;">
                                                                </button>
                                                                <button class="btn btn-block btn-flat bg-orange" style="border-radius: 16px;">
                                                                     <img src="/{{$questao->alternativaD}}" class="img-responsive center" alt="Responsive image" style="display: initial; max-height: 100px;">
                                                                </button>
                                                            </div>

                                                        @elseif(substr($questao->categoria, 1)==3)
                                                            <div class="col-md-6 " style="padding-bottom: 5px;">
                                                                <audio id="audio" controls="controls" style="display:none;">  
                                                                    <source src="/{{$questao->alternativaA}}">
                                                                </audio>
                                                                <div id="playParent" class="row" style="margin: auto;padding-left: 30px;">
                                                                    <button class="btn btn-primary" style="  font-size: xx-large;">
                                                                        <i id="play" class="ion ion-play" style=""></i>
                                                                    </button>
                                                                    <button class="btn btn-info" style="width: 140px;  font-size: xx-large;">
                                                                        <i id="enviar" class="ion ion-checkmark" style=""></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 " style="padding-bottom: 5px;">
                                                                <audio id="audio" controls="controls" style="display:none;">  
                                                                    <source src="/{{$questao->alternativaB}}">
                                                                </audio>
                                                                <div id="playParent" class="row" style="margin: auto;padding-left: 30px;">
                                                                    <button class="btn btn-primary" style="  font-size: xx-large;">
                                                                        <i id="play" class="ion ion-play" style=""></i>
                                                                    </button>
                                                                    <button class="btn btn-danger" style="width: 140px;  font-size: xx-large;">
                                                                        <i id="enviar" class="ion ion-checkmark" style=""></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 " style="padding-bottom: 5px;">
                                                                <audio id="audio" controls="controls" style="display:none;">  
                                                                    <source src="/{{$questao->alternativaC}}">
                                                                </audio>
                                                                <div id="playParent" class="row" style="margin: auto;padding-left: 30px;">
                                                                    <button class="btn btn-primary" style="  font-size: xx-large;">
                                                                        <i id="play" class="ion ion-play" style=""></i>
                                                                    </button>
                                                                    <button class="btn btn-success" style="width: 140px;  font-size: xx-large;">
                                                                        <i id="enviar" class="ion ion-checkmark" style=""></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 " style="padding-bottom: 5px;">
                                                                <audio id="audio" controls="controls" style="display:none;">  
                                                                    <source src="/{{$questao->alternativaD}}">
                                                                </audio>
                                                                <div id="playParent" class="row" style="margin: auto;padding-left: 30px;">
                                                                    <button class="btn btn-primary" style="  font-size: xx-large;">
                                                                        <i id="play" class="ion ion-play" style=""></i>
                                                                    </button>
                                                                    <button class="btn btn-warning" style="width: 140px;  font-size: xx-large;">
                                                                        <i id="enviar" class="ion ion-checkmark" style=""></i>
                                                                    </button>
                                                                </div>
                                                            </div>


                                                        @elseif(substr($categoria, 1)==1)
                                                            <div class="col-md-6" style="padding-bottom: 5px;">
                                                                <button class="btn btn-block btn-flat bg-aqua">{{$questao->alternativaA}}</button>
                                                                <button class="btn btn-block btn-flat bg-red">{{$questao->alternativaB}}</button>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button class="btn btn-block btn-flat bg-green">{{$questao->alternativaC}}</button>
                                                                <button class="btn btn-block btn-flat bg-orange">{{$questao->alternativaD}}</button>
                                                            </div>
                                                        @endif
                                                        

                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-1"></div>

                                                        <div class="col-md-10" style="padding-bottom: 5px;">
                                                            <label for="repostaCorreta" class="control-label">Resposta Correta</label>
                                                           <?php
                                                                switch($questao->respostaCerta)
                                                                {
                                                                    case 'a':
                                                                        echo '<p class="bg-aqua"> A)</p>';
                                                                        break;

                                                                    case 'b':
                                                                        echo '<p class="bg-red"> B)</p>';
                                                                        break;

                                                                    case 'c':
                                                                        echo '<p class="bg-green"> C)</p>';
                                                                        break;
                                                                        
                                                                    case 'd':
                                                                        echo '<p class="bg-orange"> D)</p>';
                                                                        break;

                                                                }
                                                            ?>
                                                        </div>

                                                    </div>
                                                    <br>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($questao->tipo == "2") <!-- Dissertativa -->

                                <div class="panel box box-primary">
                                    <div class="box-header">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#RU{{$questao->id}}" class="">Questão {{$aux++}}</a>
                                        </h4>
                                        @if(substr($questao->categoria,1) == "4")
                                            <small class="badge pull-right bg-red" style="margin: 0px 0px 0px 5px;">Rec. de Voz</small>
                                        @endif
                                        <small class="badge pull-right bg-green" style="margin: 0px 76px 0px 5px;"><?php if($questao->topico->nome != null) echo $questao->topico->nome ?></small>
		                                        	<small class="badge pull-right bg-red" style="margin: 0px 0px 0px 5px;"><?php if($questao->pontos != null) echo $questao->pontos ?> pontos</small>
                                    </div>
                                    <div id="RU{{$questao->id}}" class="panel-collapse collapse">
                                        <div class="box-body">
                                            <div class="row" style="margin:0px;">
                                                <div class="box text-center">
                                                    <div class="box-header">
                                                        <h3 class="box-title center" style="float: none;">{{$questao->enunciado}}</h3>
                                                    </div>
                                                    <div class="box-body">

                                                    @if(substr((string)$questao->categoria, 0, 1)==2)
                                                        <img src="/{{$questao->urlMidia}}" class="img-responsive center" alt="Responsive image" style="display: initial; max-height: 300px;">
                                                    @elseif(substr((string)$questao->categoria, 0, 1)==3)
                                                        <audio id="audio" controls="controls" style="display:none;">  
                                                            <source src="/{{$questao->urlMidia}}" />
                                                        </audio>
                                                        <div id="playParent" class="row">
                                                            <div class="small-box bg-aqua" style="width: 100px; margin:auto; font-size: -webkit-xxx-large;">
                                                                <i id="play" class="ion ion-play" style=""></i>
                                                            </div>
                                                        </div>

                                                        
                                                    @endif

                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-1"></div>

                                                        <div class="col-md-10" style="padding-bottom: 5px;">
                                                            <label for="repostaCorreta" class="control-label">Resposta Correta</label>
                                                            <p>{{$questao->respostaCerta}}</p>
                                                        </div>

                                                    </div>
                                                    <br>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                            
                        </div>
                    </div><!-- /.box-body -->
                </div>
            </div>

        </div>
    </div> 
</section>
@endsection

@section('scripts')

<script>

    $('#audio, #audioA, #audioB, #audioC, #audioD').on('ended', function(){
        $(this).siblings("div#playParent").find('i#play').removeClass('ion-pause').addClass('ion-play');
    });


    $('i#play').click(function () {
        console.log('Fui clicado');
        var button = $(this);
        var audio = $(this).closest('div#playParent').siblings('#audio')[0];

        if (audio.paused){
            audio.play();
            button.removeClass("ion ion-play").addClass('ion ion-pause');
        }else{
            audio.pause();
            button.removeClass("ion ion-pause").addClass('ion ion-play');
        }
    });

</script>

@endsection
           <!-- Scripts import -->