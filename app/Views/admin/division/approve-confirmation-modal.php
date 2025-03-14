<div class="approve-confirmation-modal easy-modal easy-modal--fade">
  <div class="easy-modal__content max-w-2xl p-6 rounded-lg">
    <div class="flex items-center justify-between">
      <h2 class="text-base text-gray-800 font-semibold mr-2">
        Aprovar membro
      </h2>
      <button class="flex-shrink-0 close-approve-confirmation-modal grid place-items-center p-2 rounded-full bg-black bg-opacity-10 hover:bg-opacity-20 transition">
        <i class="text-gray-600 w-4 h-4" data-feather="x"></i>
      </button>
    </div>
    <form id="approve-confirmation" @submit.prevent="approveRecord()" action="<?= base_url('admin/members/approve') ?>" method="POST" class="mt-8">
      <?= csrf_field() ?>
      <input type="hidden" name="id" :value="approveInfo.id">
      <div>
        <label for="" class="text-xs text-gray-800">Número de inscrição</label>
        <input type="text" :value="approveInfo.subscription_number" name="subscription_number" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
      </div>
      
      <!-- Parental consent document section -->
      <div v-show="approveInfo.parental_consent_document" class="mt-4">
        <label class="text-xs text-gray-800">Documento de autorização dos pais/responsáveis</label>
        <div class="mt-1 flex items-center">
          <a
            :href="`${baseURL}uploads/documents/parental_consent/${approveInfo.parental_consent_document}`" 
            target="_blank"
            download
          >
            <p class="text-xs text-blue-600 flex items-center">
              <i class="w-4 h-4 mr-2" data-feather="download"></i>
              Baixar documento
            </p>
          </a>
        </div>
      </div>
      
      <div class="mt-8 flex justify-end">
        <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 px-4 rounded-md font-medium bg-blue-600 text-white">
          Aprovar
          <i class="ml-2 animate-spin hidden" data-feather="loader"></i>
        </button>
      </div>
    </form>
  </div>
</div>

<script type="module">
  import EasyModal from '<?= base_url('scripts/easy-modal/easy-modal.js') ?>'

  window.openApproveConfirmationModal = openApproveConfirmationModal
  window.closeApproveConfirmationModal = closeApproveConfirmationModal

  const approveConfirmationModal = new EasyModal({
    el: '.approve-confirmation-modal',
    open: '.open-approve-confirmation-modal',
    close: '.close-approve-confirmation-modal'
  })

  approveConfirmationModal.start()

  function openApproveConfirmationModal (action) {
    const form = document.querySelector('#approve-confirmation')
    form.action = action

    approveConfirmationModal.open()
  }

  function closeApproveConfirmationModal () {
    approveConfirmationModal.close()
  }
</script>
