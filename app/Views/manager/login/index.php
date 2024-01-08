<!DOCTYPE html>
<html lang="en">
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
        <form @submit.prevent="onFormSubmit()" method="post" class="w-full" action="<?= base_url('manager/login') ?>">
          <?= csrf_field() ?>
          <div class="mb-8 flex justify-center">
            <img class="w-24" src="<?= base_url('images/logo.png') ?>" alt="Liga Bola 7 Society">
          </div>
          <div class="grid grid-cols-1 gap-4">
            <input type="text" name="email" required  placeholder="E-mail" class="mt-1 form-input">
            <div>
              <div class="flex items-center mt-1 form-input justify-between p-0 pr-2">
                <input :type="showPassword ? 'text' : 'password'" placeholder="Senha" name="password" class="form-input border-0 w-full h-full">
                <button v-show="showPassword" type="button" @click="showPassword = false">
                  <i class="cursor-pointer w-4 h-4" data-feather="eye"></i>
                </button>
                <button v-show="!showPassword" type="button" @click="showPassword = true">
                  <i class="cursor-pointer w-4 h-4" data-feather="eye-off"></i>
                </button>
              </div>
            </div>
            <div class="mt-4 grid place-items-center">
              <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 rounded-sm font-medium shadow-xl bg-blue-600 text-white w-full md:w-3/4 mx-auto">
                Acessar conta
                <i class="w-5 h-5 ml-2 animate-spin hidden" data-feather="loader"></i>
              </button>
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

          setFormIsLoading(form, false, true)

          window.location.href = this.baseURL + 'manager'
        }
      }
    }).mount('main')
  </script>
</body>
</html>