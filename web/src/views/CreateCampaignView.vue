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

        <!-- Success messages are now handled by toast notifications -->

        <!-- Form Card -->
        <BaseCard>
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Basic Information -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
              
              <div class="space-y-4">
                <BaseInputWithTooltip
                  v-model="form.title"
                  label="Campaign Title"
                  placeholder="e.g., Help Families in Need"
                  required
                  :error="validationErrors.title"
                  @blur="validateSingle('title', form.title)"
                />

                <BaseTextarea
                  v-model="form.description"
                  label="Description"
                  placeholder="Describe your campaign and its goals..."
                  required
                  :rows="4"
                  :error="validationErrors.description"
                  @blur="validateSingle('description', form.description)"
                />
              </div>
            </div>

            <!-- Campaign Details -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaign Details</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <BaseInputWithTooltip
                  v-model="form.goal_amount"
                  label="Goal Amount (€)"
                  type="number"
                  placeholder="5000"
                  required
                  :error="validationErrors.goal_amount"
                  @blur="validateSingle('goal_amount', form.goal_amount)"
                />

                <BaseSelect
                  v-model="form.category_id"
                  label="Category"
                  placeholder="Select a category..."
                  required
                  :options="categoryOptions"
                  :error="validationErrors.category_id"
                  @blur="validateSingle('category_id', form.category_id)"
                />
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
import { reactive, ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { api } from '@/api/client'
import AppNavbar from '@/components/layout/AppNavbar.vue'
import BaseCard from '@/components/ui/BaseCard.vue'
import BaseInputWithTooltip from '@/components/ui/BaseInputWithTooltip.vue'
import BaseTextarea from '@/components/ui/BaseTextarea.vue'
import BaseSelect from '@/components/ui/BaseSelect.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseAlert from '@/components/ui/BaseAlert.vue'
import { useFormValidation } from '@/composables/useFormValidation'

const router = useRouter()
const toast = useToast()
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

// Form validation setup
const { errors: validationErrors, validate, validateSingle, clearErrors: clearValidationErrors } = useFormValidation({
  title: { required: true, minLength: 3, maxLength: 255 },
  description: { required: true, minLength: 10, maxLength: 2000 },
  goal_amount: { required: true, min: 1, max: 1000000 },
  category_id: { required: true }
})

// Convert categories for BaseSelect
const categoryOptions = computed(() => 
  categories.value.map((cat: any) => ({
    value: cat.id,
    label: cat.name
  }))
)

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

const resetForm = () => {
  Object.keys(form).forEach(key => {
    if (key === 'featured') {
      form[key] = false
    } else {
      form[key] = ''
    }
  })
  clearValidationErrors()
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
  // Validate form first
  if (!validate(form)) {
    toast.error('Please fix the form errors before submitting.')
    return
  }

  clearValidationErrors()
  loading.value = true

  try {
    const campaignData = {
      ...form,
      status,
      goal_amount: parseFloat(form.goal_amount.toString())
    }

    const response = await api.post('/api/campaigns', campaignData)

    // Show success notifications
    if (status === 'draft') {
      toast.success('Campaign saved as draft successfully! 📝', {
        timeout: 4000
      })
      // Keep form values for draft
    } else {
      toast.success('Campaign submitted for review! 🎉', {
        timeout: 4000
      })
      // Reset form and redirect for submission
      resetForm()
      setTimeout(() => {
        router.push('/')
      }, 1000)
    }

  } catch (error: any) {
    console.error('Error creating campaign:', error)

    if (error.response?.status === 422) {
      // Handle validation errors from server
      const serverErrors = error.response.data.errors || {}
      Object.keys(serverErrors).forEach(key => {
        if (validationErrors.hasOwnProperty(key)) {
          validationErrors[key] = serverErrors[key][0] || ''
        }
      })
      toast.error('Please fix the form errors and try again.')
    } else if (error.response?.status === 419) {
      toast.error('Security error (CSRF). Please refresh the page and try again.')
    } else if (error.response?.status === 401) {
      toast.error('You must be logged in to create a campaign.')
    } else {
      toast.error(`Error: ${error.response?.data?.message || error.message || 'An unknown error occurred'}`)
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