        
        {assign var='title' value='the_title'|apply_filters:$post->post_title}
        {assign var='description' value=$post->ID|get_post_meta:'short_description':true|nl2br|strip_tags:false|truncate:250}
        {assign var='keywords' value=$postGlobalSetting->metas.configure->site->keywords|@strval}
        <title>{'name'|@bloginfo} :: {$title} {if $intPageNum > 1}:: Page {$intPageNum}{/if}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
        
        {if !$aryMetaList}
        {$aryMetaList=PageModel::loadMeta($post)}
        {/if}
        {foreach from=$aryMetaList key='name' item='meta_value'}
        {if $name == 'description'}
            {if $meta_value}
            <meta name="{$name}" content="{$meta_value}" />
            <meta property="og:{$name}" content="{$meta_value}"/>
            {else}
            <meta name="{$name}" content="{$description}" />
            <meta property="og:{$name}" content="{$description}"/>
            {/if}
        {elseif $name == 'keywords'}
            {if $meta_value}
            <meta name="{$name}" content="{$meta_value}" />
            <meta property="og:type" content="{$meta_value}"/>
            {else}
            <meta name="{$name}" content="{$keywords}" />
            <meta property="og:type" content="{$keywords}"/>
            {/if}
        {elseif $meta_value}
            <meta name="{$name}" content="{$meta_value}" />
        {/if}
        {/foreach}
        
        <meta property="og:title" content="{'name'|bloginfo} :: {$title} {if $intPageNum > 1}:: Page {$intPageNum}{/if}"/>
        <meta property="og:type" content="{'name'|bloginfo}"/>
        <meta property="og:url" content="{$source_data->ID|get_permalink}"/>
        {if $intImageId}
        <meta property="og:image" content="{$intImageId|wp_get_attachment_image_src:'article-thumb-image'|array_shift}"/>
        {/if}
        <meta property="og:site_name" content="{'name'|bloginfo}"/>
        
        <meta name="robots" content="{if get_option('blog_public')}INDEX,FOLLOW{else}noindex,nofollow{/if}" />
        <link rel="shortcut icon" type="image/x-icon" href="{$THEMEPATH}/ico/_site.ico" />
        <link rel="shortcut icon" type="image/ico" href="{$THEMEPATH}/ico/site.ico" />
        <link rel="stylesheet" type="text/css" media="screen" href="{$THEMEPATH}/css/global.css" />
        <!--[if (lt IE 8) ]><link rel="stylesheet" type="text/css" media="screen" href="{$THEMEPATH}/css/iefix.css" /><![endif]-->
        {include file='javascript.package.tpl'}
