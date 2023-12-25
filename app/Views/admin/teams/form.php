<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('admin/components/head') ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main id="app" class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('admin/teams') ?>">
        <button class="p-2 rounded-full bg-gray-200" data-tippy-content="Voltar">
          <i class="w-4 h-4 text-gray-500" data-feather="chevron-left"></i>
        </button>
      </a>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">{{ team.id ? 'Editar time' : 'Cadastrar novo time' }}</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <form @submit.prevent="onFormSubmit()" action="<?= base_url('admin/teams/save') ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <?= csrf_field() ?>
          <input type="hidden" name="id" :value="team.id">
          <div class="lg:col-span-3">
            <label for="" class="text-xs text-gray-800">Nome</label>
            <input type="text" required name="name" :value="team.name" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-3">
            <label for="" class="text-xs text-gray-800">E-mail (para acesso)</label>
            <input type="text" required name="email" :value="team.email" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-3">
            <label for="" class="text-xs text-gray-800">Senha (para acesso)</label>
            <input type="text" name="password" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="col-span-full">
            <label for="image" class="form-label">Clique para inserir uma foto do time</label>
            <div class="input-file group cursor-pointer w-32 h-32 relative mt-2">
              <input type="file" name="image" accept="image/*" class="input-file__input hidden">
              <button type="button" class="input-file__open absolute z-10 right-2 bottom-2 p-2 rounded-md text-gray-600 bg-gray-200">
                <i class="w-4 h-4" data-feather="camera"></i>
              </button>
              <div class="input-file__open w-full h-full relative rounded-md">
                <img class="input-file__preview w-full h-full object-cover rounded-md border border-gray-200" src="<?= isset($team) ? base_url("uploads/images/teams/$team->image") : '' ?>" alt="">
                <span class="transition-opacity duration-500 opacity-0 group-hover:opacity-100 top-0 rounded-md w-full h-full absolute bg-black bg-opacity-60 flex items-center justify-center text-xs font-medium text-white">Alterar</span>
              </div>
            </div>
          </div>
          <div class="lg:col-span-12 flex justify-end">
          <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 px-4 rounded-md font-medium bg-blue-600 text-white">
            Salvar
            <i class="w-5 h-5 ml-2 animate-spin hidden" data-feather="loader"></i>
          </button>
          </div>
        </form>
      </div>
    </section>
  </main>

  <?= view('admin/components/scripts') ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      team: <?= json_encode($team ?? []) ?>,
    }

    Vue.createApp({
      data () {
        return {
          ...pageData
        }
      },
      methods: {
        async onFormSubmit () {
          const form = document.querySelector('form')
          const body = new FormData(form)

          setFormIsLoading(form, true)

          const response = await fetch(form.action, {
            method: 'POST',
            body
          }).then(response => response.json())

          if (!response.success) {
            const [error] = Object.values(response.error)

            setFormIsLoading(form, false)
            return showNotification(error)
          }

          setFormIsLoading(form, false, true)
          showNotification('Time salvo com sucesso')
          setTimeout(() => window.location.href = `${this.baseURL}admin/teams`, 2000)
        }
      }
    }).mount('#app')
  </script>
</body>
</html>