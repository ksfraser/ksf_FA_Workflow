<?php
/**
 * Workflow Actions Administration
 */

$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FA_Workflow/includes/wf_db.inc");

page(_("Workflow Actions"));

$trigger_id = $_GET['trigger_id'] ?? 0;

simple_page_mode(true);

//--------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{
    $input_error = 0;
    if (strlen($_POST['name']) == 0) {
        $input_error = 1;
        display_error(_("Name cannot be empty."));
    }
    if ($input_error != 1) {
        $action_data = [
            'trigger_id' => $_POST['trigger_id'],
            'name' => $_POST['name'],
            'action_type' => $_POST['action_type'],
            'action_config' => $_POST['action_config'],
            'action_order' => $_POST['action_order'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($selected_id != -1) {
            update_action($selected_id, $action_data);
            display_notification(_('Action updated'));
        } else {
            add_action($action_data);
            display_notification(_('Action added'));
        }
        $Mode = 'RESET';
    }
}

if ($Mode == 'Delete') {
    delete_action($selected_id);
    display_notification(_('Action deleted'));
    $Mode = 'RESET';
}

//--------------------------------------------------------------------------------

if ($trigger_id > 0) {
    $trigger = get_trigger($trigger_id);
    echo '<h3>' . sprintf(_('Actions for: %s'), $trigger['name']) . '</h3>';
    
    start_form();
    
    start_table(TABLESTYLE, "width=60%");
    table_section_title(_("New Action"));
    
    hidden('trigger_id', $trigger_id);
    text_row_ex(_("Name:"), 'name', 40);
    select_row(_("Action Type:"), 'action_type', $_POST['action_type'] ?? 'update_field', [
        'update_field' => _('Update Field'),
        'set_field' => _('Set Field'),
        'calculate' => _('Calculate'),
        'trigger_event' => _('Trigger Event'),
        'send_email' => _('Send Email'),
        'assign_to' => _('Assign To'),
        'add_note' => _('Add Note'),
        'webhook' => _('Webhook'),
    ]);
    textarea_row(_("Config (JSON):"), 'action_config', $_POST['action_config'] ?? '{"field": "status", "value": "Closed"}', 40, 5);
    smallint_row(_("Order:"), 'action_order', $_POST['action_order'] ?? 0);
    check_row(_("Active:"), 'is_active', $_POST['is_active'] ?? 1);
    
    end_table();
    
    submit_center($Mode == 'EDIT_ITEM' ? _("Update") : _("Add Action"), true, '', true);
    
    end_form();
    
    echo '<h3>' . _('Existing Actions') . '</h3>';
    
    $actions = get_actions($trigger_id);
    
    start_table(TABLESTYLE, "width=80%");
    table_header([
        _("Name"), _("Type"), _("Config"), _("Order"), _("Active"), _("Actions")
    ]);
    
    while ($row = db_fetch_assoc($actions)) {
        $configTxt = is_array($row['action_config']) 
            ? json_encode($row['action_config']) 
            : $row['action_config'];
        
        href_js_edit_link("?selected_id=" . $row['id'] . "&Mode=EDIT_ITEM", 'edit', $row['name']);
        label_cell($row['action_type']);
        label_cell(mb_substr($configTxt, 0, 50) . (mb_strlen($configTxt) > 50 ? '...' : ''));
        label_cell($row['action_order']);
        label_cell($row['is_active'] ? _('Yes') : _('No'));
        delete_button_center("?selected_id=" . $row['id'] . "&Mode=Delete", _("Delete"));
        end_row();
    }
    
    end_table();
} else {
    echo '<p>' . _('Select a trigger from the Workflows page to manage its actions') . '</p>';
}

end_page();