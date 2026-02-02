<template>
  <teleport to="body">
    <div class="overlay" @mousedown="onOverlayMouseDown" v-show="open">
      <div class="modal" role="dialog" aria-modal="true" @mousedown.stop>
        <button class="close" type="button" aria-label="Close" @click="$emit('close')">
          ×
        </button>
        <div class="content">
          <slot />
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { onBeforeUnmount, onMounted } from "vue";

const props = defineProps({
  open: { type: Boolean, default: false },
});
const emit = defineEmits(["close"]);

function onKeyDown(e) {
  if (!props.open) return;
  if (e.key === "Escape") emit("close");
}

function onOverlayMouseDown(e) {
  // клик “вне окна”
  if (e.target.classList.contains("overlay")) emit("close");
}

onMounted(() => window.addEventListener("keydown", onKeyDown));
onBeforeUnmount(() => window.removeEventListener("keydown", onKeyDown));
</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.55);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  z-index: 9999;
}

.modal {
  width: min(900px, 100%);
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,.25);
  position: relative;
  max-height: 85vh;
  overflow: auto;
}

.close {
  position: absolute;
  top: 10px;
  right: 12px;
  font-size: 28px;
  border: none;
  background: transparent;
  cursor: pointer;
  line-height: 1;
}

.content { padding: 18px; }

@media (max-width: 480px) {
  .content { padding: 14px; }
  .modal { border-radius: 12px; }
}
</style>
