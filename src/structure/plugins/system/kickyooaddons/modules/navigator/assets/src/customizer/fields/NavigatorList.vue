<template>
  <div>
    <div v-show="fields.length" class="uk-margin-small">
      <select v-show="fields.length" v-model="value" v-bind="attributes" class="uk-select">
        <option disabled value="">{{ $t('Select a Target') }}</option>
        <option v-for="option in fields" :key="option.props.modalid" :value="option.props.modalid">{{ option.props.title }}</option>
      </select>
    </div>
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


  mounted() {
    this.load();
  },

  methods: {

    load() {
      var item = this.Builder.parent(this.$node);
      this.fields = this.Builder.parent(item).children;
    },
  }
};

</script>
