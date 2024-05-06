[{block name="admin_module_config_form"}]
    <form class="edittext" action="[{ $oViewConf->getModuleUrl('ginger_payments_module_settings') }]" method="post">
        <h3>[{oxmultilang ident="MY_PAYMENT_MODULE_SETTINGS_TITLE"}]</h3>

        <div class="form-group">
            <label for="ginger_payments_module_setting1">Setting 1:</label>
            <input type="text" id="ginger_payments_module_setting1" name="ginger_payments_module_setting1" value="[{ $myPaymentModuleSetting1 }]" />
        </div>

        <div class="form-group">
            <label for="ginger_payments_module_setting2">Setting 2:</label>
            <input type="text" id="ginger_payments_module_setting2" name="ginger_payments_module_setting2" value="[{ $myPaymentModuleSetting2 }]" />
        </div>

        <div class="form-group">
            <label for="ginger_payments_module_setting3">Setting 3:</label>
            <input type="text" id="ginger_payments_module_setting3" name="ginger_payments_module_setting3" value="[{ $myPaymentModuleSetting3 }]" />
        </div>

        <div class="form-group">
            <input type="submit" value="Save" />
        </div>
    </form>
    [{/block}]