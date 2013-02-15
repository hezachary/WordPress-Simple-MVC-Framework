<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{assign var='post' value=$source_data}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
    {include file='com.head.tpl'}
    </head>
    <body>
        {include file='com.header.tpl' post=$post}
        {include file='com.main_breadcrumb.tpl' post=$post}
        <div class="container _{$post->post_name}">
            <div class="page_content">
                <div class="formSubSearch">
                    <form id="formSubSearch" name="formSubSearch" action="{$search_page_data.page->ID|get_permalink}" method="GET">
                        <div>
                            <input class="" value="" name="search" type="text" placeholder="Search" />
                            <input type="submit" name="_submit" value="Search" class="accessibility" />
                            <a class="icon search" href="javascript:void(0)" onclick="document.formSubSearch.submit()"><span class="accessibility">Search</span></a>
                            <div class="clear"><br class="accessibility"/></div>
                        </div>
                    </form>
                </div>
                
                <div class="cms_content">
                    <h1>Oops! THIS PAGE CANNOT BE FOUND</h1>
                    <p>Apologies, but the page you requested could not be found.<br /> 
                    Perhaps searching will help.</p>
                    <p class="large_404">404</p>
                </div>
                {include file='com.cms_content.tpl' post_content=$post->post_content}
                
                <div class="clear"><br class="accessibility" /></div>
            </div>
            <div class="clear"><br class="accessibility" /></div>
        </div>
        {include file='com.footer.tpl'}
    </body>
</html>