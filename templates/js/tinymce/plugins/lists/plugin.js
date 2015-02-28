tinymce.PluginManager.add("lists",function(a){function b(a){return a&&/^(OL|UL|DL)$/.test(a.nodeName)}function c(a){return a.parentNode.firstChild==a}function d(a){return a.parentNode.lastChild==a}function e(b){return b&&!!a.schema.getTextBlockElements()[b.nodeName]}var f=this;a.on("init",function(){function g(a){function b(b){var d,e,f;e=a[b?"startContainer":"endContainer"],f=a[b?"startOffset":"endOffset"],1==e.nodeType&&(d=v.create("span",{"data-mce-type":"bookmark"}),e.hasChildNodes()?(f=Math.min(f,e.childNodes.length-1),b?e.insertBefore(d,e.childNodes[f]):v.insertAfter(d,e.childNodes[f])):e.appendChild(d),e=d,f=0),c[b?"startContainer":"endContainer"]=e,c[b?"startOffset":"endOffset"]=f}var c={};return b(!0),a.collapsed||b(),c}function h(a){function b(b){function c(a){for(var b=a.parentNode.firstChild,c=0;b;){if(b==a)return c;(1!=b.nodeType||"bookmark"!=b.getAttribute("data-mce-type"))&&c++,b=b.nextSibling}return-1}var d,e,f;d=f=a[b?"startContainer":"endContainer"],e=a[b?"startOffset":"endOffset"],d&&(1==d.nodeType&&(e=c(d),d=d.parentNode,v.remove(f)),a[b?"startContainer":"endContainer"]=d,a[b?"startOffset":"endOffset"]=e)}b(!0),b();var c=v.createRng();c.setStart(a.startContainer,a.startOffset),a.endContainer&&c.setEnd(a.endContainer,a.endOffset),w.setRng(c)}function i(b,c){var d,e,f,g=v.createFragment(),h=a.schema.getBlockElements();if(a.settings.forced_root_block&&(c=c||a.settings.forced_root_block),c&&(e=v.create(c),e.tagName===a.settings.forced_root_block&&v.setAttribs(e,a.settings.forced_root_block_attrs),g.appendChild(e)),b)for(;d=b.firstChild;){var i=d.nodeName;f||"SPAN"==i&&"bookmark"==d.getAttribute("data-mce-type")||(f=!0),h[i]?(g.appendChild(d),e=null):c?(e||(e=v.create(c),g.appendChild(e)),e.appendChild(d)):g.appendChild(d)}return a.settings.forced_root_block?f||tinymce.Env.ie&&!(tinymce.Env.ie>10)||e.appendChild(v.create("br",{"data-mce-bogus":"1"})):g.appendChild(v.create("br")),g}function j(){return tinymce.grep(w.getSelectedBlocks(),function(a){return/^(LI|DT|DD)$/.test(a.nodeName)})}function k(a,b,c){var d,e,f=v.select('span[data-mce-type="bookmark"]',a);c=c||i(b),d=v.createRng(),d.setStartAfter(b),d.setEndAfter(a),e=d.extractContents(),v.isEmpty(e)||v.insertAfter(e,a),v.insertAfter(c,a),v.isEmpty(b.parentNode)&&(tinymce.each(f,function(a){b.parentNode.parentNode.insertBefore(a,b.parentNode)}),v.remove(b.parentNode)),v.remove(b)}function l(a){var c,d;if(c=a.nextSibling,c&&b(c)&&c.nodeName==a.nodeName){for(;d=c.firstChild;)a.appendChild(d);v.remove(c)}if(c=a.previousSibling,c&&b(c)&&c.nodeName==a.nodeName){for(;d=c.firstChild;)a.insertBefore(d,a.firstChild);v.remove(c)}}function m(a){tinymce.each(tinymce.grep(v.select("ol,ul",a)),function(a){var c,d=a.parentNode;"LI"==d.nodeName&&d.firstChild==a&&(c=d.previousSibling,c&&"LI"==c.nodeName&&(c.appendChild(a),v.isEmpty(d)&&v.remove(d))),b(d)&&(c=d.previousSibling,c&&"LI"==c.nodeName&&c.appendChild(a))})}function n(a){function e(a){v.isEmpty(a)&&v.remove(a)}var f,g=a.parentNode,h=g.parentNode;return"DD"==a.nodeName?(v.rename(a,"DT"),!0):c(a)&&d(a)?("LI"==h.nodeName?(v.insertAfter(a,h),e(h),v.remove(g)):b(h)?v.remove(g,!0):(h.insertBefore(i(a),g),v.remove(g)),!0):c(a)?("LI"==h.nodeName?(v.insertAfter(a,h),a.appendChild(g),e(h)):b(h)?h.insertBefore(a,g):(h.insertBefore(i(a),g),v.remove(a)),!0):d(a)?("LI"==h.nodeName?v.insertAfter(a,h):b(h)?v.insertAfter(a,g):(v.insertAfter(i(a),g),v.remove(a)),!0):("LI"==h.nodeName?(g=h,f=i(a,"LI")):f=b(h)?i(a,"LI"):i(a),k(g,a,f),m(g.parentNode),!0)}function o(a){function c(c,d){var e;if(b(c)){for(;e=a.lastChild.firstChild;)d.appendChild(e);v.remove(c)}}var d,e;return"DT"==a.nodeName?(v.rename(a,"DD"),!0):(d=a.previousSibling,d&&b(d)?(d.appendChild(a),!0):d&&"LI"==d.nodeName&&b(d.lastChild)?(d.lastChild.appendChild(a),c(a.lastChild,d.lastChild),!0):(d=a.nextSibling,d&&b(d)?(d.insertBefore(a,d.firstChild),!0):d&&"LI"==d.nodeName&&b(a.lastChild)?!1:(d=a.previousSibling,d&&"LI"==d.nodeName?(e=v.create(a.parentNode.nodeName),d.appendChild(e),e.appendChild(a),c(a.lastChild,e),!0):!1)))}function p(){var b=j();if(b.length){for(var c=g(w.getRng(!0)),d=0;d<b.length&&(o(b[d])||0!==d);d++);return h(c),a.nodeChanged(),!0}}function q(){var b=j();if(b.length){var c,d,e=g(w.getRng(!0)),f=a.getBody();for(c=b.length;c--;)for(var i=b[c].parentNode;i&&i!=f;){for(d=b.length;d--;)if(b[d]===i){b.splice(c,1);break}i=i.parentNode}for(c=0;c<b.length&&(n(b[c])||0!==c);c++);return h(e),a.nodeChanged(),!0}}function r(c){function d(){function b(a){var b,c;for(b=f[a?"startContainer":"endContainer"],c=f[a?"startOffset":"endOffset"],1==b.nodeType&&(b=b.childNodes[Math.min(c,b.childNodes.length-1)]||b);b.parentNode!=g;){if(e(b))return b;if(/^(TD|TH)$/.test(b.parentNode.nodeName))return b;b=b.parentNode}return b}for(var c,d=[],g=a.getBody(),h=b(!0),i=b(),j=[],k=h;k&&(j.push(k),k!=i);k=k.nextSibling);return tinymce.each(j,function(a){if(e(a))return d.push(a),void(c=null);if(v.isBlock(a)||"BR"==a.nodeName)return"BR"==a.nodeName&&v.remove(a),void(c=null);var b=a.nextSibling;return tinymce.dom.BookmarkManager.isBookmarkNode(a)&&(e(b)||!b&&a.parentNode==g)?void(c=null):(c||(c=v.create("p"),a.parentNode.insertBefore(c,a),d.push(c)),void c.appendChild(a))}),d}var f=w.getRng(!0),i=g(f),j="LI";c=c.toUpperCase(),"DL"==c&&(j="DT"),tinymce.each(d(),function(a){var d,e;e=a.previousSibling,e&&b(e)&&e.nodeName==c?(d=e,a=v.rename(a,j),e.appendChild(a)):(d=v.create(c),a.parentNode.insertBefore(d,a),d.appendChild(a),a=v.rename(a,j)),l(d)}),h(i)}function s(){var c=g(w.getRng(!0)),d=a.getBody();tinymce.each(j(),function(a){var c,e;if(v.isEmpty(a))return void n(a);for(c=a;c&&c!=d;c=c.parentNode)b(c)&&(e=c);k(e,a)}),h(c)}function t(a){var b=v.getParent(w.getStart(),"OL,UL,DL");if(b)if(b.nodeName==a)s(a);else{var c=g(w.getRng(!0));l(v.rename(b,a)),h(c)}else r(a)}function u(b){return function(){var c=v.getParent(a.selection.getStart(),"UL,OL,DL");return c&&c.nodeName==b}}var v=a.dom,w=a.selection;f.backspaceDelete=function(c){function d(b,c){var d,e,f=b.startContainer,g=b.startOffset;if(3==f.nodeType&&(c?g<f.data.length:g>0))return f;for(d=a.schema.getNonEmptyElements(),e=new tinymce.dom.TreeWalker(b.startContainer);f=e[c?"next":"prev"]();){if("LI"==f.nodeName&&!f.hasChildNodes())return f;if(d[f.nodeName])return f;if(3==f.nodeType&&f.data.length>0)return f}}function e(a,c){var d,e,f=a.parentNode;if(b(c.lastChild)&&(e=c.lastChild),d=c.lastChild,d&&"BR"==d.nodeName&&a.hasChildNodes()&&v.remove(d),v.isEmpty(c)&&v.$(c).empty(),!v.isEmpty(a))for(;d=a.firstChild;)c.appendChild(d);e&&c.appendChild(e),v.remove(a),v.isEmpty(f)&&v.remove(f)}if(w.isCollapsed()){var f=v.getParent(w.getStart(),"LI");if(f){var i=w.getRng(!0),j=v.getParent(d(i,c),"LI");if(j&&j!=f){var k=g(i);return c?e(j,f):e(f,j),h(k),!0}if(!j&&!c&&s(f.parentNode.nodeName))return!0}}},a.addCommand("Indent",function(){return p()?void 0:!0}),a.addCommand("Outdent",function(){return q()?void 0:!0}),a.addCommand("InsertUnorderedList",function(){t("UL")}),a.addCommand("InsertOrderedList",function(){t("OL")}),a.addCommand("InsertDefinitionList",function(){t("DL")}),a.addQueryStateHandler("InsertUnorderedList",u("UL")),a.addQueryStateHandler("InsertOrderedList",u("OL")),a.addQueryStateHandler("InsertDefinitionList",u("DL")),a.on("keydown",function(b){9!=b.keyCode||tinymce.util.VK.metaKeyPressed(b)||a.dom.getParent(a.selection.getStart(),"LI,DT,DD")&&(b.preventDefault(),b.shiftKey?q():p())})}),a.addButton("indent",{icon:"indent",title:"Increase indent",cmd:"Indent",onPostRender:function(){var b=this;a.on("nodechange",function(){for(var d=a.selection.getSelectedBlocks(),e=!1,f=0,g=d.length;!e&&g>f;f++){var h=d[f].nodeName;e="LI"==h&&c(d[f])||"UL"==h||"OL"==h||"DD"==h}b.disabled(e)})}}),a.on("keydown",function(a){a.keyCode==tinymce.util.VK.BACKSPACE?f.backspaceDelete()&&a.preventDefault():a.keyCode==tinymce.util.VK.DELETE&&f.backspaceDelete(!0)&&a.preventDefault()})});