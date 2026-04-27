<?php

namespace App\Enums;

enum FeatureFlag: string
{
    case MarketplaceModule = 'marketplace_module';
    case PowerBIConnector = 'powerbi_connector';
    case FacilitiesManagement = 'facilities_management';
    case CommunicationHub = 'communication_hub';
    case DocumentVault = 'document_vault';
    case ReportsAndAnalytics = 'reports_and_analytics';

    public function labelEn(): string
    {
        return match ($this) {
            self::MarketplaceModule => 'Marketplace Module',
            self::PowerBIConnector => 'Power BI Connector',
            self::FacilitiesManagement => 'Facilities Management',
            self::CommunicationHub => 'Communication Hub',
            self::DocumentVault => 'Document Vault',
            self::ReportsAndAnalytics => 'Reports & Analytics',
        };
    }

    public function labelAr(): string
    {
        return match ($this) {
            self::MarketplaceModule => 'سوق العقارات',
            self::PowerBIConnector => 'موصّل Power BI',
            self::FacilitiesManagement => 'إدارة المرافق',
            self::CommunicationHub => 'مركز التواصل',
            self::DocumentVault => 'خزنة الوثائق',
            self::ReportsAndAnalytics => 'التقارير والتحليلات',
        };
    }

    public function includedInTiers(): array
    {
        return match ($this) {
            self::MarketplaceModule => ['Starter', 'Pro', 'Enterprise'],
            self::PowerBIConnector => ['Enterprise'],
            self::FacilitiesManagement => ['Starter', 'Pro', 'Enterprise'],
            self::CommunicationHub => ['Starter', 'Pro', 'Enterprise'],
            self::DocumentVault => ['Pro', 'Enterprise'],
            self::ReportsAndAnalytics => ['Starter', 'Pro', 'Enterprise'],
        };
    }
}
