<template>
  <div class="page">
    <header class="top">
      <div class="brand">Shop</div>

      <input
        class="search"
        v-model="q"
        placeholder="Поиск по названию…"
        @input="onSearch"
      />

      <button class="profile" type="button" title="Профиль (потом сделаем)">
        👤
      </button>
    </header>

    <main class="container">
      <div v-if="loading" class="hint">Загрузка товаров…</div>
      <div v-else-if="error" class="hint error">{{ error }}</div>

      <section v-else class="grid">
        <ProductCard
          v-for="p in products"
          :key="p.id"
          :product="p"
          @open="openProduct"
        />
      </section>
    </main>

    <BaseModal :open="modalOpen" @close="closeModal">
      <div v-if="modalLoading" class="hint">Загрузка…</div>
      <div v-else-if="modalError" class="hint error">{{ modalError }}</div>

      <div v-else-if="selected" class="details">
        <div class="bigImg">
          <img :src="selected.image" :alt="selected.title" />
        </div>

        <div class="info">
          <h2 class="h">{{ selected.title }}</h2>
          <div class="meta">
            <span class="badge">{{ selected.category }}</span>
            <span class="price">{{ formatPrice(selected.price) }}</span>
          </div>
          <p class="desc">{{ selected.description }}</p>

          <button class="buy" type="button" @click="buyClick">
            Купить
          </button>
          <div class="buyHint"></div>
        </div>
      </div>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { fetchProducts, fetchProduct } from "./api/products";
import BaseModal from "./components/BaseModal.vue";
import ProductCard from "./components/ProductCard.vue";

const q = ref("");
const products = ref([]);
const loading = ref(false);
const error = ref("");

const modalOpen = ref(false);
const selected = ref(null);
const modalLoading = ref(false);
const modalError = ref("");

let searchTimer = null;

async function loadList() {
  loading.value = true;
  error.value = "";
  try {
    products.value = await fetchProducts(q.value.trim());
  } catch (e) {
    error.value = e?.response?.data?.message || e.message || "Ошибка загрузки";
  } finally {
    loading.value = false;
  }
}

function onSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(loadList, 250);
}

async function openProduct(id) {
  modalOpen.value = true;
  selected.value = null;
  modalError.value = "";
  modalLoading.value = true;
  try {
    selected.value = await fetchProduct(id);
  } catch (e) {
    modalError.value = e?.response?.data?.message || e.message || "Ошибка загрузки";
  } finally {
    modalLoading.value = false;
  }
}

function closeModal() {
  modalOpen.value = false;
}

function formatPrice(v) {
  return new Intl.NumberFormat("ru-RU").format(v) + " ₽";
}

function buyClick() {
  alert("Потом подключим корзину через auth 🙂");
}

onMounted(loadList);
</script>

<style>
:root {
  color-scheme: light;
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
}

* { box-sizing: border-box; }

body {
  margin: 0;
  background: #fafafa;
  color: #111;
}

.page { min-height: 100vh; }

.top {
  position: sticky;
  top: 0;
  z-index: 10;
  background: #fff;
  border-bottom: 1px solid #eee;
  display: grid;
  grid-template-columns: 140px 1fr 44px;
  gap: 12px;
  padding: 12px 16px;
  align-items: center;
}

.brand { font-weight: 800; font-size: 18px; }

.search {
  width: 100%;
  height: 40px;
  border-radius: 12px;
  border: 1px solid #e5e5e5;
  padding: 0 12px;
  outline: none;
}

.search:focus { border-color: #c9c9c9; }

.profile {
  height: 40px;
  width: 44px;
  border: 1px solid #e5e5e5;
  background: #fff;
  border-radius: 12px;
  cursor: pointer;
}

.container { padding: 18px 16px; }

.grid {
  display: grid;
  gap: 14px;
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.hint {
  padding: 14px;
  background: #fff;
  border: 1px solid #eee;
  border-radius: 12px;
}

.hint.error { border-color: #ffd0d0; background: #fff5f5; }

.details {
  display: grid;
  grid-template-columns: 1.1fr 1fr;
  gap: 16px;
}

.bigImg {
  background: #f3f3f3;
  border-radius: 14px;
  overflow: hidden;
  aspect-ratio: 4 / 3;
}

.bigImg img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.info { display: grid; gap: 10px; }

.h { margin: 0; font-size: 22px; }

.meta { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

.badge {
  font-size: 12px;
  padding: 6px 10px;
  border-radius: 999px;
  background: #f0f0f0;
}

.price { font-weight: 800; font-size: 18px; }

.desc { margin: 0; line-height: 1.5; color: #333; }

.buy {
  height: 44px;
  border: none;
  background: #111;
  color: #fff;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
}

.buyHint { font-size: 12px; color: #666; }

/* планшет */
@media (max-width: 768px) {
  .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .details { grid-template-columns: 1fr; }
}

/* мобила */
@media (max-width: 480px) {
  .top { grid-template-columns: 90px 1fr 44px; }
  .grid { grid-template-columns: 1fr; }
}
</style>
