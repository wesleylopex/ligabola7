<div class="member-in-another-team-modal easy-modal easy-modal--fade">
  <div class="easy-modal__content max-w-2xl p-6 rounded-lg">
    <div class="flex items-center justify-between">
      <h2 class="text-base text-gray-800 font-semibold mr-2">
        Atleta inscrito em outra equipe
      </h2>
      <button class="flex-shrink-0 close-member-in-another-team-modal grid place-items-center p-2 rounded-full bg-black bg-opacity-10 hover:bg-opacity-20 transition">
        <i class="text-gray-600 w-4 h-4" data-feather="x"></i>
      </button>
    </div>
    <div class="mt-8">
      <div>
        <p class="paragraph leading-relaxed">
          O atleta associado ao CPF informado já está inscrito, ou em processo de inscrição, no time {{ memberInAnotherTeam }} para este campeonato.
        </p>
        <p class="mt-2 paragraph leading-relaxed">
          Conforme o regulamento da Liga Bola 7, caso ocorra a inscrição do mesmo atleta em mais de uma equipe, será válida a inscrição na equipe que o atleta jogar primeiro. Neste caso, a equipe prejudicada pode trocar/excluir o referido atleta, respeitando o período de inscrições.
        </p>
        <p class="mt-2 paragraph leading-relaxed">
          Você deseja continuar essa inscrição mesmo assim?
        </p>
      </div>
      <div class="mt-8 space-x-2 flex justify-end">
        <button @click="ignoreMemberInAnotherTeam = true" class="text-xs py-2 px-4 rounded-md font-medium bg-gray-200 text-blue-600">
          Continuar mesmo assim
        </button>
        <button class="close-member-in-another-team-modal text-xs py-2 px-4 rounded-md font-medium bg-blue-600 text-white">
          Não desejo continuar
        </button>
      </div>
    </div>
  </div>
</div>

<script type="module">
  import EasyModal from '<?= base_url('scripts/easy-modal/easy-modal.js') ?>'

  window.openMemberInAnotherTeamModal = openMemberInAnotherTeamModal
  window.closeMemberInAnotherTeamModal = closeMemberInAnotherTeamModal

  const memberInAnotherTeamModal = new EasyModal({
    el: '.member-in-another-team-modal',
    open: '.open-member-in-another-team-modal',
    close: '.close-member-in-another-team-modal'
  })

  memberInAnotherTeamModal.start()

  function openMemberInAnotherTeamModal () {
    memberInAnotherTeamModal.open()
  }

  function closeMemberInAnotherTeamModal () {
    memberInAnotherTeamModal.close()
  }
</script>
