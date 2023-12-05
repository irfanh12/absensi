<script setup>
import { reactive, computed } from "vue";

// Vuelidate, for more info and examples you can check out https://github.com/vuelidate/vuelidate
import useVuelidate from "@vuelidate/core";
import { required, minLength, email, sameAs } from "@vuelidate/validators";

// Input state variables
const state = reactive({
  dbName: null,
  dbHost: null,
  dbPrefix: null,
  dbUsername: null,
  dbPassword: null,
  userEmail: null,
  userPassword: null,
  userConfirmPassword: null,
});

// Validation rules
const rules = computed(() => {
  return {
    dbName: {
      required,
      minLength: minLength(3),
    },
    dbUsername: {
      required,
      minLength: minLength(3),
    },
    dbPassword: {
      required,
      minLength: minLength(8),
    },
    userEmail: {
      required,
      email,
    },
    userPassword: {
      required,
      minLength: minLength(8),
    },
    userConfirmPassword: {
      required,
      sameAs: sameAs(state.userPassword),
    },
  };
});

// Use vuelidate
const v$ = useVuelidate(rules, state);

// On form submission
async function onSubmit() {
  const result = await v$.value.$validate();

  if (!result) {
    // notify user form is invalid
    return;
  }

  // perform async actions
}
</script>

