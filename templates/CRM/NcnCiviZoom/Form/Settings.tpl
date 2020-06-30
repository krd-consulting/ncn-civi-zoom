{* HEADER *}
<div class="crm-block crm-form-block">
{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}
    {if $elementName == 'custom_field_id'}
      <span class="description">
      </br>
      {ts}Select the event custom field which holds the Zoom Webminar ID{/ts}
    {/if}
    {if $elementName == 'custom_field_id_meeting'}
      <span class="description">
      </br>
      {ts}Select the event custom field which holds the Zoom Meeting ID{/ts}
    {/if}
    </div>


    <div class="clear"></div>
  </div>
{/foreach}

{* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
</div>
