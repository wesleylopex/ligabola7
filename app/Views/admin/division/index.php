<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('admin/components/head', ['meta' => ['title' => 'Membros']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main id="app" class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('admin/championships/' . $division->championship_id) ?>">
        <button class="p-2 rounded-full bg-gray-100" data-tippy-content="Voltar">
          <i class="w-4 h-4 text-gray-500" data-feather="chevron-left"></i>
        </button>
      </a>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">Membros {{ division.id ? 'da ' + division.name : '' }}</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <div class="lg:flex lg:justify-between">
          <div class="lg:order-2 flex justify-end space-x-2">
            <div>
              <button @click="downloadCSV()" data-tippy-content="Baixar membros filtrados (.CSV)" class="p-2 rounded-full bg-gray-200 text-gray-600">
                <i class="w-4 h-4" data-feather="download"></i>
              </button>
            </div>
            <div>
              <a href="<?= base_url('admin/championships/division/' . $division->id . '/settings') ?>">
                <button data-tippy-content="Clique para configurar" class="p-2 rounded-full bg-gray-200 text-gray-600">
                  <i class="w-4 h-4" data-feather="settings"></i>
                </button>
              </a>
            </div>
          </div>
          <div>
            <button @click="clearFilters()" class="text-blue-600 text-xs">Limpar filtros</button>
            <div class="mt-2 w-full grid grid-cols-1 md:grid-cols-3 gap-2">
              <div class="flex items-center px-2 rounded-full bg-gray-100">
                <i class="w-5 h-5 text-gray-600" data-feather="search"></i>
                <input type="text" v-model="search" placeholder="Digite para pesquisar" class="text-gray-600 text-sm outline-none p-2 bg-transparent w-full">
                <button data-tippy-content="Limpar filtro" @click="search = ''" class="p-1 rounded-full bg-gray-200 text-gray-600">
                  <i class="w-4 h-4" data-feather="x"></i>
                </button>
              </div>
              <select v-model="teamId" class="p-2 px-3 text-sm rounded-full bg-gray-100 text-gray-600">
                <option value="">Filtrar por time</option>
                <option v-for="team in teams" :value="team.id">{{ team.name }}</option>
              </select>
              <select v-model="status" class="p-2 px-3 text-sm rounded-full bg-gray-100 text-gray-600">
                <option value="">Filtrar por status</option>
                <option v-for="statusSlug in ['pending', 'approved', 'denied']" :value="statusSlug">{{ getTranslatedMemberStatus(statusSlug) }}</option>
              </select>
            </div>
          </div>
        </div>
        <table class="mt-14 block xl:table overflow-x-auto w-full max-w-full text-left font-normal text-[#4A4251] text-sm">
          <thead>
            <tr>
              <th class="font-bold p-3">Time</th>
              <th class="font-bold p-3">Nome</th>
              <th class="font-bold p-3">Número de inscrição</th>
              <th class="font-bold p-3">CPF</th>
              <th class="font-bold p-3">Data de Nascimento</th>
              <th class="font-bold p-3">Tipo</th>
              <th class="font-bold p-3">Status</th>
              <th class="font-bold p-3">Descrição do status</th>
              <th class="font-bold p-3">Criado em</th>
              <th class="font-bold p-3">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="member in members" v-show="member.isVisible" class="even:bg-white odd:bg-gray-100">
              <td class="p-3">{{ getTeamName(member.team_id) }}</td>
              <td class="p-3">{{ member.name }}</td>
              <td class="p-3">{{ member.subscription_number }}</td>
              <td class="p-3">{{ member.cpf }}</td>
              <td class="p-3">{{ formatDate(member.birth_date) }}</td>
              <td class="p-3">{{ getTranslatedRole(member.role) }}</td>
              <td class="p-3">
                <span class="py-1 px-2 rounded-full text-xs font-semibold" :class="getMemberStatusColor(member.status)">{{ getTranslatedMemberStatus(member.status) }}</span>
              </td>
              <td class="p-3">{{ getMemberStatusDescription(member.id) }}</td>
              <td class="p-3">{{ formatDateTime(member.created_at) }}</td>
              <td class="p-3 flex space-x-1 items-center">
                <a :href="`${baseURL}admin/members-teams-divisions/update/${member.id}`">
                  <button data-tippy-content="Clique para editar" class="rounded-full p-2 font-medium hover:bg-gray-200">
                    <i class="w-4 h-4" data-feather="edit"></i>
                  </button>
                </a>
                <button @click="openDeleteConfirmationModal(member.id)" data-tippy-content="Clique para excluir" class="rounded-full p-2 font-medium hover:bg-gray-200">
                  <i class="w-4 h-4" data-feather="trash"></i>
                </button>
                <button @click="beforeApproveMember(member.id)" v-if="member.status === 'pending'" data-tippy-content="Clique para aprovar" class="rounded-full p-2 font-medium bg-gray-200 hover:text-gray-100 hover:bg-green-500 transition-colors duration-300">
                  <i class="w-4 h-4" data-feather="check"></i>
                </button>
              </td>
            </tr>
            <tr v-show="members.every(member => !member.isVisible)" class="bg-gray-100">
              <td class="p-3 text-center font-medium" colspan="11">Nenhum membro</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
    <?= view('admin/components/delete-confirmation-modal', $this->data) ?>
    <?= view('admin/division/approve-confirmation-modal', $this->data) ?>
  </main>

  <?= view('admin/components/scripts', $this->data) ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      members: <?= json_encode($members ?? []) ?>,
      teams: <?= json_encode($teams ?? []) ?>,
      division: <?= json_encode($division ?? []) ?>
    }

    Vue.createApp({
      data () {
        return {
          ...pageData,
          search: '',
          teamId: '',
          status: localStorage.getItem('status') || 'pending',
          approveInfo: {
            id: null,
            subscription_number: null
          },
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

          console.log(response)

          if (!response.success) {
            const error = typeof response.error === 'string'
              ? response.error
              : Object.values(response.error)[0]

            return showNotification(error)
          }

          const index = this.members.findIndex(member => Number(member.id) === Number(response.id))
          this.members.splice(index, 1)

          closeDeleteConfirmationModal()
          showNotification('Membro excluído com sucesso')
        },
        openDeleteConfirmationModal (mtdId) {
          const action = `${this.baseURL}admin/members-teams-divisions/delete/${mtdId}`
          openDeleteConfirmationModal(action)
        },
        async approveRecord () {
          const form = document.querySelector('form#approve-confirmation')
          const body = new FormData(form)

          setFormIsLoading(form, true)

          const response = await fetch(form.action, {
            method: 'POST',
            body
          }).then(response => response.json())

          console.log(response)

          setFormIsLoading(form, false)

          if (!response.success) {
            const error = typeof response.error === 'string'
              ? response.error
              : Object.values(response.error)[0]

            return showNotification(error)
          }

          const memberTeamDivisionId = Number(response.id)
          const index = this.members.findIndex(member => Number(member.id) === memberTeamDivisionId)

          if (index === -1) {
            return showNotification('Não foi possível encontrar o membro')
          }

          this.members[index].status = 'approved'

          const memberId = this.members[index].member_id
          const subscriptionNumber = body.get('subscription_number')

          this.members.forEach(member => {
            if (Number(member.member_id) === Number(memberId)) {
              member.subscription_number = subscriptionNumber
            }
          })

          this.approveInfo = {}

          this.runSearch()

          closeApproveConfirmationModal()
          showNotification('Membro aprovado com sucesso')
        },
        openApproveConfirmationModal (id) {
          const member = this.members.find(memberTeamDivision => Number(memberTeamDivision.id) === Number(id))

          this.approveInfo = {
            id: member.id,
            subscription_number: member.subscription_number
          }

          const action = `${this.baseURL}admin/members-teams-divisions/approve`

          openApproveConfirmationModal(action)
        },
        async beforeApproveMember (mtdId) {
          const mtd = this.members.find(member => Number(member.id) === Number(mtdId))

          if (!mtd) {
            return showNotification('Não foi possível encontrar o membro')
          }

          if (mtd.role !== 'athlete') {
          return this.openApproveConfirmationModal(mtdId)
          }

          const athletesLength = this.members.filter(member => member.role === 'athlete' && member.status === 'approved' && Number(member.team_id) === Number(mtd.team_id)).length

          if (athletesLength >= 23) {
            showNotification(`O time já possui ${athletesLength} atletas aprovados para o campeonato vigente.`)
          }
          
          return this.openApproveConfirmationModal(mtdId)
        },
        downloadCSV () {
          const rows = [['Time', 'Nome', 'Número de inscrição', 'CPF', 'RG', 'Data de nascimento', 'Tipo', 'Status', 'Descrição do status', 'Criado em']]
          const visibleMembers = this.members.filter(member => member.isVisible)

          if (!visibleMembers.length) {
            return showNotification('Nenhum membro para baixar')
          }

          visibleMembers.forEach(member => {
            rows.push([
              this.getTeamName(member.team_id),
              member.name,
              member.subscription_number,
              member.cpf,
              member.rg,
              this.formatDate(member.birth_date),
              this.getTranslatedRole(member.role),
              this.getTranslatedMemberStatus(member.status),
              this.getMemberStatusDescription(member.id),
              this.formatDateTime(member.created_at)
            ])
          })

          let csvContent = 'data:text/csv;charset=utf-8,'
          rows.forEach(rowArray => (csvContent += rowArray.join(';') + '\r\n'))

          const encodedUri = encodeURI(csvContent)
          const downloadLinkElement = document.createElement('a')

          const divisionName = this.division.id ? ('-' + this.division.name.toLowerCase()) : ''

          downloadLinkElement.setAttribute('href', encodedUri)
          downloadLinkElement.setAttribute('download', `membros${divisionName}-${this.getCurrentDate()}.csv`)

          document.body.appendChild(downloadLinkElement)

          downloadLinkElement.click()
        },
        getTeamName (teamId) {
          if (!teamId) return ''
          const team = this.teams.find(team => Number(team.id) === Number(teamId))
          return team.name
        },
        getMemberStatusDescription (memberId) {
          const member = this.members.find(member => member.id === memberId)

          const descriptions = {
            pending: 'Membro será analisado',
            approved: 'Membro inscrito com sucesso',
            denied: member.denied_reason
          }

          return descriptions[member.status]
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
        getTranslatedMemberStatus (status) {
          const statuses = {
            pending: 'Pendente',
            approved: 'Aprovado',
            denied: 'Reprovado'
          }

          return statuses[status]
        },
        getMemberStatusColor (status) {
          const colors = {
            pending: 'bg-orange-200 text-yellow-800',
            approved: 'bg-green-200 text-green-800',
            denied: 'bg-red-200 text-red-800'
          }

          return colors[status]
        },
        runSearch () {
          const searchWords = this.search.toLowerCase().split(' ')
          const propsToCompare = ['team_name', 'name', 'cpf', 'rg', 'birth_date', 'role', 'status']

          this.members.forEach(member => {
            let queryMatch = false

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
                  queryMatch = true
                  break
                }
              }

              if (queryMatch) {
                break
              }
            }

            const queryIsValidAndMatch = !this.search || queryMatch
            const teamIdIsValidAndMatch = !this.teamId || Number(member.team_id) === Number(this.teamId)
            const statusMatch = !this.status || member.status === this.status

            const isVisible = queryIsValidAndMatch && teamIdIsValidAndMatch && statusMatch

            member.isVisible = isVisible
          })
        },
        clearFilters () {
          this.search = ''
          this.teamId = ''
          this.status = ''
        },
        formatDate (date) {
          if (!date || date === '0000-00-00') return ''

          const [year, month, day] = date.split('-')
          return `${day}/${month}/${year}`
        },
        formatDateTime (dateTime) {
          const [date, time] = dateTime.split(' ')
          const [year, month, day] = date.split('-')
          return `${day}/${month}/${year} ${time}`
        },
        getCurrentDate () {
          const date = new Date()
          const day = String(date.getDate()).padStart(2, '0')
          const month = String(date.getMonth() + 1).padStart(2, '0')
          const year = date.getFullYear()
          return `${day}-${month}-${year}`
        }
      },
      watch: {
        search () {
          this.runSearch()
        },
        teamId () {
          this.runSearch()
        },
        status () {
          this.runSearch()

          localStorage.setItem('status', this.status)
        }
      },
      mounted () {
        for (member in this.members) {
          console.log({...this.members[member]})
          this.members[member].isVisible = true
        }

        this.runSearch()
      }
    }).mount('#app')
  </script>
</body>
</html>