
            <div class="header_content">
                {$arySiteSettings=PageWidget::settings($post)}
                <div class="logo"><a id="logo" href="{get_bloginfo('home')}"><span>Simple MVC Theme for Wordpress</span></a></div>
                {$search_page_data=PageWidget::main('home/search-results')}
                <p class="facebook_url"><a id="main_facebook_logo" href="{$arySiteSettings.facebook_page_url[0]}" class="icon facebook" target="_blank"><span class="accessibility">Meet Us on Facebook</span></a></p>
                <form id="formSearch" name="formSearch" action="{$search_page_data.page->ID|get_permalink}" method="GET">
                    <div class="form_container">
                        <input class="input_search" value="" name="search" type="text" placeholder="Search" />
                        <input type="submit" name="_submit" value="Search" class="accessibility" />
                        <a id="search" class="icon search" href="javascript:void(0)" onmousedown="project_tools.stopSubNavSearchAnit(document.formSearch);" onclick="project_tools.searchSubmit(document.formSearch);"><span class="accessibility">Search</span></a>
                        <a id="expend_search" class="icon expend" href="javascript:void(0)" onclick="project_tools.expendSubNavSearchForm(this)"><span class="accessibility">Expend</span></a>
                    </div>
                </form>
                {include file='widget.main_nav.tpl' post_name=$post->post_name main_name=$main_name}
                <div class="clear"><br class="accessibility"/></div>
            </div>