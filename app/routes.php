<?php

$layout = 'layouts.master';

Route::get('criarUsuariosTeste', function(){
	$u = new User;
	$u->nome = "Aluno 1";
	$u->login = "123";
	$u->tipo = "1";
	$u->password= Hash::make(123);
	$u->save();

	$a = new Aluno;
	$a->id = $u->id;
	$a->save();

	$u = new User;
	$u->nome = "Professor 1";
	$u->login = "456";
	$u->tipo = "2";
	$u->password= Hash::make(456);
	$u->save();

	$a = new Professor;
	$a->id = $u->id;
	$a->save();

	$u = new User;
	$u->nome = "Administrador 1";
	$u->login = "789";
	$u->tipo = "3";
	$u->password= Hash::make(789);
	$u->save();

	$a = new Administrador;
	$a->id = $u->id;
	$a->save();
});

Route::get('teste2', function(){
	$exercicio = Exercicio::where('id', '=', '5')->where('idAula', '=', '1')->first();
	$exercicio->testearray = array('1','2');
	$r = $exercicio->testearray;
	$r[] = "3";
	$exercicio->testearray = $r;
	dd($exercicio->testearray);
	return View::make('alunoResponderQuiz')->with('exercicio', $exercicio);
});

Route::get('admin', function(){
	return View::make('hello');
});

Route::get('pergunta', function(){
	return View::make('pergunta');
});

Route::get('dashboard', function(){
	return View::make('dashboard');
});



// ===============================================
// COMMON SECTION ================================
// ===============================================

	Route::controller('password', 'RemindersController');

	Route::get('/', function(){
		if(Auth::check()){
			if (Auth::user()->tipo == 1){
				return Redirect::to('aluno/home');
			}
			elseif(Auth::user()->tipo == 2){
				return Redirect::to('professor/home');
			}
			elseif(Auth::user()->tipo == 3){
				return Redirect::to('admin/home');
			}
		}
		return View::make('login');
	});

	Route::post('/', function(){
		if (Auth::attempt(array('login' => Input::get('usuario') , 'password' => Input::get('senha'), 'confirmed' => 1 ))){
	    	return Redirect::to('/');
		}
		else{
			return View::make('login')->with('mensagem', 'Seu login ou senha estão incorretos, tente novamente.');
		}
	});

	Route::get('logout', function(){
		Auth::logout();
		return Redirect::to('/');
	});

	Route::get('Viewer', function(){
		return View::make('view');
	});

	Route::get('registro/verificar/{confirmation_code}', function($codigo){
		Auth::logout();
		if( ! $codigo)
        {	
        	return Redirect::to('/');
        }

        $user = User::whereConfirmationCode($codigo)->first();

        if ( ! $user)
        {
           return Redirect::to('/'); 
        }

        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->save();

        return Redirect::to('/');
	});

	
// ===============================================
// ALUNO SECTION ================================
// ===============================================

