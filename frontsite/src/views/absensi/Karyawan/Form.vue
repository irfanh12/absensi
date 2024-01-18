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
  type_id: 4,
  address: "",
  perusahaan_id: "e2d43ef5-cc0c-4ebe-bdb0-f66dbb230d51",
  identify_id: "",
  position: "",
  first_name: "",
  last_name: "",
  phone_number: "",
  birthdate: "",
  gender: "male",
  address: "",
  salary: "",
})

let clients = reactive({
  data: [],
});

onMounted(() => {
  getListClients(clients);

  form.id = route.params.id ?? "";
  if(form.id) {
    loadDataForm(form, karyawan);
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
    const respData = await KaryawanController.getListClients();
    
    if (respData.success) {
      clients.data = respData.data;
    }
  } catch (error) {
    console.error(error);
  }
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
    const { success, data } = await KaryawanController.storeKaryawan(form, karyawan);
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
  <div class="content mb-4">
    <BaseBlock ref="karyawan" title="Form Karyawan" class="mb-0">
      <form @submit.prevent="onSubmit">

        <div class="mb-4">
          <h3>Status Karyawan</h3>
        </div>

        <div class="mb-4">
          <label class="form-label" for="example-radios">Status Karyawan</label>
          <div class="form-check">
            <input
              v-model.number="form.type_id"
              class="form-check-input"
              type="radio"
              id="example-radios-default1"
              name="type_id"
              value="4"
              checked />
            <label class="form-check-label" for="example-radios-default1">Internal</label>
          </div>
          <div class="form-check">
            <input
              v-model.number="form.type_id"
              class="form-check-input"
              type="radio"
              id="example-radios-default2"
              name="type_id"
              value="5" />
            <label class="form-check-label" for="example-radios-default2">Outsource</label>
          </div>
        </div>
        
        <div class="mb-4" v-show="form.type_id === '5'">
          <label class="form-label" for="email">
            Klien
          </label>
          <select class="form-select" id="perusahaan_id" name="perusahaan_id" v-model="form.perusahaan_id">
            <option selected>Pilih Klien</option>
            <option :value="client.perusahaan.id" v-for="client in clients.data" :key="client.id">
              {{ client.perusahaan.nama_perusahaan }}
            </option>
          </select>
        </div>

        <div class="mb-4">
          <h3>Login akun</h3>
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

        <div class="mb-4">
          <h3>Data Karyawan</h3>
        </div>

        <div class="mb-4">
          <label class="form-label" for="position">
            Posisi <span class="text-danger">*</span>
          </label>
          <input required type="text" class="form-control" id="position" name="position" v-model="form.position"/>
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
          <label class="form-label">Gender</label>
          <div class="space-x-2">
            <div class="form-check form-check-inline">
              <input
                class="form-check-input"
                type="radio"
                id="male-gender"
                name="gender"
                v-model="form.gender"
                value="male"
                checked
              />
              <label class="form-check-label" for="male-gender">Male</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                class="form-check-input"
                type="radio"
                id="female-gender"
                name="gender"
                v-model="form.gender"
                value="female"
              />
              <label class="form-check-label" for="female-gender">Female</label>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label" for="identify_id">
            Identity ID (KTP/SIM) <span class="text-danger">*</span>
          </label>
          <input required type="text" class="form-control" id="identify_id" name="identify_id" v-model.number="form.identify_id"/>
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
            Save Karyawan
          </button>
        </div>
        
      </form>
    </BaseBlock>
  </div>
</template>