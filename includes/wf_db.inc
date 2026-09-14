<?php
/**
 * Workflow Database Functions
 */

function add_trigger($trigger_data)
{
    $sql = "INSERT INTO " . TB_PREF . "fa_wf_triggers
        (name, entity_type, field_name, operator, field_value, trigger_type, is_active, priority)
    VALUES (" . db_escape($trigger_data['name']) . ",
            " . db_escape($trigger_data['entity_type']) . ",
            " . db_escape($trigger_data['field_name']) . ",
            " . db_escape($trigger_data['operator']) . ",
            " . db_escape($trigger_data['field_value']) . ",
            " . db_escape($trigger_data['trigger_type']) . ",
            " . db_escape($trigger_data['is_active'] ?? 1) . ",
            " . db_escape($trigger_data['priority'] ?? 0) . ")";

    db_query($sql, "Could not add trigger");
    return db_insert_id();
}

function get_trigger($trigger_id)
{
    $sql = "SELECT * FROM " . TB_PREF . "fa_wf_triggers WHERE id = " . db_escape($trigger_id);
    $result = db_query($sql, "Could not get trigger");
    return db_fetch_assoc($result);
}

function get_triggers($entity_type = null, $active_only = true)
{
    $sql = "SELECT * FROM " . TB_PREF . "fa_wf_triggers";
    $conds = [];
    
    if ($entity_type) {
        $conds[] = "entity_type = " . db_escape($entity_type);
    }
    if ($active_only) {
        $conds[] = "is_active = 1";
    }
    
    if ($conds) {
        $sql .= " WHERE " . implode(' AND ', $conds);
    }
    
    $sql .= " ORDER BY priority DESC, id";
    
    $result = db_query($sql, "Could not get triggers");
    $triggers = [];
    while ($row = db_fetch_assoc($result)) {
        $triggers[] = $row;
    }
    return $triggers;
}

function update_trigger($trigger_id, $trigger_data)
{
    $sql = "UPDATE " . TB_PREF . "fa_wf_triggers SET
        name = " . db_escape($trigger_data['name']) . ",
        entity_type = " . db_escape($trigger_data['entity_type']) . ",
        field_name = " . db_escape($trigger_data['field_name']) . ",
        operator = " . db_escape($trigger_data['operator']) . ",
        field_value = " . db_escape($trigger_data['field_value']) . ",
        trigger_type = " . db_escape($trigger_data['trigger_type']) . ",
        is_active = " . db_escape($trigger_data['is_active'] ?? 1) . ",
        priority = " . db_escape($trigger_data['priority'] ?? 0) . "
    WHERE id = " . db_escape($trigger_id);

    db_query($sql, "Could not update trigger");
}

function delete_trigger($trigger_id)
{
    $sql = "DELETE FROM " . TB_PREF . "fa_wf_triggers WHERE id = " . db_escape($trigger_id);
    db_query($sql, "Could not delete trigger");
    
    $sql = "DELETE FROM " . TB_PREF . "fa_wf_actions WHERE trigger_id = " . db_escape($trigger_id);
    db_query($sql, "Could not delete trigger actions");
}

function add_action($action_data)
{
    $config = is_array($action_data['action_config']) 
        ? json_encode($action_data['action_config']) 
        : $action_data['action_config'];
    
    $sql = "INSERT INTO " . TB_PREF . "fa_wf_actions
        (trigger_id, name, action_type, action_config, action_order, is_active)
    VALUES (" . db_escape($action_data['trigger_id']) . ",
            " . db_escape($action_data['name']) . ",
            " . db_escape($action_data['action_type']) . ",
            " . db_escape($config) . ",
            " . db_escape($action_data['action_order'] ?? 0) . ",
            " . db_escape($action_data['is_active'] ?? 1) . ")";

    db_query($sql, "Could not add action");
    return db_insert_id();
}

function get_actions($trigger_id)
{
    $sql = "SELECT * FROM " . TB_PREF . "fa_wf_actions
        WHERE trigger_id = " . db_escape($trigger_id) . "
        ORDER BY action_order";

    $result = db_query($sql, "Could not get actions");
    $actions = [];
    while ($row = db_fetch_assoc($result)) {
        if ($row['action_config']) {
            $row['action_config'] = json_decode($row['action_config'], true);
        }
        $actions[] = $row;
    }
    return $actions;
}

