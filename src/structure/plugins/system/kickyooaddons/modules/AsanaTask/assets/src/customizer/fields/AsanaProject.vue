<template>

  <div>
    <h3 class="yo-sidebar-subheading">{{ $t('Project') }}</h3>

    <div class="uk-margin-small">

      <select v-show="projects.length" v-model="value" v-bind="attributes" class="uk-select"  multiple="multiple" row="20">
        <option v-for="option in projects" :key="option.value" :value="option.value">{{ option.text }}</option>
      </select>

      <p v-show="projects.length" class="uk-text-muted uk-margin-small">{{ $t('Select the project') }}</p>
    </div>

    <p v-show="!apiKey" class="uk-text uk-text-danger uk-margin-small">{{ $t('Enter the API key in Settings > External Services.') }}</p>

    <p v-show="error" class="uk-text uk-text-danger uk-margin-small">{{ $t(`${error}`) }}</p>

  </div>

</template>

<script>

export default {

  extends: Vue.component('field'),

  inject: ['Config', '$node'],

  data: () => ({
    projects: [],
    error: false
  }),

  computed: {
    apiKey() {
      return this.Config.values[`asana_api`];
    },
    gid() {
      return this.$node.props.workspace;
    }
  },

  mounted() {
    this.load();
  },

  methods: {

    load() {
      if (!this.apiKey || !this.gid) {
        this.projects = [];
        return;
      }

      this.$http.post('theme/asana/projects', {workspace: this.gid}).then(
          res => {
            this.projects = res.data.projects;
          },
          res => this.error = res.data
      );
    },
  }
};

</script>
