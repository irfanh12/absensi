<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as KlienController from "@/controllers/Klien";

const klien = ref(null)

const route = useRoute();
const router = useRouter();

let form = reactive({
  identify_id: '',
  perusahaan_id: '',
  type_id: 0,
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  position: 'Klien',
  phone_number: '',
  birthdate: '',
  gender: 'L',
  address: '',
  salary: 0,
})

onMounted(() => {
  form.uuid = route.params.id ?? "";
  if(form.uuid) {
    loadDataForm(form, klien);
  }
});

function mappingForm(form, data) {
  form.id = data.id
  form.identify_id = data.identify_id
  form.perusahaan_id = data.perusahaan_id
  form.type_id = data.type_id
  form.first_name = data.first_name
  form.last_name = data.last_name
  form.email = data.email
  form.password = data.password
  form.position = data.position
  form.phone_number = data.phone_number
  form.birthdate = data.birthdate
  form.gender = data.gender
  form.address = data.address
  form.salary = data.salary
}

async function loadDataForm(form, klien) {
  try {
    const respData = await KlienController.loadKlien(form, klien);
    
    if (respData.success) {
      mappingForm(form, respData.data);
    }
  } catch (error) {
    console.error(error);
  }
}

async function onSubmit() {
  klien.value.statusLoading()
  try {
    const { success, data } = await KlienController.onSubmit(form);
    console.log(success, data)
    if (success) {
      form.uuid = data.id;
      const { success } = await KlienController.storeKlien(form);
      
      if (success) {
        klien.value.statusNormal()
        router.push({ name: "klien-index" });
      }
    }
  } catch (error) {
    console.error(error);
  }
}
</script>

<template>
  <div class="content">
    <BaseBlock ref="klien" title="Form Klien" class="mb-0">
      <form @submit.prevent="onSubmit">

        <div class="mb-4">
          <label class="form-label" for="email">
            Email <span class="text-danger">*</span>
          </label>
          <input required type="email" class="form-control" id="email" name="email" v-model="form.email"/>
        </div>

        <div class="mb-4">
          <label class="form-label" for="password">
            Password <span class="text-danger">*</span>
          </label>
          <input required type="password" class="form-control" id="password" name="password" v-model="form.password"/>
        </div>

        <div class="mb-4">
          <label class="form-label" for="nama">
            Name <span class="text-danger">*</span>
          </label>
          <input required type="text" class="form-control" id="nama" name="nama" v-model="form.nama"/>
        </div>

        <div class="mb-4">
          <label class="form-label" for="code">
            Code <span class="text-danger">*</span>
          </label>
          <input required type="text" class="form-control" id="code" name="code" v-model="form.code"/>
        </div>

        <div class="mb-4">
          <label class="form-label" for="address">
            Address <span class="text-danger">*</span>
          </label>
          <textarea rows="5" required class="form-control" id="address" name="address" v-model="form.address"/>
        </div>

        <div class="mb-4">
          <button class="btn btn-sm btn-primary w-100">
            Save Klien
          </button>
        </div>
        
      </form>
    </BaseBlock>
  </div>
</template>