function update_action($action_id, $action_data)
{
    $config = is_array($action_data['action_config']) 
        ? json_encode($action_data['action_config']) 
        : $action_data['action_config'];
    
    $sql = "UPDATE " . TB_PREF . "fa_wf_actions SET
        name = " . db_escape($action_data['name']) . ",
        action_type = " . db_escape($action_data['action_type']) . ",
        action_config = " . db_escape($config) . ",
        action_order = " . db_escape($action_data['action_order'] ?? 0) . ",
        is_active = " . db_escape($action_data['is_active'] ?? 1) . "
    WHERE id = " . db_escape($action_id);

    db_query($sql, "Could not update action");
}

function delete_action($action_id)
{
    $sql = "DELETE FROM " . TB_PREF . "fa_wf_actions WHERE id = " . db_escape($action_id);
    db_query($sql, "Could not delete action");
}

function evaluate_trigger($trigger, $entity, $oldEntity = null): bool
{
    $fieldName = $trigger['field_name'];
    $operator = $trigger['operator'];
    $fieldValue = $trigger['field_value'];
    
    $currentValue = $entity[$fieldName] ?? null;
    $oldValue = $oldEntity[$fieldName] ?? null;
    
    return match ($operator) {
        'equals' => $currentValue == $fieldValue,
        'not_equals' => $currentValue != $fieldValue,
        'contains' => $currentValue !== null && strpos($currentValue, $fieldValue) !== false,
        'is_empty' => empty($currentValue),
        'is_not_empty' => !empty($currentValue),
        'changes' => $currentValue != $oldValue,
        'changes_from' => $oldValue == $fieldValue && $currentValue != $fieldValue,
        'greater_than' => is_numeric($currentValue) && is_numeric($fieldValue) && $currentValue > $fieldValue,
        'less_than' => is_numeric($currentValue) && is_numeric($fieldValue) && $currentValue < $fieldValue,
        default => false,
    };
}

function execute_action($action, &$entity, $context = null): bool
{
    $actionType = $action['action_type'];
    $config = $action['action_config'] ?? [];
    
    return match ($actionType) {
        'update_field', 'set_field' => wf_update_field($entity, $config),
        'calculate' => wf_calculate($entity, $config),
        'assign_to' => wf_assign_to($entity, $config),
        'trigger_event' => wf_trigger_event($action, $entity),
        'send_email' => wf_send_email($config, $entity),
        'add_note' => wf_add_note($config, $entity),
        default => true,
    };
}

function wf_update_field(array &$entity, array $config): bool
{
    $field = $config['field'] ?? null;
    $value = $config['value'] ?? null;
    
    if ($field) {
        $entity[$field] = $value;
        return true;
    }
    return false;
}

function wf_calculate(array &$entity, array $config): bool
{
    $targetField = $config['target_field'] ?? null;
    $expression = $config['expression'] ?? null;
    
    if (!$targetField || !$expression) {
        return false;
    }
    
    foreach ($entity as $key => $val) {
        $expression = str_replace('{' . $key . '}', (string) $val, $expression);
    }
    
    $entity[$targetField] = eval("return $expression;") ?? 0;
    return true;
}

function wf_assign_to(array &$entity, array $config): bool
{
    $field = $config['target_field'] ?? 'assigned_to';
    $entity[$field] = $config['user_id'] ?? null;
    return true;
}

function wf_trigger_event($action, array $entity): bool
{
    return true;
}

function wf_send_email(array $config, array $entity): bool
{
    return true;
}

function wf_add_note(array $config, array $entity): bool
{
    return true;
}

function run_workflows($entity_type, $entity, $oldEntity = null, $context = null): array
{
    $results = [
        'triggers_fired' => [],
        'actions_executed' => 0,
        'errors' => [],
    ];
    
    $triggers = get_triggers($entity_type);
    
    foreach ($triggers as $trigger) {
        if (evaluate_trigger($trigger, $entity, $oldEntity)) {
            $results['triggers_fired'][] = $trigger['id'];
            
            $actions = get_actions($trigger['id']);
            
            foreach ($actions as $action) {
                if (execute_action($action, $entity, $context)) {
                    $results['actions_executed']++;
                } else {
                    $results['errors'][] = "Action {$action['id']} failed";
                }
            }
        }
    }
    
    return $results;
}

function log_workflow_execution($workflow_id, $trigger_id, $action_id, $entity_type, $entity_id, $status, $error = null)
{
    $sql = "INSERT INTO " . TB_PREF . "fa_wf_log
        (workflow_id, trigger_id, action_id, entity_type, entity_id, status, error_message)
    VALUES (" . db_escape($workflow_id) . ",
            " . db_escape($trigger_id) . ",
            " . db_escape($action_id) . ",
            " . db_escape($entity_type) . ",
            " . db_escape($entity_id) . ",
            " . db_escape($status) . ",
            " . db_escape($error) . ")";

    db_query($sql, "Could not log workflow execution");
}