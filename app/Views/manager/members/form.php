<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('manager/components/head', ['meta' => ['title' => 'Home']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <button onclick="history.back()" class="p-2 rounded-full bg-gray-200" data-tippy-content="Voltar">
        <i class="w-5 h-5 text-gray-500" data-feather="arrow-left"></i>
      </button>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">{{ member.id ? 'Editar membro' : 'Cadastrar novo membro' }}</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <form @submit.prevent="onFormSubmit()" action="<?= base_url('manager/members/save') ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <?= csrf_field() ?>
          <input type="hidden" name="id" :value="member.id">
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Nome completo</label>
            <input type="text" required name="name" :value="member.name" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Número de inscrição</label>
            <input type="text" @input="checkInputsRules()" name="subscription_number" :value="member.subscription_number" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">CPF</label>
            <input type="text" name="cpf" maxlength="14" data-mask="000.000.000-00" :value="member.cpf" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">RG</label>
            <input type="text" name="rg" :value="member.rg" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Data de nascimento</label>
            <input type="date" name="birth_date" :value="member.birth_date" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="role" class="text-xs text-gray-800">Tipo de membro</label>
            <select name="role" required id="role" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
              <option value=""></option>
              <option value="athlete" :selected="member.role === 'athlete'">Atleta</option>
              <option value="coach" :selected="member.role === 'coach'">Treinador</option>
              <option value="assistant" :selected="member.role === 'assistant'">Auxiliar</option>
              <option value="president" :selected="member.role === 'president'">Presidente / Representante legal</option>
            </select>
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-12 flex justify-end">
          <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 px-4 rounded-md font-medium bg-blue-600 text-white">
            Salvar
            <i class="ml-2 animate-spin hidden" data-feather="loader"></i>
          </button>
          </div>
        </form>
      </div>
    </section>
  </main>

  <!-- Scripts -->
  <?= view('manager/components/scripts') ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      member: <?= json_encode($member ?? []) ?>
    }

    Vue.createApp({
      data () {
        return {
          baseURL: pageData.baseURL,
          member: pageData.member
        }
      },
      methods: {
        checkInputsRules () {
          const subscriptionNumberInput = document.querySelector('input[name="subscription_number"]')
          const inputNamesToCheck = ['cpf', 'birth_date']

          inputNamesToCheck.forEach(inputName => {
            const input = document.querySelector(`input[name="${inputName}"]`)
            const select = document.querySelector(`select[name="${inputName}"]`)

            if (input) {
              input.required = !subscriptionNumberInput.value
            }

            if (select) {
              select.required = !subscriptionNumberInput.value
            }
          })
        },
        async onFormSubmit () {
          const form = document.querySelector('form')
          const body = new FormData(form)
          
          const cpfValid = isCPFValid(body.get('cpf'))

          // if (!cpfValid) {
          //   return showNotification('CPF inválido')
          // }

          setFormIsLoading(form, true)

          const response = await fetch(form.action, {
            method: 'POST',
            body
          }).then(response => response.json())

          console.log(response)

          if (!response.success) {
            const [error] = Object.values(response.error)

            setFormIsLoading(form, false)
            return showNotification(error)
          }

          setFormIsLoading(form, false, true)
          showNotification('Membro salvo com sucesso')
          setTimeout(() => window.location.href = `${this.baseURL}manager`, 2000)
        }
      },
      mounted () {
        this.checkInputsRules()
      }
    }).mount('main')
  </script>
</body>
</html>