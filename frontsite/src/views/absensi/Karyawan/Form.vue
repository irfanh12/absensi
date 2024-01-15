<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as KaryawanController from "@/controllers/Karyawan";

const karyawan = ref(null)

const route = useRoute();
const router = useRouter();

let form = reactive({
  email: "",
  password: "",
  user_type_id: 4,
  nama: "",
  address: "",
  perusahaan_id: "e2d43ef5-cc0c-4ebe-bdb0-f66dbb230d51",
  identify_id: "",
  type_id: "",
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
    loadDataForm(form, karyawan);
  }
});

function mappingForm(form, data) {
  form.id = data.id
  form.nama = data.nama
  form.email = data.user.email
  form.address = data.address
  form.code = data.code
}

async function loadDataForm(form, karyawan) {
  try {
    const respData = await KaryawanController.loadKaryawan(form, karyawan);
    
    if (respData.success) {
      mappingForm(form, respData.data);
    }
  } catch (error) {
    console.error(error);
  }
}

async function onSubmit() {
  karyawan.value.statusLoading()
  try {
    const { success, data } = await KaryawanController.storeKaryawan(form);
    console.log(success, data)
    if (success) {
      karyawan.value.statusNormal()
      router.push({ name: "karyawan-index" });
    }
  } catch (error) {
    console.error(error);
  }
}
</script>

<template>
  <div class="content">
    <BaseBlock ref="karyawan" title="Form Karyawan" class="mb-0">
      <form @submit.prevent="onSubmit">

        <div class="mb-4">
          <label class="form-label" for="example-select">Status Karyawan</label>
          <div class="form-check">
            <input
              v-model="form.user_type_id"
              class="form-check-input"
              type="radio"
              id="example-radios-default1"
              name="user_type_id"
              value="4"
              checked />
            <label class="form-check-label" for="example-radios-default1">Internal</label>
          </div>
          <div class="form-check">
            <input
              v-model="form.user_type_id"
              class="form-check-input"
              type="radio"
              id="example-radios-default2"
              name="user_type_id"
              value="5" />
            <label class="form-check-label" for="example-radios-default2">Outsource</label>
          </div>
        </div>
        
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
            Phone Number <span class="text-danger">*</span>
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
            Save Karyawan
          </button>
        </div>
        
      </form>
    </BaseBlock>
  </div>
</template>