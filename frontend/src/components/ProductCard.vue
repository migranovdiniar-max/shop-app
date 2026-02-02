<template>
  <button class="card" type="button" @click="$emit('open', product.id)">
    <div class="imgWrap">
      <img :src="product.image" :alt="product.title" loading="lazy" />
    </div>

    <div class="body">
      <div class="title" :title="product.title">{{ product.title }}</div>
      <div class="price">{{ formatPrice(product.price) }}</div>
    </div>
  </button>
</template>

<script setup>
const props = defineProps({
  product: { type: Object, required: true },
});
defineEmits(["open"]);

function formatPrice(v) {
  return new Intl.NumberFormat("ru-RU").format(v) + " ₽";
}
</script>

<style scoped>
.card {
  text-align: left;
  border: 1px solid var(--line);
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  padding: 0;
  transition: transform .08s ease, box-shadow .08s ease, border-color .08s ease;
}
.card:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 22px rgba(0,0,0,.08);
  border-color: rgba(0,91,255,.25);
}

.imgWrap {
  aspect-ratio: 4 / 3;
  background: #fff;
  border-bottom: 1px solid var(--line);
  display: grid;
  place-items: center;
}
img {
  width: 100%;
  height: 100%;
  object-fit: contain; /* важно: часы/рюкзак теперь не “режутся” */
  display: block;
  background: #fff;
}

.body { padding: 12px; display: grid; gap: 8px; }
.title { font-size: 14px; font-weight: 700; line-height: 1.25; }
.price { font-size: 14px; font-weight: 800; color: var(--ozon); }
</style>
