<template>
  <div class="page">
    <header class="top">
      <button class="brand" type="button" @click="goHome">Shop</button>

      <input
        class="search"
        v-model="q"
        placeholder="Поиск по названию…"
        @input="onSearch"
      />

      <div class="actionsRight">
        <div class="badgesRow">
          <span v-if="favCount > 0" class="pill red">♥ {{ favCount }}</span>
          <span v-if="cartCount > 0" class="pill green">🛒 {{ cartCount }}</span>
        </div>

        <button class="menuBtn" type="button" @click="toggleSidebar" title="Меню">
          ☰
        </button>
      </div>
    </header>

    <Sidebar
      :open="sidebarOpen"
      :cart-count="cartCount"
      :fav-count="favCount"
      @close="sidebarOpen = false"
      @open-cart="openCart"
      @open-favs="openFavs"
    />

    <main class="container">
      <div v-if="loading" class="grid">
        <div v-for="n in 8" :key="n" class="skeletonCard">
          <div class="skImg"></div>
          <div class="skLine"></div>
          <div class="skLine short"></div>
        </div>
      </div>

      <div v-else-if="error" class="hint error">{{ error }}</div>

      <div v-else-if="products.length === 0" class="empty">
        <div class="emptyTitle">Ничего не найдено</div>
        <div class="emptySub">Попробуй изменить запрос</div>
        <button class="btnBlue" type="button" @click="resetSearch">Сбросить</button>
      </div>

      <section v-else class="grid">
        <ProductCard
          v-for="p in products"
          :key="p.id"
          :product="p"
          @open="openProduct"
        />
      </section>
    </main>

    <BaseModal :open="cartOpen" :zIndex="10000" @close="cartOpen = false">
      <CartModalContent
        :items="cartItems"
        :count="cartCount"
        :total="cartTotal"
        @open-product="openProductFromList"
        @set-qty="setQty"
        @remove="removeFromCart"
        @remove-many="removeManyFromCart"
        @checkout="checkout"
      />
    </BaseModal>

    <BaseModal :open="favsOpen" :zIndex="10000" @close="favsOpen = false">
      <FavsModalContent
        :items="favItems"
        :count="favCount"
        @open-product="openProductFromList"
        @remove="toggleFav"
        @remove-many="removeManyFavs"
      />
    </BaseModal>

    <BaseModal :open="productModalOpen" :zIndex="10020" @close="closeProductModal">
      <div v-if="productLoading" class="hint">Загрузка…</div>
      <div v-else-if="productError" class="hint error">{{ productError }}</div>

      <div v-else-if="selected" class="details">
        <div class="bigImg">
          <img :src="selected.image" :alt="selected.title" />
        </div>

        <div class="info">
          <h2 class="h">{{ selected.title }}</h2>

          <div class="meta">
            <span class="badgePill">{{ selected.category }}</span>
            <span class="price">{{ formatPrice(selected.price) }}</span>
          </div>

          <p class="desc">{{ selected.description }}</p>

          <div class="actions">
            <button
              class="btnCart"
              :class="{ on: isInCart(selected.id) }"
              type="button"
              @click="toggleCartFromProduct(selected.id)"
            >
              🛒
              <span v-if="isInCart(selected.id)">В корзине ✓</span>
              <span v-else>В корзину</span>
            </button>

            <button
              class="btnFav"
              :class="{ on: isInFav(selected.id) }"
              type="button"
              @click="toggleFav(selected.id)"
            >
              <span class="heart" :class="{ on: isInFav(selected.id) }">♥</span>
              <span v-if="isInFav(selected.id)">В избранном</span>
              <span v-else>В избранное</span>
            </button>
          </div>

          <div v-if="isInCart(selected.id)" class="qtyRow">
            <div class="qtyLabel">Количество</div>
            <div class="qty">
              <button class="qtyBtn" type="button" @click="decQty(selected.id)">−</button>
              <div class="qtyVal">{{ getQty(selected.id) }}</div>
              <button class="qtyBtn" type="button" @click="incQty(selected.id)">+</button>
            </div>

            <button class="btnGhostRed" type="button" @click="removeFromCart(selected.id)">
              Убрать
            </button>
          </div>

          <div class="note">Избранное/корзина сейчас в localStorage (демо).</div>
        </div>
      </div>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { fetchProducts, fetchProduct } from "./api/products";
