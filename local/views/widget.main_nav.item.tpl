<ul>
    {foreach from=$nav key='intPostId' item='arySubPostList'}
    {assign var='aryPost' value=$items[$intPostId]}
    <li class="{if $source == $aryPost.post_name}selected{/if}">
        <a href="{$aryPost.permalink}"><span>{'the_title'|apply_filters:$aryPost.post_title}</span></a>
        {if $arySubPostList|sizeof > 0}
        {include file='widget.main_nav.item.tpl' nav=$arySubPostList items=$items source=$source}
        {/if}
    </li>
    {/foreach}
</ul>