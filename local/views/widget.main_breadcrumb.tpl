{$breadcrumb_data=NavWidget::breadcrumb($post_name)}
    {if $breadcrumb_data.nav|sizeof > 1}
    <ul class="breadcrumb_bar">
        {foreach name='breadcrumb' from=$breadcrumb_data.nav item='intPostId'}
        {assign var='aryPost' value=$breadcrumb_data.items[$intPostId]}
        <li class="{if $smarty.foreach.breadcrumb.first}first{elseif $smarty.foreach.breadcrumb.last}last{/if} li_{$smarty.foreach.breadcrumb.index}">{if !$smarty.foreach.breadcrumb.last}<a href="{$aryPost.ID|get_permalink}">{'the_title'|apply_filters:$aryPost.post_title}</a>{else}<span>{'the_title'|apply_filters:$aryPost.post_title}</span>{/if}</li>
        {/foreach}
    </ul>
    {/if}