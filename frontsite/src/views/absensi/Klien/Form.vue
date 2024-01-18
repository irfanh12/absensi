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
  address: "",
  perusahaan_id: "",
  identify_id: "",
  position: "Klien",
  first_name: "",
  last_name: "",
  phone_number: "",
  birthdate: "",
  gender: "male",
  address: "",
  salary: "",
})

let perusahaan = reactive({
  nama_perusahaan: "",
  address: "",
  phone_number: "",
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
  form.password = data.karyawan.password
  form.nama = data.karyawan.nama
  form.address = data.karyawan.address
  form.perusahaan_id = data.karyawan.perusahaan_id
  form.identify_id = data.karyawan.identify_id
  form.position = data.karyawan.position
  form.first_name = data.karyawan.first_name
  form.last_name = data.karyawan.last_name
  form.phone_number = data.karyawan.phone_number
  form.birthdate = data.karyawan.birthdate
  form.gender = data.karyawan.gender
  form.address = data.karyawan.address
  form.salary = data.karyawan.salary

  perusahaan.nama_perusahaan = data.karyawan.perusahaan.nama_perusahaan
  perusahaan.address = data.karyawan.perusahaan.address
  perusahaan.phone_number = data.karyawan.perusahaan.phone_number
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
    const payload = {
      klien: {...form},
      perusahaan: {...perusahaan},
    }

    const { success, data } = await KlienController.storeKlien(payload, klien);
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
    <BaseBlock ref="klien" title="Form Klien" class="mb-0">
      <form @submit.prevent="onSubmit">

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
          <h3>Data Perusahaan</h3>
        </div>

        <div class="mb-4">
          <label class="form-label" for="nama_perusahaan">
            Nama Perusahaan <span class="text-danger">*</span>
          </label>
          <input required type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" v-model="perusahaan.nama_perusahaan"/>
        </div>

        <div class="mb-4">
          <label class="form-label" for="perusahaan.phone_number">
            Phone Number <span class="text-danger">*</span>
          </label>
          <input required type="tel" class="form-control" id="perusahaan.phone_number" name="perusahaan.phone_number" v-model="perusahaan.phone_number"/>
        </div>

        <div class="mb-4">
          <label class="form-label" for="perusahaan.address">
            Address <span class="text-danger">*</span>
          </label>
          <textarea rows="5" required class="form-control" id="perusahaan.address" name="perusahaan.address" v-model="perusahaan.address"/>
        </div>

        <div class="mb-4">
          <h3>Data Klien</h3>
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
            Save Klien
          </button>
        </div>
        
      </form>
    </BaseBlock>
  </div>
</template>