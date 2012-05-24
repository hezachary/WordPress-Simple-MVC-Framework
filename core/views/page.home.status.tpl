home status here!
<h1>Widget Load Sample 1: (template include file)</h1>
{$nav_data=NavWidget::main($source_data->post_name)}
{include file='widget.main_nav.tpl' nav=$nav_data.nav source=$nav_data.source}

<h1>Widget Load Sample 2: (class populate html)</h1>
{NavWidget::main($source_data->post_name, 'widget.main_nav.tpl')}

<h1>Widget Load Sample 3: (class populate json)</h1>
{NavWidget::main($source_data->post_name, 'widget.main_nav.tpl', true, true)}