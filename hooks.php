<?php
/**
 * FA_Workflow Module Hooks for FrontAccounting
 */

$module_name = 'FA_Workflow';
$module_version = '1.0.0';
$module_description = 'Workflow Engine - triggers, conditions, actions';
$module_author = 'KSFII Development Team';
$module_category = 'CRM';

function fa_wf_install(): bool
{
    global $db;

    @include_once __DIR__ . '/vendor-src/Ksfraser/Common/ComposerDependencyManager.php';
    if (class_exists('Ksfraser\Common\ComposerDependencyManager')) {
        $composerMgr = new \Ksfraser\Common\ComposerDependencyManager(__DIR__);
        $composerMgr->ensureDependencies();
        @include_once $composerMgr->getAutoloadPath();
    }

    if (!fa_wf_create_tables()) return false;
    if (!fa_wf_insert_initial_data()) return false;
    return true;
}

function fa_wf_activate(): bool
{
    @include_once __DIR__ . '/vendor-src/Ksfraser/Common/ComposerDependencyManager.php';
    if (class_exists('Ksfraser\Common\ComposerDependencyManager')) {
        $composerMgr = new \Ksfraser\Common\ComposerDependencyManager(__DIR__);
        $composerMgr->ensureDependencies();
        @include_once $composerMgr->getAutoloadPath();
    }

    return true;
}

function fa_wf_deactivate(): bool { return true; }
function fa_wf_uninstall(): bool { return true; }

function fa_wf_create_tables(): bool
{
    global $db;

    $tables = [
        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_wf_triggers` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `entity_type` VARCHAR(20) NOT NULL,
            `field_name` VARCHAR(50) NOT NULL,
            `operator` VARCHAR(20) DEFAULT 'equals',
            `field_value` VARCHAR(255) DEFAULT NULL,
            `trigger_type` VARCHAR(20) DEFAULT 'on_save',
            `is_active` TINYINT(1) DEFAULT 1,
            `priority` INT(11) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_entity` (`entity_type`),
            KEY `idx_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_wf_actions` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `trigger_id` INT(11) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `action_type` VARCHAR(30) NOT NULL,
            `action_config` TEXT,
            `action_order` INT(11) DEFAULT 0,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_trigger` (`trigger_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_wf_workflows` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `description` TEXT,
            `entity_type` VARCHAR(20) NOT NULL,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_entity` (`entity_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_wf_log` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `workflow_id` INT(11) DEFAULT NULL,
            `trigger_id` INT(11) DEFAULT NULL,
            `action_id` INT(11) DEFAULT NULL,
            `entity_type` VARCHAR(20) DEFAULT NULL,
            `entity_id` INT(11) DEFAULT NULL,
            `status` VARCHAR(20) DEFAULT 'success',
            `error_message` TEXT,
            `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_workflow` (`workflow_id`),
            KEY `idx_entity` (`entity_type`, `entity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];

    foreach ($tables as $sql) {
        if (!db_query($sql, "Could not create workflow table")) return false;
    }
    return true;
}

function fa_wf_insert_initial_data(): bool
{
    return true;
}