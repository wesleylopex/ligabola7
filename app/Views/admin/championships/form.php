<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('admin/components/head', ['meta' => ['title' => 'Campeonato']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <button onclick="history.back()" class="p-2 rounded-full bg-gray-100" data-tippy-content="Voltar">
        <i class="w-4 h-4 text-gray-500" data-feather="chevron-left"></i>
      </button>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">{{ championship.id ? 'Editar campeonato' : 'Cadastrar novo campeonato' }}</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <form @submit.prevent="onFormSubmit()" action="<?= base_url('admin/championships/save') ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <?= csrf_field() ?>
          <input type="hidden" name="id" v-model="championship.id" :value="championship.id">
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Nome do campeonato</label>
            <input type="text" name="name" v-model="championship.name" :value="championship.name" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Data de início</label>
            <input type="date" name="start_date" v-model="championship.start_date" :value="championship.start_date" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Data de término</label>
            <input type="date" name="end_date" v-model="championship.end_date" :value="championship.end_date" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4 mt-8">
            <h3 class="font-semibold text-gray-600">Divisões do campeonato</h3>
            <div class="md:flex md:items-center md:space-x-4 space-y-4 md:space-y-0 mt-4">
              <input type="text" v-model="division.name" placeholder="Insira o nome da divisão" class="mt-1 flex-grow form-input">
              <div class="flex space-x-4 items-center">
                <div class="flex items-center space-x-2">
                  <div @click="division.color = 'bg-yellow-500'" :class="{ 'border-blue-600': division.color === 'bg-yellow-500' }" class="cursor-pointer border-2 w-8 h-8 rounded-md bg-yellow-500"></div>
                  <div @click="division.color = 'bg-gray-500'" :class="{ 'border-blue-600': division.color === 'bg-gray-500' }" class="cursor-pointer border-2 w-8 h-8 rounded-md bg-gray-500"></div>
                  <div @click="division.color = 'bg-yellow-700'" :class="{ 'border-blue-600': division.color === 'bg-yellow-700' }" class="cursor-pointer border-2 w-8 h-8 rounded-md bg-yellow-700"></div>
                </div>
                <div>
                  <button @click="addDivision" type="button" class="text-sm rounded-md px-6 py-2 bg-blue-600 text-white flex items-center">
                    Adicionar
                    <div class="w-5 h-5 ml-2 animate-spin hidden"><?= icon('loader') ?></div>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="lg:col-span-12">
            <table class="lg:w-1/3 mt-4 table overflow-x-auto whitespace-nowrap w-full text-left font-normal text-gray-600 text-sm">
              <thead>
                <tr>
                  <th class="font-semibold text-sm p-3">Divisão</th>
                  <th class="font-semibold text-sm p-3">Cor</th>
                  <th class="font-semibold text-sm p-3">Ações</th>
                </tr>
                <tbody>
                  <tr v-if="divisions.length === 0" class="even:bg-white odd:bg-gray-100">
                    <td class="p-3 text-sm text-center" colspan="6">Nenhuma divisão cadastrada</td>
                  </tr>
                  <tr v-if="divisions.length !== 0" v-for="(division, index) of divisions" class="even:bg-white odd:bg-gray-100">
                    <td class="p-3 text-sm">{{ division.name }}</td>
                    <td>
                      <div :class="division.color" class="w-6 h-6 rounded-md"></div>
                    </td>
                    <td class="p-3 text-sm">
                      <button type="button" @click="deleteDivision(index)" class="text-blue-600 hover:underline">Excluir</button>
                    </td>
                  </tr>
                </tbody>
              </thead>
            </table>
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
      championship: <?= json_encode($championship ?? []) ?>,
      divisions: <?= json_encode($divisions ?? []) ?>
    }

    Vue.createApp({
      data () {
        return {
          ...pageData,
          division: {
            id: null,
            name: null,
            color: null
          },
          deletedDivisions: []
        }
      },
      methods: {
        async onFormSubmit () {
          const form = document.querySelector('form')
          const body = new FormData(form)

          body.append('divisions', JSON.stringify(this.divisions))
          body.append('deletedDivisions', JSON.stringify(this.deletedDivisions))

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
          showNotification('Campeonato salvo com sucesso')
          setTimeout(() => window.location.href = document.referrer, 2000)
        },
        addDivision () {
          if (!this.division.name || !this.division.color) {
            return showNotification('Insira o nome da divisão')
          }

          this.divisions.push(this.division)
          this.division = {
            name: '',
            color: ''
          }
        },
        deleteDivision (index) {
          if (this.divisions[index].id) {
            this.deletedDivisions.push(this.divisions[index].id)
          }

          this.divisions.splice(index, 1)
        }
      }
    }).mount('main')
  </script>
</body>
</html>