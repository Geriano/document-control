<script setup>
import { getCurrentInstance, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { Link } from '@inertiajs/inertia-vue3'
import Swal from 'sweetalert2'
import Icon from '@/Components/Icon.vue'

const self = getCurrentInstance()
const { procedur, refresh, edit } = defineProps({
  procedur: Object,
  refresh: Function,
  drag: Function,
  drop: Function,
  edit: Function,
})

const right = () => Inertia.patch(route('procedur.right', procedur.id))
const left = () => Inertia.patch(route('procedur.left', procedur.id))
const up = () => Inertia.patch(route('procedur.up', procedur.id))
const down = () => Inertia.patch(route('procedur.down', procedur.id))

const destroy = async () => {
  const response = await Swal.fire({
    title: __('are you sure') + '?',
    text: __('you can\'t restore it after deleted'),
    icon: 'warning',
    showCancelButton: true,
    showCloseButton: true,
  })

  response.isConfirmed && Inertia.delete(route('procedur.destroy', procedur.id))
}

onMounted(() => {
  const { container } = self.refs

  if (container) {
    const first = container.firstElementChild
    const last = container.lastElementChild

    first && first.classList.add('rounded-l-md')
    last && last.classList.add('rounded-r-md')
  }
})
</script>

<template>
  <div
    @drag="drag(procedur)"
    @dragend.prevent=""
    @dragover.prevent=""
    @drop="drop(procedur)"
    class="flex items-center space-x-2 rounded-md px-4 bg-slate-200"
    :draggable="true"
    :id="procedur.id">
    <Link :href="route('procedur.edit', procedur.id)" v-if="procedur.childs?.length === 0" class="flex items-center space-x-2 w-full py-2" :draggable="false">
      <p :draggable="false">{{ procedur.position }}</p>
      <p :draggable="false">{{ procedur.name }}</p>
    </Link>

    <div v-else class="flex items-center space-x-2 w-full py-2" :draggable="false">
      <p :draggable="false">{{ procedur.position }}</p>
      <p :draggable="false">{{ procedur.name }}</p>
    </div>

    <div ref="container" class="flex-none flex items-center rounded-md" :draggable="false">
      <Icon v-if="procedur.parent_id" src="arrow-left" @click.prevent="left" class="px-2 py-1 bg-slate-100 hover:bg-slate-50 border border-slate-300 transition-all cursor-pointer" />
      <Icon v-if="procedur.position > 1" src="arrow-right" @click.prevent="right" class="px-2 py-1 bg-slate-100 hover:bg-slate-50 border border-slate-300 transition-all cursor-pointer" />
      <Icon v-if="procedur.position > 1" src="arrow-up" @click.prevent="up" class="px-2 py-1 bg-slate-100 hover:bg-slate-50 border border-slate-300 transition-all cursor-pointer" />
      <Icon v-if="procedur.parent?.childs_count !== procedur.position" src="arrow-down" @click.prevent="down" class="px-2 py-1 bg-slate-100 hover:bg-slate-50 border border-slate-300 transition-all cursor-pointer" />
      <Icon @click.prevent="edit(procedur)" src="edit" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 border border-blue-600 hover:border-blue-700 transition-all cursor-pointer text-white" />
      <Icon @click.prevent="destroy" src="trash" class="px-2 py-1 bg-red-600 hover:bg-red-700 border border-red-600 hover:border-red-700 transition-all cursor-pointer text-white" />
    </div>
  </div>
</template>