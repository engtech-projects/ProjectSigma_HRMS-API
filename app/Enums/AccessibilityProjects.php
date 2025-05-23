<?php

namespace App\Enums;

enum AccessibilityProjects: string
{
    case PROJECTMONITORING_DASHBOARD = "project monitoring:dashboard";
    case PROJECTMONITORING_PROJECT = "project monitoring:projects";
    case PROJECTMONITORING_MARKETING = "project monitoring:marketing";
    case PROJECT_MONITORING_MARKETING_MY_PROJECTS = "project monitoring:marketing_my projects";
    case PROJECT_MONITORING_MARKETING_BIDDING_LIST = "project monitoring:marketing_bidding list";
    case PROJECT_MONITORING_MARKETING_PROPOSAL_LIST = "project monitoring:marketing_proposal list";
    case PROJECT_MONITORING_MARKETING_ARCHIVED_LIST = "project monitoring:marketing_archived list";
    case PROJECT_MONITORING_MARKETING_ON_HOLD_LIST = "project monitoring:marketing_on hold list";
    case PROJECT_MONITORING_MARKETING_AWARDED_LIST = "project monitoring:marketing_awarded list";
    case PROJECT_MONITORING_MARKETING_DRAFT_LIST = "project monitoring:marketing_draft list";
    case PROJECTMONITORING_TSS = "project monitoring:tss";
    case PROJECTMONITORING_SETUP = "project monitoring:setup";
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
