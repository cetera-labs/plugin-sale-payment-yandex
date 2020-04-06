<?php
define('PSEUDO_FIELD_YANDEX_TAX_SYSTEM', 2224);
define('EDITOR_TEXT_YANDEX_TAX_SYSTEM', 2234);
define('PSEUDO_FIELD_YANDEX_TAX', 2225);
define('EDITOR_TEXT_YANDEX_TAX', 2235);

if (class_exists("\Sale\Payment")) {
    try {
        \Sale\Payment::addGateway('\SalePaymentYandex\Gateway');
    } catch (\Exception $e) {
    }
}

// ���������� ������� � ���������� ������
$t = $this->getTranslator();
$t->addTranslation(__DIR__.'/lang');

if ($this->getBo()) {

    $this->getBo()->addEditor(array(
        'id'    => EDITOR_TEXT_YANDEX_TAX_SYSTEM,
        'alias' => 'editor_text_yandex_tax_system',
        'name'  => $t->_('�������� ���')
    ));

    $this->getBo()->addPseudoField(array(
        'id'       => PSEUDO_FIELD_YANDEX_TAX_SYSTEM,
        'original' => FIELD_TEXT,
        'len'      => 1,
        'name'     => $t->_('Yandex ���')
    ));

    $this->getBo()->addFieldEditor(PSEUDO_FIELD_YANDEX_TAX_SYSTEM, EDITOR_TEXT_YANDEX_TAX_SYSTEM);
	
    $this->getBo()->addEditor(array(
        'id'    => EDITOR_TEXT_YANDEX_TAX,
        'alias' => 'editor_text_yandex_tax',
        'name'  => $t->_('�������� ���')
    ));

    $this->getBo()->addPseudoField(array(
        'id'       => PSEUDO_FIELD_YANDEX_TAX,
        'original' => FIELD_TEXT,
        'len'      => 1,
        'name'     => $t->_('Yandex VAT')
    ));

    $this->getBo()->addFieldEditor(PSEUDO_FIELD_YANDEX_TAX, EDITOR_TEXT_YANDEX_TAX);	
}

function editor_text_yandex_tax_system_draw($field_def, $fieldvalue)
{
    ?>
    Ext.create('Ext.form.ComboBox',{
		fieldLabel: '<?= $field_def['describ'] ?>',
		name: '<?= $field_def['name'] ?>',
		allowBlank:<?= ($field_def['required'] ? 'false' : 'true') ?>,
		value: '<?= str_replace("\r", '\r', str_replace("\n", '\n', addslashes($fieldvalue))) ?>',
		editable: false,
		valueField: 'code',
		displayField: 'value',
		store: new Ext.data.SimpleStore({
			fields: ['code', 'value'],
			data : [['1', _('����� ��')], ['2', _('���������� �� (������)')], ['3', _('���������� �� (������ ����� �������)')], ['4', _('������ ����� �� ��������� �����')], ['5', _('������ �������������������� �����')], ['6', _('��������� ��')],]
		}),
		defaultValue: '1'
    })
    <?
    return 28;
}

function editor_text_yandex_tax_draw($field_def, $fieldvalue)
{
    ?>
    Ext.create('Ext.form.ComboBox',{
		fieldLabel: '<?= $field_def['describ'] ?>',
		name: '<?= $field_def['name'] ?>',
		allowBlank:<?= ($field_def['required'] ? 'false' : 'true') ?>,
		value: '<?= str_replace("\r", '\r', str_replace("\n", '\n', addslashes($fieldvalue))) ?>',
		editable: false,
		valueField: 'code',
		displayField: 'value',
		store: new Ext.data.SimpleStore({
			fields: ['code', 'value'],
			data : [['1', _('��� ���')], ['2', _('��� �� ������ 0%')], ['3', _('��� ���� �� ������ 10%')], ['4', _('��� ���� �� ������ 18%')], ['5', _('��� ���� �� ��������� ������ 10/110')], ['6', _('��� ���� �� ��������� ������ 20/120')],]
		}),
		defaultValue: '1'
    })
    <?
    return 28;
}