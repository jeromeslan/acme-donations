<template>
  <div class="page-layout">
    <AppNavbar />
    
    <main class="page-content">
      <div class="container-sm">
        <!-- Page Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Campaign</h1>
          <p class="text-gray-600">Fill in the details for your charitable campaign</p>
        </div>

        <!-- Success Message -->
        <BaseAlert v-if="successMessage" variant="success" dismissible class="mb-6">
          <div>
            <h4 class="font-medium">Campaign Created Successfully!</h4>
            <p class="mt-1">{{ successMessage }}</p>
          </div>
        </BaseAlert>

        <!-- Form Card -->
        <BaseCard>
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Basic Information -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
              
              <div class="space-y-4">
                <BaseInput
                  v-model="form.title"
                  label="Campaign Title"
                  placeholder="e.g., Help Families in Need"
                  required
                  :error="errors.title"
                />

                <div class="form-group">
                  <label class="form-label">Description *</label>
                  <textarea
                    v-model="form.description"
                    placeholder="Describe your campaign and its goals..."
                    required
                    rows="4"
                    :class="['form-textarea', { error: errors.description }]"
                  ></textarea>
                  <span v-if="errors.description" class="form-error">{{ errors.description }}</span>
                </div>
              </div>
            </div>

            <!-- Campaign Details -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaign Details</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <BaseInput
                  v-model="form.goal_amount"
                  label="Goal Amount (€)"
                  type="number"
                  min="1"
                  step="0.01"
                  placeholder="5000"
                  required
                  :error="errors.goal_amount"
                />

                <div class="form-group">
                  <label class="form-label">Category *</label>
                  <select
                    v-model="form.category_id"
                    required
                    :class="['form-select', { error: errors.category_id }]"
                  >
                    <option value="">Select a category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                  <span v-if="errors.category_id" class="form-error">{{ errors.category_id }}</span>
                </div>
              </div>
            </div>

            <!-- Optional Settings -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Optional Settings</h3>
              
              <div class="form-group">
                <label class="flex items-center gap-3">
                  <input
                    v-model="form.featured"
                    type="checkbox"
                    class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                  />
                  <span class="text-sm text-gray-700">Request to be featured</span>
                </label>
                <p class="text-xs text-gray-500 mt-1">Featured campaigns are highlighted on the homepage</p>
              </div>
            </div>

            <!-- Error Display -->
            <BaseAlert v-if="errors.general" variant="error" dismissible>
              {{ errors.general }}
            </BaseAlert>

            <!-- Form Actions -->
            <div class="flex items-center gap-4 pt-6 border-t border-gray-200">
              <BaseButton
                type="button"
                variant="secondary"
                @click="saveAsDraft"
                :loading="loading"
              >
                Save as Draft
              </BaseButton>

              <BaseButton
                type="submit"
                variant="primary"
                :loading="loading"
                class="flex-1"
              >
                Submit for Review
              </BaseButton>
            </div>
          </form>
        </BaseCard>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import { api } from '@/api/client'
import AppNavbar from '@/components/layout/AppNavbar.vue'
import BaseCard from '@/components/ui/BaseCard.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseAlert from '@/components/ui/BaseAlert.vue'

const loading = ref(false)
const categories = ref([])
const successMessage = ref('')

const form = reactive({
  title: '',
  description: '',
  goal_amount: '',
  category_id: '',
  featured: false
})

const errors = reactive({
  title: '',
  description: '',
  goal_amount: '',
  category_id: '',
  general: ''
})

const clearErrors = () => {
  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })
}

const clearSuccessMessage = () => {
  setTimeout(() => {
    successMessage.value = ''
  }, 5000)
}

const saveAsDraft = async () => {
  await submitCampaign('draft')
}

const handleSubmit = async () => {
  await submitCampaign('pending')
}

const submitCampaign = async (status: 'draft' | 'pending') => {
  clearErrors()
  loading.value = true

  try {
    const campaignData = {
      ...form,
      status,
      goal_amount: parseFloat(form.goal_amount.toString())
    }

    console.log('Submitting campaign:', campaignData)

    const response = await api.post('/api/campaigns', campaignData)
    console.log('Campaign created successfully:', response.data)

    // Show success message
    if (status === 'draft') {
      successMessage.value = 'Campaign saved as draft successfully! You can continue editing or submit it for review later.'
    } else {
      successMessage.value = 'Campaign submitted for review! It will be reviewed by administrators before going live.'
    }

    // Reset form
    Object.keys(form).forEach(key => {
      if (key === 'featured') {
        form[key] = false
      } else {
        form[key] = ''
      }
    })

    clearSuccessMessage()

  } catch (error: any) {
    console.error('Error creating campaign:', error)

    if (error.response?.status === 422) {
      const validationErrors = error.response.data.errors || {}
      Object.keys(validationErrors).forEach(key => {
        if (errors.hasOwnProperty(key)) {
          errors[key] = validationErrors[key][0] || ''
        }
      })
    } else if (error.response?.status === 419) {
      errors.general = 'Security error (CSRF). Please refresh the page and try again.'
    } else if (error.response?.status === 401) {
      errors.general = 'You must be logged in to create a campaign.'
    } else {
      errors.general = `Error: ${error.response?.data?.message || error.message || 'An unknown error occurred'}`
    }
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/api/categories')
    categories.value = response.data
  } catch (error) {
    console.error('Error loading categories:', error)
  }
}

onMounted(() => {
  fetchCategories()
})
</script>

<style scoped>
.space-y-6 > * + * { margin-top: 1.5rem; }
.space-y-4 > * + * { margin-top: 1rem; }
.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
.gap-4 { gap: 1rem; }
.border-t { border-top-width: 1px; }
.pt-6 { padding-top: 1.5rem; }
.flex-1 { flex: 1 1 0%; }
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }

@media (min-width: 768px) {
  .md\\:grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>