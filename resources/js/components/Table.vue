<template>
  <div>
    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col" v-for="(t, key) in titulos" :key="key">
            {{ t.titulo }}
          </th>
          <th v-if="visualizar.visivel || atualizar.visivel || remover.visivel"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="obj, chave in dadosFiltrados" :key="chave">
          <td v-for="dado, chave1 in obj" :key="chave1">
            <span v-if="titulos[chave1].tipo == 'text'">{{dado}}</span>
            <span v-if="titulos[chave1].tipo == 'data'">{{dado | formataDataHoraGlobal}}</span>
            <span v-if="titulos[chave1].tipo == 'imagem'">
              <img :src="'/storage/'+dado" width="30" height="30">
            </span>
          </td>
          <td>
            <button class="btn btn-outline-primary btn-sm" v-if="visualizar.visivel" :data-toggle="visualizar.dataToggle" :data-target="visualizar.dataTarget" @click="setStore(obj)">Visualizar</button>
            <button class="btn btn-outline-success btn-sm" v-if="atualizar.visivel" :data-toggle="atualizar.dataToggle" :data-target="atualizar.dataTarget" @click="setStore(obj)">Atualizar</button>
            <button class="btn btn-outline-danger btn-sm" v-if="remover.visivel" :data-toggle="remover.dataToggle" :data-target="remover.dataTarget" @click="setStore(obj)">Excluir</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
export default {
  
  filters:{
    // formataDataHora(d){
    //   if(!d){
    //     return ''
    //   }

    //   d = d.split("T");
    //   let data = d[0].split("-");
    //   let hora = d[1].split(".")[0];
      
    //   return `${data[2]}/${data[1]}/${data[0]} ${hora}`;
    // }
  },
  props: ["dados", "titulos", 'visualizar', 'atualizar', 'remover'],
  methods: {
    setStore(obj){
      this.$store.state.item = obj;
      this.$store.state.transacao.status = '';
      this.$store.state.transacao.mensagem = '';
      this.$store.state.transacao.dados= '';
    }
  },
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
