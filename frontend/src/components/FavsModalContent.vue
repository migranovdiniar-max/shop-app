<template>
  <div class="head">
    <div class="ttl">♥ Избранное</div>
    <div class="count">Товаров: <b>{{ count }}</b></div>
  </div>

  <div v-if="items.length === 0" class="empty">
    <div class="emptyTitle">Избранное пустое</div>
    <div class="emptySub">Добавь товары сердечком</div>
  </div>

  <div v-else class="body">
    <div class="bulk">
      <label class="chk">
        <input type="checkbox" :checked="allChecked" @change="toggleAll" />
        <span>Выбрать всё</span>
      </label>

      <button class="btnRedGhost" type="button" :disabled="selectedIds.length === 0" @click="removeSelected">
        Удалить выбранное
      </button>
    </div>

    <div class="list">
      <div v-for="p in items" :key="p.id" class="row">
        <label class="sel">
          <input type="checkbox" :value="p.id" v-model="selectedIds" />
        </label>

        <button class="imgBtn" type="button" @click="$emit('open-product', p.id)">
          <img class="img" :src="p.image" :alt="p.title" />
        </button>

        <div class="mid">
          <button class="title" type="button" @click="$emit('open-product', p.id)">{{ p.title }}</button>
          <div class="price">{{ formatPrice(p.price) }}</div>
        </div>

        <div class="actions">
          <button class="btnOpen" type="button" @click="$emit('open-product', p.id)">Открыть</button>
          <button class="btnRed" type="button" @click="$emit('remove', p.id)">Удалить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
  items: { type: Array, default: () => [] },
  count: { type: Number, default: 0 },
});

const emit = defineEmits(["open-product", "remove", "remove-many"]);

const selectedIds = ref([]);

watch(
  () => props.items,
  () => {
    const set = new Set(props.items.map((x) => x.id));
    selectedIds.value = selectedIds.value.filter((id) => set.has(id));
  },
  { deep: true }
);

const allChecked = computed(() => {
  if (props.items.length === 0) return false;
  return selectedIds.value.length === props.items.length;
});

function toggleAll(e) {
  if (e.target.checked) selectedIds.value = props.items.map((x) => x.id);
  else selectedIds.value = [];
}

function removeSelected() {
  emit("remove-many", [...selectedIds.value]);
  selectedIds.value = [];
}

function formatPrice(v) {
  return new Intl.NumberFormat("ru-RU").format(v) + " ₽";
}
</script>

<style scoped>
.head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding-right: 44px;
}

.ttl { font-weight: 900; font-size: 18px; color: #0b1220; }
.count { color: #667085; }

.empty { padding-top: 8px; display: grid; gap: 6px; }
.emptyTitle { font-weight: 900; font-size: 18px; }
.emptySub { color: #667085; }

.body { display: grid; gap: 12px; margin-top: 10px; }

.bulk {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 10px 12px;
  border: 1px solid #e6eaf2;
  border-radius: 14px;
  background: #fff;
}

.chk { display: inline-flex; align-items: center; gap: 10px; font-weight: 800; }
.chk input { width: 16px; height: 16px; }

.btnRedGhost {
  height: 38px;
  padding: 0 12px;
  border-radius: 12px;
  border: 1px solid rgba(239, 68, 68, .35);
  background: #fff;
  color: #ef4444;
  font-weight: 900;
  cursor: pointer;
  transition: transform .10s ease;
}
.btnRedGhost:disabled { opacity: .45; cursor: not-allowed; }
.btnRedGhost:not(:disabled):hover { transform: translateY(-1px); }

.list { display: grid; gap: 10px; }

.row {
  display: grid;
  grid-template-columns: 30px 78px 1fr 170px;
  gap: 12px;
  align-items: center;
  border: 1px solid #e6eaf2;
  border-radius: 16px;
  padding: 10px;
  background: #fff;
}

.sel input { width: 16px; height: 16px; }

.imgBtn {
  border: 1px solid #eef1f7;
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  padding: 0;
  cursor: pointer;
  width: 78px;
  height: 60px;
  display: grid;
  place-items: center;
  transition: transform .10s ease, box-shadow .10s ease;
}
.imgBtn:hover { transform: translateY(-1px); box-shadow: 0 8px 22px rgba(0,0,0,.08); }

.img { width: 100%; height: 100%; object-fit: contain; background: #fff; display: block; }

.mid { display: grid; gap: 6px; }

.title {
  border: none;
  background: transparent;
  padding: 0;
  text-align: left;
  cursor: pointer;
  font-weight: 900;
  color: #0b1220;
}
.title:hover { color: #005bff; }

.price { color: #005bff; font-weight: 900; }

.actions {
  display: flex;
  gap: 10px;
  align-items: center;
  justify-content: flex-end;
  padding-right: 8px;
}

.btnOpen, .btnRed {
  height: 38px;
  padding: 0 14px;
  border-radius: 12px;
  font-weight: 900;
  cursor: pointer;
  transition: transform .10s ease, box-shadow .10s ease;
  background: #fff;
}

.btnOpen {
  border: 1px solid #e6eaf2;
}
.btnOpen:hover { transform: translateY(-1px); box-shadow: 0 8px 22px rgba(0,0,0,.08); }

.btnRed {
  border: 1px solid rgba(239, 68, 68, .35);
  color: #ef4444;
}
.btnRed:hover { transform: translateY(-1px); }

@media (max-width: 768px) {
  .row { grid-template-columns: 30px 78px 1fr; }

  .actions {
    grid-column: 1 / -1;
    justify-content: flex-end;
    padding-right: 0;
  }
}

</style>