import BaseModal from "./components/BaseModal.vue";
import ProductCard from "./components/ProductCard.vue";
import Sidebar from "./components/Sidebar.vue";

import CartModalContent from "./components/CartModalContent.vue";
import FavsModalContent from "./components/FavsModalContent.vue";

const q = ref("");
const products = ref([]);
const loading = ref(false);
const error = ref("");

const sidebarOpen = ref(false);
const cartOpen = ref(false);
const favsOpen = ref(false);

const productModalOpen = ref(false);
const selected = ref(null);
const productLoading = ref(false);
const productError = ref("");

let searchTimer = null;

const LS_CART = "shop_cart_v1";
const LS_FAVS = "shop_favs_v1";

const cart = ref({});
const favs = ref([]);

function loadPersisted() {
  try {
    const c = JSON.parse(localStorage.getItem(LS_CART) || "{}");
    cart.value = c && typeof c === "object" ? c : {};
  } catch {
    cart.value = {};
  }

  try {
    const f = JSON.parse(localStorage.getItem(LS_FAVS) || "[]");
    favs.value = Array.isArray(f) ? f : [];
  } catch {
    favs.value = [];
  }
}

function persistCart() {
  localStorage.setItem(LS_CART, JSON.stringify(cart.value));
}

function persistFavs() {
  localStorage.setItem(LS_FAVS, JSON.stringify(favs.value));
}

function isInCart(id) {
  return Number(cart.value[id] || 0) > 0;
}

function getQty(id) {
  return Number(cart.value[id] || 0);
}

function setQty(id, qty) {
  const n = Math.max(0, Number(qty || 0));
  if (n === 0) {
    const next = { ...cart.value };
    delete next[id];
    cart.value = next;
  } else {
    cart.value = { ...cart.value, [id]: n };
  }
  persistCart();
}

function incQty(id) {
  setQty(id, getQty(id) + 1);
}

function decQty(id) {
  setQty(id, getQty(id) - 1);
}

function addToCart(id) {
  if (!isInCart(id)) setQty(id, 1);
}

function removeFromCart(id) {
  setQty(id, 0);
}

function removeManyFromCart(ids) {
  const next = { ...cart.value };
  for (const id of ids) delete next[id];
  cart.value = next;
  persistCart();
}

function isInFav(id) {
  return favs.value.includes(id);
}

function toggleFav(id) {
  if (isInFav(id)) {
    favs.value = favs.value.filter((x) => x !== id);
  } else {
    favs.value = [...favs.value, id];
  }
  persistFavs();
}

function removeManyFavs(ids) {
  const set = new Set(ids);
  favs.value = favs.value.filter((id) => !set.has(id));
  persistFavs();
}

function toggleCartFromProduct(id) {
  if (isInCart(id)) removeFromCart(id);
  else addToCart(id);
}

const cartCount = computed(() => {
  return Object.values(cart.value).reduce((s, v) => s + Number(v || 0), 0);
});

const favCount = computed(() => favs.value.length);

const cartItems = computed(() => {
  const map = new Map(products.value.map((p) => [p.id, p]));
  const ids = Object.keys(cart.value).map((k) => Number(k));
  return ids
    .map((id) => {
      const p = map.get(id);
      if (!p) return null;
      return { ...p, qty: getQty(id) };
    })
    .filter(Boolean);
});

const favItems = computed(() => {
  const map = new Map(products.value.map((p) => [p.id, p]));
  return favs.value.map((id) => map.get(id)).filter(Boolean);
});

const cartTotal = computed(() => {
  return cartItems.value.reduce((s, it) => s + Number(it.price || 0) * Number(it.qty || 0), 0);
});

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