Route::group(array('prefix' => 'aluno', 'before'=>'aluno'), function(){

	Route::get('home', function(){
		$turmas = Auth::user()->aluno->turmas;
		return View::make('aluno/home')->with('turmas', $turmas);
	});

	Route::get('mensagens/entrada', function(){
		$mensagens = Mensagem::where('idUsuarioDestino', '=', Auth::user()->id)->orderBy('id', 'desc')->paginate(10);
		return View::make('mensagem/alunoInbox')->with('mensagens', $mensagens);
		
	});

	Route::get('mensagens/enviados', function(){
		$mensagens = Mensagem::where('idUsuarioOrgiem', '=', Auth::user()->id)->orderBy('id', 'desc')->paginate(10);
		return View::make('mensagem/alunoEnviados')->with('mensagens', $mensagens);
		
	});

	Route::get('mensagem/{id}', function($id){
		$mensagem = Mensagem::find($id);
		if(strpos(URL::previous(), 'enviados') != false){

		}else{
			$mensagem->lida = 1;
		}

		$mensagem->save();
		return View::make('mensagem/alunoShow')->with('mensagem', $mensagem);
		
	});

	Route::post('mensagem/enviar', function(){
		$mensagem = new Mensagem;
		$mensagem->idUsuarioOrgiem = Auth::user()->id;
		$mensagem->idUsuarioDestino = Input::get('idUsuarioDestino');
		$mensagem->titulo = Input::get('titulo');
		$mensagem->conteudo = Input::get('conteudo');
		$mensagem->data = date('d-m-Y H:i:s');

		$mensagem->lida = 0;
		$mensagem->save();

		return Redirect::back();
		
	});

	Route::post('mensagem/responder', function(){
		$mensagem = new Mensagem;
		$mensagem->idUsuarioOrgiem = Auth::user()->id;
		$mensagem->idUsuarioDestino = Input::get('idUsuarioDestino');
		$mensagem->titulo = Input::get('titulo');
		$mensagem->conteudo = Input::get('conteudo');
		$mensagem->data = date('d-m-Y H:i:s');
		$mensagem->idRE = Input::get('idRE');

		$mensagem->lida = 0;
		$mensagem->save();

		return Redirect::back();
		
	});

	Route::get('atividade/{idAtividade}', function($idAtividade){
		$atividade = Atividade::find($idAtividade);
		$acesso = AcessosAtividade::where('idAluno', '=', Auth::user()->id)->where('idAtividade', '=', $idAtividade)->first();
		if($acesso != null){
			if($acesso->status != 1){
				return View::make('atividade/ResponderAtividade')->with(array('questao' => $acesso->idQuestao, 'atividade' => $atividade));
			}else{
				return Redirect::back();
			}
		}else{
			return View::make('atividade/ResponderAtividade')->with('atividade', $atividade);
		}
	});

	Route::get('verificarAcesso/{idAtividade}', function($idAtividade){
		$acesso = AcessosAtividade::where('idAtividade', '=', $idAtividade)->where('idAluno', '=', Auth::user()->id)->first();
		if($acesso != null){
			return Response::json('negado');
		}else{
			return Response::json('autorizado');
		}

	});

	Route::get('registrarAcesso/{idAtividade}/{idQuestao}', function($idAtividade, $idQuestao){
		$acesso = AcessosAtividade::where('idAtividade', '=', $idAtividade)->where('idAluno', '=', Auth::user()->id)->first();
		if($acesso == null){
			$acesso = new AcessosAtividade;
		}
		$acesso->idAluno = Auth::user()->id;
		$acesso->idAtividade = $idAtividade;
		$acesso->idQuestao = $idQuestao;
		$acesso->status = '0';

		$acesso->save();

		return Response::json("registro feito!");
	});

	Route::get('registrarConclusao/{idAtividade}', function($idAtividade){
		$acesso = AcessosAtividade::where('idAtividade', '=', $idAtividade)->where('idAluno', '=', Auth::user()->id)->first();
		$acesso->status = '1';

		$acesso->save();

		return Response::json("registro feito!");
	});


	Route::get('responder/{idQuestao}/{resposta}', function($idQuestao, $respostaAluno){
		$acesso = AcessosAtividade::where('idAluno', '=', Auth::user()->id)->where('idAtividade', '=', Questao::find($idQuestao)->atividade->id)->first();
		if($acesso != null){
			if($acesso->status == 1){
				return Response::json('negado');
			}
		}
		$resposta = new Resposta();
		$resposta->idQuestao = $idQuestao;
		$resposta->idAluno = Auth::user()->id;
		$resposta->respostaAluno = $respostaAluno;
		$resposta->save();
		return Response::json('Resposta Salva!');
		//salvar resposta do aluno no futuro

	});

	//copiar essa rota para os outros tipo de users e arrumar os links do master layout de cada um
	Route::get('perfil', function(){
		if(Session::has('mensagem')){
			$mensagem = Session::get('mensagem');
			return View::make('perfil')->with('mensagem', $mensagem);
		}
		return View::make('aluno/perfil');
		
	});

	//copiar essa rota para os outros tipo de users e arrumar os links do master layout de cada um
	Route::post('atualizaSenha', function(){

		$user = User::find(Input::get('id'));

		$user->password = Input::get('senha');
		$user->save();
		return Redirect::to('aluno/perfil')->with('mensagem', 'Sua senha foi alterada!');

	});

	//copiar essa rota para os outros tipo de users e arrumar os links do master layout de cada um
	Route::post('atualizaCadastro', function(){
		$user = User::find(Input::get('id'));
		$user->nome       = Input::get('nome');
		$user->sobrenome       = Input::get('sobrenome');
		$user->email       = Input::get('email');

		$imagem = Input::file('urlImagem');
		$filename="";

		if(Input::file('urlImagem')!=NULL){
			$filename = $imagem->getClientOriginalName();

			$user->urlImagem = 'img/'.$filename;
			
			$imagem->move('img/', $filename);
		}

		$user->save();

		$aluno= Aluno::find($user->id);
		$aluno->dataNascimento = Input::get('dataNascimento');
		$aluno->sobreMim = Input::get('sobreMim');

		$aluno->save();

		return Redirect::to('aluno/perfil')->with('mensagem', 'Os dados foram atualizados!');
	});

	Route::get('modulo/{id}', function($id){
		$modulo = Modulo::find($id);
		return View::make('modulo/alunoView')->with('modulo',$modulo);
	});

});

	

