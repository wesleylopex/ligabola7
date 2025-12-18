<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('manager/components/head', ['meta' => ['title' => 'Login']]) ?>
</head>
<body
  class="w-screen min-h-screen overflow-x-hidden bg-cover bg-center bg-no-repeat"
  style="background-image: url(<?= base_url('images/default-bg.png') ?>)"
>
  <main class="w-full h-full min-h-screen py-12 md:py-0 px-4 lg:px-0 grid place-items-center">
    <div class="w-full max-w-md mx-auto bg-white shadow-2xl rounded-2xl">
      <div class="p-8 md:p-12 grid place-items-center">
        <form @submit.prevent="onFormSubmit()" method="post" class="w-full" action="<?= base_url('manager/forgot-password/save') ?>">
          <?= csrf_field() ?>
          <div class="mb-8">
            <img class="w-24" src="<?= base_url('images/logo.png') ?>" alt="Liga Bola 7 Society">
            <h1 class="text-2xl font-bold mt-8">Nova senha</h1>
            <p class="text-sm text-gray-600 mt-1">Insira sua nova senha</p>
          </div>
          <div class="grid grid-cols-1 gap-4">
            <input type="password" name="password" required  placeholder="Senha" class="mt-1 form-input">
            <input type="password" name="password_confirmation" required  placeholder="Confirmar senha" class="mt-1 form-input">
            <div class="mt-4 grid place-items-center">
              <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 rounded-sm font-medium shadow-xl bg-blue-600 text-white w-full md:w-3/4 mx-auto">
                Pronto
                <i class="w-5 h-5 ml-2 animate-spin hidden" data-feather="loader"></i>
              </button>
              <a href="<?= base_url('manager/login') ?>" class="mt-8 text-xs text-blue-600 hover:underline">Cancelar</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>

  <!-- Scripts -->
  <?= view('manager/components/scripts') ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>'
    }

    Vue.createApp({
      data () {
        return {
          baseURL: pageData.baseURL,
          showPassword: false
        }
      },
      methods: {
        async onFormSubmit () {
          const form = document.querySelector('form')
          const body = new FormData(form)

          const code = new URLSearchParams(window.location.search).get('code')

          body.append('code', code)

          setFormIsLoading(form, true)

          const response = await fetch(form.action, {
            method: form.method,
            body
          }).then(response => response.json())

          if (!response.success) {
            const error = typeof response.error === 'string'
              ? response.error
              : Object.values(response.error)[0]

            setFormIsLoading(form, false)
            return showNotification(error)
          }

          setFormIsLoading(form, false)
          showNotification('Senha alterada. Faça login')

          setTimeout(() => window.location.href = `${pageData.baseURL}/manager`, 2000)
        }
      }
    }).mount('main')
  </script>
</body>
</html>