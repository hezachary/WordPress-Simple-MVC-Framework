<?xml version="1.0" encoding="UTF-8"?>
{assign var='post' value=$source_data}
{$nav_data=NavWidget::main($post->post_name)}
{$nav=$nav_data.nav}
{$items=$nav_data.items}
{$source=$nav_data.source}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    
    {$objHomePage=get_page_by_path('/home/')}
    <url>
    
      <loc>{site_url()}/</loc>

      <lastmod>{substr_replace($objHomePage->post_modified, 'T', 10, -8)}+10:00</lastmod>

      <priority>1.0</priority>
      
    </url>
    
    {foreach name='sitemap' from=$items key='intPostId' item='aryPost'}
    {if $aryPost.post_parent}
    <url>
    
      <loc>{$aryPost.permalink}</loc>

      <lastmod>{substr_replace($aryPost.post_modified, 'T', 10, -8)}+10:00</lastmod>

      <priority>{if $aryPost.post_name=='home'
      }1.0{elseif $items[$intPostId].navigation_type=='head'
      }0.9{elseif $items[$aryPost.post_parent].navigation_type=='head'
      }0.8{elseif has_category(array('article', 'product'), get_post($intPostId)) || $aryPost.post_name=='publications'
      }0.7{else
      }0.5{/if}</priority>
      
    </url>
    {/if}
    {/foreach}


</urlset>