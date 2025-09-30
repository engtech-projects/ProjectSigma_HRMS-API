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
    case PROJECTMONITORING_MARKETING_PROJECTCATALOGLIST = "project monitoring:marketing_project catalog list";
    case PROJECTMONITORING_MARKETING_BILLOFQUANTITIES = "project monitoring:marketing_bill of quantities";
    case PROJECTMONITORING_MARKETING_SUMMARYOFRATES = "project monitoring:marketing_summary of rates";
    case PROJECTMONITORING_MARKETING_SUMMARYOFBID = "project monitoring:marketing_summary of bid";
    case PROJECTMONITORING_MARKETING_CASHFLOW = "project monitoring:marketing_cashflow";
    case PROJECTMONITORING_MARKETING_ATTACHMENT = "project monitoring:marketing_attachment";
    case PROJECTMONITORING_TSS = "project monitoring:tss";
    case PROJECTMONITORING_TSS_LIVEPROJECTS = "project monitoring:tss_live projects";
    case PROJECTMONITORING_TSS_BILLOFMATERIALS = "project monitoring:tss_bill of materials";
    case PROJECTMONITORING_TSS_DUPA = "project monitoring:tss_dupa";
    case PROJECTMONITORING_TSS_CASHFLOW = "project monitoring:tss_cashflow";
    case PROJECTMONITORING_TSS_PROJECTDETAILS = "project monitoring:tss_project details";
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
