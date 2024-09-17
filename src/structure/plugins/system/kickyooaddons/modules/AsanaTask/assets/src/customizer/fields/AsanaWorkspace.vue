<template>

  <div>
    <h3 class="yo-sidebar-subheading">{{ $t('Workspace') }}</h3>

    <div class="uk-margin-small">

      <select v-show="workspaces.length" v-model="value" v-bind="attributes" class="uk-select">
        <option disabled value="">{{ $t('Select a Workspace') }}</option>
        <option v-for="option in workspaces" :key="option.value" :value="option.value">{{ option.text }}</option>
      </select>

      <p v-show="workspaces.length" class="uk-text-muted uk-margin-small">{{ $t('Select the workspace to get projects.') }}</p>
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
    workspaces: [],
    projects: [],
    error: false
  }),

  computed: {
    apiKey() {
      return this.Config.values[`asana_api`];
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

      this.$http.post('theme/asana/workspaces').then(
          res => {
            this.workspaces = res.data.workspaces;
          },
          res => this.error = res.data
      );
    },
  }
};

</script>
