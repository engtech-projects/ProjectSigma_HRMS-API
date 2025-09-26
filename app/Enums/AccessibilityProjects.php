<?php

namespace App\Enums;

enum AccessibilityProjects: string
{
    case PROJECTMONITORING_DASHBOARD = "project monitoring:dashboard";
    case PROJECTMONITORING_PROJECT = "project monitoring:projects";
    case PROJECTMONITORING_MARKETING = "project monitoring:marketing";
    case PROJECTMONITORING_MARKETING_MYPROJECTS = "project monitoring:marketing_my projects";
    case PROJECTMONITORING_MARKETING_BIDDINGLIST = "project monitoring:marketing_bidding list";
    case PROJECTMONITORING_MARKETING_PROPOSALLIST = "project monitoring:marketing_proposal list";
    case PROJECTMONITORING_MARKETING_ARCHIVEDLIST = "project monitoring:marketing_archived list";
    case PROJECTMONITORING_MARKETING_ONHOLDLIST = "project monitoring:marketing_on hold list";
    case PROJECTMONITORING_MARKETING_AWARDEDLIST = "project monitoring:marketing_awarded list";
    case PROJECTMONITORING_MARKETING_DRAFTLIST = "project monitoring:marketing_draft list";
    case PROJECTMONITORING_MARKETING_PROJECTCATALOGLIST = "project monitoring:marketing_projectcatalog list";
    case PROJECTMONITORING_MARKETING_BILL_OF_QUANTITIES = "project monitoring:marketing_bill_of_quantities";
    case PROJECTMONITORING_MARKETING_SUMMARY_OF_RATES = "project monitoring:marketing_summary_of_rates";
    case PROJECTMONITORING_MARKETING_SUMMARY_OF_BID = "project monitoring:marketing_summary_of_bid";
    case PROJECTMONITORING_MARKETING_CASHFLOW = "project monitoring:marketing_cashflow";
    case PROJECTMONITORING_MARKETING_ATTACHMENT = "project monitoring:marketing_attachment";
    case PROJECTMONITORING_TSS = "project monitoring:tss";
    case PROJECTMONITORING_TSS_LIVE_PROJECTS = "project monitoring:tss_live_projects";
    case PROJECTMONITORING_TSS_BILLS_OF_MATERIALS = "project monitoring:tss_bills_of_materials";
    case PROJECTMONITORING_TSS_DUPA = "project monitoring:tss_dupa";
    case PROJECTMONITORING_TSS_CASHFLOW = "project monitoring:tss_cashflow";
    case PROJECTMONITORING_TSS_PROJECT_DETAILS = "project monitoring:tss_project_details";
    case PROJECTMONITORING_SETUP_APPROVALS = "project monitoring:setup_approvals";
    case PROJECTMONITORING_SETUP_POSITION = "project monitoring:setup_position";
    case PROJECTMONITORING_SETUP_SYNCHRONIZATION = "project monitoring:setup_synchronization";

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }

    public static function toArraySwapped(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->name;
        }
        return $array;
    }
}
