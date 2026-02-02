<template>
  <div class="wrap">
    <div class="head">
      <div class="title">
        <span class="ico">♥</span>
        <span>Избранное</span>
      </div>

      <div class="right">
        <div class="count">Товаров: <b>{{ items.length }}</b></div>
        <button class="x" type="button" @click="$emit('close')">×</button>
      </div>
    </div>

    <div class="panel">
      <label class="selectAll">
        <input type="checkbox" v-model="allChecked" @change="toggleAll" />
        <span>Выбрать всё</span>
      </label>

      <button class="ghostDanger" type="button" :disabled="selectedIds.length === 0" @click="removeSelected">
        Удалить выбранное
      </button>
    </div>

    <div v-if="items.length === 0" class="empty">
      <div class="emptyTitle">Пока пусто</div>
      <div class="emptyText">Добавь товары в избранное из карточки товара.</div>
    </div>

    <div v-else class="list">
      <div v-for="it in items" :key="it.id" class="row">
        <div class="chk">
          <input type="checkbox" :checked="selectedMap[it.id] === true" @change="toggleOne(it.id)" />
        </div>

        <button class="thumb" type="button" @click="$emit('open-product', it.id)">
          <img :src="it.image" :alt="it.title" />
        </button>

        <div class="main">
          <div class="name">{{ it.title }}</div>
          <div class="price">{{ formatPrice(it.price) }}</div>
        </div>

        <div class="actions">
          <button class="btn" type="button" @click="$emit('open-product', it.id)">Открыть</button>
          <button class="btnDanger" type="button" @click="removeOne(it.id)">Удалить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from "vue";

const props = defineProps({
  items: { type: Array, default: () => [] },
});

const emit = defineEmits(["close", "open-product", "update-items"]);

const selectedMap = reactive({});
const allChecked = ref(false);

watch(
  () => props.items,
  (arr) => {
    const ids = new Set(arr.map((x) => x.id));
    Object.keys(selectedMap).forEach((k) => {
      if (!ids.has(Number(k))) delete selectedMap[k];
    });
    allChecked.value = arr.length > 0 && arr.every((x) => selectedMap[x.id] === true);
  },
  { immediate: true }
);

const selectedIds = computed(() =>
  Object.keys(selectedMap).filter((k) => selectedMap[k] === true).map((k) => Number(k))
);

function toggleAll() {
  const v = allChecked.value;
  props.items.forEach((x) => (selectedMap[x.id] = v));
}

function toggleOne(id) {
  selectedMap[id] = !(selectedMap[id] === true);
  allChecked.value = props.items.length > 0 && props.items.every((x) => selectedMap[x.id] === true);
}

function update(next) {
  emit("update-items", next);
}

function removeOne(id) {
  const next = props.items.filter((x) => x.id !== id);
  delete selectedMap[id];
  update(next);
}

function removeSelected() {
  const ids = new Set(selectedIds.value);
  const next = props.items.filter((x) => !ids.has(x.id));
  selectedIds.value.forEach((id) => delete selectedMap[id]);
  allChecked.value = false;
  update(next);
}

function formatPrice(v) {
  return new Intl.NumberFormat("ru-RU").format(v) + " ₽";
}
</script>

<style scoped>
.wrap { display: grid; gap: 14px; }

.head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e9eef7;
}

.title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 22px;
  font-weight: 800;
}

.ico { color: #ff2f6d; font-size: 20px; }

.right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.count {
  color: #5d6b86;
  white-space: nowrap;
}

.x {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  border: 1px solid #e2e8f5;
  background: #fff;
  cursor: pointer;
  font-size: 22px;
  line-height: 1;
}

.panel {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 12px;
  border: 1px solid #e9eef7;
  border-radius: 14px;
  background: #fff;
}

.selectAll {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 700;
}

.ghostDanger {
  height: 40px;
  padding: 0 14px;
  border-radius: 12px;
  border: 1px solid #ffd2d2;
  background: #fff;
  color: #ff3b3b;
  font-weight: 800;
  cursor: pointer;
}

.ghostDanger:disabled {
  opacity: .45;
  cursor: not-allowed;
}

.empty {
  border: 1px solid #e9eef7;
  border-radius: 14px;
  background: #fff;
  padding: 18px;
}

.emptyTitle { font-weight: 900; font-size: 16px; }
.emptyText { color: #5d6b86; margin-top: 6px; }

.list { display: grid; gap: 12px; }

.itemRow {
  display: grid;
  grid-template-columns: 26px 64px 1fr auto;
  gap: 12px;
  align-items: center;
  padding: 14px 18px;
  border-radius: 16px;
  border: 1px solid #e6ecff;
  background: #fff;
  overflow: hidden;
}

.itemRow .actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  align-items: center;
  padding-right: 6px;
}

.itemRow .actions button {
  max-width: 140px;
}

@media (max-width: 480px) {
  .itemRow {
    grid-template-columns: 22px 56px 1fr;
    grid-auto-rows: auto;
  }

  .itemRow .actions {
    grid-column: 1 / -1;
    justify-content: flex-end;
    padding-right: 0;
  }

  .itemRow .actions button {
    max-width: none;
    width: auto;
  }
}
.chk { display: flex; justify-content: center; }

.thumb {
  width: 78px;
  height: 58px;
  border-radius: 14px;
  border: 1px solid #e9eef7;
  background: #fff;
  overflow: hidden;
  cursor: pointer;
  padding: 0;
}

.thumb img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
  background: #fff;
}

.main { display: grid; gap: 6px; min-width: 0; }
.name { font-weight: 800; font-size: 16px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.price { font-weight: 900; color: #0a66ff; }

.actions {
  display: grid;
  grid-auto-flow: row;
  gap: 10px;
  justify-items: end;
}

.btn, .btnDanger {
  height: 40px;
  padding: 0 16px;
  border-radius: 14px;
  font-weight: 900;
  cursor: pointer;
  white-space: nowrap;
}

.btn {
  border: 1px solid #e2e8f5;
  background: #fff;
}

.btnDanger {
  border: 1px solid #ffd2d2;
  background: #fff;
  color: #ff3b3b;
}

@media (max-width: 768px) {
  .row { grid-template-columns: 26px 72px 1fr; }
  .actions {
    grid-column: 1 / -1;
    grid-auto-flow: column;
    justify-items: stretch;
    justify-content: end;
  }
}

@media (max-width: 480px) {
  .panel { flex-direction: column; align-items: stretch; }
  .ghostDanger { width: 100%; }
  .actions { grid-auto-flow: row; justify-items: stretch; }
  .btn, .btnDanger { width: 100%; }
}
</style>
