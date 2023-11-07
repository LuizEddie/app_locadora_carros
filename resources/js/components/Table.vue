<template>
<div>
  <table class="table table-hover">
    <thead>
      <tr>
        <th scope="col"  v-for="t, key in titulos" :key="key">{{t}}</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="dado in dados" :key="dado.id">
        <th scope="row">{{dado.id}}</th>
        <td>{{dado.nome}}</td>
        <td>
          <img :src="'/storage/'+dado.imagem" width="30px" height="30px"></td>
      </tr>
    </tbody>
  </table>
  </div>
</template>
<script>
export default {
  props:['dados', 'titulos']
};
</script>
