<template>
  <div>
    <q-card>
      <q-card-section v-show="totalClicks.length > 0">
        <q-expansion-item
          expand-separator
          icon="dashboard"
          label="Dashboard"
        >
          <q-card>
            <q-card-section>
              <div class="row justify-center align-items-center">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 q-pa-md text-center">
                  <q-badge color="info" class="q-my-md">
                    <q-icon name="ads_click" size="md" color="white" class="q-mr-xs" />
                    <div class="text-h5" style="font-weight: bolder;">{{ totalClicks.length }}</div>
                  </q-badge>
                  <div class="text-h5">Total Clicks</div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 col-xl-9 q-pa-md">
                  <div style="height: 300px;" ref="totalLinkChart"></div>
                </div>
              </div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card-section>
      <q-card-section>
        <transition
          appear
          enter-active-class="animated fadeIn"
          leave-active-class="animated fadeOut"
        >
          <div v-show="!loading">
            <q-separator />
            <div v-if="links.length > 0" class="row justify-space-around">
              <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 q-pa-md">
                <!-- List Links -->
                <q-list>
                  <template v-for="(linkItem, linkIndex) in links" :key="linkIndex">
                    <q-item clickable @click="showDetailLink(linkIndex)">
                      <div class="row justify-center">
                        <div class="col-xs-12 col-sm-9">
                          <div class="text-caption">{{ new Date(detailLink['created_at']).toString() }}</div>
                          <div class="text-h6">{{ linkItem.title }}</div>
                          <div class="text-overline">
                            <a :href="href + linkItem.url" class="q-mr-sm text-red-4 text-subtitle2" style="text-decoration: none;" >{{ linkItem.url }}</a>
                            <q-badge color="info">
                              <q-icon name="ads_click" color="white" class="q-mr-xs" />
                              {{ linkItem.clicks.length }} 
                            </q-badge>
                          </div>
                          <div class="q-mx-auto">
                            <q-btn-group flat class="q-my-sm" spread>
                              <q-btn icon="edit" color="green" dense rounded @click="openForm({ formTitle: 'Edit', linkItem })"></q-btn>
                              <q-btn icon="delete" color="red" dense rounded @click="confirmDelete(linkItem)"></q-btn>
                            </q-btn-group>
                          </div>
                        </div>
                      </div>
                    </q-item>
                    <q-separator />
                  </template>
                </q-list>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 col-xl-9 q-pa-md" v-if="detailLink !== null">
                <!-- Details Links -->
                <div class="text-caption">CREATED : {{ new Date(detailLink['created_at']).toString() }}</div>
                <div class="text-h5">{{ detailLink.title }}</div>
                <div class="text-overline">{{ detailLink.source }}</div>
                <div class="text-subtitle2">
                  <span class="text-red-4">{{ href + detailLink.url }}</span>
                  <q-btn color="warning" class="q-mx-sm" rounded flat icon="link" :href="href + detailLink.url"></q-btn>
                  <q-btn color="warning" class="q-mx-sm" rounded flat icon="content_copy" @click="copyUrl(href + detailLink.url)"></q-btn>
                  <q-btn color="warning" class="q-mx-sm" rounded flat icon="qr_code" @click="showQrCode(detailLink)"></q-btn>
                  <q-btn color="warning" class="q-mx-sm" rounded flat icon="edit"  @click="openForm({ formTitle: 'Edit', linkItem: detailLink })"></q-btn>
                  <q-btn color="warning" class="q-mx-sm" rounded flat icon="delete" @click="confirmDelete(detailLink)"></q-btn>
                </div>
                <q-separator class="q-my-md" />
                <q-badge color="info">
                  <q-icon name="ads_click" color="white" class="q-mr-xs" />
                  {{ detailLink.clicks.length }} 
                </q-badge>
                <div class="text-subtitle2">Clicks all the time</div>
                <div style="height: 300px;" ref="detailLinkChart"></div>
              </div>
            </div>
            
            <q-banner v-else class="bg-warning text-black">
              <template v-slot:avatar>
                <q-icon name="info" color="black" />
              </template>
              Data links not found, create one first!
            </q-banner>
          </div>
        </transition>
      </q-card-section>

      <q-inner-loading
        size="lg"
        class="q-my-md"
        :showing="loading"
        label-class="text-teal"
        label-style="font-size: 1.1em"
      />

    </q-card>
    
    <q-dialog v-model="formDialog" :persistent="formLoading">
      <q-card style="width: 700px; max-width: 80vw;">
        <q-card-section>
          <div class="text-h6">{{ formDialogTitle }} Form</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit.prevent="submit">
            <q-input outlined v-model="url" autofocus type="text" :label="'URL' + '(' + href + ')' " color="red">
              <template v-slot:prepend>
                <q-icon
                  color="red"
                  name="link" 
                />
              </template>
            </q-input>

            <q-input outlined v-model="source" type="text" label="Source" color="red">
              <template v-slot:prepend>
                <q-icon
                  color="red"
                  name="source" 
                />
              </template>
            </q-input>

            <q-btn
              color="red"
              size="lg"
              label="SUBMIT"
              type="submit"
              style="width: 100%;"
              :loading="formLoading"
            />
          </q-form>
        </q-card-section>

      </q-card>
    </q-dialog>

    <q-dialog v-model="qrDialog" :persistent="formLoading">
      <q-card style="width: 700px; max-width: 80vw;">
        <q-card-section class="q-pt-none">
          <div class="row justify-center">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 text-center">
              <div class="text-h6">URL</div>
              <qrcodevue :value="qrCode1" :size="qrCodeSize1" level="H" />
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 text-center">
              <div class="text-h6">Source</div>
              <qrcodevue :value="qrCode2" :size="qrCodeSize2" level="H" />
            </div>
          </div>
        </q-card-section>

      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { ref, computed, inject, defineComponent } from 'vue'
