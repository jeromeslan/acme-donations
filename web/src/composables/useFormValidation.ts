import { ref, reactive } from 'vue'

export interface ValidationRule {
  required?: boolean
  minLength?: number
  maxLength?: number
  min?: number
  max?: number
  pattern?: RegExp
  custom?: (value: any) => string | null
}

export interface ValidationRules {
  [key: string]: ValidationRule
}

export interface ValidationErrors {
  [key: string]: string | null
}

export function useFormValidation(rules: ValidationRules) {
  const errors = reactive<ValidationErrors>({})
  const isValid = ref(true)

  const validateField = (fieldName: string, value: any): string | null => {
    const rule = rules[fieldName]
    if (!rule) return null

    // Required validation
    if (rule.required && (!value || value.toString().trim() === '')) {
      return `The ${fieldName.replace('_', ' ')} field is required.`
    }

    // Skip other validations if value is empty and not required
    if (!value || value.toString().trim() === '') {
      return null
    }

    // String validations
    if (typeof value === 'string') {
      if (rule.minLength && value.length < rule.minLength) {
        return `The ${fieldName.replace('_', ' ')} must be at least ${rule.minLength} characters.`
      }
      if (rule.maxLength && value.length > rule.maxLength) {
        return `The ${fieldName.replace('_', ' ')} must not exceed ${rule.maxLength} characters.`
      }
    }

    // Number validations
    if (typeof value === 'number' || !isNaN(Number(value))) {
      const numValue = Number(value)
      if (rule.min !== undefined && numValue < rule.min) {
        return `The ${fieldName.replace('_', ' ')} must be at least ${rule.min}.`
      }
      if (rule.max !== undefined && numValue > rule.max) {
        return `The ${fieldName.replace('_', ' ')} must not exceed ${rule.max}.`
      }
    }

    // Pattern validation
    if (rule.pattern && !rule.pattern.test(value.toString())) {
      return `The ${fieldName.replace('_', ' ')} format is invalid.`
    }

    // Custom validation
    if (rule.custom) {
      return rule.custom(value)
    }

    return null
  }

  const validate = (formData: Record<string, any>): boolean => {
    let hasErrors = false

    // Clear previous errors
    Object.keys(errors).forEach(key => {
      errors[key] = null
    })

    // Validate each field
    Object.keys(rules).forEach(fieldName => {
      const error = validateField(fieldName, formData[fieldName])
      if (error) {
        errors[fieldName] = error
        hasErrors = true
      }
    })

    isValid.value = !hasErrors
    return !hasErrors
  }

  const validateSingle = (fieldName: string, value: any): string | null => {
    const error = validateField(fieldName, value)
    errors[fieldName] = error
    return error
  }

  const clearErrors = () => {
    Object.keys(errors).forEach(key => {
      errors[key] = null
    })
    isValid.value = true
  }

  return {
    errors,
    isValid,
    validate,
    validateSingle,
    clearErrors
  }
}
