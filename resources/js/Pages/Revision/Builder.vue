<script>
import { Inertia } from "@inertiajs/inertia"
import { useForm } from "@inertiajs/inertia-vue3"
import { defineComponent, getCurrentInstance, h } from "vue"
import Child from './Child.vue'
import Parent from './Parent.vue'

export default defineComponent({
  props: {
    procedurs: Array,
    refresh: Function,
    edit: Function,
  },

  data: () => ({
    procedurOnDrag: null,
  }),

  setup(props, attrs) {
    return props => {
      const self = getCurrentInstance()
      const { procedurs, refresh, edit } = props

      const drag = procedur => {
        self.procedurOnDrag = procedur
      }

      const drop = drop => {
        const drag = self.procedurOnDrag

        if (!drag)
          return
        
        if (drag.parent_id != drop.parent_id) {
          return
        }

        if (drag.position === drop.position) {
          return
        }
        
        useForm({
          drag: drag.id,
          drop: drop.id,
        }).patch(route('procedur.drill'))
      }

      const generate = procedur => {
        if (procedur.childs?.length) {
          return h(Parent, {
            ...attrs,
            procedur,
            childs: procedur.childs,
            refresh,
            drag,
            drop,
            edit,
          }, procedur.childs.map(child => generate(child)))
        }

        return h(Child, { ...attrs, procedur, refresh, drag, drop, edit, })
      }

      return h('div', {
        ...attrs,
        class: 'flex flex-col space-y-1',
      }, procedurs.map(procedur => generate(procedur)))
    }
  },
})
</script>