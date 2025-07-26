{*
 * Configuration Options Template for Billable Hours Quick Entry Dashlet
 * Allows users to customize dashlet behavior
 *}

<table width="400" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td valign="top" scope="row">
            {$titleLbl}: <span class="required">*</span>
        </td>
        <td>
            <input class="text" name="title" type="text" value="{$title}" size="20" maxlength="50">
        </td>
    </tr>
    <tr>
        <td valign="top" scope="row">
            {$defaultActivityTypeLbl}:
        </td>
        <td>
            <select name="defaultActivityType" class="text">
                {foreach from=$activityTypes key=type_key item=type_label}
                    <option value="{$type_key}" {if $type_key == $defaultActivityType}selected{/if}>
                        {$type_label}
                    </option>
                {/foreach}
            </select>
        </td>
    </tr>
    <tr>
        <td valign="top" scope="row">
            {$defaultRateLbl}:
        </td>
        <td>
            <input class="text" name="defaultRate" type="number" value="{$defaultRate}" 
                   size="10" step="0.01" min="0" max="9999.99">
        </td>
    </tr>
    <tr>
        <td valign="top" scope="row">
            {$autoSaveLbl}:
        </td>
        <td>
            <input type="checkbox" name="autoSave" value="1" {if $autoSave}checked{/if}>
            <label for="autoSave">Enable automatic saving when timer stops</label>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="hidden" name="id" value="{$id}">
            <input type="submit" class="button" value="{$saveLbl}" name="save">
            <input type="button" class="button" value="{$clearLbl}" name="clear" 
                   onclick="document.getElementById('dashlet_options_{$id}').style.display='none';">
        </td>
    </tr>
</table>