// ===============================================
// PROFESSOR SECTION =============================
// ===============================================
Route::group(array('prefix' => 'professor', 'before'=>'professor'), function(){

	Route::get('perfil', function(){
		if(Session::has('mensagem')){
			$mensagem = Session::get('mensagem');
			return View::make('professor/perfil')->with('mensagem', $mensagem);
		}
		return View::make('professor/perfil');
		
	});

	Route::get('mensagens/entrada', function(){
		$mensagens = Mensagem::where('idUsuarioDestino', '=', Auth::user()->id)->orderBy('id', 'desc')->paginate(10);
		return View::make('mensagem/professorInbox')->with('mensagens', $mensagens);
		
	});

	Route::get('mensagens/enviados', function(){
		$mensagens = Mensagem::where('idUsuarioOrgiem', '=', Auth::user()->id)->orderBy('id', 'desc')->paginate(10);
		return View::make('mensagem/professorEnviados')->with('mensagens', $mensagens);
		
	});

	Route::get('mensagem/{id}', function($id){
		$mensagem = Mensagem::find($id);
		if(strpos(URL::previous(), 'enviados') != false){

		}else{
			$mensagem->lida = 1;
		}
		
		$mensagem->save();
		return View::make('mensagem/professorShow')->with('mensagem', $mensagem);
		
	});

	Route::post('mensagem/enviar', function(){
		$mensagem = new Mensagem;
		$mensagem->idUsuarioOrgiem = Auth::user()->id;
		$mensagem->idUsuarioDestino = Input::get('idUsuarioDestino');
		$mensagem->titulo = Input::get('titulo');
		$mensagem->conteudo = Input::get('conteudo');
		$mensagem->data = date('d-m-Y H:i:s');

		$mensagem->lida = 0;
		$mensagem->save();

		return Redirect::back();
		
	});

	Route::post('mensagem/responder', function(){
		$mensagem = new Mensagem;
		$mensagem->idUsuarioOrgiem = Auth::user()->id;
		$mensagem->idUsuarioDestino = Input::get('idUsuarioDestino');
		$mensagem->titulo = Input::get('titulo');
		$mensagem->conteudo = Input::get('conteudo');
		$mensagem->data = date('d-m-Y H:i:s');
		$mensagem->idRE = Input::get('idRE');

		$mensagem->lida = 0;
		$mensagem->save();

		return Redirect::back();
		
	});

	Route::post('atualizaSenha', function(){

		$user = User::find(Input::get('id'));

		$user->password = Input::get('senha');
		$user->save();
		return Redirect::to('professor/perfil')->with('mensagem', 'Sua senha foi alterada!');

	});

	Route::post('atualizaCadastro', function(){
		$user = User::find(Input::get('id'));
		$user->nome       = Input::get('nome');
		$user->sobrenome       = Input::get('sobrenome');
		$user->email       = Input::get('email');
		$user->login = Input::get('codRegistro');

		$professor= Professor::find($user->id);

		$imagem = Input::file('urlImagem');
		$filename="";

		if(Input::file('urlImagem')!=NULL){
			$filename = $imagem->getClientOriginalName();

			$professor->urlImagem = 'img/'.$filename;

			$user->urlImagem = 'img/'.$filename;
			
			$imagem->move('img/', $filename);
		}

		$user->save();

		$professor->formacaoAcademica = Input::get('formacaoAcademica');
		$professor->sobreMim = Input::get('sobreMim');
		$professor->codRegistro       = Input::get('codRegistro');

		$professor->save();

		return Redirect::back()->with('mensagem', 'Os dados foram atualizados!');
	});

	Route::get('modulo/{id}', function($id){
		$modulo = Modulo::find($id);
		return View::make('modulo/alunoView')->with('modulo',$modulo);
	});

	Route::get('modulo/{id}', function($id){
		$modulo = Modulo::find($id);
		return View::make('modulo/professorView')->with('modulo',$modulo);
	});

	Route::get('modulo/{idModulo}/{idTurma}', function($idModulo, $idTurma){
		$modulo = Modulo::find($idModulo);
		$turma = Turma::find($idTurma);
		$alunos = $turma->alunos;
		return View::make('modulo/professorTurmaView')->with('modulo',$modulo)
													 ->with('turma', $turma)
													 ->with('alunos', $alunos);
	});

	Route::get('turma/{id}', function($id){
		$turma = Turma::find($id);
		$alunos = $turma->alunos;
		return View::make('turma/professorView')->with('turma',$turma)
									           ->with('alunos',$alunos);
	});

	Route::get('atividade/{id}', function($id){
		$atividade = Atividade::find($id);

		return View::make('atividade/professorView')->with('atividades',$atividade);
	});

	Route::get('atividade/{idAtividade}/{idTurma}', function($idAtividade, $idTurma){
		$atividade = Atividade::find($idAtividade);
		$turma = Turma::find($idTurma);
		$alunos = $turma->alunos;
		$questoes = $atividade->questoes->sortBy('numero');

		$i;
		$j;
		for ($i = 0; $i < sizeof($alunos); $i++) {
			$alunos[$i]->respostas = array();

			for ($j = 0; $j < sizeof($questoes); $j++) {
				if($questoes[$j]->tipo == "1"){
					$resposta = Resposta::where('idAluno', '=', $alunos[$i]->id )->where('idQuestao', '=', $questoes[$j]->numero)->pluck('respostaAluno');
					if($resposta == $questoes[$j]->respostaCerta){
						$r = $alunos[$i]->respostas;
						$r[] = "1";
						$alunos[$i]->respostas = $r;
					}else{
						$r = $alunos[$i]->respostas;
						$r[] = "0";
						$alunos[$i]->respostas = $r;
					}
				}else{
					$resposta = Resposta::where('idAluno', '=', $alunos[$i]->id )->where('idQuestao', '=', $questoes[$j]->numero)->pluck('respostaAluno');
					if($resposta == $questoes[$j]->respostaCerta){
						$r = $alunos[$i]->respostas;
						$r[] = "1";
						$alunos[$i]->respostas = $r;
					}else{
						$r = $alunos[$i]->respostas;
						$r[] = "0";
						$alunos[$i]->respostas = $r;
					}

				}
			}
		}

		$atividade = Atividade::find($idAtividade);			 

		return View::make('atividade/professorTurmaView')->with('atividade',$atividade)
															  ->with('turma',$turma)
									                          ->with('alunos',$alunos);
	});
	
	//Não será necessária, atividae já é atividade extra
	Route::get('professorVisualizarAtividadeExtra/{idExercicio}', function($idExercicio){
		$atividade = Atividade::find($idAtividade);
		$turmas = Turma::where('idProfessor', '=', '2')->get();
		foreach ($turmas as $turma) {
			$turma->alunosTurma = $turma->alunos;
		}
		$alunos;
		$questoes = $exercicio->questoesRU->combine($exercicio->questoesME)->sortBy('numero');

		$i;
		$j;
		$k;
		for ($k=0; $k < sizeof($turmas) ; $k++) {
			$alunos = $turmas[$k]->alunosTurma;
			for ($i = 0; $i < sizeof($alunos); $i++) {
				$alunos[$i]->respostas = array();

				for ($j = 0; $j < sizeof($questoes); $j++) {
					if(get_class($questoes[$j]) == "QuestaoMultiplaEscolha"){
						$resposta = RespostasMultiplaEscolha::where('idAluno', '=', $alunos[$i]->id )->where('idQuestao', '=', $questoes[$j]->numero)->pluck('respostaAluno');
						if($resposta == $questoes[$j]->respostaCerta){
							$r = $alunos[$i]->respostas;
							$r[] = "1";
							$alunos[$i]->respostas = $r;
						}else{
							$r = $alunos[$i]->respostas;
							$r[] = "0";
							$alunos[$i]->respostas = $r;
						}
					}else{
						$resposta = RespostasRespostaUnica::where('idAluno', '=', $alunos[$i]->id )->where('idQuestao', '=', $questoes[$j]->numero)->pluck('respostaAluno');
						if($resposta == $questoes[$j]->respostaCerta){
							$r = $alunos[$i]->respostas;
							$r[] = "1";
							$alunos[$i]->respostas = $r;
						}else{
							$r = $alunos[$i]->respostas;
							$r[] = "0";
							$alunos[$i]->respostas = $r;
						}

					}
				}
			}
			$turmas[$k]->alunosTurma = $alunos;
		}
			

		$exercicio = Atividade::find($idExercicio);			 

		return View::make('professorVisualizarAtividadeExtra')->with('exercicio',$exercicio)
															  ->with('turmas',$turmas)
															  ->with('turmasArray',$turmas->toArray());
	});

	Route::get('atividadesExtras', function(){
		$modulos = Modulo::all();

		$categorias = $modulos->combine(Categoria::all());

		$atividadesExtras = Atividade::where('tipo', '=', '2')->where('idUsuario', '=', Auth::user()->id)->get(); // trocar por Auth::User

		return View::make('atividade/atividadeExtraProfessor')->with('categorias', $categorias)
													  ->with('atividadesExtras', $atividadesExtras);
	});

	Route::get('home/{idioma?}', function($idioma = null){
		// fazer a busca com o idioma
		global $idioma;

		if($idioma !=null){
			global $idioma;
			$turmas = Turma::where('idProfessor', '=', Auth::user()->id)->get(); //!*## substituir por Auth::user (no caso o professor que acessou);
			$cursos = Curso::whereHas('turmas', function($q)
				{
					global $idioma;
					$q->where('idProfessor', '=', Auth::user()->id)->where('idIdioma', '=', Idioma::where('nome','=', $idioma)->first()); //Substituir por Auth::user depois
				})->get();

			$cursosArray = $cursos->toArray();

			return View::make('professor/home')->with(array('cursos'=>$cursos, 'cursosArray'=>$cursosArray, 'turmas'=>$turmas));
		}else{
			$turmas = Turma::where('idProfessor', '=', Auth::user()->id)->get(); //!*## substituir por Auth::user (no caso o professor que acessou);
			$cursos = Curso::whereHas('turmas', function($q)
				{
					$q->where('idProfessor', '=', Auth::user()->id); //Substituir por Auth::user depois
				})->get();

			$cursosArray = $cursos->toArray();

			return View::make('professor/home')->with(array('cursos'=>$cursos, 'cursosArray'=>$cursosArray, 'turmas'=>$turmas));
		}
		
	});

	Route::get('atividade/{id}/editar', function($id){
		$atividade = Atividade::find($id);

		return View::make('atividade/editarProfessorView')->with('atividade',$atividade);
	});

	Route::post('criarAtividadeExtra', function(){
		$atividadeExtra = new Atividade;
		$atividadeExtra->nome = Input::get('nome');
		$AtividadesExtra->tipo = 2;
		$idModulo = Input::get('idModulo');
		$idCategoria = Input::get('idCategoria');

		if($idModulo!=""){
			$atividadeExtra->idModulo = Input::get('idModulo');
		}
		if($idCategoria!=""){
			$atividadeExtra->idCategoria = Input::get('idCategoria');
		}

		$atividadeExtra->save();

		// redirect
		Session::flash('message', 'Atividade Extra criado com sucesso!');
		return Redirect::to('admin/atividadeExtra/'.$atividadeExtra->id.'/editar');
	});

	Route::post('atualizarAtividadeExtra', function(){
		$atividadeExtra = Atividade::find(Input::get('id'));
		$atividadeExtra->nome = Input::get('nome');
		$idModulo = Input::get('idModulo');
		$idCategoria = Input::get('idCategoria');

		if(isset($idModulo)){
			$atividadeExtra->idModulo = Input::get('idModulo');
		}
		if(isset($idCategoria)){
			$atividadeExtra->idCategoria = Input::get('idCategoria');
		}

		$atividadeExtra->save();

		// redirect
		Session::flash('message', 'Atividade Extra atualizada com sucesso!');
		return Redirect::back();
	});

	Route::post('alterarOrdem', function(){
		
		$atividade = Atividade::find(Input::get('0'));
		$key=1;
		//return Response::json(Input::all());


		$questoes = $atividade->questoes->sortBy('numero');

		//return Response::json(dd($numeros));

		foreach ($questoes as $questao) {
			//return Response::json(dd($numeros[Input::get($key)-1]));
			$questao->numero = Input::get($key);
			$questao->save();
			$key++;
		}

		return Response::json("alterado");

	});


});

