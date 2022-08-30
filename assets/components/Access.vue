<script>
import axios from "axios";

export default {
  name: 'Access',
  props: ['accessValue', 'index'],
  data: function () {
    return {
    }
  },
  methods: {
    onChangeValue(value) {
      if(typeof this.index !== undefined) {
        this.changeAccessValueReceiver(value);
      }else {
        this.accessValue = value;
      }
    },
    changeAccessValueReceiver(value) {
      this.$emit('send-change-access-value', {index: this.index, access: value});
    }
  },
}
</script>
