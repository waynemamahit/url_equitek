<template>
  <q-layout view="lHh lpR lFf">

    <q-header elevated class="bg-red text-white">
      <q-toolbar class="bg-red text-white">
        <q-btn dense flat round icon="apps" size="lg" @click="toggleLeftDrawer" />
        
        <q-toolbar-title @click="goTo('/home')" class="bg-red text-white cursor-pointer">
          EQUITEK
        </q-toolbar-title>
        
        <q-btn color="danger" icon="account_circle" flat size="lg">
          <q-menu>
            <q-list style="min-width: 150px">
              <q-item>
                <q-item-section>
                  <span class="text-red-10">
                    <q-icon name="email" color="red-10" size="sm" />
                    {{ email }}
                  </span>
                </q-item-section>
              </q-item>
              <q-item clickable @click="goTo('/setting')">
                <q-item-section>
                  <span class="text-red-10">
                    <q-icon name="settings" color="red-10" size="sm" />
                    Settings
                  </span>
                </q-item-section>
              </q-item>
              <q-item clickable @click="logout()">
                <q-item-section>
                  <span class="text-red-10">
                    <q-icon name="logout" color="red-10" size="sm" />
                    Logout
                  </span>
                </q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-drawer 
      show-if-above 
      v-model="leftDrawerOpen" 
      side="left" 
      bordered
    >
      <q-scroll-area style="height: calc(100% - 115px); margin-top: 115px;">
        <q-list>
          <q-item>
            <q-item-section class="q-px-md">
              <div class="text-h5">Actions</div>
            </q-item-section>  
          </q-item>

          <q-separator />
          
          <q-item>
            <q-item-section>
              <q-btn
                icon="add_circle_outline"
                class="q-ma-sm"
                color="teal"
                label="Create Link"
                @click="openForm({ formTitle: 'Add' })"
              >
              </q-btn>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section>
              <q-input rounded outlined class="q-ma-sm" v-model="keyword" type="email" label-color="red-10" label="Search Links..." color="red-10">
                <template v-slot:prepend>
                  <q-icon
                    color="red-10"
                    name="search" 
                  />
                </template>
              </q-input>
            </q-item-section>
          </q-item>

          <q-item class="q-pl-xs">
            <q-item-section>
              <div class="text-subtitle1">
                <q-icon name="filter_alt" color="red-10"></q-icon>
                Filter Date
              </div>
              <q-date color="red" v-model="filterDate" range  />
            </q-item-section>
          </q-item>

        </q-list>
      </q-scroll-area>

      <q-img class="absolute-top cursor-pointer" @click="goTo('/setting')" src="img/drawer.jpg" style="height: 115px;">
        <template v-slot:error>
          <div class="absolute-full flex flex-center bg-negative text-white"></div>
        </template>
        <div class="absolute-bottom bg-transparent-black text-center">
          <q-icon size="xl" name="account_circle" />
          <div class="text-weight-bold">{{ name }}</div>
          <div>{{ email }}</div>
        </div>
      </q-img>
    </q-drawer>

    <q-page-container>
      <slot />
    </q-page-container>

    <q-footer elevated class="bg-grey-9 text-white">
      <q-toolbar>
        <q-toolbar-title>
          <div class="text-center">
            Copyright 
            <span class="text-bold">{{ new Date().getFullYear() }}</span> 
            by Waney Mamahit (IT Developer from Nusantara)
          </div>
        </q-toolbar-title>
      </q-toolbar>
    </q-footer>

  </q-layout>
</template>

<script>
import { computed, inject, ref, watch} from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useQuasar } from 'quasar'
import { useStore } from 'vuex'
import axios from 'axios'

