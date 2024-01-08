<template>
  <div>
    <h3 class="yo-sidebar-subheading">{{ $t('Lists') }}</h3>
    <div v-if="lists.length" class="uk-margin-small">
      <select v-model="value" v-bind="attributes" class="uk-select">
        <option disabled value="">{{ $t('Select a List') }}</option>
        <option v-for="option in lists" :key="option.value" :value="option.value">{{ option.text }} ({{ option.description }})</option>
      </select>
      <p class="uk-text-muted uk-margin-small">{{ $t('Select the list to subscribe to.') }}</p>
    </div>
    <p v-if="!apiUser || !apiPassword" class="uk-text uk-text-danger uk-margin-small">{{ $t('Enter API credentials in the settings under external services.') }}</p>
    <p v-if="error" class="uk-text uk-text-danger uk-margin-small">{{ error }}</p>
  </div>
</template>
<script>

export default {

  extends: Vue.component('field'),

  inject: ['Config'],

  data: () => ({
    lists: [],
    error: false
  }),

  computed: {
    apiUser() {
      return this.Config.values[`rapidmail_user`];
    },
    apiPassword() {
      return this.Config.values[`rapidmail_password`];
    }
  },

  mounted() {
    this.load();
  },

  methods: {

    load() {

      if (!this.apiUser || !this.apiPassword) {
        return;
      }

      this.$http.post('theme/rapidmail/recipientlists').then(
          res => {
            this.lists = res.data.lists;
          },
          res => this.error = res.data
      );
    },
  }
};
</script>
