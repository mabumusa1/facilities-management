<?php

namespace App\Enums;

/**
 * The 32 permission subjects drawn from all.json permissions.subjects.
 */
enum PermissionSubject: string
{
    case Communities = 'communities';
    case Buildings = 'buildings';
    case Units = 'units';
    case Leases = 'leases';
    case SubLeases = 'subLeases';
    case Transactions = 'transactions';
    case Payments = 'payments';
    case Owners = 'owners';
    case Tenants = 'tenants';
    case Dependents = 'dependents';
    case Admins = 'admins';
    case Professionals = 'professionals';
    case HomeServices = 'homeServices';
    case NeighbourhoodServices = 'neighbourhoodServices';
    case VisitorAccess = 'visitorAccess';
    case FacilityBookings = 'facilityBookings';
    case ManagerRequests = 'managerRequests';
    case Facilities = 'facilities';
    case Announcements = 'announcements';
    case Directories = 'directories';
    case Suggestions = 'suggestions';
    case Complaints = 'complaints';
    case MarketPlaces = 'marketPlaces';
    case MarketPlaceBookings = 'marketPlaceBookings';
    case MarketPlaceVisits = 'marketPlaceVisits';
    case OfferRequests = 'offerRequests';
    case Reports = 'reports';
    case Settings = 'settings';
    case CompanyProfile = 'companyProfile';
    case InvoiceSettings = 'invoiceSettings';
    case LeaseSettings = 'leaseSettings';
    case Leads = 'leads';
}
