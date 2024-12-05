<template>

  <div>

    <div v-show="fields.length && guid !== ''" class="uk-margin-small">

      <select v-show="fields.length && guid !== ''" v-model="value" v-bind="attributes" class="uk-select">
        <option disabled value="">{{ $t('Select a Field') }}</option>
        <option value="atthebeginning">{{ $t('At the beginning') }}</option>
        <option v-for="option in fields" :key="option.value" :value="option.value">{{ option.text }} <sup v-if="option.required">*</sup>({{ option.fieldType }})</option>
        <option value="attheend">{{ $t('At the end') }}</option>
      </select>

      <p v-show="fields.length" class="uk-text-muted uk-margin-small">{{ $t('Select the Field to Map Form Input.') }}</p>

    </div>

    <p v-show="!apiKey" class="uk-text uk-text-danger uk-margin-small">{{ $t('Enter the API key in advanced system settings.') }}</p>

    <p v-show="error" class="uk-text uk-text-danger uk-margin-small">{{ error }}</p>

  </div>

</template>

<script>

export default {

  extends: Vue.component('field'),

  inject: ['Config', '$node', 'Builder'],

  data: () => ({
    fields: [],
    error: false
  }),

  computed: {
    apiKey() {
      return this.Config.values[`hubspot_api`];
    },
    guid() {
      return this.Builder.parent(this.$node).props.hubspot_guid.id;
    }
  },

  mounted() {
    this.load();
  },

  methods: {

    load() {

      if (!this.apiKey || !this.guid) {
        this.fields = [];
        return;
      }

      this.$http.post('theme/kickhubspot/fields', {form: this.guid}).then(
          res => {
            this.fields = res.data.fields;
          },
          res => this.error = res.data
      );
    },
  }
};

</script>
