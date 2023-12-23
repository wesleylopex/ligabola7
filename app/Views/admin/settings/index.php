<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?php $this->load->view('admin/components/head', $this->data) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= assets('website/images/default-bg.png') ?>)">
  <main id="app" class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('admin/teams') ?>">
        <button class="p-2 rounded-full bg-gray-200" data-tippy-content="Voltar">
          <i class="w-5 h-5 text-gray-500" data-feather="arrow-left"></i>
        </button>
      </a>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">Editar configurações do sistema</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <form action="<?= base_url('admin/settings/save') ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <div class="lg:col-span-6">
            <label for="" class="text-xs text-gray-800">Inscrições abertas <span class="text-xs text-gray-500">(permitir que times cadastrem novos membros)</span></label>
            <select name="teams_can_create_members" required id="teams_can_create_members" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
              <option value="1" <?= !empty($settings) && $settings->teams_can_create_members ? 'selected' : '' ?>>Sim</option>
              <option value="0" <?= !empty($settings) && !$settings->teams_can_create_members ? 'selected' : '' ?>>Não</option>
            </select>
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-12 grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-6">
              <label for="" class="text-xs text-gray-800">Texto informativo <span class="text-xs text-gray-500">(este texto aparecerá na home do painel dos times)</span></label>
              <textarea type="text" rows="3" name="warning_text" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent"><?= !empty($settings) ? $settings->warning_text : '' ?></textarea>
              <label for="" class="error"></label>
            </div>
          </div>
          <div class="lg:col-span-12 flex justify-end">
          <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 px-4 rounded-md font-medium bg-blue-600 text-white">
            Salvar
            <i class="w-5 h-5 ml-2 rotating hidden" data-feather="loader"></i>
          </button>
          </div>
        </form>
      </div>
    </section>
  </main>

  <?php $this->load->view('admin/components/scripts', $this->data) ?>

  <!-- Vue JS -->
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>'
    }

    window.addEventListener('load', () => {
      onFormSubmit()
    })

    async function onFormSubmit () {
      const form = document.querySelector('form')
      
      form.addEventListener('submit', async event => {
        event.preventDefault()

        const body = new FormData(form)

        setFormIsLoading(form, true)

        const response = await fetch(form.action, {
          method: 'POST',
          body
        }).then(response => response.json())

        if (response.success !== true) {
          setFormIsLoading(form, false)
          return showNotify(response.error || 'Erro ao salvar')
        }

        setFormIsLoading(form, false, true)
        showNotify('Configurações salvas com sucesso')
        setTimeout(() => window.location.href = `${pageData.baseURL}admin`, 2000)
      })
    }
  </script>
</body>
</html>