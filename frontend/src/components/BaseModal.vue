<template>
  <teleport to="body">
    <div
      v-show="open"
      class="overlay"
      :style="{ zIndex: z }"
      @mousedown="onOverlayMouseDown"
    >
      <div class="modal" role="dialog" aria-modal="true" @mousedown.stop>
        <button class="close" type="button" aria-label="Close" @click="$emit('close')">×</button>
        <div class="content">
          <slot />
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted } from "vue";

const props = defineProps({
  open: { type: Boolean, default: false },
  level: { type: Number, default: 1 },
});

const emit = defineEmits(["close"]);

const z = computed(() => 9000 + props.level * 10);

function onKeyDown(e) {
  if (!props.open) return;
  if (e.key === "Escape") emit("close");
}

function onOverlayMouseDown(e) {
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
