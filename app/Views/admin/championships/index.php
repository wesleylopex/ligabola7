<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('admin/components/head', ['meta' => ['title' => 'Home']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main class="my-10 md:my-20">
    <header class="w-full max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('admin/home') ?>">
        <button class="p-2 rounded-full bg-gray-100" data-tippy-content="Voltar">
          <i class="w-4 h-4 text-gray-500" data-feather="chevron-left"></i>
        </button>
      </a>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">{{ championship.name }}</h1>
    </header>
    <section class="mt-10 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4 w-full max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a :href="`${baseURL}admin/championships/teams/${championship.id}`">
        <div class="w-full flex flex-col items-center rounded-md bg-white p-6 shadow-md">
          <div class="p-2 bg-gray-100 rounded-full">
            <i class="w-5 h-5 text-gray-600" data-feather="shield"></i>
          </div>
          <h2 class="my-2 text-gray-600 font-semibold text-sm">Times</h2>
          <p class="text-gray-400 text-center text-xs">Clique para visualizar os times deste campeonato</p>
        </div>
      </a>
      <a v-for="division of divisions" :href="`${baseURL}admin/championships/division/${division.id}`">
        <div class="w-full flex flex-col items-center rounded-md bg-white p-6 shadow-md">
          <div class="p-2 rounded-full" :class="division.color">
            <i class="w-5 h-5 text-gray-100" data-feather="award"></i>
          </div>
          <h2 class="my-2 text-gray-600 font-semibold text-sm">{{ division.name }}</h2>
          <p class="text-gray-400 text-center text-xs">Clique para visulizar os membros da {{ division.name }}</p>
        </div>
      </a>
      <a href="<?= base_url('admin/championships/settings/' . $championship->id) ?>">
        <div class="w-full flex flex-col items-center rounded-md bg-white p-6 shadow-md">
          <div class="p-2 bg-gray-100 rounded-full">
            <i class="w-5 h-5 text-gray-600" data-feather="settings"></i>
          </div>
          <h2 class="my-2 text-gray-600 font-semibold text-sm">Configurações</h2>
          <p class="text-gray-400 text-center text-xs">Clique para alterar as configurações do sistema</p>
        </div>
      </a>
    </section>
  </main>

  <?= view('admin/components/scripts') ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      championship: <?= json_encode($championship ?? []) ?>,
      divisions: <?= json_encode($divisions ?? []) ?>
    }

    Vue.createApp({
      data () {
        return {
          ...pageData
        }
      }
    }).mount('main')
  </script>
</body>
</html>