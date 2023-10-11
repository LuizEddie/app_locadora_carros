<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
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
        $marcas = array();

        if($request->has('atributos_modelos')){
            $atributos_modelos = $request->atributos_modelos;
            $marcas = $this->marca->with("modelos:id,".$atributos_modelos);
        }else{
            $marcas = $this->marca->with('modelos');
        }

        if($request->has("filtro")){
            $filtros = explode(";", $request->filtro);
            foreach($filtros as $key => $condicao){

                $c = explode(":", $condicao);
                $marcas = $marcas->where($c[0], $c[1], $c[2]);
            }   
        }

        if($request->has("atributos")){
            $atributos = $request->atributos;
            $marcas = $marcas->selectRaw($atributos)->get();
        }else{
            $marcas = $marcas->get();
        }

        // $marcas = $this->marca->with('modelos')->get();
        return response()->json($marcas, 200);
        // return Marca::all();
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

        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);
        }

        $img = $request->imagem;
        $urn = $img->store('imagens', 'public');

        $marca->fill($request->all());
        $marca->imagem = $urn;

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
