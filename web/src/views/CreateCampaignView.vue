<template>
  <div class="create-campaign-view">
    <div class="container">
      <div class="page-header">
        <h1>Create a New Campaign</h1>
        <p>Fill in the information for your charitable campaign</p>
      </div>

      <form @submit.prevent="handleSubmit" class="campaign-form">
        <div class="form-section">
          <h3>Basic Information</h3>

          <div class="form-group">
            <label for="title">Campaign Title *</label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              required
              placeholder="Ex: Help for families in need"
              :class="{ 'error': errors.title }"
            />
            <span v-if="errors.title" class="error-message">{{ errors.title }}</span>
          </div>

          <div class="form-group">
            <label for="description">Description *</label>
            <textarea
              id="description"
              v-model="form.description"
              required
              rows="4"
              placeholder="Describe your campaign and its goal..."
              :class="{ 'error': errors.description }"
            ></textarea>
            <span v-if="errors.description" class="error-message">{{ errors.description }}</span>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="goal_amount">Goal Amount (€) *</label>
              <input
                id="goal_amount"
                v-model.number="form.goal_amount"
                type="number"
                min="1"
                step="0.01"
                required
                placeholder="5000"
                :class="{ 'error': errors.goal_amount }"
              />
              <span v-if="errors.goal_amount" class="error-message">{{ errors.goal_amount }}</span>
            </div>

            <div class="form-group">
              <label for="category_id">Category *</label>
              <select
                id="category_id"
                v-model="form.category_id"
                required
                :class="{ 'error': errors.category_id }"
              >
                <option value="">Select a category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
              <span v-if="errors.category_id" class="error-message">{{ errors.category_id }}</span>
            </div>
          </div>
        </div>

        <div class="form-section">
          <h3>Optional Settings</h3>

          <div class="form-group">
            <label class="checkbox-label">
              <input
                v-model="form.featured"
                type="checkbox"
              />
              <span class="checkmark"></span>
              Request to be featured (featured campaign)
            </label>
            <small class="form-help">Featured campaigns are highlighted on the homepage</small>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="showSuccess" class="success-message">
          <div class="success-header">
            <i class="fas fa-check-circle"></i>
            Success!
          </div>
          <p class="success-text">{{ successMessage }}</p>
        </div>

        <!-- Error Display -->
        <div v-if="Object.keys(errors).length > 0" class="error-summary">
          <div class="error-header">
            <i class="fas fa-exclamation-triangle"></i>
            Validation Errors:
          </div>
          <ul class="error-list">
            <li v-for="(error, field) in errors" :key="field">
              <strong>{{ field === 'general' ? '' : field + ': ' }}</strong>{{ error }}
            </li>
          </ul>
        </div>

        <div class="form-actions">
          <button
            type="button"
            class="btn btn-secondary"
            @click="saveAsDraft"
            :disabled="loading"
          >
            <i class="fas fa-save"></i>
            Save as Draft
          </button>

          <button
            type="button"
            class="btn btn-outline"
            @click="diagnoseCsrf"
            :disabled="loading"
          >
            <i class="fas fa-stethoscope"></i>
            Diagnose CSRF
          </button>

          <button
            type="submit"
            class="btn btn-primary"
            :disabled="loading"
          >
            <i class="fas fa-paper-plane"></i>
            Publish Campaign
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { api, diagnoseCsrf as apiDiagnoseCsrf } from '@/api/client'

const router = useRouter()
const auth = useAuthStore()

const loading = ref(false)
const categories = ref([])
const successMessage = ref('')
const showSuccess = ref(false)

const form = ref({
  title: '',
  description: '',
  goal_amount: 0,
  category_id: '',
  featured: false
})

const errors = ref<Record<string, string>>({})

// Sauvegarder comme brouillon
const saveAsDraft = async () => {
  await submitCampaign('draft')
}

// Publier la campagne
const handleSubmit = async () => {
  await submitCampaign('pending')
}

