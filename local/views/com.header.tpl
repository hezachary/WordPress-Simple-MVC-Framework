        <!--sphider_noindex-->

        <script type="text/javascript">
        // <![CDATA[ 
        /*{literal}*/
        if(parseInt(gcode.substr(3)) != 0){
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', gcode]);
          _gaq.push(['_trackPageview']);
          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        }
        /*{/literal}*/
        // ]]>
        </script>
        
        {*
        <!-- Google Tag Manager -->
        {$arySiteSettings=PageWidget::settings($post)}
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id={$arySiteSettings.google_tag_manager_id[0]}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <script type="text/javascript">
        // <![CDATA[ 
        /*{literal}*/
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer',gtcode);
        /*{/literal}*/
        // ]]>
        </script>
        <!-- End Google Tag Manager -->
        *}
        
        <div class="header">
            {include file='com.header.header_content.tpl' post=$post main_name='header'}
        </div>
        <div class="sub_header"></div>
        <!--/sphider_noindex-->