project_tools = new function(){
    // This function creates a new anchor element and uses location
    // properties (inherent) to get the desired URL data. Some String
    // operations are used (to normalize results across browsers).
     
    this.parseURL = function(url) {
        var a =  document.createElement('a');
        a.href = url;
        return {
            source: url,
            protocol: a.protocol.replace(':',''),
            host: a.hostname,
            port: a.port,
            query: a.search,
            params: (function(){
                var ret = {},
                    seg = a.search.replace(/^\?/,'').split('&'),
                    len = seg.length, i = 0, s;
                for (;i<len;i++) {
                    if (!seg[i]) { continue; }
                    s = seg[i].split('=');
                    ret[s[0]] = s[1];
                }
                return ret;
            })(),
            file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
            hash: a.hash.replace('#',''),
            path: a.pathname.replace(/^([^\/])/,'/$1'),
            relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],
            segments: a.pathname.replace(/^\//,'').split('/')
        };
    }
    
    this.passUrlQuery = function(){
        if(window.location.search.length < 2) return {};
        var url = arguments.lengt > 0 ? arguments[0] : window.location.search.substr(1);
        var ary_url = url.split('&');
        var obj_url = {};
        for(var i = 0; i < ary_url.length ; i++){
            var ary_query_item = ary_url[i].split('=');
            if(typeof ary_query_item[1] != 'undefined'){
                obj_url[ary_query_item[0]] = ary_query_item[1];
            }
        }
        return obj_url;
    }
    
    /**
     * var data = {};
     * data = pickUpForm('form[id="form_name"]', data);
     * jq.ajax ....
     *
     **/
    this.pickUpForm = function(str_form_name, data){
        var blnPickDisabled = arguments.length > 2 && arguments[2] == true ? true : false;
        //console.log(str_form_name + ' input,' + str_form_name + ' select,' + str_form_name + ' textarea');
        jq(str_form_name + ' input,' + str_form_name + ' select,' + str_form_name + ' textarea').each(function(){
            if(!blnPickDisabled && this.disabled){
                return;
            }
            var field_type = jq(this).attr('type');
            var blnRetrieve = true;
            if(field_type == 'checkbox' || field_type == 'radio' ){
                if(!this.checked){
                    blnRetrieve = false;
                }
            }
            if(blnRetrieve){
                var input_node = jq(this);
                data[input_node.attr('name')] = input_node.val();   
            }
        });
        return data;
    }
    
    this.addLinkDataLayer = function(node){
        node.find('a').each(function(){
            var position = jq(this).parents();
            var label = jq.trim(jq(this).text().replace(/\s+/ig, ' '));
            var parents_names = [];
            var category = 'general';
            position.each(function(){
                switch(jq(this).get(0).tagName.toString().toLowerCase()){
                    case 'body':
                    case 'html':
                        return;
                        break;
                }
                
                parents_names.unshift((jq(this).attr('class') ? jq(this).attr('class') : '' ));
            })
            var action = parents_names.join(' ') + ' ' + (jq(this).attr('class') ? jq(this).attr('class') : '' );
            var locater = action + ' : ' + label;
            //console.log(locater);
            jq(this).bind('click', function(){
                dataLayer.push(['click', locater]);
                
                if(typeof _gaq != 'undefined'){
                    switch(true){
                        case /header.+header_content.+sub_panel/ig.test(locater):
                            category = 'super nav sub item click';
                            break;
                        //...
                    }
                    //console.log([category, action, label]);
                    _gaq.push(['_trackEvent', category, action, label]);
                }
                return true;
            });
        });
    }
}
var dataLayer = [{}];
jq(document).ready(function(){
    project_tools.addLinkDataLayer(jq('body'));
});