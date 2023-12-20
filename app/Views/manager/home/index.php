<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('manager/components/head', ['meta' => ['title' => 'Home']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('manager/login/logout') ?>">
        <button class="p-2 rounded-full bg-gray-100" data-tippy-content="Sair da conta">
          <i class="w-4 h-4 text-gray-500 transform rotate-180" data-feather="log-out"></i>
        </button>
      </a>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">Membros do <?= $currentTeam->name ?></h1>
      <div class="p-6 rounded-md bg-white shadow-md max-w-md mt-10">
        <p class="text-sm text-gray-500 leading-normal max-w-sm">
          Warning
        </p>
      </div>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <div class="w-full flex justify-between items-center">
          <div class="flex items-center px-2 rounded-full max-w-sm w-full bg-gray-100">
            <i class="w-5 h-5 text-gray-600" data-feather="search"></i>
            <input type="text" v-model="search" placeholder="Digite para pesquisar" class="text-sm outline-none p-2 bg-transparent w-full">
            <button data-tippy-content="Limpar filtro" @click="search = ''" class="p-1 rounded-full bg-gray-200 text-gray-600">
              <i class="w-4 h-4 flex-shrink-0" data-feather="x"></i>
            </button>
          </div>
          <a href="<?= base_url('manager/members/create') ?>">
            <button data-tippy-content="Novo membro" class="ml-2 p-2 rounded-full bg-blue-600 text-white">
              <i class="w-4 h-4" data-feather="plus"></i>
            </button>
          </a>
        </div>
        <div class="mt-14 grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-4">
          <div class="bg-gray-50 p-4 rounded-md">
            <h2 class="text-sm">Atletas aprovados</h2>
            <p class="mt-2 font-bold text-green-500">10</p>
          </div>
          <div class="bg-gray-50 p-4 rounded-md">
            <h2 class="text-sm">Atletas pendentes</h2>
            <p class="mt-2 font-bold text-orange-400">10</p>
          </div>
          <div class="bg-gray-50 p-4 rounded-md">
            <h2 class="text-sm">Atletas reprovados</h2>
            <p class="mt-2 font-bold text-red-500">10</p>
          </div>
        </div>
        <table class="mt-14 block md:table overflow-x-auto w-full text-left font-normal text-[#4A4251] text-sm">
          <thead>
            <tr>
              <th class="font-bold p-3">#</th>
              <th class="font-bold p-3">Nome</th>
              <th class="font-bold p-3">Número de inscrição</th>
              <th class="font-bold p-3">CPF</th>
              <th class="font-bold p-3">Data de nascimento</th>
              <th class="font-bold p-3">Tipo</th>
              <th class="font-bold p-3">Status</th>
              <th class="font-bold p-3">Descrição do status</th>
              <th v-show="members.some(member => member.status === 'denied')" class="font-bold p-3">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(member, index) in members" v-show="member.isVisible" class="even:bg-white odd:bg-gray-100">
              <td class="p-3">{{ (index + 1) }}</td>
              <td class="p-3">{{ member.name }}</td>
              <td class="p-3">{{ member.subscription_number }}</td>
              <td class="p-3">{{ member.cpf }}</td>
              <td class="p-3">{{ getFormattedDate(member.birth_date) }}</td>
              <td class="p-3">{{ getTranslatedRole(member.role) }}</td>
              <td class="p-3">
                <span class="py-1 px-2 rounded-full text-xs font-semibold" :class="getStatusColor(member.status)">{{ getTranslatedStatus(member.status) }}</span>
              </td>
              <td class="p-3">{{ getStatusDescription(member.id) }}</td>
              <td v-show="members.some(member => member.status === 'denied')" class="p-3 flex items-center">
                <a v-if="member.status === 'denied'" :href="`${baseURL}manager/members/update/${member.id}`">
                  <button data-tippy-content="Clique para editar" class="rounded-full p-2 font-medium hover:bg-gray-200">
                    <i class="w-4 h-4" data-feather="edit"></i>
                  </button>
                </a>
              </td>
            </tr>
            <tr v-show="members.length === 0" class="even:bg-white odd:bg-gray-100">
              <td class="p-3 text-center font-medium" colspan="100%">Nenhum membro cadastrado até o momento</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <!-- Scripts -->
  <?= view('manager/components/scripts') ?>

  <script>
    const homePageData = {
      baseURL: '<?= base_url() ?>',
      members: <?= json_encode($members) ?>
    }

    Vue.createApp({
      data () {
        return {
          ...homePageData,
          search: ''
        }
      },
      methods: {
        getFormattedDate (date) {
          const [year, month, day] = date.split('-')
          return `${day}/${month}/${year}`
        },
        getTranslatedRole (role) {
          const roles = {
            athlete: 'Atleta',
            coach: 'Treinador',
            assistant: 'Auxiliar',
            president: 'Presidente / Representante legal'
          }

          return roles[role]
        },
        getTranslatedStatus (status) {
          const statuses = {
            approved: 'Aprovado',
            pending: 'Pendente',
            denied: 'Reprovado'
          }

          return statuses[status]
        },
        getStatusColor (status) {
          const colors = {
            pending: 'bg-orange-200 text-yellow-800',
            approved: 'bg-green-200 text-green-800',
            denied: 'bg-red-200 text-red-800'
          }

          return colors[status]
        },
        getStatusDescription (memberId) {
          const member = this.members.find(member => member.id === memberId)

          const descriptions = {
            pending: 'Membro será analisado',
            approved: 'Membro inscrito com sucesso',
            denied: member.denied_reason
          }

          return descriptions[member.status]
        },
        runSearch () {
          const searchWords = this.search.toLowerCase().split(' ')
          const propsToCompare = ['name', 'cpf', 'rg', 'birth_date', 'role', 'status']

          this.members.forEach(member => {
            if (!this.search) {
              return member.isVisible = true
            }

            let isVisible = false

            for (prop in member) {
              if (!propsToCompare.includes(prop)) {
                continue
              }

              const propValue = String(member[prop]).toLowerCase()

              for (word in searchWords) {
                if (!searchWords[word]) {
                  continue
                }

                if (propValue.includes(searchWords[word])) {
                  isVisible = true
                  break
                }
              }

              if (isVisible) {
                break
              }
            }

            member.isVisible = isVisible
          })
        },
      },
      watch: {
        search () {
          this.runSearch()
        }
      },
      mounted () {
        for (member in this.members) {
          this.members[member].isVisible = true
        }
      }
    }).mount('main')
  </script>
</body>
</html>