function resetSearch() {
  q.value = "";
  loadList();
}

async function openProduct(id) {
  productModalOpen.value = true;
  selected.value = null;
  productError.value = "";
  productLoading.value = true;

  try {
    selected.value = await fetchProduct(id);
  } catch (e) {
    productError.value = e?.response?.data?.message || e.message || "Ошибка загрузки";
  } finally {
    productLoading.value = false;
  }
}

function openProductFromList(id) {
  openProduct(id);
}

function closeProductModal() {
  productModalOpen.value = false;
}

function formatPrice(v) {
  return new Intl.NumberFormat("ru-RU").format(v) + " ₽";
}

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

function openCart() {
  cartOpen.value = true;
  sidebarOpen.value = false;
}

function openFavs() {
  favsOpen.value = true;
  sidebarOpen.value = false;
}

function goHome() {
  q.value = "";
  loadList();
}

function checkout() {
  alert("Оформление (демо). Позже реализуется бэк.");
}

onMounted(() => {
  loadPersisted();
  loadList();
});
</script>

<style>
:root {
  color-scheme: light;
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  --ozon: #005bff;
  --bg: #f6f8ff;
  --text: #0b1220;
  --muted: #667085;
  --border: #e6eaf2;
  --green: #16a34a;
  --red: #ef4444;
}

* { box-sizing: border-box; }

body {
  margin: 0;
  background: var(--bg);
  color: var(--text);
}

.page { min-height: 100vh; }

.top {
  position: sticky;
  top: 0;
  z-index: 10;
  background: #fff;
  border-bottom: 1px solid var(--border);
  display: grid;
  grid-template-columns: 140px 1fr auto;
  gap: 12px;
  padding: 12px 16px;
  align-items: center;
}

.brand {
  font-weight: 900;
  font-size: 18px;
  color: var(--ozon);
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
  text-align: left;
}

.search {
  width: 100%;
  height: 40px;
  border-radius: 14px;
  border: 1px solid var(--border);
  padding: 0 12px;
  outline: none;
  background: #fff;
}

.search:focus {
  border-color: rgba(0, 91, 255, .35);
  box-shadow: 0 0 0 4px rgba(0, 91, 255, .12);
}

.actionsRight {
  display: inline-flex;
  align-items: center;
  gap: 10px;
}

.badgesRow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.pill {
  height: 26px;
  padding: 0 10px;
  border-radius: 999px;
  font-weight: 900;
  font-size: 12px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  border: 1px solid var(--border);
  background: #fff;
}

.pill.red { border-color: rgba(239,68,68,.35); color: var(--red); }
.pill.green { border-color: rgba(22,163,74,.25); color: var(--green); }

.menuBtn {
  height: 40px;
  width: 52px;
  border: 1px solid var(--border);
  background: #fff;
  border-radius: 14px;
  cursor: pointer;
  font-size: 18px;
  transition: transform .10s ease, box-shadow .10s ease;
}
.menuBtn:hover { transform: translateY(-1px); box-shadow: 0 8px 22px rgba(0,0,0,.08); }
.menuBtn:active { transform: translateY(0); }

.container { padding: 18px 16px; }

.grid {
  display: grid;
  gap: 14px;
  grid-template-columns: repeat(6, minmax(0, 1fr));
}

.hint {
  padding: 14px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 14px;
}

