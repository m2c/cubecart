<?php
/**
 * CubeCart v6
 * ========================================
 * CubeCart is a registered trade mark of CubeCart Limited
 * Copyright CubeCart Limited 2014. All rights reserved.
 * UK Private Limited Company No. 5323904
 * ========================================
 * Web:   http://www.cubecart.com
 * Email:  sales@cubecart.com
 * License:  GPL-3.0 http://opensource.org/licenses/GPL-3.0
 */
?>
<form action="{$VAL_SELF}" method="post" enctype="multipart/form-data">
  <div id="Kiplepay" class="tab_content">
    <h3>{$TITLE}</h3>
    <fieldset>
      <legend>{$LANG.module.cubecart_settings}</legend>
      <div><label for="status">{$LANG.common.status}</label><span><input type="hidden" name="module[status]" id="status" class="toggle" value="{$MODULE.status}" /></span></div>
      <div><label for="position">{$LANG.module.position}</label><span><input type="text" name="module[position]" id="position" class="textbox number" value="{$MODULE.position}" /></span></div>
      <div>
        <label for="scope">{$LANG.module.scope}</label>
        <span>
          <select name="module[scope]">
            <option value="both" {$SELECT_scope_both}>{$LANG.module.both}</option>
            <option value="main" {$SELECT_scope_main}>{$LANG.module.main}</option>
            <option value="mobile" {$SELECT_scope_mobile}>{$LANG.module.mobile}</option>
          </select>
        </span>
      </div>
      <div><label for="default">{$LANG.common.default}</label><span><input type="hidden" name="module[default]" id="default" class="toggle" value="{$MODULE.default}" /></span></div>
      <div><label for="description">{$LANG.common.description} *</label><span><input name="module[desc]" id="description" class="textbox" type="text" value="{$MODULE.desc}" /></span></div>
      <div><label for="sk_live">{$LANG.kiplepay.sk_live}</label><span><input name="module[sk_live]" id="sk_live" class="textbox" type="text" value="{$MODULE.sk_live}" /></span></div>
      <div><label for="pk_live">{$LANG.kiplepay.pk_live}</label><span><input name="module[pk_live]" id="pk_live" class="textbox" type="text" value="{$MODULE.pk_live}" /></span></div>
      <div>
        <label for="testMode">{$LANG.module.mode_test}</label>
        <span>
          <input type="hidden" name="module[testMode]" id="testMode" class="toggle" value="{$MODULE.testMode}" />
        </span>
      </div>
    </fieldset>
    <p>{$LANG.module.description_options}</p>
  </div>
  {$MODULE_ZONES}
  <div class="form_control">
    <input type="submit" name="save" value="{$LANG.common.save}" />
  </div>
  <input type="hidden" name="token" value="{$SESSION_TOKEN}" />
</form>