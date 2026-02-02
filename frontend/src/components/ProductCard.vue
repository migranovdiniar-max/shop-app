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
  border: 1px solid #e8e8e8;
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  cursor: pointer;
  padding: 0;
  transition: transform .08s ease, box-shadow .08s ease;
}
.card:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(0,0,0,.08);
}
.imgWrap {
  aspect-ratio: 4 / 3;
  background: #f6f6f6;
}
img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.body { padding: 12px; display: grid; gap: 8px; }
.title { font-size: 14px; font-weight: 600; line-height: 1.2; }
.price { font-size: 14px; font-weight: 700; }
</style>
