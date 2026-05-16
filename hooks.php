<?php
/**
 * FA_Workflow Module Hooks for FrontAccounting
 */

define('SS_WORKFLOW', 143 << 8);

class hooks_ksf_FA_Workflow extends hooks {
    var $module_name = 'ksf_FA_Workflow';
    var $version = '2.4.0';

    function install_options($app) {
        global $path_to_root;

        switch($app->id) {
            case 'CRM':
                $app->add_lapp_function(0, _("Workflows"),
                    $path_to_root."/modules/".$this->module_name."/workflows.php", 'SA_WORKFLOWVIEW', MENU_ENTRY);
                $app->add_lapp_function(1, _("Triggers"),
                    $path_to_root."/modules/".$this->module_name."/triggers.php", 'SA_WORKFLOWMANAGE', MENU_ENTRY);
                $app->add_lapp_function(2, _("Actions"),
                    $path_to_root."/modules/".$this->module_name."/actions.php", 'SA_WORKFLOWMANAGE', MENU_ENTRY);
                $app->add_rapp_function(3, _("Workflow Log"),
                    $path_to_root."/modules/".$this->module_name."/log.php", 'SA_WORKFLOWVIEW', MENU_INQUIRY);
                break;
        }
    }

    function install_access() {
        $security_sections[SS_WORKFLOW] = _("Workflow Engine");
        $security_areas['SA_WORKFLOWVIEW'] = array(SS_WORKFLOW | 1, _("View Workflows"));
        $security_areas['SA_WORKFLOWMANAGE'] = array(SS_WORKFLOW | 2, _("Manage Workflows"));
        return array($security_areas, $security_sections);
    }

    function install_extension($check_only=true) {
        return true;
    }

    function install_tabs($app) {
    }

    function activate_extension($company, $check_only=true) {
        $updates = array('sql/update.sql' => array($this->module_name));
        $ok = $this->update_databases($company, $updates, $check_only);
        if ($check_only || !$ok) {
            return $ok;
        }
        $this->ensure_workflow_schema();
        return $ok;
    }

    private function table_exists($table) {
        $sql = "SHOW TABLES LIKE " . db_escape($table);
        $res = db_query($sql, 'Failed checking table existence');
        return db_num_rows($res) > 0;
    }

    private function ensure_workflow_schema() {
        $tables = array(
            TB_PREF . "fa_wf_triggers" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_wf_triggers` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_wf_actions" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_wf_actions` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_wf_workflows" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_wf_workflows` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(100) NOT NULL,
                    `description` TEXT,
                    `entity_type` VARCHAR(20) NOT NULL,
                    `is_active` TINYINT(1) DEFAULT 1,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_entity` (`entity_type`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_wf_log" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_wf_log` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        foreach ($tables as $table_name => $sql) {
            db_query($sql, "Could not create Workflow table: $table_name");
        }
    }

    function db_prevoid($trans_type, $trans_no) {
        // Handle voiding if needed
    }
}
?>
