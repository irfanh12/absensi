<script setup>
import { reactive, computed, ref } from "vue";
import { useRouter } from "vue-router";
import { useTemplateStore } from "@/stores/template";
import { useAuth } from "@/stores/auth";

// Vuelidate, for more info and examples you can check out https://github.com/vuelidate/vuelidate
import useVuelidate from "@vuelidate/core";
import { required, email, minLength } from "@vuelidate/validators";

// Main store and Router
const store = useTemplateStore();
const router = useRouter();

// Auth store
const auth = useAuth();

const login = ref(null);

// Input state variables
const state = reactive({
  email: null,
  password: null,
});

// Validation rules
const rules = computed(() => {
  return {
    email: {
      required,
      email,
      minLength: minLength(3),
    },
    password: {
      required,
    },
  };
});

// Use vuelidate
const v$ = useVuelidate(rules, state);

/**
 * Submit the form data and log the user in.
 *
 * @return {Promise<void>} A promise that resolves when the login is successful.
 */
async function onSubmit() {
  login.value.statusLoading()
  const result = await v$.value.$validate();

  if (!result) {
    login.value.statusNormal()
    return;
  }

  try {
    await axios.get('sanctum/csrf-cookie');
    const response = await axios.post('api/v1/auth/login', state);

    const { token, user_details, position } = response.data.data;
    const { first_name, last_name } = user_details.karyawan;

    localStorage.setItem("token", token);
    localStorage.setItem("fullname", `${first_name} ${last_name}`);
    localStorage.setItem("position", position);
    localStorage.setItem("user", JSON.stringify(user_details));

    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

    // Put into store
    auth.login(`${first_name} ${last_name}`, position);
    
    login.value.statusNormal()

    // Redirect to the dashboard
    router.push({ name: "dashboard" });
  } catch (err) {
    login.value.statusNormal()
    if(err.response) {
        localStorage.clear();
        alert(err.response.data.error.message);
    } else {
        alert(err);
    }
  }
}
</script>

<template>
  <!-- Page Content -->
  <div class="hero-static d-flex align-items-center">
    <div class="content">
      <div class="row justify-content-center push">
        <div class="col-md-8 col-lg-6 col-xl-4">
          <!-- Sign In Block -->
          <BaseBlock ref="login" >
            <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5">
              <h1 class="h2 mb-1">{{ store.app.name }}</h1>
              <p class="fw-medium text-muted">Welcome, please login.</p>

              <!-- Sign In Form -->
              <form @submit.prevent="onSubmit">
                <div class="py-3">
                  <div class="mb-4">
                    <input
                      type="text"
                      class="form-control form-control-alt form-control-lg"
                      id="login-email"
                      name="login-email"
                      placeholder="email"
                      :class="{
                        'is-invalid': v$.email.$errors.length,
                      }"
                      v-model="state.email"
                      @blur="v$.email.$touch"
                    />
                    <div
                      v-if="v$.email.$errors.length"
                      class="invalid-feedback animated fadeIn"
                    >
                      Please enter your email
                    </div>
                  </div>
                  <div class="mb-4">
                    <input
                      type="password"
                      class="form-control form-control-alt form-control-lg"
                      id="login-password"
                      name="login-password"
                      placeholder="Password"
                      :class="{
                        'is-invalid': v$.password.$errors.length,
                      }"
                      v-model="state.password"
                      @blur="v$.password.$touch"
                    />
                    <div
                      v-if="v$.password.$errors.length"
                      class="invalid-feedback animated fadeIn"
                    >
                      Please enter your password
                    </div>
                  </div>
                  <!-- <div class="mb-4">
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        value=""
                        id="login-remember"
                        name="login-remember"
                      />
                      <label class="form-check-label" for="login-remember"
                        >Remember Me</label
                      >
                    </div>
                  </div> -->
                </div>
                <div class="row mb-4">
                  <div class="col-md-12 col-xl-12">
                    <button type="submit" class="btn btn-block w-100 btn-alt-primary">
                      <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i>
                      Sign In
                    </button>
                  </div>
                </div>
              </form>
              <!-- END Sign In Form -->
            </div>
          </BaseBlock>
          <!-- END Sign In Block -->
        </div>
      </div>
      <div class="fs-sm text-muted text-center">
        &copy;
        {{ store.app.copyright }}
        <strong>{{ store.app.name + " " + store.app.version }}</strong>
      </div>
    </div>
  </div>
  <!-- END Page Content -->
</template>
