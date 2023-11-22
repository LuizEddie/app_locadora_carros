<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class MarcaController extends Controller
{

    private $marca;
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   

        $marcaRepository = new MarcaRepository($this->marca);

        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        }else{
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }


        if($request->has("filtro")){
            $marcaRepository->filtro($request->filtro);
        }


        if($request->has("atributos")){
            $atributos = $request->atributos;

            $marcaRepository->selectAtributos($atributos);
        }

        $marcas = $marcaRepository->getResultadoPaginado(3);
       
        return response()->json($marcas, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $marca = Marca::create($request->all());
   

        $regras = $this->marca->rules();

        $feedback = $this->marca->feedbacks();

        $request->validate($regras, $feedback);

        $img = $request->imagem;
        $urn = $img->store('imagens', 'public');
        
        $marca = $this->marca->create(['nome'=>$request->nome, 'imagem'=>$urn]);
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $marca = $this->marca->with('modelos')->find($id);

        if($marca === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);

        if($marca === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe, não é possivel atualiza-lo'], 404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();

            //percorrer todas as regras definidas no model
            foreach($marca->rules() as $input => $regra){
                //coletar apenas as regras aplicaveis
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $marca->feedbacks());
        }else{
            $request->validate($marca->rules(), $marca->feedbacks());
        }

        $marca->fill($request->all());

        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);

            $imagem = $request->file('imagem');
            $img_urn = $imagem->store("imagens", "public");

            $marca->imagem = $img_urn;
        }

        $marca->save();

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe, não é possivel deleta-lo'], 404);
        }

        Storage::disk('public')->delete($marca->imagem);

        $marca->delete($id);
        return response()->json(['msg'=>'A marca foi removida com sucesso!'], 200);
    }
}
