<?php
/**
 * Workflow Administration Page
 */

$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FA_Workflow/includes/wf_db.inc");

page(_("Workflow Administration"));

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
        $trigger_data = [
            'name' => $_POST['name'],
            'entity_type' => $_POST['entity_type'],
            'field_name' => $_POST['field_name'],
            'operator' => $_POST['operator'],
            'field_value' => $_POST['field_value'],
            'trigger_type' => $_POST['trigger_type'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'priority' => $_POST['priority'],
        ];
        if ($selected_id != -1) {
            update_trigger($selected_id, $trigger_data);
            display_notification(_('Trigger updated'));
        } else {
            add_trigger($trigger_data);
            display_notification(_('Trigger added'));
        }
        $Mode = 'RESET';
    }
}

if ($Mode == 'Delete') {
    delete_trigger($selected_id);
    display_notification(_('Trigger deleted'));
    $Mode = 'RESET';
}

if ($Mode == 'EDIT_ITEM') {
    $myrow = get_trigger($selected_id);
    if ($myrow) $_POST = $myrow;
}

if ($Mode == 'RESET') {
    $_POST['name'] = '';
    $_POST['entity_type'] = 'debtor';
    $_POST['field_name'] = '';
    $_POST['operator'] = 'equals';
    $_POST['field_value'] = '';
    $_POST['trigger_type'] = 'on_save';
    $_POST['is_active'] = 1;
    $_POST['priority'] = 0;
}

//--------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE, "width=60%");
table_section_title($Mode == 'EDIT_ITEM' ? _("Edit Trigger") : _("New Trigger"));

text_row_ex(_("Name:"), 'name', 40);
select_row(_("Entity Type:"), 'entity_type', $_POST['entity_type'] ?? 'debtor', [
    'debtor' => _('Customer'),
    'contact' => _('Contact'),
    'opportunity' => _('Opportunity'),
    'ticket' => _('Ticket'),
    'lead' => _('Lead'),
]);
text_row_ex(_("Field Name:"), 'field_name', 30);
select_row(_("Operator:"), 'operator', $_POST['operator'] ?? 'equals', [
    'equals' => _('Equals'),
    'not_equals' => _('Not Equals'),
    'contains' => _('Contains'),
    'is_empty' => _('Is Empty'),
    'is_not_empty' => _('Is Not Empty'),
    'changes' => _('Changes'),
    'changes_from' => _('Changes From'),
    'greater_than' => _('Greater Than'),
    'less_than' => _('Less Than'),
]);
text_row_ex(_("Field Value:"), 'field_value', 30);
select_row(_("Trigger Type:"), 'trigger_type', $_POST['trigger_type'] ?? 'on_save', [
    'on_save' => _('On Save'),
    'on_create' => _('On Create'),
    'on_update' => _('On Update'),
    'on_delete' => _('On Delete'),
]);
check_row(_("Active:"), 'is_active', $_POST['is_active'] ?? 1);
smallint_row(_("Priority:"), 'priority', $_POST['priority'] ?? 0);

end_table();

submit_center($Mode == 'EDIT_ITEM' ? _("Update") : _("Add Trigger"), true, '', true);

end_form();

//--------------------------------------------------------------------------------

echo '<h3>' . _('Workflow Triggers') . '</h3>';

$triggers = get_triggers();

start_table(TABLESTYLE, "width=80%");
table_header([
    _("Name"), _("Entity"), _("Field"), _("Operator"), _("Value"), _("Type"), _("Priority"), _("Active"), _("Actions")
]);

while ($row = db_fetch_assoc($triggers)) {
    href_js_edit_link("?selected_id=" . $row['id'] . "&Mode=EDIT_ITEM", 'edit', $row['name']);
    label_cell($row['entity_type']);
    label_cell($row['field_name']);
    label_cell($row['operator']);
    label_cell($row['field_value'] ?? '-');
    label_cell($row['trigger_type']);
    label_cell($row['priority']);
    label_cell($row['is_active'] ? _('Yes') : _('No'));
    delete_button_center("?selected_id=" . $row['id'] . "&Mode=Delete", _("Delete"));
    end_row();
}

end_table();

end_page();