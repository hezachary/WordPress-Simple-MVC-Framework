<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{assign var='post' value=$source_data}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <link rel="canonical" href="{get_permalink($post->ID)}" />
    {include file='com.head.tpl'}
    </head>
    <body>
        {include file='com.header.tpl' post=$post}
        {include file='com.main_breadcrumb.tpl' post=$post}
        
                {include file='com.cms_content.tpl' post_content=$post->post_content}
                
    </body>
</html>