// Soumettre la campagne
const submitCampaign = async (status: 'draft' | 'pending') => {
  loading.value = true
  errors.value = {}

  try {
    const campaignData = {
      ...form.value,
      status,
      goal_amount: parseFloat(form.value.goal_amount.toString())
    }

    console.log('Submitting campaign:', campaignData)

    // Utiliser axios au lieu de fetch pour bénéficier des intercepteurs CSRF
    const response = await api.post('/api/campaigns', campaignData)

    console.log('Campaign created successfully:', response.data)

    // Afficher le message de succès selon le statut
    if (status === 'draft') {
      successMessage.value = 'Campaign saved as draft successfully! You can continue editing or publish it later.'
    } else {
      successMessage.value = 'Campaign submitted for publication! It will be reviewed by administrators before going live.'
    }

    showSuccess.value = true

    // Réinitialiser le formulaire après 3 secondes
    setTimeout(() => {
      form.value = {
        title: '',
        description: '',
        goal_amount: 0,
        category_id: '',
        featured: false
      }
      successMessage.value = ''
      showSuccess.value = false
    }, 3000)

  } catch (error: any) {
    console.error('Error creating campaign:', error)

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      console.log('Validation errors:', errors.value)
    } else if (error.response?.status === 419) {
      errors.value.general = 'Security error (CSRF). Please refresh the page.'
      console.error('CSRF token mismatch - diagnostic:')
      // Diagnostic CSRF
      console.log('Current cookies:', document.cookie)
      const xsrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))
      console.log('XSRF token found:', !!xsrfToken)
    } else if (error.response?.status === 401) {
      errors.value.general = 'You must be logged in to create a campaign.'
    } else {
      errors.value.general = `Error: ${error.response?.data?.message || error.message || 'An unknown error occurred'}`
    }
  } finally {
    loading.value = false
  }
}

// Fonction de diagnostic CSRF
const diagnoseCsrf = async () => {
  console.log('=== Démarrage diagnostic CSRF ===')
  await apiDiagnoseCsrf()

  // Vérifier aussi l'état de l'utilisateur
  console.log('User authenticated:', !!auth.user)
  console.log('User token:', auth.user?.token ? 'Present' : 'Missing')
}

// Charger les catégories
const loadCategories = async () => {
  try {
    const response = await api.get('/api/categories')
    categories.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement des catégories:', error)
  }
}

onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.create-campaign-view {
  min-height: 100vh;
  background-color: #f8fafc;
  padding: 2rem 0;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 1rem;
}

.page-header {
  text-align: center;
  margin-bottom: 3rem;
}

.page-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1rem;
}

.page-header p {
  font-size: 1.125rem;
  color: #6b7280;
}

.campaign-form {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.form-section {
  padding: 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.form-section:last-child {
  border-bottom: none;
}

.form-section h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  color: #1f2937;
  transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

.form-group.error input,
.form-group.error select,
.form-group.error textarea {
  border-color: #ef4444;
}

.error-message {
  display: block;
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-weight: normal;
  margin-bottom: 0.5rem;
}

.checkbox-label input {
  margin-right: 0.75rem;
  width: auto;
}

.form-help {
  display: block;
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.form-actions {
  padding: 2rem;
  background-color: #f9fafb;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  font-size: 1rem;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #4b5563;
}

.btn-primary {
  background: #4f46e5;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #4338ca;
}

.btn-outline {
  background: transparent;
  color: #6b7280;
  border: 2px solid #d1d5db;
}

.btn-outline:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
}

/* Success Message */
.success-message {
  background: #ecfdf5;
  border: 1px solid #d1fae5;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 2rem;
}

.success-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #059669;
  margin-bottom: 0.5rem;
}

.success-text {
  color: #065f46;
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.5;
}

/* Error Summary */
.error-summary {
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 2rem;
}

.error-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #dc2626;
  margin-bottom: 0.5rem;
}

.error-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.error-list li {
  color: #dc2626;
  margin-bottom: 0.25rem;
  font-size: 0.875rem;
}

.error-list li:last-child {
  margin-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .page-header h1 {
    font-size: 2rem;
  }
}
</style>