.hint.error { border-color: #ffd0d0; background: #fff5f5; }

.empty {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 18px;
  display: grid;
  gap: 10px;
  justify-items: start;
}

.emptyTitle { font-weight: 900; font-size: 18px; }
.emptySub { color: var(--muted); }

.btnBlue {
  height: 42px;
  padding: 0 14px;
  border: none;
  background: var(--ozon);
  color: #fff;
  border-radius: 12px;
  font-weight: 900;
  cursor: pointer;
  transition: transform .08s ease;
}
.btnBlue:hover { transform: translateY(-1px); }
.btnBlue:active { transform: translateY(0); }

.skeletonCard {
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  background: #fff;
}
.skImg {
  aspect-ratio: 4 / 3;
  background: linear-gradient(90deg, #eef2ff, #f7f9ff, #eef2ff);
  background-size: 200% 100%;
  animation: sh 1.1s infinite linear;
}
.skLine {
  height: 12px;
  margin: 12px;
  border-radius: 8px;
  background: linear-gradient(90deg, #eef2ff, #f7f9ff, #eef2ff);
  background-size: 200% 100%;
  animation: sh 1.1s infinite linear;
}
.skLine.short { width: 55%; }

@keyframes sh {
  0% { background-position: 0% 0; }
  100% { background-position: 200% 0; }
}

.details {
  display: grid;
  grid-template-columns: 1.1fr 1fr;
  gap: 16px;
}

.bigImg {
  background: #eef2ff;
  border-radius: 14px;
  overflow: hidden;
  aspect-ratio: 4 / 3;
}

.bigImg img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
  background: #fff;
}

.info { display: grid; gap: 10px; }

.h { margin: 0; font-size: 22px; }

.meta { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

.badgePill {
  font-size: 12px;
  padding: 6px 10px;
  border-radius: 999px;
  background: rgba(0, 91, 255, .08);
  color: var(--ozon);
  font-weight: 900;
}

.price { font-weight: 900; font-size: 18px; }

.desc { margin: 0; line-height: 1.5; color: #263145; }

.actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.btnCart {
  height: 44px;
  border: 2px solid rgba(0, 91, 255, .18);
  background: var(--ozon);
  color: #fff;
  border-radius: 12px;
  font-weight: 900;
  cursor: pointer;
  transition: transform .10s ease, filter .10s ease, background .12s ease;
}
.btnCart:hover { transform: translateY(-1px); filter: brightness(1.03); }
.btnCart:active { transform: translateY(0); }
.btnCart.on { background: var(--green); }

.btnFav {
  height: 44px;
  border: 2px solid rgba(239, 68, 68, .35);
  background: #fff;
  color: #111;
  border-radius: 12px;
  font-weight: 900;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: transform .10s ease, box-shadow .10s ease;
}
.btnFav:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,0,0,.08); }
.btnFav:active { transform: translateY(0); }

.heart {
  font-size: 18px;
  color: transparent;
  -webkit-text-stroke: 2px var(--red);
  line-height: 1;
}
.heart.on { color: var(--red); -webkit-text-stroke: 0; }

.qtyRow {
  display: grid;
  grid-template-columns: 1fr auto auto;
  gap: 12px;
  align-items: center;
}

.qtyLabel { color: var(--muted); font-weight: 800; }

.qty {
  display: grid;
  grid-template-columns: 38px 44px 38px;
  border: 1px solid var(--border);
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
}

.qtyBtn {
  border: none;
  background: #fff;
  cursor: pointer;
  font-size: 18px;
  font-weight: 900;
  transition: background .12s ease;
}
.qtyBtn:hover { background: #f3f6ff; }
.qtyVal { display: grid; place-items: center; font-weight: 900; }

.btnGhostRed {
  height: 40px;
  padding: 0 12px;
  border-radius: 12px;
  border: 1px solid rgba(239, 68, 68, .4);
  background: #fff;
  color: var(--red);
  font-weight: 900;
  cursor: pointer;
  transition: transform .08s ease;
}
.btnGhostRed:hover { transform: translateY(-1px); }
.btnGhostRed:active { transform: translateY(0); }

.note { font-size: 12px; color: var(--muted); }

/* ≤768px */
@media (max-width: 768px) {
  .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .details { grid-template-columns: 1fr; }
}

/* ≤480px */
@media (max-width: 480px) {
  .top { grid-template-columns: 90px 1fr auto; }
  .actions { grid-template-columns: 1fr; }
  .badgesRow { display: none; }
  .grid { grid-template-columns: 1fr; }
}

/* обычный десктоп */
@media (max-width: 1200px) and (min-width: 769px) {
  .grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
}
</style>
