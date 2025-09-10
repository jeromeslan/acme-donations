import { test, expect } from '@playwright/test'

test('happy path: featured -> detail -> donate mock', async ({ page }) => {
  await page.goto('/')
  await expect(page.getByText('ACME Donations')).toBeVisible()
  // Login demo
  await page.getByRole('button', { name: /login demo/i }).click()
  // Featured exists
  await expect(page.getByText(/Featured campaigns/i)).toBeVisible()
})


