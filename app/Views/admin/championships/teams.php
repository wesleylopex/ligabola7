<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('admin/components/head', ['meta' => ['title' => 'Times']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <button onclick="history.back()" class="p-2 rounded-full bg-gray-100" data-tippy-content="Voltar">
        <i class="w-4 h-4 text-gray-500" data-feather="chevron-left"></i>
      </button>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">Times de {{ championship.name }}</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <form @submit.prevent="onFormSubmit()" action="<?= base_url('admin/championships/saveTeamsDivisions') ?>" class="w-full rounded-md bg-white p-12 shadow-md grid grid-cols-1 gap-12">
        <?= csrf_field() ?>
        <div v-for="division of divisions">
          <h2 class="font-bold text-lg text-gray-800">{{ division.name }} <small>({{ getTeamsByDivision(division.id).length }})</small></h2>
          <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-8 xl:grid-cols-12 gap-4">
            <div @click="openTeamSelectionModal(division.id)" class="cursor-pointer w-full h-24 flex flex-col justify-center items-center rounded-md bg-white p-2 border">
              <div class="p-1 bg-gray-100 rounded-full">
                <i class="w-4 h-4 text-gray-600" data-feather="plus"></i>
              </div>
              <h2 class="mt-2 text-gray-600 font-medium text-xs text-center">Novo time</h2>
            </div>
            <div
              v-for="team of teams"
              :key="team.id"
              v-show="teamsDivisions.findIndex(teamDivision => Number(teamDivision.team_id) === Number(team.id) && Number(teamDivision.division_id) === Number(division.id)) !== -1"
              class="border flex flex-col items-center justify-center p-1 w-full h-24 text-center rounded-md"
            >
              <img class="w-full h-full object-contain rounded-md" :src="`${baseURL}uploads/images/teams/${team.image}`" alt="">
            </div>
          </div>
        </div>
        <div class="flex justify-end">
          <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 px-4 rounded-md font-medium bg-blue-600 text-white">
            Salvar
            <i class="w-5 h-5 ml-2 animate-spin hidden" data-feather="loader"></i>
          </button>
        </div>
      </form>
    </section>
  </main>

  <!-- Team selection modal -->
  <?= view('admin/championships/team-selection-modal') ?>

  <?= view('admin/components/scripts') ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      championship: <?= json_encode($championship ?? []) ?>,
      divisions: <?= json_encode($divisions ?? []) ?>,
      teams: <?= json_encode($teams ?? []) ?>,
      teamsDivisions: <?= json_encode($teamsDivisions ?? []) ?>
    }

    Vue.createApp({
      data () {
        return {
          ...pageData,
          currentDivisionId: null,
          search: '',
          deletedTeamsDivisions: []
        }
      },
      methods: {
        openTeamSelectionModal (divisionId) {
          this.currentDivisionId = Number(divisionId)
          openTeamSelectionModal()
        },
        toggleTeamDivision (teamId, divisionId) {
          const index = this.teamsDivisions.findIndex(teamDivision => +teamDivision.team_id === +teamId && +teamDivision.division_id === +divisionId)

          if (index !== -1) {
            const teamDivision = this.teamsDivisions[index]

            if (teamDivision.id) {
              this.deletedTeamsDivisions.push(teamDivision.id)
            }

            return this.teamsDivisions.splice(index, 1)
          }

          this.teamsDivisions.push({
            team_id: Number(teamId),
            division_id: Number(divisionId)
          })
        },
        teamIsInOtherDivision (teamId, divisionId) {
          return this.teamsDivisions.findIndex(teamDivision => {
            const division = this.divisions.find(division => Number(division.id) === Number(teamDivision.division_id))

            if (!division) {
              return false
            }

            const divisionBelongsToChampionship = Number(division.championship_id) === Number(this.championship.id)

            if (!divisionBelongsToChampionship) {
              return false
            }

            return Number(teamDivision.team_id) === Number(teamId) && Number(teamDivision.division_id) !== Number(divisionId)
          }) >= 0
        },
        getTeamsByDivision (divisionId) {
          const teamDivision = this.teamsDivisions.filter(teamDivision => Number(teamDivision.division_id) === Number(divisionId))

          return this.teams.filter(team => teamDivision.findIndex(teamDivision => Number(teamDivision.team_id) === Number(team.id)) !== -1)
        },
        async onFormSubmit () {
          const form = document.querySelector('form')
          const body = new FormData(form)

          body.append('teamsDivisions', JSON.stringify(this.teamsDivisions))
          body.append('deletedTeamsDivisions', JSON.stringify(this.deletedTeamsDivisions))

          setFormIsLoading(form, true)

          const response = await fetch(form.action, {
            method: 'POST',
            body
          }).then(response => response.json())

          if (!response.success) {
            const [error] = Object.values(response.error)

            setFormIsLoading(form, false)
            return showNotification(error)
          }

          setFormIsLoading(form, false, true)
          showNotification('Campeonato salvo com sucesso')
          setTimeout(() => window.location.href = document.referrer, 2000)
        },
        runSearch () {
          const searchWords = this.search.toLowerCase().split(' ')
          const propsToCompare = ['name']

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
        },
      },
      watch: {
        search () {
          this.runSearch()
        }
      },
      mounted () {
        for (team in this.teams) {
          this.teams[team].isVisible = true
        }
      }
    }).mount('body')
  </script>
</body>
</html>