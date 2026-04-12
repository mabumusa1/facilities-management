import { test, expect } from '../fixtures/scanner.fixture';

const COMPLAINTS_ROUTES = [
  // Complaints/Issues from signals.md
  { path: '/dashboard/complaints', name: 'dashboard-complaints' },
  { path: '/dashboard/complaints/1', name: 'dashboard-complaints-details' },
  
  // Announcements
  { path: '/dashboard/announcements/2', name: 'dashboard-announcements-details-2' },
  
  // Bookings detail
  { path: '/dashboard/bookings/1', name: 'dashboard-bookings-details' },
  { path: '/dashboard/booking-contracts/1', name: 'dashboard-booking-contracts-details' },
  
  // Admins/Users
  { path: '/contacts/admins', name: 'contacts-admins' },
  
  // Accounting module
  { path: '/accounting', name: 'accounting-main' },
  { path: '/reporting', name: 'reporting-main' },
];

test.describe('Complaints Agent', () => {
  for (const route of COMPLAINTS_ROUTES) {
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
