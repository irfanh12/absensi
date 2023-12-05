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
  <div class="hero-static d-flex align-items-center">
    <div class="content">
      <div class="row justify-content-center push">
        <div class="col-md-8 col-lg-6 col-xl-4">
          <!-- Two Factor Block -->
          <BaseBlock title="OneUI" class="mb-0 text-center" content-full>
            <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5">
              <h1 class="h3 fw-bold mb-1">Two Factor Authentication</h1>
              <p class="fw-medium text-muted">
                Please confirm your account by entering the authorization code
                sent to your mobile number *******5974.
              </p>

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
                  <button type="submit" class="btn btn-alt-primary px-4">
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
          </BaseBlock>
          <!-- END Two Factor Block -->
        </div>
      </div>
      <div class="fs-sm text-muted text-center">
        <strong>{{ store.app.name + " " + store.app.version }}</strong> &copy;
        {{ store.app.copyright }}
      </div>
    </div>
  </div>
  <!-- END Page Content -->
</template>
