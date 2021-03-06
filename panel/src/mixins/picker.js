import Field from "@/components/Forms/Field.vue";

export default {
  inheritAttrs: false,
  props: {
    ...Field.props,
    empty: String,
    layout: String,
    max: Number,
    multiple: Boolean,
    parent: String,
    size: String,
    value: {
      type: Array,
      default() {
        return [];
      }
    }
  },
  data() {
    return {
      selected: this.value
    };
  },
  computed: {
    elements() {
      const layouts = {
        cards: {
          list: "k-cards",
          item: "k-card"
        },
        list: {
          list: "k-list",
          item: "k-list-item"
        }
      };

      if (layouts[this.layout]) {
        return layouts[this.layout];
      }

      return layouts["list"];
    },
    more() {
      if (!this.max) {
        return true;
      }

      return this.max > this.selected.length;
    }
  },
  watch: {
    value(value) {
      this.selected = value;
    }
  },
  methods: {
    focus() {
    },
    onInput() {
      this.$emit("input", this.selected);
    },
    remove(index) {
      this.selected.splice(index, 1);
      this.onInput();
    },
    removeById(id) {
      this.selected = this.selected.filter(item => item.id !== id);
      this.onInput();
    },
    select(items) {

      // remove all items that are no longer selected
      this.selected.forEach((selected, index) => {
        if (items.filter(item => item.id === selected.id).length === 0) {
          this.selected.splice(index, 1);
        }
      });

      // add files that are not yet in the selected list
      items.forEach(item => {
        if (this.selected.filter(selected => item.id === selected.id).length === 0) {
          this.selected.push(item);
        }
      });

      this.onInput();
    }
  }
};