<template>
  <!-- Page Content -->
  <div class="row g-0 justify-content-center">
    <div class="hero-static col-lg-7">
      <div class="content content-full overflow-hidden">
        <!-- Header -->
        <div class="py-5 text-center">
          <a href="index.php">
            <i class="fa fa-2x fa-circle-notch text-primary"></i>
          </a>
          <h1 class="h3 fw-bold mt-3 mb-2">Welcome to your web app</h1>
          <h2 class="fs-base fw-medium text-muted mb-0">
            Let's get it installed, it will only take a few seconds!
          </h2>
        </div>
        <!-- END Header -->

        <!-- Installation form -->
        <form @submit.prevent="onSubmit">
          <!-- Database section -->
          <BaseBlock title="1. Database">
            <div class="row items-push">
              <div class="col-lg-4">
                <p class="fs-sm text-muted">
                  Please pay extra attention because adding the correct database
                  info is vital for a successful app installation.
                </p>
              </div>
              <div class="col-lg-6 offset-lg-1">
                <div class="mb-4">
                  <label class="form-label" for="install-db-name">Name</label>
                  <input
                    type="text"
                    class="form-control form-control-lg"
                    id="install-db-name"
                    name="install-db-name"
                    placeholder="What's the name of your database?"
                    :class="{
                      'is-invalid': v$.dbName.$errors.length,
                    }"
                    v-model="state.dbName"
                    @blur="v$.dbName.$touch"
                  />
                  <div
                    v-if="v$.dbName.$errors.length"
                    class="invalid-feedback animated fadeIn"
                  >
                    Please enter a database name
                  </div>
                </div>
                <div class="mb-4">
                  <label class="form-label" for="install-db-host">Host</label>
                  <input
                    type="text"
                    class="form-control form-control-lg"
                    id="install-db-host"
                    name="install-db-host"
                    placeholder="Leave empty for 'localhost'"
                  />
                </div>
                <div class="mb-4">
                  <label class="form-label" for="install-db-prefix"
                    >Table Prefix</label
                  >
                  <input
                    type="text"
                    class="form-control form-control-lg"
                    id="install-db-prefix"
                    name="install-db-prefix"
                    placeholder="Leave empty for 'app_'"
                  />
                </div>
                <div class="mb-4">
                  <label class="form-label" for="install-db-username"
                    >Username</label
                  >
                  <input
                    type="text"
                    class="form-control form-control-lg"
                    id="install-db-username"
                    name="install-db-username"
                    placeholder="Database username"
                    :class="{
                      'is-invalid': v$.dbUsername.$errors.length,
                    }"
                    v-model="state.dbUsername"
                    @blur="v$.dbUsername.$touch"
                  />
                  <div
                    v-if="v$.dbUsername.$errors.length"
                    class="invalid-feedback animated fadeIn"
                  >
                    Please enter a database username
                  </div>
                </div>
                <div class="mb-4">
                  <label class="form-label" for="install-db-password"
                    >Password</label
                  >
                  <input
                    type="password"
                    class="form-control form-control-lg"
                    id="install-db-password"
                    name="install-db-password"
                    placeholder="Database password"
                    :class="{
                      'is-invalid': v$.dbPassword.$errors.length,
                    }"
                    v-model="state.dbPassword"
                    @blur="v$.dbPassword.$touch"
                  />
                  <div
                    v-if="v$.dbPassword.$errors.length"
                    class="invalid-feedback animated fadeIn"
                  >
                    Please enter a database password
                  </div>
                </div>
              </div>
            </div>
          </BaseBlock>
          <!-- END Database section -->

          <!-- Administrator section -->
          <BaseBlock title="2. Administrator">
            <div class="row items-push">
              <div class="col-lg-4">
                <p class="fs-sm text-muted">
                  Please add your email and a strong password to create the
                  administrator account.
                </p>
              </div>
              <div class="col-lg-6 offset-lg-1">
                <div class="mb-4">
                  <label class="form-label" for="install-admin-email"
                    >Email</label
                  >
                  <input
                    type="text"
                    class="form-control form-control-lg"
                    id="install-admin-email"
                    name="install-admin-email"
                    :class="{
                      'is-invalid': v$.userEmail.$errors.length,
                    }"
                    v-model="state.userEmail"
                    @blur="v$.userEmail.$touch"
                  />
                  <div
                    v-if="v$.userEmail.$errors.length"
                    class="invalid-feedback animated fadeIn"
                  >
                    Please enter your email
                  </div>
                </div>
                <div class="mb-4">
                  <label class="form-label" for="install-admin-password"
                    >Password</label
                  >
                  <input
                    type="password"
                    class="form-control form-control-lg"
                    id="install-admin-password"
                    name="install-admin-password"
                    :class="{
                      'is-invalid': v$.userPassword.$errors.length,
                    }"
                    v-model="state.userPassword"
                    @blur="v$.userPassword.$touch"
                  />
                  <div
                    v-if="v$.userPassword.$errors.length"
                    class="invalid-feedback animated fadeIn"
                  >
                    Please enter a strong password
                  </div>
                </div>
                <div class="mb-4">
                  <label class="form-label" for="install-admin-password-confirm"
                    >Password Confirmation</label
                  >
                  <input
                    type="password"
                    class="form-control form-control-lg"
                    id="install-admin-password-confirm"
                    name="install-admin-password-confirm"
                    :class="{
                      'is-invalid': v$.userConfirmPassword.$errors.length,
                    }"
                    v-model="state.userConfirmPassword"
                    @blur="v$.userConfirmPassword.$touch"
                  />
                  <div
                    v-if="v$.userConfirmPassword.$errors.length"
                    class="invalid-feedback animated fadeIn"
                  >
                    Please confirm your password
                  </div>
                </div>
              </div>
            </div>
          </BaseBlock>
          <!-- END Administrator section -->

          <!-- Actions -->
          <div class="block block-transparent">
            <div class="block-content">
              <div class="row items-push">
                <div class="col-lg-7 offset-lg-5 space-x-2">
                  <button type="submit" class="btn btn-primary mb-2">
                    <i class="fa fa-terminal opacity-50 me-1"></i> Install
                  </button>
                  <button type="submit" class="btn btn-secondary mb-2">
                    <i class="fa fa-folder-open opacity-50 me-1"></i> Config
                    folder
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- END Actions -->
        </form>
        <!-- END Installation Form -->
      </div>
    </div>
  </div>
  <!-- END Page Content -->
</template>
