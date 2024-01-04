<template>

  <div>
    <h3 class="yo-sidebar-subheading">{{ $t('Forms') }}</h3>

    <div class="uk-margin-small">

      <select v-show="forms.length" v-model="value" v-bind="attributes" class="uk-select" v-on:change="checkCaptcha">
        <option disabled value="">{{ $t('Select a Form') }}</option>
        <option v-for="option in forms" :key="option" :value="option">{{ option.name }} ({{ option.legalConsentOptions.type }})</option>
      </select>
    </div>
    <p v-show="!apiKey" class="uk-text uk-text-danger uk-margin-small">{{ $t('Enter the API key in advanced system settings.') }}</p>

    <p v-show="error" class="uk-text uk-text-danger uk-margin-small">{{ $t(`${error}`) }}</p>

    <p v-show="reCaptcha" class="uk-text uk-text-danger uk-margin-small">{{ $t('reCAPTCHA has been enabled on this form, submissions will not be accepted. ') }}</p>

  </div>

</template>

<script>

export default {

  extends: Vue.component('field'),

  inject: ['Config'],

  data: () => ({
    hubspot_guid : '',
    forms: [],
    error: false,
    reCaptcha: false
  }),

  computed: {
    apiKey() {
      return this.Config.values[`hubspot_api`];
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

      this.$http.post('theme/kickhubspot/forms').then(
          res => {
            this.forms = res.data.forms;
          },
          res => this.error = res.data
      );
    },

    checkCaptcha() {
      if (this.value.configuration.recaptchaEnabled)
        this.reCaptcha = true
      else
        this.reCaptcha = false
    }
  }
};

</script>
