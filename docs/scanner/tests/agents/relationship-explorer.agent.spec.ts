import { test, expect } from '../fixtures/scanner.fixture';

/**
 * Test relationship exploration on pages with cascading dropdowns
 */
test.describe('Relationship Explorer Agent', () => {

  test('explore contacts-tenant-form relationships', async ({ scanner }) => {
    const result = await scanner.scanPage('/contacts/Tenant/form', 'contacts-tenant-form-relationships', {
      waitForNetworkIdle: true,
      takeScreenshot: true,
      exploreRelationships: true,
      waitTimeout: 60000,
    });

    console.log(`Scanned with relationships: ${result.endpoints.length} endpoints`);
    if (result.relationships) {
      console.log(`Found ${result.relationships.dropdownRelationships.length} dropdown relationships`);
      console.log(`Found ${result.relationships.hardcodedValues.length} hardcoded values`);
      console.log(`Found ${Object.keys(result.relationships.staticOptions).length} static option sets`);
    }
  });

  test('explore properties community->building->unit relationships', async ({ scanner, page }) => {
    // First scan the units page which has community -> building -> unit cascading
    const result = await scanner.scanPage('/properties/units', 'properties-units-relationships', {
      waitForNetworkIdle: true,
      takeScreenshot: true,
      exploreRelationships: true,
      waitTimeout: 60000,
    });

    console.log(`Scanned units with relationships: ${result.endpoints.length} endpoints`);
    if (result.relationships) {
      console.log(`Found ${result.relationships.dropdownRelationships.length} dropdown relationships`);

      // Log the relationships found
      for (const rel of result.relationships.dropdownRelationships) {
        console.log(`\nDropdown: ${rel.triggerDropdown.name}`);
        console.log(`  Selected: ${rel.triggerDropdown.selectedLabel}`);
        console.log(`  Triggered ${rel.triggeredApiCalls.length} API calls:`);
        for (const call of rel.triggeredApiCalls) {
          console.log(`    ${call.method} ${call.url}`);
        }
        if (rel.affectedDropdowns.length > 0) {
          console.log(`  Affected ${rel.affectedDropdowns.length} other dropdowns`);
        }
      }
    }
  });

  test('explore lease form relationships', async ({ scanner }) => {
    // Lease forms typically have cascading: community -> building -> unit -> tenant
    const result = await scanner.scanPage('/leasing/Lease/form', 'leasing-form-relationships', {
      waitForNetworkIdle: true,
      takeScreenshot: true,
      exploreRelationships: true,
      waitTimeout: 60000,
    });

    console.log(`Scanned lease form with relationships: ${result.endpoints.length} endpoints`);
    if (result.relationships) {
      console.log(`Found ${result.relationships.dropdownRelationships.length} dropdown relationships`);
    }
  });

  test('explore service request form relationships', async ({ scanner }) => {
    // Service request forms have category -> subcategory cascading
    const result = await scanner.scanPage('/requests/home-services/form', 'requests-form-relationships', {
      waitForNetworkIdle: true,
      takeScreenshot: true,
      exploreRelationships: true,
      waitTimeout: 60000,
      failOnNotFound: false,
    });

    console.log(`Scanned request form with relationships: ${result.endpoints.length} endpoints`);
    if (result.relationships) {
      console.log(`Found ${result.relationships.dropdownRelationships.length} dropdown relationships`);
    }
  });
});
