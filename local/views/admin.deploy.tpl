<div class="wrap">
<div id="icon-users" class="icon32"><br /></div>
<h2>Deploy</h2>
<hr />
<h3>Git Tagged Version(s)</h3>
<h4>Local Changes</h4>
<table class="wp-list-table widefat fixed users" cellspacing="0">
    <thead>
        <tr>
            <th scope="col" class="manage-column column-history"><span>History</span></th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th scope="col" class="manage-column column-history"><span>History</span></th>
        </tr>
    </tfoot>

    <tbody id="the-list" class='list:user'>
        {foreach from=$aryGitStatus item='strGitStatus'}
        <tr class="{cycle values=",alternate"}">
            <td class="manage-column column-tage_name"><span>{$strGitStatus|escape:"html"}</span></td>
        </tr>
        {/foreach}
    </tbody>
</table>

<h4>Pull Latest Changes</h4>
<table class="wp-list-table widefat fixed users" cellspacing="0">
    <tbody id="the-list" class='list:user'>
        <tr class="{cycle values=",alternate"}">
            <td class="manage-column column-tage_name"><span>Pull Latest Update</span></td>
            <td scope="col" class="manage-column column-status" width="300" align="right">
                <form name="reborn_deploy_pull" action="" method="POST">
                    <input type="hidden" value="{$strTag}" name="tag" />
                    <input type="hidden" value="git_pull" name="r" />
                    <input class="button-primary" style="text-align:center" type="submit" name="submit" value="Pull Latest Update"/>
                </form>
            </td>
        </tr>
    </tbody>
</table>

<h4>Avaliable Tag List</h4>
<p>Please backup changes before you try to roll back version.</p>
<table class="wp-list-table widefat fixed users" cellspacing="0">
    <thead>
        <tr>
            <th scope="col" class="manage-column column-tage_name"><span>Tag</span></th>
            <th scope="col" class="manage-column column-status" width="300"><span></span></th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th scope="col" class="manage-column column-tage_name"><span>Tag</span></th>
            <th scope="col" class="manage-column column-status" width="300"><span></span></th>
        </tr>
    </tfoot>

    <tbody id="the-list" class='list:user'>
        {foreach from=$aryTagList item='strTag'}
        <tr class="{cycle values=",alternate"}">
            <td class="manage-column column-tage_name"><span>{$strTag}</span></td>
            <td scope="col" class="manage-column column-status" width="300" align="right">
                <form name="reborn_deploy_{$strTag|regex_replace:'/\W/':'_'}" action="" method="POST">
                    <input type="hidden" value="{$strTag}" name="tag" />
                    <input type="hidden" value="reversion_update" name="r" />
                    <input class="button-primary" style="text-align:center" type="submit" name="submit" value="Roll Back Version Now"/>
                </form>
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>