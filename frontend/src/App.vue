<script setup>
import { ref } from "vue";
import axios from "axios";

axios.defaults.baseURL = "http://127.0.0.1:8000";
axios.defaults.withCredentials = true;

const email = ref("test@example.com");
const password = ref("password123");
const out = ref("");

function show(label, data) {
  out.value = label + ":\n" + JSON.stringify(data, null, 2);
}

async function csrf() {
  try {
    await axios.get("/sanctum/csrf-cookie");
    out.value = "CSRF cookie OK";
  } catch (e) {
    show("CSRF ERROR", e?.response?.data ?? e.message);
  }
}

async function login() {
  try {
    await csrf();
    const res = await axios.post("/api/auth/login", {
      email: email.value,
      password: password.value,
    });
    show("LOGIN OK", res.data);
  } catch (e) {
    show("LOGIN ERROR", e?.response?.data ?? e.message);
  }
}

async function me() {
  try {
    const res = await axios.get("/api/me");
    show("ME OK", res.data);
  } catch (e) {
    show("ME ERROR", e?.response?.data ?? e.message);
  }
}

async function logout() {
  try {
    const res = await axios.post("/api/auth/logout");
    show("LOGOUT OK", res.data);
  } catch (e) {
    show("LOGOUT ERROR", e?.response?.data ?? e.message);
  }
}
</script>


<template>
  <div style="max-width: 600px; margin: 40px auto; font-family: sans-serif;">
    <h2>Auth test (Sanctum cookies)</h2>

    <div style="display:flex; gap:8px; margin-bottom:12px;">
      <input v-model="email" placeholder="email" style="flex:1; padding:8px;" />
      <input v-model="password" type="password" placeholder="password" style="flex:1; padding:8px;" />
    </div>

    <div style="display:flex; gap:8px; margin-bottom:12px;">
      <button @click="csrf">CSRF</button>
      <button @click="login">Login</button>
      <button @click="me">Me</button>
      <button @click="logout">Logout</button>
    </div>

    <pre style="background:#111; color:#0f0; padding:12px; white-space:pre-wrap;">{{ out }}</pre>
  </div>
</template>
