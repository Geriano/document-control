<script setup>
import { getCurrentInstance, nextTick, ref } from 'vue'
import { useForm, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import DataTable from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'
import Card from '@/Components/Card.vue'
import Icon from '@/Components/Icon.vue'
import Swal from 'sweetalert2'

const self = getCurrentInstance()
const open = ref(false)
const a = ref(true)
const form = useForm({
  id: null,
  name: '',
  code: '',
  max_revision_interval: 36,
})

const show = () => {
  open.value = true
  nextTick(() => self.refs.name?.focus())
}

const close = () => {
  open.value = false
  form.clearErrors()
}

const store = () => {
  return form.post(route('document.store'), {
    onSuccess: () => {
      close()
      form.reset()
      a.value = false
      nextTick(() => a.value = true)
    },
    onError: () => nextTick(show),
  })
}

const edit = (document, refresh) => {
  form.id = document.id
  form.name = document.name
  form.code = document.code
  form.max_revision_interval = document.max_revision_interval
  
  show()

  Inertia.on('success', () => refresh())
}

const update = () => {
  return form.patch(route('document.update', form.id), {
    onSuccess: () => close() || form.reset(),
    onError: () => nextTick(show),
  })
}

const destroy = async (document, refresh) => {
  try {
    const response = await Swal.fire({
      title: __('are you sure') + '?',
      text: __('this will delete all of revision too and it can\'t recovered'),
      icon: 'warning',
      showCloseButton: true,
      showCancelButton: true,
    })

    if (response.isConfirmed) {
      Inertia.on('success', () => refresh())
      Inertia.delete(route('document.destroy', document.id))
    }
  } catch (e) {
    const response = await Swal.fire({
      title: __('are you want to try again') + '?',
      text: `${e}`,
      icon: 'error',
      showCancelButton: true,
      showCloseButton: true,
    })

    response.isConfirmed && destroy(document, refresh)
  }
}

const submit = () => {
  return form.id ? update() : store()
}
</script>

<template>
  <DashboardLayout :title="__('Document')">
    <div class="flex flex-col space-y-2 bg-white rounded-md">
      <div class="flex items-center space-x-2 bg-slate-200 p-2 rounded-t-md">
        <button @click.prevent="show" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-sm text-white transition-all">
          <div class="flex items-center space-x-1">
            <Icon src="plus" />
            <p class="uppercase font-semibold">{{ __('create') }}</p>
          </div>
        </button>
      </div>

      <div class="flex flex-col space-y-4 p-4">
        <DataTable v-if="a" :url="route('api.v1.document.paginate')" :sticky="true">
          <template v-slot:thead="table">
            <tr class="bg-slate-100">
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap px-1 py-2">no</Th>
              <Th :table="table" :sort="true" name="name" class="border border-slate-200 text-center whitespace-nowrap px-3 py-2">{{ __('name') }}</Th>
              <Th :table="table" :sort="true" name="code" class="border border-slate-200 text-center whitespace-nowrap px-3 py-2">{{ __('code') }}</Th>
              <Th :table="table" :sort="true" name="max_revision_interval" class="border border-slate-200 text-center whitespace-nowrap px-3 py-2">{{ __('revision interval month') }}</Th>
              <Th :table="table" :sort="true" name="created_at" class="border border-slate-200 text-center whitespace-nowrap px-3 py-2">{{ __('created at') }}</Th>
              <Th :table="table" :sort="true" name="updated_at" class="border border-slate-200 text-center whitespace-nowrap px-3 py-2">{{ __('updated at') }}</Th>
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap px-3 py-2">{{ __('last revision') }}</Th>
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap px-3 py-2">{{ __('action') }}</Th>
            </tr>
          </template>

          <template v-slot:tfoot="table">
            <tr class="bg-slate-100">
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap py-2">no</Th>
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap py-2">{{ __('name') }}</Th>
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap py-2">{{ __('code') }}</Th>
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap py-2">{{ __('revision interval month') }}</Th>
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap py-2">{{ __('created at') }}</Th>
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap py-2">{{ __('updated at') }}</Th>
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap py-2">{{ __('last revision') }}</Th>
              <Th :table="table" :sort="false" class="border border-slate-200 text-center whitespace-nowrap py-2">{{ __('action') }}</Th>
            </tr>
          </template>

          <template v-slot:tbody="{ data, refresh }">
            <tr v-for="(document, i) in data" :key="i">
              <td class="border border-slate-200 text-center py-1">{{ i + 1 }}</td>
              <td class="border border-slate-200 px-2 py-1">{{ document.name }}</td>
              <td class="border border-slate-200 px-2 py-1">{{ document.code }}</td>
              <td class="border border-slate-200 px-2 py-1">{{ document.max_revision_interval }}</td>
              <td class="border border-slate-200 px-2 py-1">{{ new Date(document.created_at).toLocaleString('id') }}</td>
              <td class="border border-slate-200 px-2 py-1">{{ new Date(document.updated_at).toLocaleString('id') }}</td>
              <td class="border border-slate-200 px-2 py-1">{{ document.revision?.code }}</td>
              <td class="border border-slate-200 px-2 py-1">
                <div class="flex items-center justify-center">
                  <div class="flex-wrap w-fit">
                    <button v-if="document.approved" @click.prevent="Inertia.get(route('document.revisions', document.id))" class="bg-emerald-600 hover:bg-emerald-700 rounded-md px-3 py-1 text-sm transition-all m-[1px] text-white">
                      <div class="flex items-center space-x-1">
                        <Icon src="list" />
                        <p class="uppercase font-semibold">{{ __('revisions') }}</p>
                      </div>
                    </button>

                    <button v-if="document.approve ? false : (document.approved ? false : (document.rejected ? false : !document.pending))" @click.prevent="Inertia.get(route('document.approvers', document.id))" class="bg-orange-600 hover:bg-orange-700 rounded-md px-3 py-1 text-sm transition-all m-[1px] text-white">
                      <div class="flex items-center space-x-1">
                        <Icon src="user-cog" />
                        <p class="uppercase font-semibold">{{ __('approvers') }}</p>
                      </div>
                    </button>

                    <button v-if="document.approvers_count > 0 && !document.approved" @click.prevent="Inertia.get(route('document.approvals', document.id))" class="bg-cyan-600 hover:bg-cyan-700 rounded-md px-3 py-1 text-sm transition-all m-[1px] text-white">
                      <div class="flex items-center space-x-1">
                        <Icon src="user-check" />
                        <p class="uppercase font-semibold">{{ __('approvals') }}</p>
                      </div>
                    </button>

                    <button v-if="document.approved ? false : (document.rejected ? true : !document.pending)" @click.prevent="edit(document, refresh)" class="bg-blue-600 hover:bg-blue-700 rounded-md px-3 py-1 text-sm transition-all m-[1px] text-white">
                      <div class="flex items-center space-x-1">
                        <Icon src="edit" />
                        <p class="uppercase font-semibold">{{ __('edit') }}</p>
                      </div>
                    </button>

                    <button v-if="document.approved ? false : (document.rejected ? true : !document.pending)" @click.prevent="destroy(document, refresh)" class="bg-red-600 hover:bg-red-700 rounded-md px-3 py-1 text-sm transition-all m-[1px] text-white">
                      <div class="flex items-center space-x-1">
                        <Icon src="edit" />
                        <p class="uppercase font-semibold">{{ __('delete') }}</p>
                      </div>
                    </button>
                  </div>
                </div>
              </td>
            </tr>

            <tr v-if="data?.length === 0">
              <td colspan="1000" class="text-5xl font-semibold text-center p-4 lowercase first-letter:capitalize">
                {{ __('there are no data available :\'(') }}
              </td>
            </tr>
          </template>
        </DataTable>
      </div>
    </div>
  </DashboardLayout>

  <transition name="fade">
    <div v-if="open" class="fixed top-0 left-0 w-full h-screen flex items-center justify-center bg-black bg-opacity-40">
      <form @submit.prevent="submit" class="w-full max-w-xl shadow-xl">
        <div class="flex flex-col space-y-4 bg-white rounded-md">
          <div class="flex items-center space-x-2 justify-end p-2 bg-slate-200 rounded-md">
            <Icon @click.prevent="close" src="times" class="px-2 py-1 rounded-md bg-slate-100 hover:bg-slate-50 transition-all cursor-pointer" />
          </div>

          <div class="flex flex-col space-y-4 p-4">
            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="name" class="lowercase first-letter:capitalize w-1/3">{{ __('name') }}</label>
                <input type="text" name="name" ref="name" v-model="form.name" class="w-full bg-transparent rounded-md border border-slate-200 px-3 py-1 uppercase" required :placeholder="__('name')">
              </div>

              <div v-if="form.errors.name" class="text-sm text-red-500 text-right">{{ form.errors.name }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="code" class="lowercase first-letter:capitalize w-1/3">{{ __('code') }}</label>
                <input type="text" name="code" v-model="form.code" class="w-full bg-transparent rounded-md border border-slate-200 px-3 py-1 uppercase" required :placeholder="__('code')">
              </div>

              <div v-if="form.errors.code" class="text-sm text-red-500 text-right">{{ form.errors.code }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="max_revision_interval" class="lowercase first-letter:capitalize w-1/3">{{ __('revision interval') }}</label>
                <input type="number" name="max_revision_interval" min="1" v-model="form.max_revision_interval" class="w-full bg-transparent rounded-md border border-slate-200 px-3 py-1 uppercase" required :placeholder="__('revision interval')">
              </div>

              <div v-if="form.errors.max_revision_interval" class="text-sm text-red-500 text-right">{{ form.errors.max_revision_interval }}</div>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-2 px-2 py-1 bg-slate-200 rounded-b-md">
            <button type="submit" class="bg-slate-700 hover:bg-slate-800 rounded-md px-3 py-1 text-sm text-white transition-all">
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