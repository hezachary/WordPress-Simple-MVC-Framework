<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{assign var='post' value=$source_data}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
    {include file='com.head.tpl'}
    </head>
    <body>
        {include file='com.header.tpl' post=$post}
        <p>Below content is from [home control - index]</p> 
        <p>{$content}</p>
        <p>To change the router, in url query or in post add [r=router_name], for home page - such as: ?r=login</p>
    </body>
</html>