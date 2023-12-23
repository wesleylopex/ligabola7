<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('admin/components/head', ['meta' => ['title' => 'Home']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main class="my-10 md:my-20">
    <header class="w-full max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('admin/login/logout') ?>">
        <button class="p-2 rounded-full bg-gray-100" data-tippy-content="Sair da conta">
          <i class="w-4 h-4 text-gray-500 transform rotate-180" data-feather="log-out"></i>
        </button>
      </a>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">Liga Bola 7</h1>
    </header>
    <section class="mt-10 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4 w-full max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <a href="<?= base_url('admin/championships/create') ?>">
        <div class="w-full flex flex-col items-center rounded-md bg-white p-6 shadow-md">
          <div class="p-2 bg-gray-100 rounded-full">
            <i class="w-5 h-5 text-gray-600" data-feather="plus"></i>
          </div>
          <h2 class="my-2 text-gray-600 font-semibold text-sm">Novo campeonato</h2>
          <p class="text-gray-400 text-center text-xs">Clique para adicionar um novo campeonato</p>
        </div>
      </a>
      <a v-for="championship of championships" :href="`${baseURL}admin/championships/${championship.id}`">
        <div class="w-full flex flex-col items-center rounded-md bg-white p-6 shadow-md">
          <div class="p-2 bg-gray-100 rounded-full">
            <i class="w-5 h-5 text-gray-600" data-feather="info"></i>
          </div>
          <h2 class="my-2 text-gray-600 font-semibold text-sm">{{ championship.name }}</h2>
          <p class="text-gray-400 text-center text-xs">Clique para mais informações desse campeonato</p>
        </div>
      </a>
    </section>
  </main>

  <?= view('admin/components/scripts') ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      championships: <?= json_encode($championships ?? []) ?>
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