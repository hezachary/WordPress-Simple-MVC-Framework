                
                {if $intPageNum}<!--sphider_noindex-->{/if}
                {$post_content=apply_filters('the_content', $post_content)}
                {if $post_content || $display}
                    <div class="cms_content">
                        {$post_content}
                        {if $add_tags}
                        {include file='com.tags.tpl' post_id=$post_id}
                        {/if}
                    </div>
                {/if}
                {if $intPageNum}<!--/sphider_noindex-->{/if}