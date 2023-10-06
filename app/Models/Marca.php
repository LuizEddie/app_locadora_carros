<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

    public function rules(){
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpg,jpeg,gif'
        ];

        /*
            1 - tabela, 2 - nome da coluna, 3 - id do registro que será desconsiderado na pesquisa
        */
    }

    public function feedbacks(){
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'Esta marca ja está cadastrada',
            'imagem.mimes'=> 'O arquivo deve ser uma imagem do tipo png, jpg, jpeg ou gif'
        ];
    }
}
