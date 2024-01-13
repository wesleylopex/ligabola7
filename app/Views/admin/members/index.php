<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('admin/components/head') ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main id="app" class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('admin') ?>">
        <button class="p-2 rounded-full bg-gray-200" data-tippy-content="Voltar">
          <i class="w-4 h-4 text-gray-500" data-feather="chevron-left"></i>
        </button>
      </a>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">Todos os membros</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div id="members-list" class="w-full rounded-md bg-white p-6 shadow-md">
        <div class="lg:flex lg:justify-between">
          <div class="lg:order-2 flex justify-end">
            <div>
              <a href="<?= base_url('admin/members/create') ?>">
                <button data-tippy-content="Novo membro" class="p-2 rounded-full bg-blue-600 text-white">
                  <i class="w-4 h-4" data-feather="plus"></i>
                </button>
              </a>
            </div>
          </div>
          <div>
            <button onclick="clearFilters()" class="text-blue-600 text-xs">Limpar filtros</button>
            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
              <div class="flex items-center px-2 rounded-full bg-gray-100">
                <i class="w-5 h-5 text-gray-600" data-feather="search"></i>
                <input type="text" placeholder="Digite para pesquisar" class="search text-gray-600 text-sm outline-none p-2 bg-transparent w-full">
                <button data-tippy-content="Limpar filtro" onclick="clearFilters()"  class="p-1 rounded-full bg-gray-200 text-gray-600">
                  <i class="w-4 h-4" data-feather="x"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <table class="mt-14 block sm:table overflow-x-auto w-full text-left font-normal text-[#4A4251] text-sm">
          <thead>
            <tr>
              <th class="font-bold p-3 cursor-pointer sort" data-sort="name">Nome</th>
              <th class="font-bold p-3 cursor-pointer sort" data-sort="subscription_number">Número de inscrição</th>
              <th class="font-bold p-3">CPF</th>
              <th class="font-bold p-3 cursor-pointer">Data de Nascimento</th>
              <!-- <th class="font-bold p-3">Criado em</th> -->
              <th class="font-bold p-3">Ações</th>
            </tr>
          </thead>
          <tbody class="list">
            <?php foreach ($members as $member) : ?>
            <tr class="even:bg-white odd:bg-gray-100">
              <td class="p-3 name"><?= $member->name ?></td>
              <td class="p-3 subscription_number"><?= $member->subscription_number ?></td>
              <td class="p-3 cpf"><?= $member->cpf ?></td>
              <td class="p-3 birth_date"><?= date('d/m/Y', strtotime($member->birth_date)) ?></td>
              <!-- <td class="p-3">{{ formatDateTime(member.created_at) }}</td> -->
              <td class="p-3 flex space-x-1 items-center">
                <a href="<?= base_url('admin/members/update/' . $member->id) ?>">
                  <button data-tippy-content="Clique para editar" class="rounded-full p-2 font-medium hover:bg-gray-200">
                    <i class="w-4 h-4" data-feather="edit"></i>
                  </button>
                </a>
                <button onclick="setActionDeleteConfirmationModal(<?= $member->id ?>)" data-tippy-content="Clique para excluir" class="rounded-full p-2 font-medium hover:bg-gray-200">
                  <i class="w-4 h-4" data-feather="trash"></i>
                </button>
              </td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <div class="mt-12 flex items-center justify-end">
          <ul class="pagination flex items-center"></ul>
        </div>
      </div>
    </section>
    <?= view('admin/components/delete-confirmation-modal') ?>
  </main>

  <?= view('admin/components/scripts') ?>

  <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      members: <?= json_encode($members ?? []) ?>
    }

    window.addEventListener('load', function () {
      feather.replace()
    })

    const membersList = new List('members-list', {
      valueNames: ['name', 'subscription_number', 'cpf', 'rg', 'birth_date', 'created_at'],
      page: 50,
      pagination: [{
        name: "pagination",
        paginationClass: "pagination",
        outerWindow: 2
      }, {
        paginationClass: "pagination",
        innerWindow: 3,
        left: 2,
        right: 4
      }]
    })

    membersList.on('updated', () => {
      feather.replace()
    })

    function setActionDeleteConfirmationModal (memberId) {
      const action = `${pageData.baseURL}admin/members/delete/${memberId}`
      openDeleteConfirmationModal(action)
    }

    function clearFilters () {
      document.querySelector('input.search').value = ''
      membersList.search()
    }

    // Vue.createApp({
    //   data () {
    //     return {
    //       ...pageData,
    //       search: ''
    //     }
    //   },
    //   methods: {
    //     async deleteRecord () {
    //       const form = document.querySelector('form#delete-confirmation')
    //       const body = new FormData(form)

    //       const response = await fetch(form.action, {
    //         method: 'POST',
    //         body
    //       }).then(response => response.json())

    //       console.log(response)

    //       if (response.success !== true) {
    //         return showNotification(response.error)
    //       }

    //       const index = this.members.findIndex(team => Number(team.id) === Number(response.id))
    //       this.members.splice(index, 1)

    //       closeDeleteConfirmationModal()
    //       showNotification('Membro excluído com sucesso')
    //     },
        // openDeleteConfirmationModal (memberId) {
        //   const action = `${this.baseURL}admin/members/delete/${memberId}`
        //   openDeleteConfirmationModal(action)
        // },
    //     runSearch () {
    //       const searchWords = this.search.toLowerCase().split(' ')
    //       const propsToCompare = ['name', 'subscription_number', 'cpf', 'rg']

    //       this.members.forEach(team => {
    //         let queryMatch = false

    //         for (prop in team) {
    //           if (!propsToCompare.includes(prop)) {
    //             continue
    //           }

    //           const propValue = String(team[prop]).toLowerCase()

    //           for (word in searchWords) {
    //             if (!searchWords[word]) {
    //               continue
    //             }

    //             if (propValue.includes(searchWords[word])) {
    //               queryMatch = true
    //               break
    //             }
    //           }

    //           if (queryMatch) {
    //             break
    //           }
    //         }

    //         const queryIsValidAndMatch = !this.search || queryMatch
    //         const divisionIdIsValidAndMatch = !this.divisionId || Number(team.division_id) === Number(this.divisionId)

    //         const isVisible = queryIsValidAndMatch && divisionIdIsValidAndMatch

    //         team.isVisible = isVisible
    //       })
    //     },
    //     clearFilters () {
    //       this.search = ''
    //       this.divisionId = ''
    //     },
    //     formatDate (date) {
    //       if (!date || date === '0000-00-00') return ''

    //       const [year, month, day] = date.split('-')
    //       return `${day}/${month}/${year}`
    //     },
    //     formatDateTime (dateTime) {
    //       if (!dateTime || dateTime === '0000-00-00 00:00:00') return ''

    //       const [date, time] = dateTime.split(' ')
    //       const [year, month, day] = date.split('-')
    //       return `${day}/${month}/${year} ${time}`
    //     }
    //   },
    //   watch: {
    //     search () {
    //       this.runSearch()
    //     },
    //     divisionId () {
    //       this.runSearch()
    //     }
    //   },
    //   mounted () {
    //     for (member in this.members) {
    //       this.members[member].isVisible = true
    //     }
    //   }
    // }).mount('#app')
  </script>
</body>
</html>