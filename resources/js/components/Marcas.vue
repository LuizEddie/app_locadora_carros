<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <!-- Inicio do card de busca -->
        <card-component titulo="Busca de Marcas">
          <template v-slot:conteudo>
            <div class="form-row">
              <div class="col mb-3">
                <input-container-component
                  titulo="ID"
                  id="inputId"
                  id-help="idHelp"
                  texto-ajuda="Opcional. Informe o ID da marca"
                >
                  <input
                    type="number"
                    class="form-control"
                    id="inputId"
                    aria-describedby="idHelp"
                    placeholder="ID"
                  />
                </input-container-component>
              </div>
              <div class="col mb-3">
                <input-container-component
                  titulo="Nome da Marca"
                  id="inputNome"
                  id-help="nomeHelp"
                  texto-ajuda="Opcional. Informe o nome da marca"
                >
                  <input
                    type="number"
                    class="form-control"
                    id="inputNome"
                    aria-describedby="nomeHelp"
                    placeholder="Informe o nome da marca"
                  />
                </input-container-component>
              </div>
            </div>
          </template>
          <template v-slot:rodape>
            <button type="submit" class="btn btn-primary btn-sm float-right">
              Pesquisar
            </button>
          </template>
        </card-component>
        <!--Fim-->
        <!-- Inicio do card de listagem -->

        <card-component titulo="Relação de marcas">
          <template v-slot:conteudo>
            <table-component :dados="marcas.data" :titulos="{
              id: {titulo: 'ID', tipo: 'text'},
              created_at: {titulo: 'Criado em', tipo: 'data'},
              nome: {titulo: 'Nome', tipo: 'text'},
              imagem: {titulo: 'Imagem', tipo: 'imagem'}
            }"></table-component>
          </template>
          <template v-slot:rodape>
            <button
              type="button"
              class="btn btn-primary btn-sm float-right"
              data-toggle="modal"
              data-target="#modalMarca"
            >
              Adicionar
            </button>
          </template>
        </card-component>
        <!--Fim-->
      </div>
    </div>

    <!--Modal-->
    <modal-component id="modalMarca" titulo="Adicionar Marca">
      <template v-slot:alertas>
        <alert-component
          tipo="success"
          :detalhes="transacaoDetalhes"
          titulo="Sucesso no Cadastro"
          v-if="transacaoStatus == 'adicionado'"
        ></alert-component>
        <alert-component
          tipo="danger"
          :detalhes="transacaoDetalhes"
          titulo="Erro no Cadastro"
          v-if="transacaoStatus == 'erro'"
        ></alert-component>
      </template>
      <template v-slot:conteudo>
        <div class="form-group">
          <input-container-component
            titulo="Nome da Marca"
            id="novoNome"
            id-help="novoNomeHelp"
            texto-ajuda="Digite o nome da marca"
          >
            <input
              type="text"
              class="form-control"
              id="novoNome"
              aria-describedby="novoNomeHelp"
              placeholder="Nome da Marca"
              required
              v-model="nomeMarca"
            />
          </input-container-component>
        </div>
        <div class="form-group">
          <input-container-component
            titulo="Imagem"
            id="novoImagem"
            id-help="novoImagemHelp"
            texto-ajuda="Faça o upload do logo da marca"
          >
            <input
              type="file"
              class="form-control-file"
              id="novoImagem"
              aria-describedby="novoImagemHelp"
              placeholder="Imagem da Marca"
              required
              @change="carregarImagem($event)"
            />
          </input-container-component>
        </div>
      </template>
      <template v-slot:rodape>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Fechar
        </button>
        <button type="button" class="btn btn-primary" @click="salvar()">
          Salvar
        </button>
      </template>
    </modal-component>
    <!--Fim-->
  </div>
</template>

<script>
export default {
  data() {
    return {
      urlBase: "http://localhost:8000/api/v1/marca",
      nomeMarca: "",
      arquivoImagem: [],
      transacaoStatus: "",
      transacaoDetalhes: {},
      marcas: []
    };
  },
  computed: {
    token() {
      let token = document.cookie
        .split(";")
        .find((indices) => {
          return indices.includes("token=");
        })
        .replace("token=", "Bearer ");
      return token;
    },
  },
  methods: {
    carregarLista() {
      let config = {
        headers: {
          Accept: "application/json",
          Authorization: this.token,
        },
      };

      axios
        .get(this.urlBase, config)
        .then((response) => {
          this.marcas = response.data
          console.log(this.marcas);
        })
        .catch((errors) => {
          console.log(errors);
        });
    },

    carregarImagem(e) {
      this.arquivoImagem = e.target.files;
    },

    salvar() {
      console.log(this.nomeMarca, this.arquivoImagem[0]);

      let formData = new FormData();

      formData.append("nome", this.nomeMarca);
      formData.append("imagem", this.arquivoImagem[0]);

      let config = {
        headers: {
          "Content-Type": "multipart/form-data",
          Accept: "application/json",
          Authorization: this.token,
        },
      };

      axios
        .post(this.urlBase, formData, config)
        .then((response) => {
          this.transacaoStatus = "adicionado";
          this.transacaoDetalhes = {
            mensagem: "ID do registro " + response.data.id,
          };
          console.log(response);
        })
        .catch((errors) => {
          this.transacaoStatus = "erro";
          this.transacaoDetalhes = {
            mensagem: errors.response.data.message,
            dados: errors.response.data.errors,
          };
        });
    },
  },
  mounted(){
    this.carregarLista();
  }
};
</script>