import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'Facilities Management',
  description: 'User documentation for the Facilities Management System',
  base: '/facilities-management/',

  head: [
    ['link', { rel: 'icon', type: 'image/svg+xml', href: '/facilities-management/favicon.svg' }],
    ['meta', { name: 'theme-color', content: '#1a56db' }],
  ],

  themeConfig: {
    logo: { src: '/logo.svg', width: 24, height: 24 },

    nav: [
      { text: 'Getting Started', link: '/guide/overview' },
      { text: 'Properties', link: '/properties/' },
      { text: 'Leasing', link: '/leasing/' },
      { text: 'Service Requests', link: '/service-requests/' },
      {
        text: 'More',
        items: [
          { text: 'Contacts', link: '/contacts/' },
          { text: 'Transactions', link: '/transactions/' },
          { text: 'Facilities', link: '/facilities/' },
          { text: 'Reports', link: '/reports/' },
          { text: 'Settings', link: '/settings/' },
        ],
      },
    ],

    sidebar: {
      '/guide/': [
        {
          text: 'Getting Started',
          items: [
            { text: 'System Overview', link: '/guide/overview' },
            { text: 'Logging In', link: '/guide/login' },
            { text: 'Dashboard', link: '/guide/dashboard' },
            { text: 'Roles & Permissions', link: '/guide/roles' },
          ],
        },
      ],

      '/properties/': [
        {
          text: 'Properties',
          items: [
            { text: 'Overview', link: '/properties/' },
            { text: 'Communities', link: '/properties/communities' },
            { text: 'Buildings', link: '/properties/buildings' },
            { text: 'Units', link: '/properties/units' },
            { text: 'Facilities & Amenities', link: '/properties/facilities' },
          ],
        },
      ],

      '/leasing/': [
        {
          text: 'Leasing',
          items: [
            { text: 'Overview', link: '/leasing/' },
            { text: 'Creating a Lease', link: '/leasing/create' },
            { text: 'Managing Leases', link: '/leasing/manage' },
            { text: 'Lease Applications', link: '/leasing/applications' },
            { text: 'Renewals', link: '/leasing/renewals' },
            { text: 'Sub-Leases', link: '/leasing/sub-leases' },
            { text: 'Statistics', link: '/leasing/statistics' },
          ],
        },
      ],

      '/contacts/': [
        {
          text: 'Contacts',
          items: [
            { text: 'Overview', link: '/contacts/' },
            { text: 'Owners', link: '/contacts/owners' },
            { text: 'Tenants', link: '/contacts/tenants' },
            { text: 'Admins & Staff', link: '/contacts/admins' },
            { text: 'Service Professionals', link: '/contacts/professionals' },
          ],
        },
      ],

      '/service-requests/': [
        {
          text: 'Service Requests',
          items: [
            { text: 'Overview', link: '/service-requests/' },
            { text: 'Creating a Request', link: '/service-requests/create' },
            { text: 'Tracking Requests', link: '/service-requests/tracking' },
            { text: 'Assigning Professionals', link: '/service-requests/assign' },
            { text: 'Request Categories', link: '/service-requests/categories' },
          ],
        },
      ],

      '/transactions/': [
        {
          text: 'Transactions',
          items: [
            { text: 'Overview', link: '/transactions/' },
            { text: 'Recording Transactions', link: '/transactions/recording' },
            { text: 'Chart of Accounts', link: '/transactions/chart-of-accounts' },
            { text: 'Journal Entries', link: '/transactions/journal-entries' },
            { text: 'Overdue Tracking', link: '/transactions/overdues' },
          ],
        },
      ],

      '/facilities/': [
        {
          text: 'Facilities & Visitors',
          items: [
            { text: 'Overview', link: '/facilities/' },
            { text: 'Facility Bookings', link: '/facilities/bookings' },
            { text: 'Visitor Access', link: '/facilities/visitor-access' },
            { text: 'Community Directory', link: '/facilities/directory' },
          ],
        },
      ],

      '/announcements/': [
        {
          text: 'Announcements',
          items: [{ text: 'Managing Announcements', link: '/announcements/' }],
        },
      ],

      '/reports/': [
        {
          text: 'Reports',
          items: [
            { text: 'Overview', link: '/reports/' },
            { text: 'Lease Statistics', link: '/reports/leases' },
            { text: 'Occupancy Report', link: '/reports/occupancy' },
            { text: 'Maintenance Report', link: '/reports/maintenance' },
            { text: 'Rent Collection', link: '/reports/rent-collection' },
          ],
        },
      ],

      '/settings/': [
        {
          text: 'Settings',
          items: [
            { text: 'Overview', link: '/settings/' },
            { text: 'Home Services', link: '/settings/home-services' },
            { text: 'Visitor Requests', link: '/settings/visitor-requests' },
            { text: 'Facility Bookings', link: '/settings/facility-bookings' },
            { text: 'Forms Builder', link: '/settings/forms' },
            { text: 'Invoice Templates', link: '/settings/invoices' },
            { text: 'Bank Details', link: '/settings/bank-details' },
          ],
        },
      ],
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/mabumusa1/facilities-management' },
    ],

    footer: {
      message: 'Facilities Management System Documentation',
      copyright: 'Copyright © 2025',
    },

    search: {
      provider: 'local',
    },

    editLink: {
      pattern: 'https://github.com/mabumusa1/facilities-management/edit/main/docs-site/:path',
      text: 'Edit this page on GitHub',
    },

    lastUpdated: {
      text: 'Updated at',
      formatOptions: {
        dateStyle: 'full',
        timeStyle: 'medium',
      },
    },
  },
})
