<script setup>
import { getCurrentInstance } from 'vue'
import Child from './Child.vue'

const self = getCurrentInstance()
const { procedur, childs, refresh } = defineProps({
  procedur: Object,
  childs: Array,
  refresh: Function,
  drag: Function,
  drop: Function,
  edit: Function,
})
</script>

<template>
  <div class="flex flex-col space-y-1">
    <Child :procedur="procedur" :refresh="refresh" :drag="drag" :drop="drop" :edit="edit" />

    <div class="flex flex-col space-y-1 ml-8">
      <slot />
    </div>
  </div>
</template>