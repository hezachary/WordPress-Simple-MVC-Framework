User-agent: *
{foreach from=$aryDisallowList item='strPagePath'}
Disallow: {$strPagePath}
{/foreach}
Sitemap: {$strSitemapUrl}