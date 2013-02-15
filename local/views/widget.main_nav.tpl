{$nav_data=NavWidget::main($post_name)}
<ul>
    {foreach from=$nav_data.nav key='intPostId' item='arySubPostList'}
    {if $nav_data.items[$intPostId].navigation_type=='head'}
    {assign var='aryPost' value=$nav_data.items[$intPostId]}
    <li id="{$main_name}$sub_nav${$aryPost.post_name}" class="{$aryPost.post_name}">
        <a href="{$aryPost.permalink}"><span>{'the_title'|apply_filters:$aryPost.post_title}</span></a>
        {if $arySubPostList|sizeof > 0}
        <div class="sub_panel">
            <div class="category">{'the_title'|apply_filters:$aryPost.post_title}</div>
            <div class="sub_nav">
                {include file='widget.main_nav.item.tpl' nav=$arySubPostList items=$nav_data.items source=$nav_data.source}
                <div class="clear"><br class="accessibility" /></div>
            </div>
        </div>
        {/if}
    </li>
    {/if}
    {/foreach}
</ul>