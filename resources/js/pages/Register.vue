<template>
  <div class="row justify-center items-center container">
    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
      <q-card>
        <q-card-section>
          <div class="row justify-center">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
               <q-form @submit.prevent="register()">
                 <div class="text-h5 text-center">
                  <q-icon color="red" name="account_circle" />
                  <br/>
                  Sign Up
                </div>
                <q-input outlined v-model="name" autofocus type="text" label="Name" color="red">
                  <template v-slot:prepend>
                    <q-icon
                      color="red"
                      name="account_circle" 
                    />
                  </template>
                </q-input>
                <q-input outlined v-model="email" label="Email" color="red">
                  <template v-slot:prepend>
                    <q-icon
                      color="red"
                      name="email" 
                    />
                  </template>
                </q-input>
                <q-input v-model="password" outlined :type="isPwd ? 'password' : 'text'" label="Password" color="red">
                  <template v-slot:prepend>
                    <q-icon
                      color="red"
                      name="key"
                    />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      color="red"
                      :name="isPwd ? 'visibility_off' : 'visibility'"
                      class="cursor-pointer"
                      @click="isPwd = !isPwd"
                    />
                  </template>
                </q-input>
                <q-input v-model="password_confirm" outlined :type="isNewPwd ? 'password' : 'text'" label="Confirmation Password" color="red">
                  <template v-slot:prepend>
                    <q-icon
                      color="red"
                      name="password"
                    />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      color="red"
                      :name="isNewPwd ? 'visibility_off' : 'visibility'"
                      class="cursor-pointer"
                      @click="isNewPwd = !isNewPwd"
                    />
                  </template>
                </q-input>
                <q-btn
                  color="red"
                  size="lg"
                  label="CREATE"
                  type="submit"
                  style="width: 100%;"
                  :loading="loading"
                />
                <a class="text-red cursor-pointer q-mx-auto" style="display:inline-block;text-align: center;" @click="goTo('/')">Have a account? Login Here</a>
              </q-form>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script>
import { Inertia } from "@inertiajs/inertia";
import { useQuasar } from "quasar";
import { ref } from "vue"


export default {
  name: "Register",
  setup() {
    const $q = useQuasar()
    
    const name = ref("")
    const email = ref("")
    const password = ref("")
    const password_confirm = ref("")
    const isPwd = ref(true)
    const isNewPwd = ref(true)
    const loading = ref(false);
    
    const register = async () => {
      loading.value = true

      Inertia.post('/register', {
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirm.value,
      }, {
        onError: (errors) => {
          errors = Object.values(errors)
          errors = errors.join('\n')

          $q.notify({
            message: errors,
            icon: 'info',
            color: 'warning',
            textColor: 'black'
          })
        },
        onFinish: () => {
          loading.value = false;
        }
      })
    }

    return {
      name,
      email,
      password,
      password_confirm,
      isPwd,
      isNewPwd,
      loading,
      register,
      goTo(path) {
        Inertia.get(path)
      }
    }
    
  }
}
</script>

<style scoped>
.container {
  height: 100vh;
  background: #e53935;  /* fallback for old browsers */
  background: -webkit-linear-gradient(to right, #e35d5b, #e53935);  /* Chrome 10-25, Safari 5.1-6 */
  background: linear-gradient(to right, #e35d5b, #e53935); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
}
</style>