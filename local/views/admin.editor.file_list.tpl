
    
    <div id="reborn_file_list">
    <h3>Template File List</h3>
	<form action="theme-editor.php" method="post">
	<select name="file_jump" onchange="window.location=this.value">
        {foreach from=$aryFileList item='aryTemplateList' key='text'}
    		<optgroup label="{_e($text)}">
        	{foreach from=$aryTemplateList item='ary_template_file'}
                {if $strPluginDirName}
                {assign var='file' value=$strPluginDirName|cat:'/':$ary_template_file[0]}
                <option {if $org_file==$file}selected="selected"{/if} value="plugin-editor.php?file={urlencode($file)}&amp;plugin={urlencode($ary_template_file[3])}">{$file}</option>
                {else}
                <option {if $ary_template_file[1]}selected="selected"{/if} value="theme-editor.php?file={urlencode($ary_template_file[0])}&amp;theme={urlencode($ary_template_file[3])}&amp;dir=theme">{$ary_template_file[0]}</option>
                {/if}
            {/foreach}
            </optgroup>
        {/foreach}
	</select>
	</form>
    </div>
    <script type="text/javascript">
        //<!--
        jQuery('#templateside').hide();
        jQuery('#templateside').before(jQuery('#reborn_file_list'));
        //->
    </script>