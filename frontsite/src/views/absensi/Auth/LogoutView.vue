<script setup>
import { useAuth } from "@/stores/auth";
import { onMounted } from "vue";

const auth = useAuth();

onMounted(() => {
  auth.logout();
});
</script>

<template></template>
