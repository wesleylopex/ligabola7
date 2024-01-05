<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('admin/components/head', ['meta' => ['title' => 'Configurações']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main id="app" class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('admin/championships/' . $championship->id) ?>">
        <button class="p-2 rounded-full bg-gray-200" data-tippy-content="Voltar">
          <i class="w-4 h-4 text-gray-500" data-feather="chevron-left"></i>
        </button>
      </a>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">Editar configurações do campeonato</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <form @submit.prevent="onFormSubmit()" action="<?= base_url('admin/championships/saveSettings') ?>" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= $championship->id ?>">

          <div class="md:col-span-4">
            <label for="" class="text-xs text-gray-800">Nome do campeonato</label>
            <input type="text" name="name" value="<?= !empty($championship) ? $championship->name : '' ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="md:col-span-4">
            <label for="" class="text-xs text-gray-800">Data de início</label>
            <input type="date" name="start_date" value="<?= !empty($championship) ? $championship->start_date : '' ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="md:col-span-4">
            <label for="" class="text-xs text-gray-800">Data de término</label>
            <input type="date" name="end_date" value="<?= !empty($championship) ? $championship->end_date : '' ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="col-span-full flex justify-end">
          <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 px-4 rounded-md font-medium bg-blue-600 text-white">
            Salvar
            <i class="w-5 h-5 ml-2 animate-spin hidden" data-feather="loader"></i>
          </button>
          </div>
        </form>
      </div>
    </section>
  </main>

  <?= view('admin/components/scripts', $this->data) ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      championship: <?= json_encode($championship) ?>
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
            const error = typeof response.error === 'string'
              ? response.error
              : Object.values(response.error)[0]

            setFormIsLoading(form, false)
            return showNotification(error)
          }

          setFormIsLoading(form, false, true)
          showNotification('Campeonato salvo com sucesso')
          setTimeout(() => window.location.href = document.referrer, 2000)
        }
      }
    }).mount('main')
  </script>
</body>
</html>