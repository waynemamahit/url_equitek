import { createStore } from 'vuex'

// Create a new store instance.
export default createStore({
  state() {
    return {
      name: "",
      email: "",
      // Main
      links: [],
      detailLink: null,
      loading: false,
      keyword: "",
      url: "",
      source: "",
      loading: false,
      formDialog: false,
      formDialogTitle: "",
      formLoading: false,
      formItem: null,
    }
  },
  mutations: {
    changeUser (state, item) {
      state.name = item.name
      state.email = item.email
    },
    toggleLoading(state, item) {
      if (item) state.formLoading = !state.formLoading
      else state.loading = !state.loading
    },
    updateLinks(state, item) {
      state.links = item.links
    },
    updateDetailLink(state, item) {
      state.detailLink = item
    },
    updateKeyword(state, item) {
      state.keyword = item.value
    },
    updateUrl(state, item) {
      state.url = item.value
    },
    updateSource(state, item) {
      state.source = item.value
    },
    updateFormDialog(state) {
      state.formDialog = !state.formDialog
    },
    openForm(state, item) {
      state.formDialog = true
      state.formDialogTitle = item.formTitle
      state.url = ""
      state.source = ""

      if (item.formTitle == "Edit") {
        state.formItem = item.linkItem
        state.url = item.linkItem.url
        state.source = item.linkItem.source
      }
    },
  },
  actions: {
    
  }
})
