<!-- Feather Icons -->
<script src="https://unpkg.com/feather-icons"></script>

<!-- Vue JS -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<!-- Intl Tel Input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<!-- IMask -->
<script src="https://unpkg.com/imask"></script>

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tippy.js/6.3.7/tippy.umd.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script src="<?= base_url('scripts/notification.js') ?>"></script>
<script src="<?= base_url('scripts/main.js') ?>"></script>
<script src="<?= base_url('scripts/easy-modal/easy-modal.js') ?>"></script>

<script>
  window.addEventListener('load', function () {
    feather.replace()
    initTippy()
  })

  function closeAlert (event) {
    const alert = event.target.closest('.alert')
    alert.remove()
  }

  function initTippy () {
    const tippies = document.querySelectorAll('[data-tippy-content]')
    tippy(tippies)
  }

  function closeNotify () {
    const alert = document.querySelector('.notify')
    alert.classList.add('hidden')
  }

  function showNotify (message) {
    const alert = document.querySelector('.notify')
    const alertMessage = document.querySelector('.notify__message')

    alertMessage.innerHTML = message
    alert.classList.remove('hidden')

    hideAfterSeconds(alert, 5)

    function hideAfterSeconds (element, seconds) {
      setTimeout(() => {
        element.classList.add('hidden')
      }, seconds * 1000)
    }
  }

  function initDropdown () {
    const dropdowns = document.querySelectorAll('.tailwind-dropdown')

    function clearAllContent () {
      const content = document.querySelectorAll('.tailwind-dropdown__content')
      content.forEach(item => item.classList.add('hidden'))
    }

    dropdowns.forEach(dropdown => {
      const toggle = dropdown.querySelector('.tailwind-dropdown__toggle')
      const content = dropdown.querySelector('.tailwind-dropdown__content')

      toggle.addEventListener('click', event => {
        event.stopPropagation()
        clearAllContent()
        content.classList.toggle('hidden')
      })

      const items = content.querySelectorAll('.tailwind-dropdown__item')

      items.forEach(item => {
        item.addEventListener('click', () => {
          content.classList.add('hidden')
        })
      })

      window.addEventListener('click', event => {
        if (!dropdown.contains(event.target)) {
          content.classList.add('hidden')
        }
      })
    })
  }
</script>