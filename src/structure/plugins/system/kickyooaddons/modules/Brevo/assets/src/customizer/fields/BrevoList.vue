<template>

  <div>
    <h3 class="yo-sidebar-subheading">{{ $t('Folders') }}</h3>

    <div class="uk-margin-small">

      <select v-show="folders.length" v-model="folder_id" v-bind="attributes" class="uk-select" v-on:change="loadLists">
        <option disabled value="">{{ $t('Select a Folder') }}</option>
        <option v-for="option in folders" :key="option.value" :value="option.value">{{ option.text }} ({{ option.subscribers }})</option>
      </select>

      <p v-show="folders.length" class="uk-text-muted uk-margin-small">{{ $t('Select the folder to get lists.') }}</p>

    </div>

    <h3 v-show="lists.length && folder_id !== ''" class="yo-sidebar-subheading">{{ $t('Lists') }}</h3>

    <div v-show="lists.length && folder_id !== ''" class="uk-margin-small">

      <select v-show="lists.length && folder_id !== ''" v-model="value[folder_id]" v-bind="attributes" class="uk-select" multiple="multiple" row="20">
        <option v-for="option in lists" :key="option.value" :value="option.value">#{{ option.value }} {{ option.text }} ({{ option.subscribers }})</option>
      </select>

      <p v-show="lists.length" class="uk-text-muted uk-margin-small">{{ $t('Select the list to subscribe to.') }}</p>

    </div>

    <p v-show="!apiKey" class="uk-text uk-text-danger uk-margin-small">{{ $t('Enter the API key in Settings > External Services.') }}</p>

    <p v-show="error" class="uk-text uk-text-danger uk-margin-small">{{ $t(`${error}`) }}</p>

  </div>

</template>

<script>

export default {

  extends: Vue.component('field'),

  inject: ['Config'],

  data: () => ({
    folder_id : '',
    folders: [],
    lists: [],
    error: false
  }),

  computed: {
    apiKey() {
      return this.Config.values[`brevo_api`];
    }
  },

  mounted() {
    this.load();
  },

  methods: {

    load() {
      if (!this.apiKey) {
        return;
      }

      this.$http.post('theme/brevo/checkapi').then(
          res => {
            this.$http.post('theme/brevo/folder').then(
                res => {
                  this.folders = res.data.folders;
                },
                res => this.error = res.data
            );
          },
          res =>  {
            this.error = res;
          }
      );
    },

    loadLists() {

      if (!this.apiKey || !this.folder_id) {
        this.lists = [];
        return;
      }

      this.$http.post('theme/brevo/list', {folder: this.folder_id}).then(
          res => {
            this.lists = res.data.lists;
          },
          res => this.error = res.data
      );
    }
  }
};

</script>
