<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?php $this->load->view('admin/components/head', $this->data) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= assets('website/images/default-bg.png') ?>)">
  <main id="app" class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <button onclick="history.back()" class="p-2 rounded-full bg-gray-200" data-tippy-content="Voltar">
        <i class="w-5 h-5 text-gray-500" data-feather="arrow-left"></i>
      </button>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">{{ member.id ? 'Editar membro' : 'Cadastrar novo membro' }}</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <form @submit.prevent="onFormSubmit()" action="<?= base_url('admin/teamMembers/save') ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <input type="hidden" name="id" v-model="member.id" :value="member.id">
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Nome completo</label>
            <input type="text" name="name" v-model="member.name" :value="member.name" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Número de inscrição</label>
            <input type="text" name="subscription_number" v-model="member.subscription_number" :value="member.subscription_number" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">CPF</label>
            <input type="text" name="cpf" v-model="member.cpf" maxlength="14" @input="setFormattedCPF()" :value="formatCPF(member.cpf)" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">RG</label>
            <input type="text" name="rg" v-model="member.rg" :value="member.rg" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Data de nascimento</label>
            <input type="date" name="birth_date" v-model="member.birth_date" :value="member.birth_date" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="type" class="text-xs text-gray-800">Tipo de membro</label>
            <select name="type" v-model="member.type" id="type" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
              <option value=""></option>
              <option value="athlete" :selected="member.type === 'athlete'">Atleta</option>
              <option value="coach" :selected="member.type === 'coach'">Treinador</option>
              <option value="assistant" :selected="member.type === 'assistant'">Auxiliar</option>
              <option value="president" :selected="member.type === 'president'">Presidente / Representante legal</option>
            </select>
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="type" class="text-xs text-gray-800">Status</label>
            <select name="status" v-model="member.status" id="status" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
              <option value=""></option>
              <option value="pending" :selected="member.status === 'pending'">Pendente</option>
              <option value="approved" :selected="member.status === 'approved'">Aprovado</option>
              <option value="denied" :selected="member.status === 'denied'">Reprovado</option>
            </select>
            <label for="" class="error"></label>
          </div>
          <div v-show="member.status === 'denied'" class="lg:col-span-4">
            <label for="type" class="text-xs text-gray-800">Motivo de reprovação</label>
            <input type="text" placeholder="Digite aqui o motivo pelo qual o membro foi reprovado" name="denied_reason" v-model="member.denied_reason" :value="member.denied_reason" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
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
        async onFormSubmit () {
          const form = document.querySelector('form')
          const body = new FormData(form)

          const cpfValid = isCPFValid(body.get('cpf'))

          if (!cpfValid) {
            return showNotify('CPF inválido')
          }

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
          showNotify('Membro salvo com sucesso')
          setTimeout(() => window.location.href = document.referrer, 2000)
        },
        setFormattedCPF () {
          const input = document.querySelector('input[name="cpf"]')
          input.value = this.formatCPF(input.value)
        },
        formatCPF (value) {
          const cleanValue = value.replace(/\D/g, '')

          const formattedCPF = cleanValue.replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2')

          return formattedCPF
        }
      }
    }).mount('#app')
  </script>
</body>
</html>