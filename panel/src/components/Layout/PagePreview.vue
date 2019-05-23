<template>
  <iframe
    ref="iframe"
    class="k-page-preview"
    name="k-page-preview"
    @load="onLoaded"
  />
</template>

<script>
export default {
  props: {
    page: Object,
  },
  data() {
    return {
      cors: false,
      scroll: 0
    }
  },
  computed: {
    changes() {
      return this.$store.getters["form/values"](this.id);
    },
    id() {
      return this.$store.state.form.current;
    }
  },
  watch: {
    changes() {
      this.load();
    },
    page() {
      this.load();
    }
  },
  mounted() {
    this.canAccess();
    this.load();
    this.$events.$on("model.update", this.load);
  },
  destroyed() {
    this.$events.$off("model.update", this.load);
  },
  methods: {
    canAccess() {
      try {
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        this.cors = doc.body.innerHTML !== null;
      } catch(err){
        this.cors = false
      }
    },
    load() {
      if (this.page.previewUrl) {
        // if permitted, store scroll position
        if (this.cors) {
          this.scroll = this.$refs.iframe.contentWindow.pageYOffset;
        }

        // create fake form element
        let form = document.createElement("form");
        form.action = this.page.previewUrl;
        form.target = "k-page-preview";

        // create fake input element with JSOn stringified changes
        let input = document.createElement("input");
        input.type = "hidden";
        input.name = "preview";
        input.value = JSON.stringify(this.changes);

        // submit form to iframe
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
      }
    },
    onLoaded() {
      // if permitted, restore scroll position
      if (this.cors) {
        this.$refs.iframe.contentWindow.scrollTo({top: this.scroll});
      }
    }
  }
};
</script>

<style lang="scss">
.k-page-preview {
  width: 66.6666%;
  background: #fff;
  border: 0;
  border-left: 1px solid $color-border;
}
</style>
