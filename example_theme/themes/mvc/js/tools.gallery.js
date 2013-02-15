var gallery_tools = new function(){
    
    this.ini = function(){
        jq('.gallery:has(.gallery_item + .gallery_item)').each(function(){
            populateGallery(jq(this));
        });
    }
    
    var gallery_image_container, gallery_desc_container, gallery_paging_container;
    function populateGallery(node){
        var mapping = [];
        node.find('.gallery_item').each(function(){
            var node_image = jq(this).find('.gallery_image');
            var node_desc = jq(this).find('.gallery_desc');
            console.log(node_desc.height());
            mapping.push([node_image, node_desc, node_image.innerHeight(), node_desc.innerHeight()]);
            jq(this).hide();
        });
        gallery_image_container = jq('<div class="gallery_image_container"/>');
        gallery_desc_container = jq('<div class="gallery_desc_container"/>');
        gallery_paging_container = jq('<div class="gallery_paging_container"/>');
        
        for(var i = 0; i < mapping.length; i++){
            //1. populate Paging
            var paging_item = jq('<a href="javascript:void(0)"/>');
            paging_item.text(i+1);
            paging_item.data('gallery_index', i);
            paging_item.bind('click', function(){
                shiftGalleryItem(node, mapping, jq(this).data('gallery_index'));
            });
            gallery_paging_container.append(paging_item);
            
            //2. Populate Image List
            gallery_image_container.append(mapping[i][0]);
            
            //3. Populate Desc List
            gallery_desc_container.append(mapping[i][1]);
            
            //4. Initial Gallery
            if( i == 0){
                paging_item.addClass('selected');
            }else{
                mapping[i][0].hide();
                mapping[i][1].hide();
            }
        }
        gallery_paging_container.append('<div class="clear"><br class="accessibility"/></div>');
        
        node.append(gallery_image_container);
        node.append(gallery_paging_container);
        node.append(gallery_desc_container);
        
    }
    
    function shiftGalleryItem(node, mapping, selected_index){
        if(mapping[selected_index][0].is(':hidden')){
            for(var i = 0; i < mapping.length; i++){
                if( mapping[i][0].is(':visible')){
                    //show
                    gallery_paging_container.find('a.selected').removeClass('selected');
                    gallery_image_container.css('height', mapping[i][2] + 'px');
                    mapping[i][0].fadeOut(100, function(){
                        gallery_paging_container.find('a:eq(' + selected_index + ')').addClass('selected');
                        gallery_image_container.css('height', mapping[selected_index][2] + 'px');
                        mapping[selected_index][0].fadeIn(100);
                    });
                    gallery_desc_container.css('height', mapping[i][3] + 'px');
                    mapping[i][1].fadeOut(100, function(){
                        gallery_desc_container.css('height', mapping[selected_index][3] + 'px');
                        mapping[selected_index][1].fadeIn(100);
                    });
                    break;
                }
            }
        }
        
        
    }
    
}
jq(document).ready(function(){
    gallery_tools.ini();
});