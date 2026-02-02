<template>
  <teleport to="body">
    <div class="overlay" v-show="open" @mousedown="onOverlay">
      <aside class="drawer" @mousedown.stop>
        <div class="head">
          <div class="avatar">👤</div>
          <div class="who">
            <div class="t">Гость</div>
            <div class="s">Демо-режим (без auth)</div>
          </div>

          <button class="x" type="button" @click="$emit('close')" aria-label="Close">×</button>
        </div>

        <div class="actions">
          <button class="item fav" type="button" @click="$emit('openFavorites')">
            <span class="ico">❤</span>
            <span class="lbl">Избранное</span>
            <span class="count" v-if="favoritesCount">{{ favoritesCount }}</span>
          </button>

          <button class="item cart" type="button" @click="$emit('openCart')">
            <span class="ico">🛒</span>
            <span class="lbl">Корзина</span>
            <span class="count green" v-if="cartCount">{{ cartCount }}</span>
          </button>
        </div>

        <div class="hint">
            Демо-режим.
        </div>
      </aside>
    </div>
  </teleport>
</template>

<script setup>
import { onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
  open: { type: Boolean, default: false },
  favoritesCount: { type: Number, default: 0 },
  cartCount: { type: Number, default: 0 },
});

const emit = defineEmits(["close", "openFavorites", "openCart"]);

function onOverlay(e) {
  if (e.target.classList.contains("overlay")) emit("close");
}

function onKeyDown(e) {
  if (!props.open) return;
  if (e.key === "Escape") emit("close");
}

onMounted(() => window.addEventListener("keydown", onKeyDown));
onBeforeUnmount(() => window.removeEventListener("keydown", onKeyDown));
</script>

<style scoped>
:root, .drawer {
  --red: #ef4444;
  --green: #16a34a;
}

.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.45);
  z-index: 9998;
}

.drawer {
  position: absolute;
  right: 0;
  top: 0;
  height: 100%;
  width: min(360px, 92vw);
  background: #fff;
  box-shadow: -10px 0 30px rgba(0,0,0,.18);
  display: grid;
  grid-template-rows: auto auto 1fr;
}

.head {
  padding: 14px 14px 10px;
  border-bottom: 1px solid var(--line);
  display: grid;
  grid-template-columns: 44px 1fr 32px;
  gap: 10px;
  align-items: center;
}

.avatar {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: var(--ozon-soft);
  display: grid;
  place-items: center;
  font-size: 20px;
}

.who .t { font-weight: 900; }
.who .s { font-size: 12px; color: var(--muted); }

.x {
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  font-size: 26px;
  line-height: 1;
  cursor: pointer;
}

.actions {
  padding: 12px;
  display: grid;
  gap: 10px;
}

.item {
  width: 100%;
  height: 46px;
  display: grid;
  grid-template-columns: 28px 1fr auto;
  align-items: center;
  gap: 10px;

  border: 1px solid var(--line);
  background: #fff;
  border-radius: 16px;
  cursor: pointer;
  text-align: left;
  padding: 0 12px;

  transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
}
.item:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(0,0,0,.10);
}
.item:active {
  transform: translateY(0px) scale(.98);
}

.ico { font-size: 18px; }
.lbl { font-weight: 800; }

.item.fav .ico { color: var(--red); }
.item.cart .ico { color: var(--green); }

.count {
  min-width: 24px;
  height: 24px;
  padding: 0 8px;
  border-radius: 999px;
  background: var(--ozon);
  color: #fff;
  font-weight: 900;
  display: grid;
  place-items: center;
  font-size: 12px;
}
.count.green { background: var(--green); }

.hint {
  padding: 12px;
  color: var(--muted);
  font-size: 12px;
}
</style>
