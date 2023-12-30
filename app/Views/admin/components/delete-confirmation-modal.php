<div class="delete-confirmation-modal easy-modal easy-modal--fade">
  <div class="easy-modal__content max-w-2xl p-6 rounded-lg">
    <div class="flex items-center justify-between">
      <h2 class="text-base text-gray-800 font-semibold mr-2">
        Deseja mesmo excluir?
      </h2>
      <button class="flex-shrink-0 close-delete-confirmation-modal grid place-items-center p-2 rounded-full bg-black bg-opacity-10 hover:bg-opacity-20 transition">
        <i class="text-gray-600 w-4 h-4" data-feather="x"></i>
      </button>
    </div>
    <form id="delete-confirmation" @submit.prevent="deleteRecord()" action="" method="POST" class="mt-8">
      <?= csrf_field() ?>
      <p class="text-sm text-gray-600">Após a exclusão deste registro não será possível recuperá-lo</p>
      <div class="mt-8 flex justify-end">
        <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 px-4 rounded-sm font-medium bg-blue-600 text-white">
          Excluir
          <i class="ml-2 rotating hidden" data-feather="loader"></i>
        </button>
      </div>
    </form>
  </div>
</div>

<script type="module">
  import EasyModal from '<?= base_url('scripts/easy-modal/easy-modal.js') ?>'

  window.openDeleteConfirmationModal = openDeleteConfirmationModal
  window.closeDeleteConfirmationModal = closeDeleteConfirmationModal

  const deleteConfirmationModal = new EasyModal({
    el: '.delete-confirmation-modal',
    open: '.open-delete-confirmation-modal',
    close: '.close-delete-confirmation-modal',
    beforeClose: () => {
      const form = document.querySelector('form#delete-confirmation')
      form.action = ''
    }
  })

  deleteConfirmationModal.start()

  function openDeleteConfirmationModal (action) {
    const form = document.querySelector('form#delete-confirmation')
    form.action = action

    deleteConfirmationModal.open()
  }

  function closeDeleteConfirmationModal () {
    deleteConfirmationModal.close()
  }
</script>
