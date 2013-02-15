
        <script type="text/javascript" src="{$THEMEPATH}/lib/js/jquery__1.8.1/jquery-tools__1.2.7__all/jquery-ui__1.8.23/tools.js"></script>
        
        {$arySiteSettings=PageWidget::settings($post)}
        <script type="text/javascript">
            // <![CDATA[
            
            var wwwroot = "{$SITEURL}";
            var template_path = "{$THEMEPATH}";
            var www = "{$THEMEPATH}";
            var time = new Date('{'r'|@date}');
            var gcode = '{$arySiteSettings.google_analytics_id[0]}';
            var gtcode = '{$arySiteSettings.google_tag_manager_id[0]}';
            
            // ]]>
        </script>
        
        <script type="text/javascript" src="{$THEMEPATH}/js/project/tools.glossary.js"></script>