import { useStore } from 'vuex'
import MainLayoutVue from '../components/MainLayout.vue'
import { useQuasar } from 'quasar'
import axios from 'axios'
import QrCodeVue from 'qrcode.vue'
import * as am5 from '@amcharts/amcharts5';
import * as am5xy from '@amcharts/amcharts5/xy';
import am5themes_Animated from '@amcharts/amcharts5/themes/Animated';
import am5themes_Kelly from '@amcharts/amcharts5/themes/Kelly';

export default defineComponent({
  name: "Home",
  layout: MainLayoutVue,
  props: {
    user: {}
  },
  components: {
    "qrcodevue": QrCodeVue
  },
  setup(props) {
    window['onCustomMounted'] = 0
    window['onFirstDetail'] = 0
    
    const emitter = inject('emitter')
    const $q = useQuasar()
    const store = useStore()
    
    const qrDialog = ref(false)
    const qrCode1 = ref("")
    const qrCodeSize1 = ref(300)
    const qrCode2 = ref("")
    const qrCodeSize2 = ref(300)
    const detailLinkChart = ref(null)
    const detailLink = computed(() => store.state.detailLink)
    const links = computed(() => store.state.links)
    const totalLinkChart = ref(null)
    const totalClicks = ref([])
    const loading = computed(() => store.state.loading)
    const url = computed({
      get: () => store.state.url,
      set: value => store.commit('updateUrl', {
        value
      }) 
    })
    const source = computed({
      get: () => store.state.source,
      set: value => store.commit('updateSource', {
        value
      }) 
    })
    const formDialog = computed({
      get: () => store.state.formDialog,
      set: () => store.commit('updateFormDialog') 
    })
    const formDialogTitle = computed(() => store.state.formDialogTitle)
    const formLoading = computed(() => store.state.formLoading)

    const showChartDetailLink = () => {
      
      if (window['rootDetail']) {
        window['rootDetail'].dispose()
      }
      
      // Create root element
      // https://www.amcharts.com/docs/v5/getting-started/#Root_element
      window['rootDetail'] = am5.Root.new(detailLinkChart.value);
      
      // Set themes
      // https://www.amcharts.com/docs/v5/concepts/themes/
      window['rootDetail'].setThemes([
        am5themes_Animated.new(window['rootDetail'])
      ]);
      
      // Create chart
      // https://www.amcharts.com/docs/v5/charts/xy-chart/
      let chart = window['rootDetail'].container.children.push(am5xy.XYChart.new(window['rootDetail'], {
        panX: 1,
        panY: 1,
        wheelX: "panX",
        wheelY: "zoomX",
      }));


      // Add cursor
      // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
      let cursor = chart.set("cursor", am5xy.XYCursor.new(window['rootDetail'], {
        behavior: "zoomX"
      }));
      cursor.lineY.set("visible", false);

      // Create axes
      // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
      let xAxis = chart.xAxes.push(am5xy.DateAxis.new(window['rootDetail'], {
        maxDeviation: 0,
        baseInterval: {
          timeUnit: "day",
          count: 1
        },
        renderer: am5xy.AxisRendererX.new(window['rootDetail'], {}),
        tooltip: am5.Tooltip.new(window['rootDetail'], {})
      }));

      let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(window['rootDetail'], {
        renderer: am5xy.AxisRendererY.new(window['rootDetail'], {})
      }));


      // Add series
      // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
      let series = chart.series.push(am5xy.ColumnSeries.new(window['rootDetail'], {
        name: "Series",
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: "value",
        valueXField: "date",
        tooltip: am5.Tooltip.new(window['rootDetail'], {
          labelText: "{valueY}"
        })
      }));



      // Add scrollbar
      // https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
      chart.set("scrollbarX", am5.Scrollbar.new(window['rootDetail'], {
        orientation: "horizontal"
      }));

      let data = []
      if (store.state.detailLink.clicks.length > 0) {
        data = store.state.detailLink.clicks.map(clickItem => {
          let clickDate = new Date(clickItem.created_at)
          return clickDate.toLocaleDateString()
        })

        data = new Set(data)
        data = Array.from(data)
        data = data.map(dataItem => {
          let clickDate = new Date(dataItem)
          let clickValue = store.state.detailLink.clicks.filter(clickItem => {
            let clickItemDate = new Date(clickItem.created_at)
            
            return clickItemDate.toLocaleDateString() === dataItem
          })

          clickValue = clickValue.length

          return {
            date: clickDate.getTime(),
            value: clickValue
          }
        })
      }

      series.data.setAll(data);

      // Make stuff animate on load
      // https://www.amcharts.com/docs/v5/concepts/animations/
      series.appear(1000);
      chart.appear(1000, 100);
    }

    const showChartTotalLink = () => {
      
      if (window['rootTotal']) {
        window['rootTotal'].dispose()
      }

      // Create root element
      // https://www.amcharts.com/docs/v5/getting-started/#Root_element
      window['rootTotal'] = am5.Root.new(totalLinkChart.value);
      
      // Set themes
      // https://www.amcharts.com/docs/v5/concepts/themes/
      window['rootTotal'].setThemes([
        am5themes_Kelly.new(window['rootTotal'])
      ]);
      
      // Create chart
      // https://www.amcharts.com/docs/v5/charts/xy-chart/
      let chart = window['rootTotal'].container.children.push(am5xy.XYChart.new(window['rootTotal'], {
        panX: 1,
        panY: 1,
        wheelX: "panX",
        wheelY: "zoomX",
      }));


      // Add cursor
      // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
      let cursor = chart.set("cursor", am5xy.XYCursor.new(window['rootTotal'], {
        behavior: "zoomX"
      }));
      cursor.lineY.set("visible", false);

      // Create axes
      // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
      let xAxis = chart.xAxes.push(am5xy.DateAxis.new(window['rootTotal'], {
        maxDeviation: 0,
        baseInterval: {
          timeUnit: "day",
          count: 1
        },
        renderer: am5xy.AxisRendererX.new(window['rootTotal'], {}),
        tooltip: am5.Tooltip.new(window['rootTotal'], {})
      }));

      let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(window['rootTotal'], {
        renderer: am5xy.AxisRendererY.new(window['rootTotal'], {})
      }));


      // Add series
      // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
      let series = chart.series.push(am5xy.ColumnSeries.new(window['rootTotal'], {
        name: "Series",
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: "value",
        valueXField: "date",
        tooltip: am5.Tooltip.new(window['rootTotal'], {
          labelText: "{valueY}"
        })
      }));



      // Add scrollbar
      // https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
      chart.set("scrollbarX", am5.Scrollbar.new(window['rootTotal'], {
        orientation: "horizontal"
      }));

      let data = []
      if (totalClicks.value.length > 0) {
        data = totalClicks.value.map(clickItem => {
          let clickDate = new Date(clickItem.created_at)
          return clickDate.toLocaleDateString()
        })

        data = new Set(data)
        data = Array.from(data)
        data = data.map(dataItem => {
          let clickDate = new Date(dataItem)
          let clickValue = totalClicks.value.filter(clickItem => {
            let clickItemDate = new Date(clickItem.created_at)
            
            return clickItemDate.toLocaleDateString() === dataItem
          })

          clickValue = clickValue.length

          return {
            date: clickDate.getTime(),
            value: clickValue
          }
        })
      }

      series.data.setAll(data);

      // Make stuff animate on load
      // https://www.amcharts.com/docs/v5/concepts/animations/
      series.appear(1000);
      chart.appear(1000, 100);
    }

    emitter.on('show-chart-detail-event', () => {
      if (detailLinkChart.value != null) {
        showChartDetailLink()
      }
    })

    emitter.on('get-dashboard-event', () => {
      if (totalLinkChart.value != null) {
        getDashboard()
      }
    })

    // emitter.on('get-links-event', () => {
    //   getLinks()
    // })


    // const reloadLinks = () => {
    //   getLinks()
    //   showChartDetailLink()  
    // }

    const getDashboard = () => {
      totalClicks.value = []
      let links = store.state.links
      for (let linkIndex = 0; linkIndex < links.length; linkIndex++) {
        totalClicks.value = [
          ...totalClicks.value,
          ...links[linkIndex].clicks
        ]
      }

      showChartTotalLink()
    }

    const getLinks = async () => {
      store.commit('toggleLoading')
      try {
        let response = await axios.get('/link')
        response = await response.data
        
        if (!response.data) throw response
        
        store.commit('updateLinks', {
          links: response.data
        })

        store.commit('updateDetailLink', store.state.links[0])
        
      } catch (error) {
        console.error(error)

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

    setInterval(() => {
      if (window['onCustomMounted'] == 0) {
        getLinks()
        window['onCustomMounted'] = 1
      }

      if (detailLinkChart.value != null && window['onFirstDetail'] == 0) {
        getDashboard()
        showChartDetailLink()
        window['onFirstDetail'] = 1
      } 
    }, 250);

    const openForm = (item) => store.commit('openForm', item)

    let href = window.location.href.split('/')
    href.pop()
    href = href.join('/')
    href = href + "/" + props.user.id + '/'
    return {
      totalLinkChart,
      totalClicks,
      qrDialog,
      qrCode1,
      qrCodeSize1,
      qrCode2,
      qrCodeSize2,
      showQrCode: (detailLink) => {
        qrDialog.value = true
        qrCode1.value = detailLink.url
        qrCode2.value = detailLink.source
      },
      href,
      detailLink,
      detailLinkChart,
      loading,
      links,
      url,
      source,
      formDialog,
      formDialogTitle,
      formLoading,
      openForm,
      getLinks,
      // reloadLinks,
      async confirmDelete(linkItem) {
        let dialog = $q.dialog({
          title: 'Delete Confirm',
          message: 'Would you like to delete this link?',
          cancel: true,
          persistent: false,
        }).onOk(async () => {
          dialog.update({
            progress: true
          })
          try {
            let response = await axios.delete('/link/' + linkItem.id)
            response = await response.data
            
            if (!response.data) throw response
            
            let links = store.state.links
            let linkIndex = links.findIndex(linkObj => linkObj.id == linkItem.id);
            links.splice(linkIndex, 1)
            
            store.commit('updateLinks', {
              links
            })


            if (links.length > 0) {
              store.commit('updateDetailLink', links[0])
              showChartDetailLink()
            }

            $q.notify({
              message: response.message,
              color: "green",
              textColor: "white",
              icon: "check_circle"
            })
            
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
            dialog.update({
              progress: false
            })
          }
        })
      },
      showDetailLink (linkIndex) {
        store.commit('updateDetailLink', store.state.links[linkIndex])
        
        showChartDetailLink()
      },
      copyUrl: (url) => {
        navigator.clipboard.writeText(url);
        $q.notify({
          message: "Copied on clipboard",
          color: "green",
          textColor: "white",
          icon: "check_circle"
        })
      },
      async submit () {
        store.commit('toggleLoading', { value: 'form' })
        
        let form = {
          method: "post",
          url: "/link",
          data: {
            url: store.state.url,
            source: store.state.source
          }
        }

        if (store.state.formDialogTitle == "Edit") {
          form.method = "put"
          form.url = "/link/" + store.state.formItem.id
        }

        try {
          let response = await axios[form.method](form.url, form.data)
          response = await response.data
          
          if (!response.data) throw response

          let links = store.state.links
          if (store.state.formDialogTitle == "Edit") {
            let linkIndex = links.findIndex(linkItem => linkItem.id == store.state.formItem.id)
            links.splice(linkIndex, 1, response.data)
          } else {
            links.push(response.data)
          }

          store.commit('updateLinks', {
            links
          })

          if (links.length > 0) {
            store.commit('updateDetailLink', links[0])
            emitter.emit('show-chart-detail-event')
            emitter.emit('get-dashboard-event')
          }

          store.commit('updateFormDialog')

          $q.notify({
            message: response.message,
            color: "green",
            textColor: "white",
            icon: "check_circle"
          })
          
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
          store.commit('toggleLoading', { value: 'form '})
        }
      },
    }
  }
});
</script>

<style>

</style>