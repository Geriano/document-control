<script setup>
import { getCurrentInstance, nextTick, onMounted, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Icon from '@/Components/Icon'
import axios from 'axios'
import Swal from 'sweetalert2'
import Builder from './Builder.vue'

const self = getCurrentInstance()
const { revision } = defineProps({
  revision: Object,
})

const a = ref(true)

const open = ref(false)
const form = useForm({
  id: null,
  name: '',
  revision_id: revision.id,
})
const procedures = ref([])

const show = () => {
  open.value = true
  
  nextTick(() => self.refs.name?.focus())
}

const close = () => {
  open.value = false

  fetch()
}

const fetch = async () => {
  try {
    const response = await axios.get(route('api.v1.revision.procedures', revision.id))
    procedures.value = response.data
  } catch (e) {
    const response = await Swal.fire({
      title: 'Are you want to try again?',
      text: `${e}`,
      icon: 'question',
      showCancelButton: true,
      showCloseButton: true,
    })

    response.isConfirmed && fetch()
  }
}

const store = () => {
  return form.post(route('procedure.store'), {
    onSuccess: () => {
      close()
      fetch()
      form.reset()
    },
    onError: () => nextTick(show),
  })
}

const edit = procedure => {
  form.id = procedure.id
  form.name = procedure.name

  show()
}

const update = () => {
  return form.patch(route('procedure.update', form.id), {
    onSuccess: () => {
      close()
      fetch()
      form.reset()
    },
    onError: () => nextTick(show),
  })
}

const submit = () => form.id ? update() : store()

Inertia.on('finish', () => {
  fetch().finally(() => {
    a.value = false
    nextTick(() => a.value = true)
  })
})

onMounted(fetch)
</script>

<template>
  <DashboardLayout :title="__('Revision procedures')">
    <div class="flex flex-col rounded-md bg-white">
      <div class="flex items-center space-x-1 bg-slate-200 rounded-t-md p-2">
        <Link :href="route('document.revisions', revision.document_id)" class="bg-slate-600 hover:bg-slate-700 rounded-md px-3 py-1 text-white text-sm transition-all">
          <div class="flex items-center space-x-1">
            <Icon src="caret-left" />
            <p class="uppercase font-semibold">{{ __('back') }}</p>
          </div>
        </Link>

        <button @click.prevent="show" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-white text-sm transition-all">
          <div class="flex items-center space-x-1">
            <Icon src="plus" />
            <p class="uppercase font-semibold">{{ __('create') }}</p>
          </div>
        </button>
      </div>

      <div class="flex flex-col space-y-4 p-4">
        <Builder v-if="a" :procedures="procedures" :refresh="fetch" :edit="edit" />
      </div>
    </div>
  </DashboardLayout>

  <transition name="fade">
    <div v-if="open" class="fixed top-0 left-0 w-full h-screen bg-black bg-opacity-40 flex items-center justify-center">
      <form @submit.prevent="submit" class="w-full max-w-xl shadow-xl">
        <div class="flex flex-col rounded-md bg-white">
          <div class="flex items-center space-x-2 justify-end p-2 bg-slate-200 rounded-t-md">
            <Icon @click.prevent="close" src="times" class="px-2 py-1 bg-slate-100 hover:bg-slate-50 rounded-md transition-all cursor-pointer" />
          </div>

          <div class="flex flex-col space-y-4 p-4">
            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="name" class="lowercase first-letter:capitalize w-1/3">{{ __('name') }}</label>
                <input v-model="form.name" ref="name" type="text" class="w-full bg-transparent rounded-md px-3 py-1 placeholder:capitalize" :placeholder="__('table of content')">
              </div>

              <div v-if="form.errors.name" class="text-sm text-right text-red-500">{{ form.errors.name }}</div>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-2 px-2 py-1 bg-slate-200 rounded-b-md">
            <button type="submit" class="bg-slate-700 hover:bg-slate-800 rounded-md px-3 py-1 text-white text-sm transition-all">
              <div class="flex items-center space-x-1">
                <Icon src="check" />
                <p class="uppercase font-semibold">{{ __(form.id ? 'update' : 'create') }}</p>
              </div>
            </button>
          </div>
        </div>
      </form>
    </div>
  </transition>
</template>