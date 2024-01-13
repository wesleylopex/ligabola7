<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('admin/components/head', ['meta' => ['title' => 'Home']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main id="app" class="my-10 md:my-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <button onclick="history.back()" class="p-2 rounded-full bg-gray-200" data-tippy-content="Voltar">
        <i class="w-4 h-4 text-gray-500" data-feather="chevron-left"></i>
      </button>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">{{ member.id ? 'Editar membro' : 'Cadastrar novo membro' }}</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <form @submit.prevent="onFormSubmit()" action="<?= base_url('admin/members-teams-divisions/save') ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <?= csrf_field() ?>
          <input type="hidden" name="member_id" value="<?= !empty($member) ? $member->id : '' ?>">
          <input type="hidden" name="mtd_id" value="<?= !empty($mtd) ? $mtd->id : '' ?>">

          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Nome completo</label>
            <input type="text" name="name" value="<?= $member->name ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Número de inscrição</label>
            <input type="text" name="subscription_number" value="<?= $member->subscription_number ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">CPF</label>
            <div>
              <div class="flex items-center mt-1 form-input border-gray-200 justify-between p-0 pr-2">
                <input type="text" name="cpf" maxlength="14" data-mask="000.000.000-00" value="<?= !empty($member) ? $member->cpf : '' ?>"  class="form-input border-0 w-full h-full">
                <button data-tippy-content="Copiar CPF" type="button" @click="copyCPF()">
                  <i class="cursor-pointer w-4 h-4" data-feather="copy"></i>
                </button>
              </div>
            </div>
            <!-- <input type="text" name="cpf" maxlength="14" data-mask="000.000.000-00" value="<?= !empty($member) ? $member->cpf : '' ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent"> -->
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">RG</label>
            <input type="text" name="rg" value="<?= $member->rg ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Data de nascimento</label>
            <input type="date" name="birth_date" value="<?= $member->birth_date ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="role" class="text-xs text-gray-800">Tipo de membro</label>
            <select name="role" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
              <option value=""></option>
              <option value="athlete" :selected="mtd.role === 'athlete'">Atleta</option>
              <option value="coach" :selected="mtd.role === 'coach'">Treinador</option>
              <option value="assistant" :selected="mtd.role === 'assistant'">Auxiliar</option>
              <option value="president" :selected="mtd.role === 'president'">Presidente / Representante legal</option>
            </select>
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="status" class="text-xs text-gray-800">Status</label>
            <select name="status" v-model="mtd.status" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
              <option value=""></option>
              <option value="pending" <?= !empty($mtd) && $mtd->status === 'pending' ? 'selected' : '' ?>>Pendente</option>
              <option value="approved" <?= !empty($mtd) && $mtd->status === 'approved' ? 'selected' : '' ?>>Aprovado</option>
              <option value="denied" <?= !empty($mtd) && $mtd->status === 'denied' ? 'selected' : '' ?>>Reprovado</option>
            </select>
            <label for="" class="error"></label>
          </div>
          <div v-show="mtd.status === 'denied'" class="lg:col-span-4">
            <label for="type" class="text-xs text-gray-800">Motivo de reprovação</label>
            <input type="text" placeholder="Digite aqui o motivo pelo qual o membro foi reprovado" name="denied_reason" :value="mtd.denied_reason" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
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

  <?= view('admin/components/scripts', $this->data) ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      member: <?= json_encode($member ?? []) ?>,
      mtd: <?= json_encode($mtd ?? []) ?>
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

          const cpfValid = isCPFValid(body.get('cpf'))

          if (!cpfValid) {
            return showNotify('CPF inválido')
          }

          setFormIsLoading(form, true)

          const response = await fetch(form.action, {
            method: 'POST',
            body
          }).then(response => response.json())

          console.log(response)

          if (!response.success) {
            const error = typeof response.error === 'string'
              ? response.error
              : Object.values(response.error)[0]

            setFormIsLoading(form, false)
            return showNotification(error)
          }

          setFormIsLoading(form, false, true)
          showNotification('Membro salvo com sucesso')
          setTimeout(() => window.location.href = document.referrer, 2000)
        },
        copyCPF () {
          const cpfInput = document.querySelector('input[name="cpf"]')
          cpfInput.select()
          document.execCommand('copy')
          showNotification('CPF copiado')
        }
      }
    }).mount('#app')
  </script>
</body>
</html>