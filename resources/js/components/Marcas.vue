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
                    v-model="busca.id"
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
                    type="text"
                    class="form-control"
                    id="inputNome"
                    aria-describedby="nomeHelp"
                    placeholder="Opcional. Informe o nome da marca"
                    v-model="busca.nome"
                  />
                </input-container-component>
              </div>
            </div>
          </template>
          <template v-slot:rodape>
            <button
              type="submit"
              class="btn btn-primary btn-sm float-right"
              @click="pesquisar()"
            >
              Pesquisar
            </button>
          </template>
        </card-component>
        <!--Fim-->
        <!-- Inicio do card de listagem -->

        <card-component titulo="Relação de marcas">
          <template v-slot:conteudo>
            <table-component
              :dados="marcas.data"
              :visualizar="{
                visivel: true,
                dataToggle: 'modal',
                dataTarget: '#modalMarcaVisualizar',
              }"
              :atualizar="{
                visivel: true,
                dataToggle: 'modal',
                dataTarget: '#modalMarcaAtualizar',
              }"
              :remover="{
                visivel: true,
                dataToggle: 'modal',
                dataTarget: '#modalMarcaRemover',
              }"
              :titulos="{
                id: { titulo: 'ID', tipo: 'text' },
                created_at: { titulo: 'Criado em', tipo: 'data' },
                nome: { titulo: 'Nome', tipo: 'text' },
                imagem: { titulo: 'Imagem', tipo: 'imagem' },
              }"
            ></table-component>
          </template>
          <template v-slot:rodape>
            <div class="row">
              <div class="col-10">
                <paginate-component>
                  <li
                    v-for="(l, key) in marcas.links"
                    :key="key"
                    :class="l.active ? 'page-item active' : 'page-item'"
                    @click="paginacao(l)"
                  >
                    <a class="page-link" v-html="l.label"></a>
                  </li>
                </paginate-component>
              </div>
              <div class="col-2">
                <button
                  type="button"
                  class="btn btn-primary btn-sm float-right"
                  data-toggle="modal"
                  data-target="#modalMarca"
                >
                  Adicionar
                </button>
              </div>
            </div>
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

    <!--Modal-->
    <modal-component id="modalMarcaVisualizar" titulo="Visualizar Marca">
      <template v-slot:alertas> </template>
      <template v-slot:conteudo>
        <input-container-component titulo="ID">
          <input
            type="text"
            class="form-control"
            :value="$store.state.item.id"
            readonly
          />
        </input-container-component>
        <input-container-component titulo="Nome">
          <input
            type="text"
            class="form-control"
            :value="$store.state.item.nome"
            readonly
          />
        </input-container-component>
        <input-container-component titulo="Imagem">
          <img
            :src="'storage/' + $store.state.item.imagem"
            width="120"
            height="120"
            v-if="$store.state.item.imagem"
          />
        </input-container-component>
        <input-container-component titulo="Data de Criação">
          <input
            type="text"
            class="form-control"
            :value="$store.state.item.created_at"
            readonly
          />
        </input-container-component>
      </template>
      <template v-slot:rodape>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Fechar
        </button>
      </template>
    </modal-component>
    <!--Fim-->

    <!--Modal-->
    <modal-component id="modalMarcaRemover" titulo="Remover Marca">
      <template v-slot:alertas>
        <alert-component
          tipo="success"
          titulo="Sucesso"
          :detalhes="$store.state.transacao"
          v-if="$store.state.transacao.status == 'sucesso'"
        ></alert-component>
        <alert-component
          tipo="danger"
          titulo="Erro"
          :detalhes="$store.state.transacao"
          v-if="$store.state.transacao.status == 'erro'"
        ></alert-component>
      </template>
      <template
        v-slot:conteudo
        v-if="$store.state.transacao.status != 'sucesso'"
      >
        <input-container-component titulo="ID">
          <input
            type="text"
            class="form-control"
            :value="$store.state.item.id"
            readonly
          />
        </input-container-component>
        <input-container-component titulo="Nome">
          <input
            type="text"
            class="form-control"
            :value="$store.state.item.nome"
            readonly
          />
        </input-container-component>
      </template>
      <template v-slot:rodape>
        <button
          type="button"
          class="btn btn-danger"
          @click="remover()"
          v-if="$store.state.transacao.status != 'sucesso'"
        >
          Remover
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Fechar
        </button>
      </template>
    </modal-component>
    <!--Fim-->

    <!--Modal-->
    <modal-component id="modalMarcaAtualizar" titulo="Atualizar Marca">
       <template v-slot:alertas>
        <alert-component
          tipo="success"
          titulo="Sucesso"
          :detalhes="$store.state.transacao"
          v-if="$store.state.transacao.status == 'sucesso'"
        ></alert-component>
        <alert-component
          tipo="danger"
          titulo="Erro"
          :detalhes="$store.state.transacao"
          v-if="$store.state.transacao.status == 'erro'"
        ></alert-component>
      </template>
      <template v-slot:conteudo>
        <div class="form-group">
          <input-container-component
            titulo="Nome da Marca"
            id="atualizarNome"
            id-help="atualizarNomeHelp"
            texto-ajuda="Digite o nome da marca"
          >
            <input
              type="text"
              class="form-control"
              id="atualizarNome"
              aria-describedby="atualizarNomeHelp"
              placeholder="Nome da Marca"
              required
              v-model="$store.state.item.nome"
            />
          </input-container-component>
        </div>
        <div class="form-group">
          <input-container-component
            titulo="Imagem"
            id="atualizarImagem"
            id-help="atualizarImagemHelp"
            texto-ajuda="Faça o upload do logo da marca"
          >
            <input
              type="file"
              class="form-control-file"
              id="atualizarImagem"
              aria-describedby="atualizarImagemHelp"
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
        <button type="button" class="btn btn-primary" @click="atualizar()">
          Atualizar
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
      urlPaginacao: "",
      urlFiltro: "",
      nomeMarca: "",
      arquivoImagem: [],
      transacaoStatus: "",
      transacaoDetalhes: {},
      marcas: { data: [] },
      busca: {
        id: "",
        nome: "",
      },
    };
  },
  // computed: {
  //   token() {
  //     let token = document.cookie
  //       .split(";")
  //       .find((indices) => {
  //         return indices.includes("token=");
  //       })
  //       .replace("token=", "Bearer ");
  //     return token;
  //   },
  // },
  methods: {
    remover() {
      let confirmacao = confirm(
        "Tem certeza que deseja remover este registro?"
      );

      if (!confirmacao) {
        return false;
      }
      let url = this.urlBase + "/" + this.$store.state.item.id;

      let formData = new FormData();
      formData.append("_method", "delete");

      axios
        .post(url, formData)
        .then((response) => {
          this.$store.state.transacao.status = "sucesso";
          this.$store.state.transacao.mensagem = response.data.msg;
          this.carregarLista();
        })
        .catch((error) => {
          this.$store.state.transacao.status = "erro";
          this.$store.state.transacao.mensagem = error.response.data.erro;
        });
    },

    pesquisar() {
      let filtro = "";

      for (let chave in this.busca) {
        if (this.busca[chave]) {
          if (filtro != "") {
            filtro += ";";
          }
          filtro += chave + ":like:" + this.busca[chave];
        }
      }

      if (filtro != "") {
        this.urlFiltro = "&filtro=" + filtro;
        this.urlPaginacao = "page=1";
      } else {
        this.urlFiltro = "";
      }
      this.carregarLista();
    },

    paginacao(l) {
      if (l.url) {
        this.urlPaginacao = l.url.split("?")[1];
        this.carregarLista();
      }
    },

    carregarLista() {
      let url = this.urlBase + "?" + this.urlPaginacao + this.urlFiltro;

      axios
        .get(url)
        .then((response) => {
          this.marcas = response.data;
        })
        .catch((errors) => {
          console.log(errors);
        });
    },

    carregarImagem(e) {
      this.arquivoImagem = e.target.files;
    },

    salvar() {
      let formData = new FormData();

      formData.append("nome", this.nomeMarca);
      formData.append("imagem", this.arquivoImagem[0]);

      let config = {
        headers: {
          "Content-Type": "multipart/form-data",
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

    atualizar() {
      let formData = new FormData();
      formData.append("_method", "patch");
      formData.append("nome", this.$store.state.item.nome);

      if(this.arquivoImagem[0]){
        formData.append("imagem", this.arquivoImagem[0]);
      }

      let config = {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      };

      let url = this.urlBase + "/" + this.$store.state.item.id;

      axios.post(url, formData, config).then(response=>{
        this.$store.state.transacao.status = 'sucesso';
        this.$store.state.transacao.mensagem = "Registro Atualizado com sucesso!";
        atualizarImagem.value = "";
        this.carregarLista();
      }).catch(errors=>{
        this.$store.state.transacao.status = 'erro';
        this.$store.state.transacao.mensagem = errors.response.data.message;
        this.$store.state.transacao.dados = errors.response.data.errors;
      })
    },
  },
  mounted() {
    this.carregarLista();
  },
};
</script>