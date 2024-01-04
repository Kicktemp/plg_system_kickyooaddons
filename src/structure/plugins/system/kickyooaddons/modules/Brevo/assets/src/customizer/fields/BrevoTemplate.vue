<template>

  <div>
    <select v-show="templates.length" v-model="value" v-bind="attributes" class="uk-select">
      <option disabled value="">{{ $t('Select a Template') }}</option>
      <option v-for="option in templates" :key="option.value" :value="option.value">{{ option.text }}</option>
    </select>

    <p v-show="templates.length" class="uk-text-muted uk-margin-small">{{ $t('Select the template.') }}</p>

    <p v-show="!apiKey" class="uk-text uk-text-danger uk-margin-small">{{ $t('Enter the API key in Settings > External Services.') }}</p>

    <p v-show="error" class="uk-text uk-text-danger uk-margin-small">{{ $t(`${error}`) }}</p>

  </div>

</template>

<script>


export default {

  extends: Vue.component('field'),

  inject: ['Config'],

  data: () => ({
    templates: [],
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
            this.$http.post('theme/brevo/template').then(
                res => {
                  this.templates = res.data.templates;
                  if (!this.value && this.templates.length) {
                    this.value = this.templates[0].value;
                  }
                },
                res => this.error = res.data
            );
          },
          res =>  {
            this.error = res;
          }
      );
    }
  }
};

</script>