export default {
  name: "MainLayout",
  props: {
    user: {
      type: Object
    },
    menu: {
      type: Array
    }
  },
  setup (props) {
    const emitter = inject('emitter')
    const $q = useQuasar()
    const store = useStore()
    
    store.commit('changeUser', {
      name: props.user.name,
      email: props.user.email
    })

    const leftDrawerOpen = ref(false)
    const name = computed(() => store.state.name)
    const email = computed(() => store.state.email)
    const searchLink = async (value) => {
      store.commit('updateKeyword', {
        value
      })
      store.commit('toggleLoading')

      try {
        let response = await axios.post('/search/link', {
          keyword: store.state.keyword
        })
        response = await response.data
        
        if (!response.data) throw response
        
        store.commit('updateLinks', {
          links: response.data
        })
        
        if (response.data.length > 0) {
          store.commit('updateDetailLink', response.data[0])

          emitter.emit('show-chart-detail-event')
        }

        emitter.emit('get-dashboard-event')
      
      } catch (error) {
        $q.notify({
          message: error.message || "Something went wrong, try again!",
          icon: "info",
          color: "warning",
          textColor: "black",
          closeBtn: "X",
        })

        if (error.errors) {
          let errors = Object.values(error.errors)
          errors = errors.join('\n')
          
          $q.notify({
            message: errors,
            icon: "info",
            color: "warning",
            textColor: "black",
            closeBtn: "X",
          })
        } 

      } finally {
        store.commit('toggleLoading')
      }
    }

    const keyword = computed({
      get: () => store.state.keyword,
      set: (value) => searchLink(value)
    })
    let nowDate = new Date()
    let nextDate = new Date(nowDate.getTime() + 1000 * 60 * 60 * 24 * 10)
    nextDate = nextDate.toJSON().split('T')
    nextDate = nextDate[0].split('-').join('/')
    nowDate = nowDate.toJSON().split('T')
    nowDate = nowDate[0].split('-').join('/')

    const filterDate = ref({ from: nowDate, to: nextDate })
    watch(filterDate, async () => {
      if (!filterDate.value) return
      if (!Object.keys(filterDate.value).includes('from')) {
        $q.notify({
          message: "Choose range of date.",
          icon: "info",
          color: "warning",
          textColor: "black",
          closeBtn: "X",
        })
        return;
      }

      store.commit('toggleLoading')
      try {

        let start = filterDate.value.from.split('/').join('-')
        let end = filterDate.value.to.split('/').join('-')

        let response = await axios.put('/filter/link', {
          start,
          end
        })
        response = await response.data
        
        if (!response.data) throw response
        
        store.commit('updateLinks', {
          links: response.data
        })

        if (response.data.length > 0) {
          store.commit('updateDetailLink', response.data[0])
          
          emitter.emit('show-chart-detail-event')
        }

        emitter.emit('get-dashboard-event')

      } catch (error) {

        $q.notify({
          message: error.message || "Something went wrong, try again!",
          icon: "info",
          color: "warning",
          textColor: "black",
          closeBtn: "X",
        })

        if (error.errors) {
          let errors = Object.values(error.errors)
          errors = errors.join('\n')
          
          $q.notify({
            message: errors,
            icon: "info",
            color: "warning",
            textColor: "black",
            closeBtn: "X",
          })
        } 

      } finally {
        store.commit('toggleLoading')
      }
    })

    return {
      leftDrawerOpen,
      name, 
      email,
      keyword,
      filterDate,
      toggleLeftDrawer () {
        leftDrawerOpen.value = !leftDrawerOpen.value
      },
      goTo(path, method = 'get') {
        Inertia[method](path, {}, {
          onError() {
            $q.notify({
              message: "Terjadi kesalahan coba lagi!",
              color: "warning",
              textColor: "black"
            })
          }
        })
      },
      logout() {
        $q.dialog({
          title: 'Logout Confirm',
          message: 'Would you like to logout?',
          cancel: true,
          persistent: false
        }).onOk(() => {
          Inertia.put('/', {}, {
            onError: errors => {
              errors = Object.values(errors)
              errors = errors.join('\n')
              
              $q.notify({
                message: errors,
                color: 'danger',
              })
            }
          });
        })
      },
      openForm: (item) => store.commit('openForm', item) 
    }
  }
}
</script>

<style scoped>
.q-layout {
  padding: 20px;
  background-color: rgb(236, 236, 236);
}
</style>