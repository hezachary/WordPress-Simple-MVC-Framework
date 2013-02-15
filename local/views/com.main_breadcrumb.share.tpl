{if $share}
    {if !$aryMetaList}
        {$aryMetaList=PageModel::loadMeta($post)}
    {/if}
    {$arySiteSettings=PageWidget::settings($post)}
    {assign var='url' value=$post->ID|get_permalink|rawurlencode}
    {assign var='title' value='name'|get_bloginfo:'display'|cat:' :: ':$post->post_title}
    {assign var='title' value=ToolsExt::mb_filter($title)|unescape:'htmlall'|rawurlencode}
    {assign var='image_src' value=$image_src|rawurlencode}
    {assign var='desc' value=ToolsExt::mb_filter(trim($aryMetaList.description))|rawurlencode}
    {assign var='twitter_via' value=ToolsExt::mb_filter($arySiteSettings.twitter_via[0])|rawurlencode}
    {assign var='share_via_email_subject' value=';;'|explode:$arySiteSettings.share_via_email_subject[0]}
    {if $email_type == 'article'}
    {assign var='share_via_email_subject' value=$share_via_email_subject[1]}
    {elseif $email_type == 'product'}
    {assign var='share_via_email_subject' value=$share_via_email_subject[2]}
    {else}
    {assign var='share_via_email_subject' value=$share_via_email_subject[0]}
    {/if}
    {assign var='share_via_email_subject' value=ToolsExt::mb_filter(trim($share_via_email_subject))|rawurlencode}
    {assign var='line_break' value="\n"|rawurlencode}
    {assign var='email_content' value='Page Url: '|cat:$url:$line_break:$desc}
    <ul class="social_share_bar">
        <li><a class="share_bg" onclick="return project_tools.popupShareWindow(this, 'href')" href="http://www.facebook.com/sharer/sharer.php?s=100&amp;p[title]={$title}&amp;p[url]={$url}&amp;p[images][0]={$image_src}&amp;p[summary]={$desc}"><span class="icon facebook"><span class="accessibility">Facebook Share</span></span></a></li>
        <li><a class="share_bg" onclick="return project_tools.popupShareWindow(this, 'href')" href="http://twitter.com/share?url={$url}&amp;text={$title}&amp;via={$twitter_via}"><span class="icon twitter"><span class="accessibility">Twitter</span></span></a></li>
        <li><a class="share_bg" onclick="return project_tools.popupShareWindow(this, 'href')" href="https://plus.google.com/share?url={$url}"><span class="icon google"><span class="accessibility">Google Plus</span></span></a></li>
        <li><a class="share_bg" href="mailto:?subject={$share_via_email_subject}&amp;body={$email_content}"><span class="icon email"><span class="accessibility">Email</span></span></a></li>
    </ul>
{/if}