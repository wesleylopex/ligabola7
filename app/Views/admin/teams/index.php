<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?php $this->load->view('admin/components/head', $this->data) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= assets('website/images/default-bg.png') ?>)">
  <main id="app" class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('admin') ?>">
        <button class="p-2 rounded-full bg-gray-200" data-tippy-content="Voltar">
          <i class="w-5 h-5 text-gray-500" data-feather="arrow-left"></i>
        </button>
      </a>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">Times</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <div class="lg:flex lg:justify-between">
          <div class="lg:order-2 flex justify-end">
            <div>
              <a href="<?= base_url('admin/teams/create') ?>">
                <button data-tippy-content="Novo time" class="p-2 rounded-full bg-blue-600 text-white">
                  <i class="w-4 h-4" data-feather="plus"></i>
                </button>
              </a>
            </div>
          </div>
          <div>
            <button @click="clearFilters()" class="text-blue-600 text-xs">Limpar filtros</button>
            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
              <div class="flex items-center px-2 rounded-full bg-gray-100">
                <i class="w-5 h-5 text-gray-600" data-feather="search"></i>
                <input type="text" v-model="search" placeholder="Digite para pesquisar" class="text-gray-600 text-sm outline-none p-2 bg-transparent w-full">
                <button data-tippy-content="Limpar filtro" @click="search = ''" class="p-1 rounded-full bg-gray-200 text-gray-600">
                  <i class="w-4 h-4" data-feather="x"></i>
                </button>
              </div>
              <select v-model="divisionId" class="p-2 px-3 text-sm rounded-full bg-gray-100 text-gray-600">
                <option value="">Filtrar por divisão</option>
                <option v-for="division in divisions" :value="division.id">{{ division.name }}</option>
              </select>
            </div>
          </div>
        </div>
        <table class="mt-14 block sm:table overflow-x-auto w-full text-left font-normal text-[#4A4251] text-sm">
          <thead>
            <tr>
              <th class="font-bold p-3">Nome</th>
              <th class="font-bold p-3">Nome de usuário</th>
              <th class="font-bold p-3">Divisão</th>
              <th class="font-bold p-3">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="team in teams" v-show="team.isVisible" class="even:bg-white odd:bg-gray-100">
              <td class="p-3">{{ team.name }}</td>
              <td class="p-3">{{ team.username }}</td>
              <td class="p-3">{{ team.division_name }}</td>
              <td class="p-3 flex items-center">
                <a :href="`${baseURL}admin/teams/update/${team.id}`">
                  <button data-tippy-content="Clique para editar" class="rounded-full p-2 font-medium hover:bg-gray-200">
                    <i class="w-4 h-4" data-feather="edit"></i>
                  </button>
                </a>
                <button @click="openDeleteConfirmationModal(team.id)" data-tippy-content="Clique para excluir" class="rounded-full p-2 font-medium hover:bg-gray-200">
                  <i class="w-4 h-4" data-feather="trash"></i>
                </button>
              </td>
            </tr>
            <tr v-show="teams.every(team => !team.isVisible)" class="bg-gray-100">
              <td class="p-3 text-center font-medium" colspan="4">Nenhum time</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
    <?php $this->load->view('admin/components/delete-confirmation-modal', $this->data) ?>
  </main>

  <?php $this->load->view('admin/components/scripts', $this->data) ?>

  <!-- Vue JS -->
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      teams: <?= json_encode($teams ?? []) ?>,
      divisions: <?= json_encode($divisions ?? []) ?>
    }

    Vue.createApp({
      data () {
        return {
          baseURL: pageData.baseURL,
          teams: pageData.teams,
          divisions: pageData.divisions,
          search: '',
          divisionId: '',
        }
      },
      methods: {
        async deleteRecord () {
          const form = document.querySelector('form#delete-confirmation')
          const body = new FormData(form)

          const response = await fetch(form.action, {
            method: 'POST',
            body
          }).then(response => response.json())

          if (response.success !== true) {
            return showNotify(response.error)
          }

          const index = this.teams.findIndex(team => Number(team.id) === Number(response.teamId))
          this.teams.splice(index, 1)

          closeDeleteConfirmationModal()
          showNotify('Time excluído com sucesso')
        },
        openDeleteConfirmationModal (memberId) {
          const action = `${this.baseURL}admin/teams/delete/${memberId}`
          openDeleteConfirmationModal(action)
        },
        runSearch () {
          const searchWords = this.search.toLowerCase().split(' ')
          const propsToCompare = ['name', 'username', 'division_name']

          this.teams.forEach(team => {
            let queryMatch = false

            for (prop in team) {
              if (!propsToCompare.includes(prop)) {
                continue
              }

              const propValue = String(team[prop]).toLowerCase()

              for (word in searchWords) {
                if (!searchWords[word]) {
                  continue
                }

                if (propValue.includes(searchWords[word])) {
                  queryMatch = true
                  break
                }
              }

              if (queryMatch) {
                break
              }
            }

            const queryIsValidAndMatch = !this.search || queryMatch
            const divisionIdIsValidAndMatch = !this.divisionId || Number(team.division_id) === Number(this.divisionId)

            const isVisible = queryIsValidAndMatch && divisionIdIsValidAndMatch

            team.isVisible = isVisible
          })
        },
        clearFilters () {
          this.search = ''
          this.divisionId = ''
        },
      },
      watch: {
        search () {
          this.runSearch()
        },
        divisionId () {
          this.runSearch()
        }
      },
      mounted () {
        for (team in this.teams) {
          this.teams[team].isVisible = true
        }
      }
    }).mount('#app')
  </script>
</body>
</html>