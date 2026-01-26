<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?= view('manager/components/head', ['meta' => ['title' => 'Home']]) ?>
</head>
<body class="min-h-screen w-screen overflow-x-hidden bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url(<?= base_url('images/default-bg.png') ?>)">
  <main class="py-10 md:py-20">
    <header class="max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <button onclick="history.back()" class="p-2 rounded-full bg-gray-200" data-tippy-content="Voltar">
        <i class="w-4 h-4 text-gray-500" data-feather="chevron-left"></i>
      </button>
      <h1 class="mt-10 text-3xl font-bold text-gray-100">{{ member.id ? 'Editar membro' : 'Cadastrar novo membro' }}</h1>
    </header>
    <section class="mt-10 max-w-screen-2xl mx-auto px-10 md:px-20 2xl:px-10">
      <div class="w-full rounded-md bg-white p-6 shadow-md">
        <form @submit.prevent="onFormSubmit()" action="<?= base_url('manager/members/save') ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= !empty($member) ? $member->id : '' ?>">
          <input type="hidden" name="mtd_id" value="<?= !empty($memberTeamDivision) ? $memberTeamDivision->id : '' ?>">

          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">CPF</label>
            <input type="text" name="cpf" maxlength="14" @blur="findMember()" data-mask="000.000.000-00" value="<?= !empty($member) ? $member->cpf : '' ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Nome completo</label>
            <input type="text" required name="name" value="<?= !empty($member) ? $member->name : '' ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">RG</label>
            <input type="text" name="rg" value="<?= !empty($member) ? $member->rg : '' ?>" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Data de nascimento</label>
            <input type="date" required name="birth_date" v-model="birthDate" @change="checkAge()" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-4">
            <label for="role" class="text-xs text-gray-800">Tipo de membro</label>
            <select name="role" required id="role" v-model="memberRole" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
              <option value=""></option>
              <option
                value="athlete"
                <?= !empty($member) && $member->role === 'athlete' ? 'selected' : '' ?>
              >Atleta</option>
              <option
                value="coach"
                <?= !empty($member) && $member->role === 'coach' ? 'selected' : '' ?>
              >Treinador</option>
              <option
                value="assistant"
                <?= !empty($member) && $member->role === 'assistant' ? 'selected' : '' ?>
              >Auxiliar</option>
              <option
                value="president"
                <?= !empty($member) && $member->role === 'president' ? 'selected' : '' ?>
              >Presidente / Representante legal</option>
            </select>
            <label for="" class="error"></label>
          </div>
          <div v-if="memberRole === 'president'" class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">E-mail</label>
            <input type="email" required name="email" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>
          <div v-if="memberRole === 'president'" class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Telefone</label>
            <input type="text" required name="phone" class="mt-1 text-sm p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <label for="" class="error"></label>
          </div>

          <!-- New parental consent document field that shows only for minors -->
          <div v-show="isMinor" class="lg:col-span-4">
            <label for="" class="text-xs text-gray-800">Termo de autorização dos pais/responsáveis <span class="text-red-500">*</span></label>
            <input type="file" name="parental_consent_document" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 text-xs p-2 w-full rounded-md border border-gray-200 bg-transparent">
            <p class="text-xs text-gray-500 mt-1">Anexar o documento assinado pelos pais ou responsáveis autorizando a participação do menor.</p>
            <label for="" class="error"></label>
          </div>
          <div class="lg:col-span-full">
            <div id="ban-message" v-show="memberIsBanned" class="hidden w-fit bg-red-200 border border-red-600 rounded-lg p-2 flex items-center">
              <p class="text-xs leading-relaxed">  
                <span class="font-bold">Atenção</span>, o atleta que você está tentando inscrever está atualmente suspenso até {{ formatDate(member.ban_expires_at) }}, por decisão da {{ member.banned_by }}.<br>Em caso de dúvidas, entre em contato com o administrador da Liga Bola 7.
              </p>
            </div>
          </div>
          <div v-if="!this.member || !this.member.subscription_number" class="col-span-full">
            <button @click.prevent="clearForm()" type="button" class="text-xs text-blue-600">Limpar dados</button>
          </div>
          <div class="lg:col-span-12 flex items-center justify-end space-x-2">
            <p class="paragraph mr-4">Por favor, confirme os dados antes de salvar.</p>
            <button type="submit" data-loader=".feather-loader" class="flex items-center justify-center text-sm py-2 px-4 rounded-md font-medium bg-blue-600 text-white">
              Salvar
              <i class="w-5 h-5 ml-2 animate-spin hidden" data-feather="loader"></i>
            </button>
          </div>
        </form>
      </div>
    </section>

    <div id="is-finding-member" class="hidden fixed top-0 left-0 w-screen h-screen bg-black bg-opacity-40 text-white grid place-items-center">
      <i class="w-8 h-8 animate-spin" data-feather="loader"></i>
    </div>

    <!-- Scripts -->
    <?= view('manager/members/member-in-another-team-modal') ?>
  </main>

  <!-- Scripts -->
  <?= view('manager/components/scripts') ?>

  <script>
    const pageData = {
      baseURL: '<?= base_url() ?>',
      member: <?= json_encode($member ?? []) ?>
    }

    window.addEventListener('load', () => {
      document.querySelector('#ban-message').classList.remove('hidden')
    })

    Vue.createApp({
      data () {
        return {
          ...pageData,
          memberInAnotherTeam: null,
          ignoreMemberInAnotherTeam: false,
          memberIsBanned: false,
          memberRole: '',
          isMinor: false,
          birthDate: this.member && this.member.birth_date ? this.member.birth_date : ''
        }
      },
      methods: {
        checkAge () {
          if (!this.birthDate) return
          
          const today = new Date()
          const birthDate = new Date(this.birthDate)
          let age = today.getFullYear() - birthDate.getFullYear()
          const m = today.getMonth() - birthDate.getMonth()
          
          if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--
          }

          console.log(age)
          
          this.isMinor = age < 18
        },
        async findMember () {
          const cpf = document.querySelector('input[name="cpf"]').value

          if (!cpf || cpf.length !== 14) {
            return
          }

          this.setIsFindingMember(true)

          const response = await fetch(`${this.baseURL}manager/members/find?cpf=${cpf}`, {
            method: 'GET'
          }).then(response => response.json())

          const member = response.member

          this.setIsFindingMember(false)

          if (!response.success) {
            return false
          }

          const inputs = ['cpf', 'name', 'rg', 'birth_date']

          inputs.forEach(name => {
            const input = document.querySelector(`input[name="${name}"]`)

            if (!input) {
              return
            }

            input.value = member[name]
            input.readOnly = input.value
            
            // Update birthDate when birth_date is found
            if (name === 'birth_date') {
              this.birthDate = member[name]
              this.checkAge()
            }
          })

          const banExpiresAtTimestamp = member.ban_expires_at ? Date.parse(member.ban_expires_at.replace(' ', 'T')) : null
          const nowTimestamp = Date.now()

          if (banExpiresAtTimestamp && banExpiresAtTimestamp > nowTimestamp) {
            this.memberIsBanned = true
            this.member.ban_expires_at = member.ban_expires_at
            this.member.banned_by = member.banned_by
          }

          showNotification(`Os dados do atleta vinculado à esse CPF foram automaticamente completados.`)
        },
        async onFormSubmit () {
          const form = document.querySelector('form')
          const body = new FormData(form)
          
          if (this.ignoreMemberInAnotherTeam) {
            body.append('ignore_member_in_another_team', true)
          }

          const cpfValid = isCPFValid(body.get('cpf'))

          if (!cpfValid) {
            return showNotification('CPF inválido')
          }

          setFormIsLoading(form, true)

          const response = await fetch(form.action, {
            method: 'POST',
            body
          }).then(response => response.json())

          console.log(response)

          if (response.memberInAnotherTeam) {
            this.memberInAnotherTeam = response.memberInAnotherTeam

            setFormIsLoading(form, false)

            return openMemberInAnotherTeamModal()
          }

          if (!response.success) {
            const error = typeof response.error === 'string'
              ? response.error
              : Object.values(response.error)[0]

            setFormIsLoading(form, false)
            return showNotification(error)
          }

          setFormIsLoading(form, false, true)
          showNotification('Membro salvo com sucesso')
          setTimeout(() => window.location.href = `${this.baseURL}manager`, 2000)
        },
        clearForm () {
          const form = document.querySelector('form')

          form.reset()

          const inputs = ['cpf', 'name', 'rg', 'birth_date']

          inputs.forEach(name => {
            const input = document.querySelector(`input[name="${name}"]`)

            if (!input) {
              return
            }

            input.readOnly = false
          })

          this.memberIsBanned = false
        },
        setIsFindingMember (bool) {
          document.querySelector('#is-finding-member').classList.toggle('hidden', !bool)
        },
        checkInputsReadOnly () {
          if (!this.member || !this.member.subscription_number) {
            return
          }

          const inputs = ['cpf', 'name', 'rg', 'birth_date']

          inputs.forEach(name => {
            const input = document.querySelector(`input[name="${name}"]`)

            if (!input) {
              return
            }

            input.readOnly = input.value
          })
        },
        formatDate (date) {
          if (!date) {
            return ''
          }

          // Manually parse YYYY-MM-DD to avoid timezone issues that might shift the date
          if (typeof date === 'string' && /^\d{4}-\d{2}-\d{2}/.test(date)) {
            const [year, month, day] = date.split(' ')[0].split('-')
            return `${day}/${month}/${year}`
          }

          return new Date(date).toLocaleDateString('pt-BR')
        }
      },
      watch: {
        ignoreMemberInAnotherTeam (value) {
          console.log(value)
          if (value === true) {
            closeMemberInAnotherTeamModal()
            this.onFormSubmit()
          }
        }
      },
      mounted () {
        this.checkInputsReadOnly();
        this.checkAge();
      }
    }).mount('main')
  </script>
</body>
</html>