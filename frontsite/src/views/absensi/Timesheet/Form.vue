<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as OwnerController from "@/controllers/Owner";

const owner = ref(null)

const route = useRoute();
const router = useRouter();

let form = reactive({
  uuid: "",
  email: "",
  password: "",
  user_type_id: 2,
  nama: "",
  address: "",
  code: "",
})

onMounted(() => {
  form.uuid = route.params.id ?? "";
  if(form.uuid) {
    loadDataForm(form, owner);
  }
});

function mappingForm(form, data) {
  form.uuid = data.id
  form.nama = data.nama
  form.email = data.user.email
  form.address = data.address
  form.code = data.code
}

async function loadDataForm(form, owner) {
  try {
    const respData = await OwnerController.loadOwner(form, owner);
    
    if (respData.success) {
      mappingForm(form, respData.data);
    }
  } catch (error) {
    console.error(error);
  }
}

async function onSubmit() {
  owner.value.statusLoading()
  try {
    const { success, data } = await OwnerController.onSubmit(form);
    console.log(success, data)
    if (success) {
      form.uuid = data.id;
      const { success } = await OwnerController.storeOwner(form);
      
      if (success) {
        owner.value.statusNormal()
        router.push({ name: "owner-index" });
      }
    }
  } catch (error) {
    console.error(error);
  }
}
</script>

<template>
  <div class="content">
    <BaseBlock ref="owner" title="Form Owner" class="mb-0">
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
            Save Owner
          </button>
        </div>
        
      </form>
    </BaseBlock>
  </div>
</template>