function showNotification (message) {
  const token = Math.random().toString(36)

  const notificationHTML = `
    <div onclick="closeNotification('${token}')" data-token="${token}" class="cursor-pointer fixed z-50 right-10 transition-transform transform -bottom-20 w-full max-w-xs border-l-8 border-blue-600 p-4 bg-blue-200 text-blue-600 shadow-md rounded-md text-sm flex items-center justify-between">
      <p>${message}</p>
      <button class="p-2 rounded-full transition-colors hover:bg-white bg-opacity-10">
        <i class="w-5 h-5" data-feather="x"></i>
      </button>
    </div>
  `

  const body = document.querySelector('body')
  body.insertAdjacentHTML('beforeend', notificationHTML)

  feather.replace()

  setTimeout(() => {
    const notificationElement = document.querySelector(`[data-token="${token}"]`)
    notificationElement.classList.add('-translate-y-32')
  }, 100)

  setTimeout(() => closeNotification(token), 5000)
}

function closeNotification (token) {
  const notificationElement = document.querySelector(`[data-token="${token}"]`)

  if (!notificationElement) return

  notificationElement.classList.remove('-translate-y-32')
  
  setTimeout(() => notificationElement.remove(), 500)
}