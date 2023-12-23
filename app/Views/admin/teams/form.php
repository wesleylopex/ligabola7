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
      <h1 class="mt-10 text-3xl font-bold text-gray-100">{{ team.id ? 'Editar time' : 'Cadastrar novo time' }}</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <form @submit.prevent="onFormSubmit()" action="<?= base_url('admin/teams/save') ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <input type="hidden" name="id" :value="team.id">
          <div class="lg:col-span-3">
            <label for="" class="text-xs text-gray-800">Nome</label>
            <input type="text" required name="name" :value="team.name" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-3">
            <label for="" class="text-xs text-gray-800">Nome de usuário (para acesso)</label>
            <input type="text" required name="username" :value="team.username" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-3">
            <label for="" class="text-xs text-gray-800">Senha (para acesso)</label>
            <input type="text" <?= empty($team) ? 'required' : '' ?> name="password" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-3">
            <label for="type" class="text-xs text-gray-800">Divisão</label>
            <select name="division_id" required id="division_id" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
              <option value=""></option>
              <option v-for="division in divisions" :value="division.id" :selected="team.division_id === division.id">{{ division.name }}</option>
            </select>
            <label for="" class="error"></label>
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
      baseURL: '<?= base_url() ?>',
      team: <?= json_encode($team ?? []) ?>,
      divisions: <?= json_encode($divisions ?? []) ?>
    }

    Vue.createApp({
      data () {
        return {
          baseURL: pageData.baseURL,
          team: pageData.team,
          divisions: pageData.divisions
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

          if (response.success !== true) {
            setFormIsLoading(form, false)
            return showNotify(response.error || 'Erro ao salvar')
          }

          setFormIsLoading(form, false, true)
          showNotify('Time salvo com sucesso')
          setTimeout(() => window.location.href = `${this.baseURL}admin/teams`, 2000)
        }
      }
    }).mount('#app')
  </script>
</body>
</html>