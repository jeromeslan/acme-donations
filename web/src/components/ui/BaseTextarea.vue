<template>
  <div class="form-group" :class="{ 'has-error': error }">
    <label v-if="label" :for="inputId" class="form-label">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="input-wrapper" ref="wrapperRef">
      <textarea
        :id="inputId"
        :value="modelValue"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :rows="rows"
        :class="['form-textarea', { 'error': error }]"
        ref="textareaRef"
      ></textarea>
      
      <!-- Error Tooltip -->
      <Teleport to="body">
        <div 
          v-if="error && showTooltip && tooltipPosition" 
          class="error-tooltip"
          :style="tooltipPosition"
        >
          <div class="tooltip-content">
            {{ error }}
          </div>
          <div class="tooltip-arrow"></div>
        </div>
      </Teleport>
    </div>
    <span v-if="hint" class="text-xs text-gray-500 mt-1 block">{{ hint }}</span>
  </div>
</template>

<script setup lang="ts">
import { computed, useId, ref, nextTick } from 'vue'

interface Props {
  modelValue: string
  label?: string
  placeholder?: string
  required?: boolean
  disabled?: boolean
  error?: string
  hint?: string
  rows?: number
}

const props = withDefaults(defineProps<Props>(), {
  required: false,
  disabled: false,
  rows: 4
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  blur: [event: FocusEvent]
  focus: [event: FocusEvent]
}>()

const inputId = useId()
const textareaRef = ref<HTMLTextAreaElement>()
const wrapperRef = ref<HTMLElement>()
const showTooltip = ref(false)
const tooltipPosition = ref<Record<string, string> | null>(null)

const handleInput = (event: Event) => {
  const target = event.target as HTMLTextAreaElement
  emit('update:modelValue', target.value)
}

const handleFocus = (event: FocusEvent) => {
  if (props.error) {
    showTooltip.value = true
    updateTooltipPosition()
  }
  emit('focus', event)
}

const handleBlur = (event: FocusEvent) => {
  setTimeout(() => {
    showTooltip.value = false
  }, 150)
  emit('blur', event)
}

const updateTooltipPosition = async () => {
  await nextTick()
  if (!textareaRef.value) return

  const rect = textareaRef.value.getBoundingClientRect()
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop
  const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft

  tooltipPosition.value = {
    position: 'absolute',
    top: `${rect.top + scrollTop}px`,
    left: `${rect.left + scrollLeft + rect.width + 10}px`,
    zIndex: '9999'
  }
}
</script>

<style scoped>
.input-wrapper {
  position: relative;
}

.error-tooltip {
  background: #dc2626;
  color: white;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  max-width: 200px;
  word-wrap: break-word;
  animation: tooltipFadeIn 0.2s ease-out;
}

.tooltip-content {
  position: relative;
}

.tooltip-arrow {
  position: absolute;
  top: 20px;
  left: -4px;
  transform: translateY(-50%);
  width: 0;
  height: 0;
  border-top: 4px solid transparent;
  border-bottom: 4px solid transparent;
  border-right: 4px solid #dc2626;
}

@keyframes tooltipFadeIn {
  from {
    opacity: 0;
    transform: translateX(-4px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.form-textarea.error {
  border-color: #dc2626;
  box-shadow: 0 0 0 1px #dc2626;
}

.form-textarea.error:focus {
  border-color: #dc2626;
  box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.2);
}
</style>
