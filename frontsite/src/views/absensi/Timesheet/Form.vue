<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as KlienController from "@/controllers/Klien";

const klien = ref(null)

const route = useRoute();
const router = useRouter();

let form = reactive({
  email: "",
  password: "",
  type_id: 2,
  nama: "",
  address: "",
  perusahaan_id: "",
  identify_id: "",
  position: "",
  first_name: "",
  last_name: "",
  phone_number: "",
  birthdate: "",
  gender: "",
  address: "",
  salary: "",
})

onMounted(() => {
  form.id = route.params.id ?? "";
  if(form.id) {
    loadDataForm(form, klien);
  }
});

function mappingForm(form, data) {
  form.id = data.id
  form.email = data.email
  form.password = data.password
  form.nama = data.nama
  form.address = data.address
  form.perusahaan_id = data.perusahaan_id
  form.identify_id = data.identify_id
  form.position = data.position
  form.first_name = data.first_name
  form.last_name = data.last_name
  form.phone_number = data.phone_number
  form.birthdate = data.birthdate
  form.gender = data.gender
  form.address = data.address
  form.salary = data.salary
}

async function getListClients(clients) {
  try {
    const respData = await KlienController.getListClients();
    
    if (respData.success) {
      clients.data = respData.data;
    }
  } catch (error) {
    console.error(error);
  }
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
    const { success, data } = await KlienController.storeKlien(form);
    console.log(success, data)
    if (success) {
      klien.value.statusNormal()
      router.push({ name: "klien-index" });
    }
  } catch (error) {
    console.error(error);
  }
}
</script>

<template>
  <div class="content mb-4">
    <BaseBlock ref="klien" title="Form Klien" >
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

        <div class="row mb-4">
          <div class="col-12 col-md-6">
            <label class="form-label" for="first_name">
              First Name <span class="text-danger">*</span>
            </label>
            <input required type="text" class="form-control" id="first_name" name="first_name" v-model="form.first_name"/>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="last_name">
              Last Name <span class="text-danger">*</span>
            </label>
            <input required type="text" class="form-control" id="last_name" name="last_name" v-model="form.last_name"/>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label" for="phone_number">
            Phone Number <span class="text-danger">*</span>
          </label>
          <input required type="tel" class="form-control" id="phone_number" name="phone_number" v-model="form.phone_number"/>
        </div>

        <div class="mb-4">
          <label class="form-label" for="birthdate">
            Birthdate <span class="text-danger">*</span>
          </label>
          <input required type="date" class="form-control" id="birthdate" name="birthdate" v-model="form.birthdate"/>
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