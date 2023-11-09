<template>
  <div>
    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col" v-for="(t, key) in titulos" :key="key">
            {{ t.titulo }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="obj, chave in dadosFiltrados" :key="chave">
          <td v-for="dado, chave1 in obj" :key="chave1">
            <span v-if="titulos[chave1].tipo == 'text'">{{dado}}</span>
            <span v-if="titulos[chave1].tipo == 'data'">{{'...'+dado}}</span>
            <span v-if="titulos[chave1].tipo == 'imagem'">
              <img :src="'/storage/'+dado" width="30" height="30">
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
export default {
  props: ["dados", "titulos"],
  computed: {
    dadosFiltrados(){
      let campos = Object.keys(this.titulos);
      let dadosFiltrados = [];

      this.dados.map((item, chave)=>{
        let itemFiltrado = {}
        campos.forEach(campo => {
          itemFiltrado[campo] = item[campo];
        });
        dadosFiltrados.push(itemFiltrado);
      });

      return dadosFiltrados;
    }
  }
};
</script>
