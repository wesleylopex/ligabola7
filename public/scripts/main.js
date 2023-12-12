window.addEventListener('load', () => {
  runFeatherIcons()
  runMasks()
  runCards()
  runFileInputs()
  startTailwindDropdown()
})

function runFeatherIcons () {
  feather.replace()
}

function runMasks () {
  const inputs = document.querySelectorAll('[data-mask]')

  inputs.forEach(input => {
    const maskFormat = input.dataset.mask
    const mask = maskFormat === 'Number' ? Number : maskFormat
    const maskOptions = {
      mask,
      radix: '.',
      mapToRadix: [',']
    }

    IMask(input, maskOptions)
  })
}

function runCards () {
  const cards = document.querySelectorAll('.card')

  cards.forEach(card => {
    const cardHeaderIcon = card.querySelector('.card-header__icon')
    const cardHeader = card.querySelector('.card-header')
    const cardBody = card.querySelector('.card-body')

    cardHeader.addEventListener('click', () => {
      cardHeaderIcon.classList.toggle('rotate-180')
      cardBody.classList.toggle('hidden')
    })
  })
}

function runFileInputs () {
  const wrappers = document.querySelectorAll('.input-file')

  wrappers.forEach(wrapper => {
    const input = wrapper.querySelector('.input-file__input')
    const openButtons = wrapper.querySelectorAll('.input-file__open')

    openButtons.forEach(openButton => {
      openButton.addEventListener('click', () => {
        input.click()
      })
    })

    input.addEventListener('change', () => {
      const [file] = input.files

      if (!file) return

      const preview = wrapper.querySelector('.input-file__preview')

      preview.src = URL.createObjectURL(file)

    })
  })
}

function startTailwindDropdown () {
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

    window.addEventListener('scroll', event => {
      content.classList.add('hidden')
    })
  })
}

// Helpers

function setFormIsLoading (form, isLoading, buttonIsDisabled = false) {
  const button = form.querySelector('button[type="submit"]')
  const loader = button.querySelector(button.dataset?.loader || '.animate-spin')

  if (isLoading) {
    loader.classList.remove('hidden')
    button.disabled = true
  } else {
    loader.classList.add('hidden')
    button.disabled = buttonIsDisabled
  }
}

function formatDate (date) {
  if (!date) return ''

  const [year, month, day] = date.split('-')

  return `${day}/${month}/${year}`
}
