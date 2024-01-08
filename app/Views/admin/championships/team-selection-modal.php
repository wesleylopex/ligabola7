<div class="team-selection-modal easy-modal easy-modal--fade">
  <div class="easy-modal__content max-w-xl p-10 rounded-lg">
    <div class="flex items-center justify-between">
      <h2 v-if="currentDivisionId" class="text-base text-gray-800 font-semibold mr-2">
        Selecionar times para {{ divisions.find(division => Number(division.id) === currentDivisionId).name }}
      </h2>
      <button class="flex-shrink-0 close-team-selection-modal grid place-items-center p-2 rounded-full bg-black bg-opacity-10 hover:bg-opacity-20 transition">
        <i class="text-gray-600 w-4 h-4" data-feather="x"></i>
      </button>
    </div>
    <div class="mt-8">
      <div class="flex items-center px-2 rounded-full bg-gray-100">
        <i class="w-5 h-5 text-gray-600" data-feather="search"></i>
        <input type="text" v-model="search" placeholder="Digite para pesquisar times" class="text-gray-600 text-sm outline-none p-2 bg-transparent w-full">
        <button data-tippy-content="Limpar filtro" @click="search = ''" class="p-1 rounded-full bg-gray-200 text-gray-600">
          <i class="w-4 h-4" data-feather="x"></i>
        </button>
      </div>
      <div v-if="currentDivisionId" class="mt-4 grid grid-cols-1 gap-2">
        <div
          v-for="team of teams"
          v-show="team.isVisible && !teamIsInOtherDivision(team.id, currentDivisionId)"
          :key="team.id"
          @click="toggleTeamDivision(team.id, currentDivisionId)"
          :class="{ 'border-blue-600': teamsDivisions.findIndex(teamDivision => Number(teamDivision.team_id) === Number(team.id) && Number(teamDivision.division_id) === currentDivisionId) !== -1 }"
          class="border flex items-center p-2 w-full rounded-md cursor-pointer"
        >
          <picture class="w-8 h-8">
            <img class="mx-auto h-full object-contain rounded-md" :src="`${baseURL}uploads/images/teams/${team.image}`" alt="">
          </picture>
          <h3 class="ml-2 text-gray-800 font-medium text-xs">{{ team.name }}</h3>
        </div>
      </div>
      <div class="flex items-center mt-4 justify-end">
        <button type="button" class="close-team-selection-modal text-xs py-2 px-4 rounded-md font-medium bg-blue-600 text-white">Pronto</button>
      </div>
    </div>
  </div>
</div>

<script type="module">
  import EasyModal from '<?= base_url('scripts/easy-modal/easy-modal.js') ?>'

  window.openTeamSelectionModal = openTeamSelectionModal
  window.closeTeamSelectionModal = closeTeamSelectionModal

  const teamSelectionModal = new EasyModal({
    el: '.team-selection-modal',
    open: '.open-team-selection-modal',
    close: '.close-team-selection-modal'
  })

  teamSelectionModal.start()

  function openTeamSelectionModal () {
    teamSelectionModal.open()
  }

  function closeTeamSelectionModal () {
    teamSelectionModal.close()
  }
</script>
