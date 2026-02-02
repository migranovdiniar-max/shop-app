<template>
  <teleport to="body">
    <div class="wrap" v-show="open" @mousedown="onOverlay">
      <aside class="panel" @mousedown.stop>
        <div class="head">
          <div class="t">Меню</div>
          <button class="x" type="button" @click="$emit('close')" aria-label="Close">×</button>
        </div>

        <button class="item fav" type="button" @click="$emit('open-favs')">
          <div class="left">
            <span class="ic heart">♥</span>
            <span class="txt">Избранное</span>
          </div>
          <span v-if="favCount > 0" class="cnt red">{{ favCount }}</span>
        </button>

        <button class="item cart" type="button" @click="$emit('open-cart')">
          <div class="left">
            <span class="ic">🛒</span>
            <span class="txt">Корзина</span>
          </div>
          <span v-if="cartCount > 0" class="cnt green">{{ cartCount }}</span>
        </button>
      </aside>
    </div>
  </teleport>
</template>

<script setup>
import { onBeforeUnmount, onMounted } from "vue";

const props = defineProps({
  open: { type: Boolean, default: false },
  cartCount: { type: Number, default: 0 },
  favCount: { type: Number, default: 0 },
});

const emit = defineEmits(["close", "open-cart", "open-favs"]);

function onOverlay(e) {
  if (e.target.classList.contains("wrap")) emit("close");
}

function onKeyDown(e) {
  if (!props.open) return;
  if (e.key === "Escape") emit("close");
}

onMounted(() => window.addEventListener("keydown", onKeyDown));
onBeforeUnmount(() => window.removeEventListener("keydown", onKeyDown));
</script>

<style scoped>
.wrap {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.45);
  z-index: 9990;
  display: flex;
  justify-content: flex-end;
}

.panel {
  width: min(320px, 92vw);
  height: 100%;
  background: #fff;
  border-left: 1px solid #e6eaf2;
  padding: 14px;
  display: grid;
  gap: 10px;
  box-shadow: -10px 0 30px rgba(0,0,0,.18);
}

.head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 10px;
  border-bottom: 1px solid #eef1f7;
}

.t { font-weight: 900; color: #0b1220; }

.x {
  border: none;
  background: transparent;
  font-size: 28px;
  cursor: pointer;
  line-height: 1;
  width: 40px;
  height: 40px;
  border-radius: 12px;
}
.x:hover { background: rgba(0,0,0,.06); }

.item {
  border: 1px solid #e6eaf2;
  background: #fff;
  border-radius: 14px;
  padding: 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  transition: transform .10s ease, box-shadow .10s ease;
}
.item:hover { transform: translateY(-1px); box-shadow: 0 8px 22px rgba(0,0,0,.08); }
.item:active { transform: translateY(0); }

.left { display: inline-flex; align-items: center; gap: 10px; }
.ic { font-size: 18px; }
.heart { color: transparent; -webkit-text-stroke: 2px #ef4444; }

.txt { font-weight: 900; color: #0b1220; }

.cnt {
  min-width: 22px;
  height: 22px;
  padding: 0 8px;
  border-radius: 999px;
  color: #fff;
  display: grid;
  place-items: center;
  font-weight: 900;
  font-size: 12px;
}
.cnt.red { background: #ef4444; }
.cnt.green { background: #16a34a; }
</style>
