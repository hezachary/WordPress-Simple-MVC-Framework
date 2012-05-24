<ul>
    {foreach from=$nav key='slug' item='name'}
    <li>{$name} {if $source == $slug}-- Selected{/if}</li>
    {/foreach}
</ul>