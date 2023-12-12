class EasyModal {
  modal = null
  settings = {
    el: null,
    persistent: false,
    open: null,
    close: null,
    beforeOpen: null,
    beforeClose: null
  }

  constructor (settings) {
    if (!settings) {
      throw new Error('Missing parameter: settings')
    }

    if (!settings.el) {
      throw new Error('Missing parameter: el')
    }

    this.modal = document.querySelector(settings.el)
    this.settings = settings
  }

  start () {
    this._startOpen()
    this._startClose()
    this._startPersistent()
  }

  open () {
    this._beforeOpen()
    this.modal.classList.add('easy-modal--active')
  }

  close () {
    this._beforeClose()
    this.modal.classList.remove('easy-modal--active')
  }

  _beforeOpen () {
    if (this.settings.beforeOpen && typeof this.settings.beforeOpen === 'function') {
      this.settings.beforeOpen()
    }
  }

  _beforeClose () {
    if (this.settings.beforeClose && typeof this.settings.beforeClose === 'function') {
      this.settings.beforeClose()
    }
  }

  _startOpen () {
    if (!this.settings.open) return false

    const openElements = document.querySelectorAll(this.settings.open)
      
    openElements.forEach(element => {
      element.addEventListener('click', () => {
        this.open()
      })
    })
  }

  _startClose () {
    if (!this.settings.close) return false
    
    const closeElements = document.querySelectorAll(this.settings.close)
      
    closeElements.forEach(element => {
      element.addEventListener('click', () => {
        this.close()
      })
    })
  }

  _startPersistent () {
    const modalContent = this.modal.querySelector('.easy-modal__content')

    if (!modalContent) return false

    this.modal.addEventListener('click', event => {
      if (!this.settings.persistent && !modalContent.contains(event.target)) {
        this.close()
      }
    })
  }
}

export default EasyModal
