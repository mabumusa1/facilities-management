import { test, expect } from '../fixtures/scanner.fixture';

// Contact form routes from signals.md
const CONTACTS_ROUTES = [
  { path: '/contacts/Manager/form', name: 'contacts-manager-form' },
  { path: '/contacts/Owner/form', name: 'contacts-owner-form' },
  { path: '/contacts/Tenant/form', name: 'contacts-tenant-form' },
  { path: '/contacts/ServiceProfessional', name: 'contacts-service-professional' },
  { path: '/contacts/1/form', name: 'contacts-edit-form' },
  
  // Requests with type filters
  { path: '/requests?type=homeServices', name: 'requests-home-services' },
  { path: '/requests?type=neighbourhoodServices', name: 'requests-neighbourhood-services' },
  
  // More routes
  { path: '/more', name: 'more-page' },
  { path: '/pricing', name: 'pricing-page' },
];

test.describe('Contacts Forms Agent', () => {
  for (const route of CONTACTS_ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
        waitTimeout: 45000,
      });
      console.log(`Scanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }
});
