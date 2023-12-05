<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useTemplateStore } from "@/stores/template";

// Main store and Router
const store = useTemplateStore();
const router = useRouter();

// Set input references
const num1 = ref("");
const num2 = ref("");
const num3 = ref("");
const num4 = ref("");
const num5 = ref("");
const num6 = ref("");

// Set input models
const num1Val = ref("");
const num2Val = ref("");
const num3Val = ref("");
const num4Val = ref("");
const num5Val = ref("");
const num6Val = ref("");

// On form submission
function onSubmit() {
  // ...

  // Go to dashboard
  router.push({ name: "backend-pages-auth" });
}

// Check if is number
function isNumber(value) {
  if (["1", "2", "3", "4", "5", "6", "7", "8", "9"].includes(value)) {
    return true;
  }

  return false;
}

// On content mounted
onMounted(() => {
  // Focus the first number input on load
  num1.value.focus();

  // Move focus to the next input
  num1.value.addEventListener("keyup", () => {
    if (isNumber(num1Val.value)) {
      num2.value.focus();
    } else {
      num1Val.value = "";
    }
  });

  num2.value.addEventListener("keyup", () => {
    if (isNumber(num2Val.value)) {
      num3.value.focus();
    } else {
      num2Val.value = "";
    }
  });

  num3.value.addEventListener("keyup", () => {
    if (isNumber(num3Val.value)) {
      num4.value.focus();
    } else {
      num3Val.value = "";
    }
  });

  num4.value.addEventListener("keyup", () => {
    if (isNumber(num4Val.value)) {
      num5.value.focus();
    } else {
      num4Val.value = "";
    }
  });

  num5.value.addEventListener("keyup", () => {
    if (isNumber(num5Val.value)) {
      num6.value.focus();
    } else {
      num5Val.value = "";
    }
  });

  num6.value.addEventListener("keyup", () => {
    if (isNumber(num6Val.value)) {
      onSubmit();
    } else {
      num6Val.value = "";
    }
  });
});
</script>

<template>
  <!-- Page Content -->
  <BaseBackground image="/assets/media/photos/photo28@2x.jpg">
    <div class="row g-0 bg-primary-dark-op">
      <!-- Meta Info Section -->
      <div
        class="hero-static col-lg-4 d-none d-lg-flex flex-column justify-content-center"
      >
        <div class="p-4 p-xl-5 flex-grow-1 d-flex align-items-center">
          <div class="w-100">
            <RouterLink
              :to="{ name: 'landing' }"
              class="link-fx fw-semibold fs-2 text-white"
            >
              One<span class="fw-normal">UI</span>
            </RouterLink>
            <p class="text-white-75 me-xl-8 mt-2">
              Welcome to your amazing app. Feel free to login and start managing
              your projects and clients.
            </p>
          </div>
        </div>
        <div
          class="p-4 p-xl-5 d-xl-flex justify-content-between align-items-center fs-sm"
        >
          <p class="fw-medium text-white-50 mb-0">
            <strong>{{ store.app.name + " " + store.app.version }}</strong>
            &copy; {{ store.app.copyright }}
          </p>
          <ul class="list list-inline mb-0 py-2">
            <li class="list-inline-item">
              <a class="text-white-75 fw-medium" href="javascript:void(0)"
                >Legal</a
              >
            </li>
            <li class="list-inline-item">
              <a class="text-white-75 fw-medium" href="javascript:void(0)"
                >Contact</a
              >
            </li>
            <li class="list-inline-item">
              <a class="text-white-75 fw-medium" href="javascript:void(0)"
                >Terms</a
              >
            </li>
          </ul>
        </div>
      </div>
      <!-- END Meta Info Section -->

      <!-- Main Section -->
      <div
        class="hero-static col-lg-8 d-flex flex-column align-items-center bg-body-extra-light text-center"
      >
        <div class="p-3 w-100 d-lg-none">
          <RouterLink
            :to="{ name: 'landing' }"
            class="link-fx fw-semibold fs-3 text-dark"
          >
            One<span class="fw-normal">UI</span>
          </RouterLink>
        </div>
        <div
          class="p-4 w-100 flex-grow-1 d-flex align-items-center justify-content-center"
        >
          <div class="col-md-8 col-xl-6">
            <!-- Header -->
            <div class="mb-5">
              <p class="mb-3">
                <i class="fa fa-2x fa-circle-notch text-primary-light"></i>
              </p>
              <h1 class="fw-bold mb-2">Two Factor Authentication</h1>
              <p class="fw-medium text-muted">
                Please confirm your account by entering the authorization code
                sent to your mobile number *******5974.
              </p>
            </div>
            <!-- END Header -->

            <!-- Two Factor Form -->
            <form @submit.prevent="onSubmit">
              <div
                class="d-flex items-center justify-content-center gap-1 gap-sm-2 mb-4"
              >
                <input
                  type="text"
                  class="form-control form-control-alt form-control-lg text-center px-0"
                  ref="num1"
                  v-model="num1Val"
                  maxlength="1"
                  style="width: 38px"
                />
                <input
                  type="text"
                  class="form-control form-control-alt form-control-lg text-center px-0"
                  ref="num2"
                  v-model="num2Val"
                  maxlength="1"
                  style="width: 38px"
                />
                <input
                  type="text"
                  class="form-control form-control-alt form-control-lg text-center px-0"
                  ref="num3"
                  v-model="num3Val"
                  maxlength="1"
                  style="width: 38px"
                />
                <span class="d-flex align-items-center">-</span>
                <input
                  type="text"
                  class="form-control form-control-alt form-control-lg text-center px-0"
                  ref="num4"
                  v-model="num4Val"
                  maxlength="1"
                  style="width: 38px"
                />
                <input
                  type="text"
                  class="form-control form-control-alt form-control-lg text-center px-0"
                  ref="num5"
                  v-model="num5Val"
                  maxlength="1"
                  style="width: 38px"
                />
                <input
                  type="text"
                  class="form-control form-control-alt form-control-lg text-center px-0"
                  ref="num6"
                  v-model="num6Val"
                  maxlength="1"
                  style="width: 38px"
                />
              </div>
              <div class="mb-4">
                <button type="submit" class="btn btn-lg btn-alt-primary">
                  Submit
                  <i class="fa fa-fw fa-arrow-right ms-1 opacity-50"></i>
                </button>
              </div>
              <p class="fs-sm pt-4 text-muted mb-0">
                Haven't received it?
                <a href="javascript:void(0)">Resend a new code</a>
              </p>
            </form>
            <!-- END Two Factor Form -->
          </div>
        </div>
        <div
          class="px-4 py-3 w-100 d-lg-none d-flex flex-column flex-sm-row justify-content-between fs-sm text-center text-sm-start"
        >
          <p class="fw-medium text-black-50 py-2 mb-0">
            <strong>{{ store.app.name + " " + store.app.version }}</strong>
            &copy; {{ store.app.copyright }}
          </p>
          <ul class="list list-inline py-2 mb-0">
            <li class="list-inline-item">
              <a class="text-muted fw-medium" href="javascript:void(0)"
                >Legal</a
              >
            </li>
            <li class="list-inline-item">
              <a class="text-muted fw-medium" href="javascript:void(0)"
                >Contact</a
              >
            </li>
            <li class="list-inline-item">
              <a class="text-muted fw-medium" href="javascript:void(0)"
                >Terms</a
              >
            </li>
          </ul>
        </div>
      </div>
      <!-- END Main Section -->
    </div>
  </BaseBackground>
  <!-- END Page Content -->
</template>
