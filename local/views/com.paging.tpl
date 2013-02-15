        {$href=$href|replace:'?&amp;':'?'}
        {if substr($href, -1, 1) == '?'}{$href_1st=substr($href, strlen($href) - 1)}{else}{$href_1st=substr($href, strlen('&amp;') - 1)}{/if}
        <div class="tablenav-pages {if !$page_list|is_array || $page_list|sizeof < 1}noDisplay{/if}">
            <span class="displaying-num">{$item_num} items</span>
			<span class="pagination-links">
				{math assign=int_paging_rangeLowest equation="x" x=$int_paging_range}
				{math assign=int_paging_rangeHeighest equation="y - x + 1" x=$int_paging_range y=$page_list|@sizeof}
				{if $select_page > $int_paging_rangeLowest && $select_page < $int_paging_rangeHeighest}
					{math assign=int_paging_rangeLow equation="x - (y-1)/2" x=$select_page y=$int_paging_range}
					{math assign=int_paging_rangeHeigh equation="x + (y-1)/2" x=$select_page y=$int_paging_range}
					{math assign=intPagePrev equation="x - 1" x=$int_paging_rangeLow}
					{math assign=intPageNext equation="x + 1" x=$int_paging_rangeHeigh}
					{math assign=intPagePrevJump equation="x - (y-1)/2" x=$int_paging_rangeLow y=$int_paging_range}
					{math assign=intPageNextJump equation="x + (y-1)/2" x=$int_paging_rangeHeigh y=$int_paging_range}
				{elseif $select_page <= $int_paging_rangeLowest}
					{assign var=int_paging_rangeLow value=1}
					{assign var=int_paging_rangeHeigh value=$int_paging_rangeLowest}
					{assign var=intPagePrev value=0}
					{math assign=intPageNext equation="x + 1" x=$int_paging_rangeHeigh}
					{assign var=intPagePrevJump value=0}
					{math assign=intPageNextJump equation="x + (y-1)/2" x=$int_paging_rangeHeigh y=$int_paging_range}
				{elseif $select_page >= $int_paging_rangeHeighest}
					{assign var=int_paging_rangeLow value=$int_paging_rangeHeighest}
					{assign var=int_paging_rangeHeigh value=$page_list|@sizeof}
					{math assign=intPagePrev equation="x - 1" x=$int_paging_rangeLow}
					{assign var=intPageNext value=0}
					{math assign=intPagePrevJump equation="x - (y-1)/2" x=$int_paging_rangeLow y=$int_paging_range}
					{assign var=intPageNextJump value=0}
				{/if}
				{foreach name=topPaging from=$page_list item=intPageNum}
				{if ($intPageNum >= $int_paging_rangeLow && $intPageNum <= $int_paging_rangeHeigh)}
                    {if $select_page == $intPageNum}
                    <span class="selected"><strong class="href" data-title="{$page_title_list[$smarty.foreach.topPaging.index]}">{$intPageNum}</strong></span>
                    {else}
                    <span><a {if $nofollow}rel="nofollow"{/if} data-title="{$page_title_list[$smarty.foreach.topPaging.index]}" href="{if $intPageNum==1}{$href}{else}{$href|cat:'page_num=':$intPageNum}{/if}">{$intPageNum}</a></span>
                    {/if}
                {elseif $smarty.foreach.topPaging.first}
                <span class="first"><a {if $nofollow}rel="nofollow"{/if} data-title="{$page_title_list[$smarty.foreach.topPaging.index]}" href="{if $intPageNum==1}{$href}{else}{$href|cat:'page_num=':$intPageNum}{/if}">{$intPageNum}</a></span>
                {elseif $smarty.foreach.topPaging.last}
                <span class="last"><a {if $nofollow}rel="nofollow"{/if} data-title="{$page_title_list[$smarty.foreach.topPaging.index]}" href="{if $intPageNum==1}{$href}{else}{$href|cat:'page_num=':$intPageNum}{/if}">{$intPageNum}</a></span>
				{elseif $intPageNum==$intPagePrev}
				<span class="prev-page"><a {if $nofollow}rel="nofollow"{/if} data-title="{$page_title_list[$smarty.foreach.topPaging.index]}" href="{if $intPageNum==1}{$href}{else}{$href|cat:'page_num=':$intPageNum}{/if}">...</a></span>
				{elseif $intPageNum==$intPageNext}
				<span class="next-page"><a {if $nofollow}rel="nofollow"{/if} data-title="{$page_title_list[$smarty.foreach.topPaging.index]}" href="{if $intPageNum==1}{$href}{else}{$href|cat:'page_num=':$intPageNum}{/if}">...</a></span>
				{elseif $intPageNum==$intPagePrevJump}
				<span class="prev-range"><a {if $nofollow}rel="nofollow"{/if} data-title="{$page_title_list[$smarty.foreach.topPaging.index]}" href="{if $intPageNum==1}{$href}{else}{$href|cat:'page_num=':$intPageNum}{/if}">&laquo;</a></span>
				{elseif $intPageNum==$intPageNextJump}
				<span class="next-range"><a {if $nofollow}rel="nofollow"{/if} data-title="{$page_title_list[$smarty.foreach.topPaging.index]}" href="{if $intPageNum==1}{$href}{else}{$href|cat:'page_num=':$intPageNum}{/if}">&raquo;</a></span>
				{/if}
				{/foreach}
				<span class="paging-input"><form style="display:inline" name="paging" method="get" action=""><input style="width: 50px" type="text" name="page_num"/><input type="button" name="page_jump" class="button-primary" value="Jump To" onclick="fnPageJump(this.form.page_num.value)"/></form></span>
			</span>
            {if $quick_link}
            <span class="pagination-quick-link">
                {if $select_page != $page_list|sizeof}
                    {math assign=intPageNext equation="x + 1" x=$select_page}
                    <span class="next"><a {if $nofollow}rel="nofollow"{/if} data-title="{$page_title_list[$intPageNext]}" href="{if $intPageNext==1}{$href}{else}{$href|cat:'page_num=':$intPageNext}{/if}">{$intPageNext}</a></span>
                {/if}
                {if $select_page > 1}
                    {math assign=intPagePrev equation="x - 1" x=$select_page}
                    <span class="prev"><a {if $nofollow}rel="nofollow"{/if} data-title="{$page_title_list[$intPagePrev]}" href="{if $intPagePrev==1}{$href}{else}{$href|cat:'page_num=':$intPagePrev}{/if}">{$intPagePrev}</a></span>
                {/if}
            </span>
            {/if}
            <div class="clear"><br class="accessibility"/></div>
        </div>
        