// ===============================================
// ADMIN SECTION =================================
// ===============================================

Route::group(array('prefix' => 'admin', 'before'=>'admin'), function(){

	Route::get('home', function(){
		$cursos = Curso::all();
		$cursosArray = $cursos->toArray();
		return View::make('administrador/home')->with(array('cursos'=>$cursos, 'cursosArray'=>$cursosArray));
	});

	Route::get('perfil', function(){
		if(Session::has('mensagem')){
			$mensagem = Session::get('mensagem');
			return Redirect::to('admin/administrador/'.Auth::user()->id)->with('mensagem', $mensagem);
		}
		return Redirect::to('admin/administrador/'.Auth::user()->id);
		
	});

	Route::get('atividadesExtras', function(){
		$modulos = Modulo::all();

		$categorias = $modulos->combine(Categoria::all());

		$atividadesExtras = Atividade::where('tipo', '=', '2')->get();

		return View::make('atividade/atividadeExtraAdmin')->with('categorias', $categorias)
													  ->with('atividadesExtras', $atividadesExtras);
	});

	Route::post('atualizarCurso', function(){
		$Curso 			    = Curso::find(Input::get('id')); 
		$Curso->nome       	= Input::get('nome');
		$Curso->idIdioma      = Input::get('idioma');
		
		$Curso->save();

		Session::flash('message', "Alterações salvas com sucesso!");
		return Redirect::back();
	});

	Route::get('modulo/{id}', function($id){
		$modulo = Modulo::find($id);
		return View::make('modulo/adminView')->with('modulo',$modulo);
	});

	Route::get('turma/{id}', function($id){
		$turma = Turma::find($id);
		$alunos = $turma->alunos;
		return View::make('turma/adminView')->with('turma',$turma)
									  ->with('alunos',$alunos);
	});

	Route::get('atividade/{id}/editar', function($id){
		$atividade = Atividade::find($id);

		return View::make('atividade/editarAdmin')->with('atividade',$atividade);
	});

    Route::get('listarProfessores', function(){

		$data = array();

		$users = User::where('tipo', '=', 2)->get();
		//dd($users);
		foreach ($users as $key => $user) {
			$user->codRegistro = Professor::find($user->id)->codRegistro;
			$user->formacaoAcademica = Professor::find($user->id)->formacaoAcademica;
			$user->action = "<a href='professor/$user->id'><button style='margin-right: 5px;' class='btn btn-xs btn-primary'><i class='fa fa-user'></i></button></a><a href='professor/$user->id'><button style='margin-right: 5px;' class='btn btn-xs btn-success'><i class='fa fa-pencil'></i></button></a><button class='btn btn-xs btn-danger'><i class='fa fa-times'></i></button>";
			array_push($data, $user);
		}

		$response = array(
				"data" => $data,
				"iTotalRecords" => count($data),
				"iTotalDisplayRecords" => count($data)
			);
		return Response::json($response);
	});

	Route::get('listarAlunos', function(){

		$data = array();

		$users = User::where('tipo', '=', 1)->get();
		//dd($users);
		foreach ($users as $key => $user) {
			$user->matricula = Aluno::find($user->id)->matricula;
			$user->dataNascimento = Aluno::find($user->id)->dataNascimento;
			$user->action = "<a style='color:white;' href='aluno/$user->id'><button style='margin-right: 5px;' class='btn btn-xs btn-primary'><i class='fa fa-user'></i></buton></a><a href='aluno/$user->id'><button style='margin-right: 5px;' class='btn btn-xs btn-success'><i class='fa fa-pencil'></i></button></a><button class='btn btn-xs btn-danger'><i class='fa fa-times'></i></button>";
			array_push($data, $user);
		}
		//dd($data);



		$response = array(
				"data" => $data,
				"iTotalRecords" => count($data),
				"iTotalDisplayRecords" => count($data)
			);
		return Response::json($response);
	});

	Route::get('listarAdministradores', function(){

		$data = array();

		$users = User::where('tipo', '=', 3)->get();
		//dd($users);
		foreach ($users as $key => $user) {
			$user->codRegistro = Administrador::find($user->id)->codRegistro;
			$user->action = "<a style='color: white;' href='administrador/$user->id'><button style='margin-right: 5px;' class='btn btn-xs btn-primary'><i class='fa fa-user'></i></buton></a><a href='administrador/$user->id'><button style='margin-right: 5px;' class='btn btn-xs btn-success'><i class='fa fa-pencil'></i></button></a><button class='btn btn-xs btn-danger'><i class='fa fa-times'></i></button>";
			array_push($data, $user);
		}
		//dd($data);



		$response = array(
				"data" => $data,
				"iTotalRecords" => count($data),
				"iTotalDisplayRecords" => count($data)
			);
		return Response::json($response);
	});

	Route::get('administradores', function(){

		return View::make('administrador/showAdmin');
	});

	Route::get('alunos', function(){

		return View::make('aluno/showAdmin');
	});

	Route::get('professores', function(){

		return View::make('professor/showAdmin');
	});

	Route::get('professor/{id}', function($id){
		$mensagem =NULL;
		if(Session::has('mensagem')){
			$mensagem = Session::get('mensagem');
		}
		$professor = Professor::find($id);
		$user = User::find($id);
		$professor->nome = $user->nome;
		$professor->sobrenome = $user->sobrenome;
		$professor->password = $user->password;
		$professor->email = $user->email;
		$professor->respostaSecreta = $user->respostaSecreta;
		$professor->nome = $user->nome;
		return View::make('professor/editarAdmin')->with('professor', $professor)
											->with('mensagem', $mensagem);
		
	});

	Route::get('administrador/{id}', function($id){
		$mensagem =NULL;
		if(Session::has('mensagem')){
			$mensagem = Session::get('mensagem');
		}
		$administrador = Administrador::find($id);
		$user = User::find($id);
		$administrador->nome = $user->nome;
		$administrador->sobrenome = $user->sobrenome;
		$administrador->password = $user->password;
		$administrador->email = $user->email;
		$administrador->respostaSecreta = $user->respostaSecreta;
		$administrador->nome = $user->nome;
		return View::make('administrador/editarAdmin')->with('administrador', $administrador)
												->with('mensagem', $mensagem);
		
	});

	Route::get('aluno/{id}', function($id){
		$mensagem=NULL;
		if(Session::has('mensagem')){
			$mensagem = Session::get('mensagem');
		}
		$aluno = Aluno::find($id);
		$user = User::find($id);
		$aluno->nome = $user->nome;
		$aluno->sobrenome = $user->sobrenome;
		$aluno->password = $user->password;
		$aluno->email = $user->email;
		$aluno->respostaSecreta = $user->respostaSecreta;
		$aluno->nome = $user->nome;
		return View::make('aluno/editarAdmin')->with('aluno', $aluno)
										->with('mensagem', $mensagem);
		
	});

	// CRUD

		Route::post('criarCurso', function(){
			$Curso = new Curso;
			$Curso->nome = Input::get('nome');
			$Curso->idIdioma = Input::get('idioma');

			$Curso->save();
			Session::flash('message', "Curso criado com sucesso!");
			return Redirect::back();
		});

		Route::post('criarModulo', function(){
			$modulos = new Modulo;
			$modulos->nome = Input::get('nome');
			$modulos->idCurso = Input::get('idCurso');

			$modulos->save();

			// redirect
			Session::flash('message', 'Módulo criado com sucesso!');
			return Redirect::back();
		});

		Route::post('criarTurma', function(){
			$turma = new Turma;
			$turma->nome = Input::get('nome');
			$turma->idProfessor = Input::get('idprofessor');
			$turma->idModulo = Input::get('idModulo');

			$turma->save();

			// redirect
			Session::flash('message', 'Módulo criado com sucesso!');
			return Redirect::back();
		});

		Route::post('criarAula', function(){
			$Aula = new Aula;
			$Aula->titulo = Input::get('nome');
			$Aula->idModulo = Input::get('idModulo');

			$Aula->save();

			// redirect
			Session::flash('message', 'Aula criada com sucesso!');
			return Redirect::back();
		});

		Route::post('criarAtividade', function(){
			$Atividade = new Atividade;
			$Atividade->nome = Input::get('nome');
			$Atividade->idAula = Input::get('idAula');
			$Atividade->idUsuario = Auth::user()->id;

			$Atividade->save();

			// redirect
			Session::flash('message', 'Atividade criado com sucesso!');
			return Redirect::to('/admin/atividade/'.$Atividade->id.'/editar');
		});

		Route::post('criarMaterial', function(){
			$Material = new Materialapoio;
			$Material->nome = Input::get('nome');
			$Material->idAula = Input::get('idAula');

			$arquivo = Input::file('arquivo');
			$filename="";
			if($arquivo!=NULL){
				$filename = $arquivo->getClientOriginalName();

				$Material->url = 'files/'.$filename;
				
				$arquivo->move('files/', $filename);
			}

			$Material->save();

			// redirect
			Session::flash('message', 'Material criado com sucesso!');
			return Redirect::back();
		});

		Route::post('criarQuestaoRU', function(){
			$questao = new Questao;
			$pergunta = Input::get('pergunta');

			$questao->textopergunta = Input::get('textopergunta');

			if($pergunta!=1){
				$arquivo = Input::file('arquivo');
				$filename = $arquivo->getClientOriginalName();

				$questao->urlMidia = 'files/'.$filename;
				
				$arquivo->move('files/', $filename);
			}

	        $questao->idAtividade = Input::get('idatividade');

			$questao->categoria = Input::get('pergunta');

			$questao->tipo=2;

			$questao->respostaCerta = Input::get('respostaCerta');
			$questao->save();

			$questao->numero = $questao->id;
			$questao->save();

			Session::flash('message', 'Questao criada com sucesso!');
			return Redirect::back();

		});

		//Mudar
		Route::post('criarQuestaoME', function(){
			$questao = new Questao;
			$pergunta = Input::get('pergunta');
			$resposta = Input::get('resposta');

			$questao->textopergunta = Input::get('textopergunta');

			$questao->tipo=1;

			if($pergunta!=1){
				$arquivo = Input::file('arquivo');
				$filename = $arquivo->getClientOriginalName();

				$questao->urlMidia = 'files/'.$filename;
				
				$arquivo->move('files/', $filename);
			}

			if($resposta==1){

				$questao->alternativaA = Input::get('a');
				$questao->alternativaB = Input::get('b');
				$questao->alternativaC = Input::get('c');
				$questao->alternativaD = Input::get('d');

			}else{

				//#A
				
				$alternativaA = Input::file('a');
				$filenameA = $alternativaA->getClientOriginalName();
				$questao->alternativaA = 'files/'.$filenameA;
				$alternativaA->move('files/', $filenameA);

				//#B

				$alternativaB = Input::file('b');
				$filenameB = $alternativaB->getClientOriginalName();
				$questao->alternativaB = 'files/'.$filenameB;
				$alternativaB->move('files/', $filenameB);

				//C

				$alternativaC = Input::file('c');
				$filenameC = $alternativaC->getClientOriginalName();
				$questao->alternativaC = 'files/'.$filenameC;
				$alternativaC->move('files/', $filenameC);

				//#D
				$alternativaD = Input::file('d');
				$filenameD = $alternativaD->getClientOriginalName();
				$questao->alternativaD = 'files/'.$filenameD;
				$alternativaD->move('files/', $filenameD);
			}

			$questao->respostaCerta = Input::get('respostaCerta');
			$questao->idAtividade = Input::get('idatividade');

			$questao->categoria = (Input::get('pergunta')).(Input::get('resposta'));

			$questao->save();

			$questao->numero = $questao->id;
			$questao->save();

			// redirect
			Session::flash('message', 'Questao criada com sucesso!');
			return Redirect::back();
		});

		Route::post('atualizarRespostaUnica', function(){
			$questao 			    = Questao::find(Input::get('id')); 
			$pergunta = Input::get('pergunta');

			$questao->textopergunta = Input::get('textopergunta');

			if($pergunta!=1 && Input::file('arquivo')!= NULL){
				$arquivo = Input::file('arquivo');
				$filename = $arquivo->getClientOriginalName();

				$questao->urlMidia = 'files/'.$filename;
				
				$arquivo->move('files/', $filename);
			}

			$questao->categoria = $pergunta;

			$questao->respostaCerta = Input::get('respostaCerta');
			$questao->save();

			Session::flash('message', 'Questao atualizada com sucesso!');
			return Redirect::back();
		});

		Route::post('atualizarMultiplaEscolha', function(){
			$questao = Questao::find(Input::get('id')); 
			$pergunta = Input::get('pergunta');
			$resposta = Input::get('resposta');

			$questao->textopergunta = Input::get('textopergunta');

			if($pergunta!=1 && Input::file('arquivo')!= NULL){
				$arquivo = Input::file('arquivo');

				if($arquivo != null){
					$filename = $arquivo->getClientOriginalName();

					$questao->urlMidia = 'files/'.$filename;
					
					$arquivo->move('files/', $filename);
				}
			}

			if($resposta==1){
				if(Input::get('a')!=null)
					$questao->alternativaA = Input::get('a');

				if(Input::get('b')!=null)
					$questao->alternativaB = Input::get('b');

				if(Input::get('c')!=null)
					$questao->alternativaC = Input::get('c');

				if(Input::get('d')!=null)
					$questao->alternativaD = Input::get('d');

			}else{

				//#A
				
				$alternativaA = Input::file('a');
				if($alternativaA != NULL){
					$filenameA = $alternativaA->getClientOriginalName();
					$questao->alternativaA = 'files/'.$filenameA;
					$alternativaA->move('files/', $filenameA);
				}

				//#B

				$alternativaB = Input::file('b');
				if($alternativaB != NULL){
					$filenameB = $alternativaB->getClientOriginalName();
					$questao->alternativaB = 'files/'.$filenameB;
					$alternativaB->move('files/', $filenameB);
				}

				//C

				$alternativaC = Input::file('c');
				if($alternativaC != NULL){
					$filenameC = $alternativaC->getClientOriginalName();
					$questao->alternativaC = 'files/'.$filenameC;
					$alternativaC->move('files/', $filenameC);
				}

				//#D
				$alternativaD = Input::file('d');
				if($alternativaC != NULL){
					$filenameD = $alternativaD->getClientOriginalName();
					$questao->alternativaD = 'files/'.$filenameD;
					$alternativaD->move('files/', $filenameD);
				}
			}

			$questao->respostaCerta = Input::get('respostacerta');
			
			$questao->categoria = $pergunta. $resposta;

			$questao->save();

			// redirect
			Session::flash('message', 'Alterações salvas com sucesso!');
			return Redirect::back();
		});

		Route::post('atualizarModulo', function(){
			$Modulo 			    = Modulo::find(Input::get('id')); 
			$Modulo->nome       	= Input::get('nome');
			
			$Modulo->save();

			Session::flash('message', "Alterações salvas com sucesso!");
			return Redirect::back();
		});

		Route::post('atualizarTurma', function(){
			$Turma 			    = Turma::find(Input::get('id')); 
			$Turma->nome       	= Input::get('nome');
			
			$Turma->save();

			Session::flash('message', "Alterações salvas com sucesso!");
			return Redirect::back();
		});

		Route::post('atualizarAula', function(){
			$Aula 			    = Aula::find(Input::get('id')); 
			$Aula->titulo       	= Input::get('nome');
			
			$Aula->save();

			Session::flash('message', "Alterações salvas com sucesso!");
			return Redirect::back();
		});

		Route::post('atualizarAtividade', function(){
			$Atividade 			    = Atividade::find(Input::get('id')); 
			$Atividade->nome       	= Input::get('nome');
			
			$Atividade->save();

			Session::flash('message', "Alterações salvas com sucesso!");
			return Redirect::back();
		});

		Route::post('atualizarMaterial', function(){
			$Material 			    = MaterialApoio::find(Input::get('id')); 
			$Material->nome       	= Input::get('nome');

			$arquivo = Input::file('arquivo');

			if($arquivo!=NULL){
				$filename = $arquivo->getClientOriginalName();
				$Material->url = 'files/'.$filename;
				$arquivo->move('files/', $filename);

			}
			
			$Material->save();

			Session::flash('message', "Alterações salvas com sucesso!");
			return Redirect::back();
		});

		Route::post('matricularAluno', function(){
			$turma = Turma::find(Input::get('idTurma'));
			$aluno = Aluno::find(Input::get('idAluno'));

			$turma->alunos()->save($aluno);
			return Redirect::back();
		});

		Route::get('desmatricularAluno/{idAluno}/{idTurma}', function($idAluno, $idTurma){
			$turma = Turma::find($idTurma);
			$aluno = Aluno::find($idAluno);

			$turma->alunos()->detach($aluno);
			return Redirect::back();
		});

		Route::post('atualizarAluno', function(){
			$user = User::find(Input::get('id'));
			$user->nome       = Input::get('nome');
			$user->sobrenome       = Input::get('sobrenome');
			$user->email       = Input::get('email');
			$user->login = Input::get('matricula');

			$aluno= Aluno::find($user->id);

			$imagem = Input::file('urlImagem');
			$filename="";


			if(Input::file('urlImagem')!=NULL){
				$user->urlImagem = 'img/'.$filename;
				$filename = $imagem->getClientOriginalName();

				$aluno->urlImagem = 'img/'.$filename;
				
				$imagem->move('img/', $filename);
			}

			$user->save();

			$aluno->dataNascimento = Input::get('dataNascimento');
			$aluno->sobreMim = Input::get('sobreMim');
			$aluno->matricula       = Input::get('matricula');
			$aluno->dataVencimentoBoleto       = Input::get('dataVencimentoBoleto');

			$aluno->save();

			return Redirect::back()->with('mensagem', 'Os dados foram atualizados!');
		});

		Route::post('criarAluno', function(){
			$user = new User;
			$user->nome       = Input::get('nome');
			$user->sobrenome       = Input::get('sobrenome');
			$user->email       = Input::get('email');
			$user->login = Input::get('matricula');
			$user->password = Hash::make(Input::get('password'));
			$user->tipo = 1;

			$confirmation_code = str_random(30);
			foreach(User::all() as $u){
				if($u->confirmation_code = $confirmation_code){
					$confirmation_code = str_random(30);
				}
			}
			$user->confirmation_code = $confirmation_code;

			$imagem = Input::file('urlImagem');
			$filename="";

			if(Input::file('urlImagem')!=NULL){
				$user->urlImagem = 'img/'.$filename;
				$filename = $imagem->getClientOriginalName();
				
				$imagem->move('img/', $filename);
			}
			$user->save();


			$aluno = new Aluno;

			$aluno->id = $user->id;

			$aluno->dataNascimento = Input::get('dataNascimento');
			$aluno->sobreMim = Input::get('sobreMim');
			$aluno->matricula       = Input::get('matricula');
			$aluno->dataVencimentoBoleto       = Input::get('dataVencimentoBoleto');

			$aluno->save();

			Mail::send('templateEmail', array('confirmation_code' => $confirmation_code), function($message) {
	            $message->to(Input::get('email'), Input::get('nome'))
	                ->subject('KalanGO! - Verifique sua conta');
	        });

			return Redirect::back();
		});

		Route::post('atualizarProfessor', function(){
			$user = User::find(Input::get('id'));
			$user->nome       = Input::get('nome');
			$user->sobrenome       = Input::get('sobrenome');
			$user->email       = Input::get('email');
			$user->login = Input::get('codRegistro');

			$professor= Professor::find($user->id);

			$imagem = Input::file('urlImagem');
			$filename="";

			if(Input::file('urlImagem')!=NULL){
				$filename = $imagem->getClientOriginalName();

				$professor->urlImagem = 'img/'.$filename;

				$user->urlImagem = 'img/'.$filename;
				
				$imagem->move('img/', $filename);
			}

			$user->save();

			$professor->formacaoAcademica = Input::get('formacaoAcademica');
			$professor->sobreMim = Input::get('sobreMim');
			$professor->codRegistro       = Input::get('codRegistro');

			$professor->save();

			return Redirect::back()->with('mensagem', 'Os dados foram atualizados!');
		});

		Route::post('criarProfessor', function(){
			$user->nome       = Input::get('nome');
			$user->sobrenome       = Input::get('sobrenome');
			$user->email       = Input::get('email');
			$user->login = Input::get('codRegistro');

			$imagem = Input::file('urlImagem');
			$filename="";

			if(Input::file('urlImagem')!=NULL){
				$filename = $imagem->getClientOriginalName();

				$user->urlImagem = 'img/'.$filename;
				
				$imagem->move('img/', $filename);
			}

			$user->save();
			$professor->id= $user->id;
			$professor->urlImagem = $user->urlImagem;

			$professor->formacaoAcademica = Input::get('formacaoAcademica');
			$professor->sobreMim = Input::get('sobreMim');
			$professor->codRegistro       = Input::get('codRegistro');

			$professor->save();

			return Redirect::back();
		});

		Route::post('atualizarAdministrador', function(){
			$user = User::find(Input::get('id'));
			$user->nome       = Input::get('nome');
			$user->sobrenome       = Input::get('sobrenome');
			$user->email       = Input::get('email');
			$user->login = Input::get('codRegistro');

			$administrador= Administrador::find($user->id);

			$imagem = Input::file('urlImagem');
			$filename="";

			if(Input::file('urlImagem')!=NULL){
				$filename = $imagem->getClientOriginalName();
				$user->urlImagem = 'img/'.$filename;

				$administrador->urlImagem = 'img/'.$filename;
				
				$imagem->move('img/', $filename);
			}

			$user->save();

			$administrador= Administrador::find($user->id);
			$administrador->codRegistro = Input::get('codRegistro');

			$administrador->save();

			return Redirect::back()->with('mensagem', 'Os dados foram atualizados!');
		});

		Route::post('criarAdministrador', function(){
			$user->nome       = Input::get('nome');
			$user->sobrenome       = Input::get('sobrenome');
			$user->email       = Input::get('email');
			$user->login = Input::get('codRegistro');

			$imagem = Input::file('urlImagem');
			$filename="";

			if(Input::file('urlImagem')!=NULL){
				$filename = $imagem->getClientOriginalName();
				$user->urlImagem = 'img/'.$filename;
				
				$imagem->move('img/', $filename);
			}

			$user->save();
			$administrador= Administrador::find($user->id);
			$administrador->urlImagem = $user->urlImagem;

			$administrador->codRegistro = Input::get('codRegistro');

			$administrador->save();

			return Redirect::back();
		});

		Route::post('criarCategoria', function(){
			$categoria = new Categoria;
			$categoria->nome = Input::get('nome');

			$categoria->save();

			// redirect
			Session::flash('message', 'Categoria criada com sucesso!');
			return Redirect::back();
		});

		Route::post('atualizarCategoria', function(){
			$categoria = Categoria::find(Input::get('id'));
			$categoria->nome = Input::get('nome');

			$categoria->save();

			// redirect
			Session::flash('message', 'Alterações salvas com sucesso!');
			return Redirect::back();
		});

		Route::post('criarAtividadeExtra', function(){
			$atividadeExtra = new Atividade;
			$atividadeExtra->nome = Input::get('nome');
			$atividadeExtra->tipo = 2;
			$idModulo = Input::get('idModulo');
			$idCategoria = Input::get('idCategoria');
			$atividadeExtra->idUsuario = Auth::user()->id;

			if($idModulo!=""){
				$atividadeExtra->idModulo = Input::get('idModulo');
			}
			if($idCategoria!=""){
				$atividadeExtra->idCategoria = Input::get('idCategoria');
			}

			$atividadeExtra->save();

			// redirect
			Session::flash('message', 'Atividade Extra criado com sucesso!');
			return Redirect::to('admin/atividadeExtra/'.$atividadeExtra->id.'/editar');
		});

		Route::post('atualizarAtividadeExtra', function(){
			$atividadeExtra = Atividade::find(Input::get('id'));
			$atividadeExtra->nome = Input::get('nome');
			$idModulo = Input::get('idModulo');
			$idCategoria = Input::get('idCategoria');

			if(isset($idModulo)){
				$atividadeExtra->idModulo = Input::get('idModulo');
			}
			if(isset($idCategoria)){
				$atividadeExtra->idCategoria = Input::get('idCategoria');
			}

			$atividadeExtra->save();

			// redirect
			Session::flash('message', 'Atividade Extra atualizada com sucesso!');
			return Redirect::back();
		});

		Route::get('atividadeExtra/{id}/editar', function($id){
			$atividadeExtra = Atividade::find($id);

			return View::make('atividade/extraEditarAdmin')->with('atividade',$atividadeExtra);
		});

		Route::post('alterarOrdem', function(){
			
			$atividade = Atividade::find(Input::get('0'));
			$key=1;
			//return Response::json(Input::all());


			$questoes = $atividade->questoes->sortBy('numero');

			//return Response::json(dd($numeros));

			foreach ($questoes as $questao) {
				//return Response::json(dd($numeros[Input::get($key)-1]));
				$questao->numero = Input::get($key);
				$questao->save();
				$key++;
			}

			return Response::json("alterado");

		});

	// CRUD - FIM

	
});


// Crud Controllers - Admin pannel

Route::resource('acessos', 'AcessoController');
Route::resource('administradores', 'AdministradorController');
Route::resource('alunos', 'AlunoController');
Route::resource('atividadesextras', 'AtividadesExtraController');
Route::resource('aulas', 'AulaController');
Route::resource('categorias', 'CategoriaController');
Route::resource('cursos', 'CursoController');
Route::resource('exercicios', 'AtividadeController');
Route::resource('materialapoio', 'MaterialApoioController');
Route::resource('modulos', 'ModuloController');
Route::resource('professores', 'ProfessorController');
Route::resource('turmas', 'TurmaController');
Route::resource('turmasalunos', 'TurmasAlunoController');
Route::resource('usuarios', 'UserController');

 	

?>


