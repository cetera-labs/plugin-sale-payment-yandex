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

// Подключаем каталог с переводами модуля
$t = $this->getTranslator();
$t->addTranslation(__DIR__.'/lang');

if ($this->getBo()) {

    $this->getBo()->addEditor(array(
        'id'    => EDITOR_TEXT_YANDEX_TAX_SYSTEM,
        'alias' => 'editor_text_yandex_tax_system',
        'name'  => $t->_('Редактор СНО')
    ));

    $this->getBo()->addPseudoField(array(
        'id'       => PSEUDO_FIELD_YANDEX_TAX_SYSTEM,
        'original' => FIELD_TEXT,
        'len'      => 1,
        'name'     => $t->_('Yandex СНО')
    ));

    $this->getBo()->addFieldEditor(PSEUDO_FIELD_YANDEX_TAX_SYSTEM, EDITOR_TEXT_YANDEX_TAX_SYSTEM);
	
    $this->getBo()->addEditor(array(
        'id'    => EDITOR_TEXT_YANDEX_TAX,
        'alias' => 'editor_text_yandex_tax',
        'name'  => $t->_('Редактор НДС')
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
			data : [['1', _('общая СН')], ['2', _('упрощенная СН (доходы)')], ['3', _('упрощенная СН (доходы минус расходы)')], ['4', _('единый налог на вмененный доход')], ['5', _('единый сельскохозяйственный налог')], ['6', _('патентная СН')],]
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
			data : [['1', _('без НДС')], ['2', _('НДС по ставке 0%')], ['3', _('НДС чека по ставке 10%')], ['4', _('НДС чека по ставке 18%')], ['5', _('НДС чека по расчетной ставке 10/110')], ['6', _('НДС чека по расчетной ставке 20/120')],]
		}),
		defaultValue: '1'
    })
    <?
    return 28;
}