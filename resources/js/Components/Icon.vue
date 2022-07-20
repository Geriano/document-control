<script setup>
import { getCurrentInstance } from "vue"; 

const { src } = defineProps({
  src: String,
})
</script>

<template>
  <i :class="`fa fa-${src} ${$props.class}`"></i>
</template>