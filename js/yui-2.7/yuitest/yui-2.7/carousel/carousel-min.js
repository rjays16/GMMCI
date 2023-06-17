/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.7.0
*/
(function(){var M;YAHOO.widget.Carousel=function(n,m){YAHOO.widget.Carousel.superclass.constructor.call(this,n,m);};var Q=YAHOO.widget.Carousel,a=YAHOO.util.Dom,Y=YAHOO.util.Event,k=YAHOO.lang;M="Carousel";var P={},F="afterScroll",b="allItemsRemoved",X="beforeHide",I="beforePageChange",e="beforeScroll",U="beforeShow",B="blur",T="focus",W="hide",O="itemAdded",j="itemRemoved",C="itemSelected",K="loadItems",H="navigationStateChange",c="pageChange",G="render",R="show",V="startAutoPlay",l="stopAutoPlay",J="uiUpdate";function S(n,m){var o=document.createElement(n);m=m||{};if(m.className){a.addClass(o,m.className);}if(m.parent){m.parent.appendChild(o);}if(m.id){o.setAttribute("id",m.id);}if(m.content){if(m.content.nodeName){o.appendChild(m.content);}else{o.innerHTML=m.content;}}return o;}function Z(o,n,m){var q;if(!o){return 0;}function p(t,s){var u;if(s=="marginRight"&&YAHOO.env.ua.webkit){u=parseInt(a.getStyle(t,"marginLeft"),10);}else{u=parseInt(a.getStyle(t,s),10);}return k.isNumber(u)?u:0;}function r(t,s){var u;if(s=="marginRight"&&YAHOO.env.ua.webkit){u=parseFloat(a.getStyle(t,"marginLeft"));}else{u=parseFloat(a.getStyle(t,s));}return k.isNumber(u)?u:0;}if(typeof m=="undefined"){m="int";}switch(n){case"height":q=o.offsetHeight;if(q>0){q+=p(o,"marginTop")+p(o,"marginBottom");}else{q=r(o,"height")+p(o,"marginTop")+p(o,"marginBottom")+p(o,"borderTopWidth")+p(o,"borderBottomWidth")+p(o,"paddingTop")+p(o,"paddingBottom");}break;case"width":q=o.offsetWidth;if(q>0){q+=p(o,"marginLeft")+p(o,"marginRight");}else{q=r(o,"width")+p(o,"marginLeft")+p(o,"marginRight")+p(o,"borderLeftWidth")+p(o,"borderRightWidth")+p(o,"paddingLeft")+p(o,"paddingRight");}break;default:if(m=="int"){q=p(o,n);}else{if(m=="float"){q=r(o,n);}else{q=a.getStyle(o,n);}}break;}return q;}function L(p){var o=this,q,n=0,m=false;if(o._itemsTable.numItems===0){return 0;}if(typeof p=="undefined"){if(o._itemsTable.size>0){return o._itemsTable.size;}}if(k.isUndefined(o._itemsTable.items[0])){return 0;}q=a.get(o._itemsTable.items[0].id);if(typeof p=="undefined"){m=o.get("isVertical");}else{m=p=="height";}if(m){n=Z(q,"height");}else{n=Z(q,"width");}if(typeof p=="undefined"){o._itemsTable.size=n;}return n;}function D(n){var m=this.get("numVisible");return Math.floor(n/m)*m;}function f(o){var n=0,m=0;n=L.call(this);m=n*o;if(this.get("isVertical")){m-=o;}return m;}function d(m,n){n.scrollPageBackward();Y.preventDefault(m);}function g(m,n){n.scrollPageForward();Y.preventDefault(m);}function i(r,n){var v=this,w=v.CLASSES,m,t=v._firstItem,o=v.get("isCircular"),s=v.get("numItems"),u=v.get("numVisible"),q=n,p=t+u-1;if(q>=0&&q<s){if(!k.isUndefined(v._itemsTable.items[q])){m=a.get(v._itemsTable.items[q].id);if(m){a.removeClass(m,w.SELECTED_ITEM);}}}if(k.isNumber(r)){r=parseInt(r,10);r=k.isNumber(r)?r:0;}else{r=t;}if(k.isUndefined(v._itemsTable.items[r])){r=D.call(v,r);v.scrollTo(r);}if(!k.isUndefined(v._itemsTable.items[r])){m=a.get(v._itemsTable.items[r].id);if(m){a.addClass(m,w.SELECTED_ITEM);}}if(r<t||r>p){r=D.call(v,r);v.scrollTo(r);}}function h(){var o=false,r=this,n=r.CLASSES,q,m,p;if(!r._hasRendered){return;}m=r.get("navigation");p=r._firstItem+r.get("numVisible");if(m.prev){if(r.get("numItems")===0||r._firstItem===0){if(r.get("numItems")===0||!r.get("isCircular")){Y.removeListener(m.prev,"click",d);a.addClass(m.prev,n.FIRST_NAV_DISABLED);for(q=0;q<r._navBtns.prev.length;q++){r._navBtns.prev[q].setAttribute("disabled","true");}r._prevEnabled=false;}else{o=!r._prevEnabled;}}else{o=!r._prevEnabled;}if(o){Y.on(m.prev,"click",d,r);a.removeClass(m.prev,n.FIRST_NAV_DISABLED);for(q=0;q<r._navBtns.prev.length;q++){r._navBtns.prev[q].removeAttribute("disabled");}r._prevEnabled=true;}}o=false;if(m.next){if(p>=r.get("numItems")){if(!r.get("isCircular")){Y.removeListener(m.next,"click",g);a.addClass(m.next,n.DISABLED);for(q=0;q<r._navBtns.next.length;q++){r._navBtns.next[q].setAttribute("disabled","true");}r._nextEnabled=false;}else{o=!r._nextEnabled;}}else{o=!r._nextEnabled;}if(o){Y.on(m.next,"click",g,r);a.removeClass(m.next,n.DISABLED);for(q=0;q<r._navBtns.next.length;q++){r._navBtns.next[q].removeAttribute("disabled");}r._nextEnabled=true;}}r.fireEvent(H,{next:r._nextEnabled,prev:r._prevEnabled});}function N(o){var p=this,m,n;if(!p._hasRendered){return;}n=p.get("numVisible");if(!k.isNumber(o)){o=Math.ceil(p.get("selectedItem")/n);}m=Math.ceil(p.get("numItems")/n);p._pages.num=m;p._pages.cur=o;if(m>p.CONFIG.MAX_PAGER_BUTTONS){p._updatePagerMenu();}else{p._updatePagerButtons();}}function A(n){var m=this;if(!k.isObject(n)){return;}switch(n.ev){case O:m._syncUiForItemAdd(n);break;case j:m._syncUiForItemRemove(n);break;case K:m._syncUiForLazyLoading(n);break;}m.fireEvent(J);}function E(p,n){var r=this,q=r.get("currentPage"),o,m=r.get("numVisible");o=parseInt(r._firstItem/m,10);if(o!=q){r.setAttributeConfig("currentPage",{value:o});r.fireEvent(c,o);}if(r.get("selectOnScroll")){if(r.get("selectedItem")!=r._selectedItem){r.set("selectedItem",r._selectedItem);}}clearTimeout(r._autoPlayTimer);delete r._autoPlayTimer;if(r.isAutoPlayOn()){r.startAutoPlay();}r.fireEvent(F,{first:r._firstItem,last:n},r);}Q.getById=function(m){return P[m]?P[m].object:false;};YAHOO.extend(Q,YAHOO.util.Element,{_animObj:null,_carouselEl:null,_clipEl:null,_firstItem:0,_hasFocus:false,_hasRendered:false,_isAnimationInProgress:false,_isAutoPlayInProgress:false,_itemsTable:null,_navBtns:null,_navEl:null,_nextEnabled:true,_pages:null,_prevEnabled:true,_recomputeSize:true,CLASSES:{BUTTON:"yui-carousel-button",CAROUSEL:"yui-carousel",CAROUSEL_EL:"yui-carousel-element",CONTAINER:"yui-carousel-container",CONTENT:"yui-carousel-content",DISABLED:"yui-carousel-button-disabled",FIRST_NAV:" yui-carousel-first-button",FIRST_NAV_DISABLED:"yui-carousel-first-button-disabled",FIRST_PAGE:"yui-carousel-nav-first-page",FOCUSSED_BUTTON:"yui-carousel-button-focus",HORIZONTAL:"yui-carousel-horizontal",ITEM_LOADING:"yui-carousel-item-loading",MIN_WIDTH:"yui-carousel-min-width",NAVIGATION:"yui-carousel-nav",NEXT_NAV:" yui-carousel-next-button",NEXT_PAGE:"yui-carousel-next",NAV_CONTAINER:"yui-carousel-buttons",PAGE_FOCUS:"yui-carousel-nav-page-focus",PREV_PAGE:"yui-carousel-prev",SELECTED_ITEM:"yui-carousel-item-selected",SELECTED_NAV:"yui-carousel-nav-page-selected",VERTICAL:"yui-carousel-vertical",VERTICAL_CONTAINER:"yui-carousel-vertical-container",VISIBLE:"yui-carousel-visible"},CONFIG:{FIRST_VISIBLE:0,HORZ_MIN_WIDTH:180,MAX_PAGER_BUTTONS:5,VERT_MIN_WIDTH:99,NUM_VISIBLE:3},STRINGS:{ITEM_LOADING_CONTENT:"Loading",NEXT_BUTTON_TEXT:"Next Page",PAGER_PREFIX_TEXT:"Go to page ",PREVIOUS_BUTTON_TEXT:"Previous Page"},addItem:function(r,n){var s=this,p,q,m,o=s.get("numItems");
if(!r){return false;}if(k.isString(r)||r.nodeName){q=r.nodeName?r.innerHTML:r;}else{if(k.isObject(r)){q=r.content;}else{return false;}}p=r.className||"";m=r.id?r.id:a.generateId();if(k.isUndefined(n)){s._itemsTable.items.push({item:q,className:p,id:m});}else{if(n<0||n>=o){return false;}s._itemsTable.items.splice(n,0,{item:q,className:p,id:m});}s._itemsTable.numItems++;if(o<s._itemsTable.items.length){s.set("numItems",s._itemsTable.items.length);}s.fireEvent(O,{pos:n,ev:O});return true;},addItems:function(m){var o,q,p=true;if(!k.isArray(m)){return false;}for(o=0,q=m.length;o<q;o++){if(this.addItem(m[o][0],m[o][1])===false){p=false;}}return p;},blur:function(){this._carouselEl.blur();this.fireEvent(B);},clearItems:function(){var m=this,o=m.get("numItems");while(o>0){if(!m.removeItem(0)){}if(m._itemsTable.numItems===0){m.set("numItems",0);break;}o--;}m.fireEvent(b);},focus:function(){var v=this,q,r,s,p,u,w,n,o,m;if(!v._hasRendered){return;}if(v.isAnimating()){return;}m=v.get("selectedItem");w=v.get("numVisible");n=v.get("selectOnScroll");o=(m>=0)?v.getItem(m):null;q=v.get("firstVisible");u=q+w-1;s=(m<q||m>u);r=(o&&o.id)?a.get(o.id):null;p=v._itemsTable;if(!n&&s){r=(p&&p.items&&p.items[q])?a.get(p.items[q].id):null;}if(r){try{r.focus();}catch(t){}}v.fireEvent(T);},hide:function(){var m=this;if(m.fireEvent(X)!==false){m.removeClass(m.CLASSES.VISIBLE);m.fireEvent(W);}},init:function(o,n){var p=this,m=o,q=false;if(!o){return;}p._hasRendered=false;p._navBtns={prev:[],next:[]};p._pages={el:null,num:0,cur:0};p._itemsTable={loading:{},numItems:0,items:[],size:0};if(k.isString(o)){o=a.get(o);}else{if(!o.nodeName){return;}}Q.superclass.init.call(p,o,n);if(o){if(!o.id){o.setAttribute("id",a.generateId());}q=p._parseCarousel(o);if(!q){p._createCarousel(m);}}else{o=p._createCarousel(m);}m=o.id;p.initEvents();if(q){p._parseCarouselItems();}if(!n||typeof n.isVertical=="undefined"){p.set("isVertical",false);}p._parseCarouselNavigation(o);p._navEl=p._setupCarouselNavigation();P[m]={object:p};p._loadItems();},initAttributes:function(m){var n=this;m=m||{};Q.superclass.initAttributes.call(n,m);n.setAttributeConfig("carouselEl",{validator:k.isString,value:m.carouselEl||"OL"});n.setAttributeConfig("carouselItemEl",{validator:k.isString,value:m.carouselItemEl||"LI"});n.setAttributeConfig("currentPage",{readOnly:true,value:0});n.setAttributeConfig("firstVisible",{method:n._setFirstVisible,validator:n._validateFirstVisible,value:m.firstVisible||n.CONFIG.FIRST_VISIBLE});n.setAttributeConfig("selectOnScroll",{validator:k.isBoolean,value:m.selectOnScroll||true});n.setAttributeConfig("numVisible",{method:n._setNumVisible,validator:n._validateNumVisible,value:m.numVisible||n.CONFIG.NUM_VISIBLE});n.setAttributeConfig("numItems",{method:n._setNumItems,validator:n._validateNumItems,value:n._itemsTable.numItems});n.setAttributeConfig("scrollIncrement",{validator:n._validateScrollIncrement,value:m.scrollIncrement||1});n.setAttributeConfig("selectedItem",{method:n._setSelectedItem,validator:k.isNumber,value:-1});n.setAttributeConfig("revealAmount",{method:n._setRevealAmount,validator:n._validateRevealAmount,value:m.revealAmount||0});n.setAttributeConfig("isCircular",{validator:k.isBoolean,value:m.isCircular||false});n.setAttributeConfig("isVertical",{method:n._setOrientation,validator:k.isBoolean,value:m.isVertical||false});n.setAttributeConfig("navigation",{method:n._setNavigation,validator:n._validateNavigation,value:m.navigation||{prev:null,next:null,page:null}});n.setAttributeConfig("animation",{validator:n._validateAnimation,value:m.animation||{speed:0,effect:null}});n.setAttributeConfig("autoPlay",{validator:k.isNumber,value:m.autoPlay||0});n.setAttributeConfig("autoPlayInterval",{validator:k.isNumber,value:m.autoPlayInterval||0});},initEvents:function(){var o=this,n=o.CLASSES,m;o.on("keydown",o._keyboardEventHandler);o.on(F,h);o.on(O,A);o.on(j,A);o.on(C,function(){if(o._hasFocus){o.focus();}});o.on(K,A);o.on(b,function(p){o.scrollTo(0);h.call(o);N.call(o);});o.on(c,N,o);o.on(G,function(p){o.set("selectedItem",o.get("firstVisible"));h.call(o,p);N.call(o,p);o._setClipContainerSize();});o.on("selectedItemChange",function(p){i.call(o,p.newValue,p.prevValue);if(p.newValue>=0){o._updateTabIndex(o.getElementForItem(p.newValue));}o.fireEvent(C,p.newValue);});o.on(J,function(p){h.call(o,p);N.call(o,p);});o.on("firstVisibleChange",function(p){if(!o.get("selectOnScroll")){if(p.newValue>=0){o._updateTabIndex(o.getElementForItem(p.newValue));}}});o.on("click",function(p){if(o.isAutoPlayOn()){o.stopAutoPlay();}o._itemClickHandler(p);o._pagerClickHandler(p);});Y.onFocus(o.get("element"),function(p,r){var q=Y.getTarget(p);if(q&&q.nodeName.toUpperCase()=="A"&&a.getAncestorByClassName(q,n.NAVIGATION)){if(m){a.removeClass(m,n.PAGE_FOCUS);}m=q.parentNode;a.addClass(m,n.PAGE_FOCUS);}else{if(m){a.removeClass(m,n.PAGE_FOCUS);}}r._hasFocus=true;r._updateNavButtons(Y.getTarget(p),true);},o);Y.onBlur(o.get("element"),function(p,q){q._hasFocus=false;q._updateNavButtons(Y.getTarget(p),false);},o);},isAnimating:function(){return this._isAnimationInProgress;},isAutoPlayOn:function(){return this._isAutoPlayInProgress;},getElementForItem:function(m){var n=this;if(m<0||m>=n.get("numItems")){return null;}if(n._itemsTable.numItems>m){if(!k.isUndefined(n._itemsTable.items[m])){return a.get(n._itemsTable.items[m].id);}}return null;},getElementForItems:function(){var o=this,n=[],m;for(m=0;m<o._itemsTable.numItems;m++){n.push(o.getElementForItem(m));}return n;},getItem:function(m){var n=this;if(m<0||m>=n.get("numItems")){return null;}if(n._itemsTable.numItems>m){if(!k.isUndefined(n._itemsTable.items[m])){return n._itemsTable.items[m];}}return null;},getItems:function(m){return this._itemsTable.items;},getItemPositionById:function(q){var o=this,m=0,p=o._itemsTable.numItems;while(m<p){if(!k.isUndefined(o._itemsTable.items[m])){if(o._itemsTable.items[m].id==q){return m;}}m++;}return -1;},getVisibleItems:function(){var p=this,m=p.get("firstVisible"),q=m+p.get("numVisible"),o=[];
while(m<q){o.push(p.getElementForItem(m));m++;}return o;},removeItem:function(n){var p=this,o,m=p.get("numItems");if(n<0||n>=m){return false;}o=p._itemsTable.items.splice(n,1);if(o&&o.length==1){p._itemsTable.numItems--;p.set("numItems",m-1);p.fireEvent(j,{item:o[0],pos:n,ev:j});return true;}return false;},render:function(n){var o=this,m=o.CLASSES;o.addClass(m.CAROUSEL);if(!o._clipEl){o._clipEl=o._createCarouselClip();o._clipEl.appendChild(o._carouselEl);}if(n){o.appendChild(o._clipEl);o.appendTo(n);}else{if(!a.inDocument(o.get("element"))){return false;}o.appendChild(o._clipEl);}if(o.get("isVertical")){o.addClass(m.VERTICAL);}else{o.addClass(m.HORIZONTAL);}if(o.get("numItems")<1){return false;}o._refreshUi();return true;},scrollBackward:function(){var m=this;m.scrollTo(m._firstItem-m.get("scrollIncrement"));},scrollForward:function(){var m=this;m.scrollTo(m._firstItem+m.get("scrollIncrement"));},scrollPageBackward:function(){var n=this,m=n._firstItem-n.get("numVisible");if(n.get("selectOnScroll")){n._selectedItem=n._getSelectedItem(m);}else{m=n._getValidIndex(m);}n.scrollTo(m);},scrollPageForward:function(){var n=this,m=n._firstItem+n.get("numVisible");if(n.get("selectOnScroll")){n._selectedItem=n._getSelectedItem(m);}else{m=n._getValidIndex(m);}n.scrollTo(m);},scrollTo:function(AB,n){var AA=this,m,r,p,z,x,w,u,v,q,t,o,s,y;if(k.isUndefined(AB)||AB==AA._firstItem||AA.isAnimating()){return;}r=AA.get("animation");p=AA.get("isCircular");w=AA._firstItem;u=AA.get("numItems");v=AA.get("numVisible");t=AA.get("currentPage");y=function(){if(AA.isAutoPlayOn()){AA.stopAutoPlay();}};if(AB<0){if(p){AB=u+AB;}else{y.call(AA);return;}}else{if(u>0&&AB>u-1){if(AA.get("isCircular")){AB=u-AB;}else{y.call(AA);return;}}}x=(AA._firstItem>AB)?"backward":"forward";s=w+v;s=(s>u-1)?u-1:s;o=AA.fireEvent(e,{dir:x,first:w,last:s});if(o===false){return;}AA.fireEvent(I,{page:t});z=w-AB;AA._firstItem=AB;AA.set("firstVisible",AB);AA._loadItems();s=AB+v;s=(s>u-1)?u-1:s;q=f.call(AA,z);m=r.speed>0;if(m){AA._animateAndSetCarouselOffset(q,AB,s,n);}else{AA._setCarouselOffset(q);E.call(AA,AB,s);}},selectPreviousItem:function(){var o=this,n=0,m=o.get("selectedItem");if(m==this._firstItem){n=m-o.get("numVisible");o._selectedItem=o._getSelectedItem(m-1);o.scrollTo(n);}else{n=o.get("selectedItem")-o.get("scrollIncrement");o.set("selectedItem",o._getSelectedItem(n));}},selectNextItem:function(){var n=this,m=0;m=n.get("selectedItem")+n.get("scrollIncrement");n.set("selectedItem",n._getSelectedItem(m));},show:function(){var n=this,m=n.CLASSES;if(n.fireEvent(U)!==false){n.addClass(m.VISIBLE);n.fireEvent(R);}},startAutoPlay:function(){var m=this,n;if(k.isUndefined(m._autoPlayTimer)){n=m.get("autoPlayInterval");if(n<=0){return;}m._isAutoPlayInProgress=true;m.fireEvent(V);m._autoPlayTimer=setTimeout(function(){m._autoScroll();},n);}},stopAutoPlay:function(){var m=this;if(!k.isUndefined(m._autoPlayTimer)){clearTimeout(m._autoPlayTimer);delete m._autoPlayTimer;m._isAutoPlayInProgress=false;m.fireEvent(l);}},toString:function(){return M+(this.get?" (#"+this.get("id")+")":"");},_animateAndSetCarouselOffset:function(r,p,n){var q=this,o=q.get("animation"),m=null;if(q.get("isVertical")){m=new YAHOO.util.Motion(q._carouselEl,{points:{by:[0,r]}},o.speed,o.effect);}else{m=new YAHOO.util.Motion(q._carouselEl,{points:{by:[r,0]}},o.speed,o.effect);}q._isAnimationInProgress=true;m.onComplete.subscribe(q._animationCompleteHandler,{scope:q,item:p,last:n});m.animate();},_animationCompleteHandler:function(m,n,q){q.scope._isAnimationInProgress=false;E.call(q.scope,q.item,q.last);},_autoScroll:function(){var n=this,o=n._firstItem,m;if(o>=n.get("numItems")-1){if(n.get("isCircular")){m=0;}else{n.stopAutoPlay();}}else{m=o+n.get("numVisible");}n._selectedItem=n._getSelectedItem(m);n.scrollTo.call(n,m);},_createCarousel:function(n){var p=this,m=p.CLASSES,o=a.get(n);if(!o){o=S("DIV",{className:m.CAROUSEL,id:n});}if(!p._carouselEl){p._carouselEl=S(p.get("carouselEl"),{className:m.CAROUSEL_EL});}return o;},_createCarouselClip:function(){return S("DIV",{className:this.CLASSES.CONTENT});},_createCarouselItem:function(m){return S(this.get("carouselItemEl"),{className:m.className,content:m.content,id:m.id});},_getValidIndex:function(o){var q=this,m=q.get("isCircular"),p=q.get("numItems"),n=p-1;if(o<0){o=m?p+o:0;}else{if(o>n){o=m?o-p:n;}}return o;},_getSelectedItem:function(q){var p=this,m=p.get("isCircular"),o=p.get("numItems"),n=o-1;if(q<0){if(m){q=o+q;}else{q=p.get("selectedItem");}}else{if(q>n){if(m){q=q-o;}else{q=p.get("selectedItem");}}}return q;},_itemClickHandler:function(p){var r=this,m=r.get("element"),n,o,q=YAHOO.util.Event.getTarget(p);while(q&&q!=m&&q.id!=r._carouselEl){n=q.nodeName;if(n.toUpperCase()==r.get("carouselItemEl")){break;}q=q.parentNode;}if((o=r.getItemPositionById(q.id))>=0){r.set("selectedItem",r._getSelectedItem(o));r.focus();}},_keyboardEventHandler:function(o){var p=this,n=Y.getCharCode(o),m=false;if(p.isAnimating()){return;}switch(n){case 37:case 38:p.selectPreviousItem();m=true;break;case 39:case 40:p.selectNextItem();m=true;break;case 33:p.scrollPageBackward();m=true;break;case 34:p.scrollPageForward();m=true;break;}if(m){if(p.isAutoPlayOn()){p.stopAutoPlay();}Y.preventDefault(o);}},_loadItems:function(){var q=this,r=q.get("firstVisible"),n=0,m=q.get("numItems"),o=q.get("numVisible"),p=q.get("revealAmount");n=r+o-1+(p?1:0);n=n>m-1?m-1:n;if(!q.getItem(r)||!q.getItem(n)){q.fireEvent(K,{ev:K,first:r,last:n,num:n-r});}},_pagerClickHandler:function(n){var p=this,r,o=Y.getTarget(n),q;function m(t){var s=p.get("carouselItemEl");if(t.nodeName.toUpperCase()==s.toUpperCase()){t=a.getChildrenBy(t,function(u){return u.href||u.value;});if(t&&t[0]){return t[0];}}else{if(t.href||t.value){return t;}}return null;}if(o){o=m(o);if(!o){return;}q=o.href||o.value;if(k.isString(q)&&q){r=q.lastIndexOf("#");if(r!=-1){q=p.getItemPositionById(q.substring(r+1));p._selectedItem=q;p.scrollTo(q);if(!o.value){p.focus();}Y.preventDefault(n);}}}},_parseCarousel:function(o){var r=this,s,m,n,q,p;
m=r.CLASSES;n=r.get("carouselEl");q=false;for(s=o.firstChild;s;s=s.nextSibling){if(s.nodeType==1){p=s.nodeName;if(p.toUpperCase()==n){r._carouselEl=s;a.addClass(r._carouselEl,r.CLASSES.CAROUSEL_EL);q=true;}}}return q;},_parseCarouselItems:function(){var q=this,r,m,n,p,o=q._carouselEl;m=q.get("carouselItemEl");for(r=o.firstChild;r;r=r.nextSibling){if(r.nodeType==1){p=r.nodeName;if(p.toUpperCase()==m){if(r.id){n=r.id;}else{n=a.generateId();r.setAttribute("id",n);}q.addItem(r);}}}},_parseCarouselNavigation:function(s){var t=this,r,u=t.CLASSES,n,q,p,m,o=false;m=a.getElementsByClassName(u.PREV_PAGE,"*",s);if(m.length>0){for(q in m){if(m.hasOwnProperty(q)){n=m[q];if(n.nodeName=="INPUT"||n.nodeName=="BUTTON"){t._navBtns.prev.push(n);}else{p=n.getElementsByTagName("INPUT");if(k.isArray(p)&&p.length>0){t._navBtns.prev.push(p[0]);}else{p=n.getElementsByTagName("BUTTON");if(k.isArray(p)&&p.length>0){t._navBtns.prev.push(p[0]);}}}}}r={prev:m};}m=a.getElementsByClassName(u.NEXT_PAGE,"*",s);if(m.length>0){for(q in m){if(m.hasOwnProperty(q)){n=m[q];if(n.nodeName=="INPUT"||n.nodeName=="BUTTON"){t._navBtns.next.push(n);}else{p=n.getElementsByTagName("INPUT");if(k.isArray(p)&&p.length>0){t._navBtns.next.push(p[0]);}else{p=n.getElementsByTagName("BUTTON");if(k.isArray(p)&&p.length>0){t._navBtns.next.push(p[0]);}}}}}if(r){r.next=m;}else{r={next:m};}}if(r){t.set("navigation",r);o=true;}return o;},_refreshUi:function(){var m=this;m._hasRendered=true;m.fireEvent(G);},_setCarouselOffset:function(o){var m=this,n;n=m.get("isVertical")?"top":"left";o+=o!==0?Z(m._carouselEl,n):0;a.setStyle(m._carouselEl,n,o+"px");},_setupCarouselNavigation:function(){var r=this,p,n,m,t,q,s,o;m=r.CLASSES;q=a.getElementsByClassName(m.NAVIGATION,"DIV",r.get("element"));if(q.length===0){q=S("DIV",{className:m.NAVIGATION});r.insertBefore(q,a.getFirstChild(r.get("element")));}else{q=q[0];}r._pages.el=S("UL");q.appendChild(r._pages.el);t=r.get("navigation");if(k.isString(t.prev)||k.isArray(t.prev)){if(k.isString(t.prev)){t.prev=[t.prev];}for(p in t.prev){if(t.prev.hasOwnProperty(p)){r._navBtns.prev.push(a.get(t.prev[p]));}}}else{o=S("SPAN",{className:m.BUTTON+m.FIRST_NAV});a.setStyle(o,"visibility","visible");p=a.generateId();o.innerHTML='<button type="button" '+'id="'+p+'" name="'+r.STRINGS.PREVIOUS_BUTTON_TEXT+'">'+r.STRINGS.PREVIOUS_BUTTON_TEXT+"</button>";q.appendChild(o);p=a.get(p);r._navBtns.prev=[p];n={prev:[o]};}if(k.isString(t.next)||k.isArray(t.next)){if(k.isString(t.next)){t.next=[t.next];}for(p in t.next){if(t.next.hasOwnProperty(p)){r._navBtns.next.push(a.get(t.next[p]));}}}else{s=S("SPAN",{className:m.BUTTON+m.NEXT_NAV});a.setStyle(s,"visibility","visible");p=a.generateId();s.innerHTML='<button type="button" '+'id="'+p+'" name="'+r.STRINGS.NEXT_BUTTON_TEXT+'">'+r.STRINGS.NEXT_BUTTON_TEXT+"</button>";q.appendChild(s);p=a.get(p);r._navBtns.next=[p];if(n){n.next=[s];}else{n={next:[s]};}}if(n){r.set("navigation",n);}return q;},_setClipContainerSize:function(n,p){var u=this,q,m,r,s,t,v,o;r=u.get("isVertical");t=u.get("revealAmount");o=r?"height":"width";q=r?"top":"left";n=n||u._clipEl;if(!n){return;}p=p||u.get("numVisible");s=L.call(u,o);v=s*p;u._recomputeSize=(v===0);if(u._recomputeSize){u._hasRendered=false;return;}if(t>0){t=s*(t/100)*2;v+=t;m=parseFloat(a.getStyle(u._carouselEl,q));m=k.isNumber(m)?m:0;a.setStyle(u._carouselEl,q,m+(t/2)+"px");}if(r){v+=Z(u._carouselEl,"marginTop")+Z(u._carouselEl,"marginBottom")+Z(u._carouselEl,"paddingTop")+Z(u._carouselEl,"paddingBottom")+Z(u._carouselEl,"borderTopWidth")+Z(u._carouselEl,"borderBottomWidth");a.setStyle(n,o,(v-(p-1))+"px");}else{v+=Z(u._carouselEl,"marginLeft")+Z(u._carouselEl,"marginRight")+Z(u._carouselEl,"paddingLeft")+Z(u._carouselEl,"paddingRight")+Z(u._carouselEl,"borderLeftWidth")+Z(u._carouselEl,"borderRightWidth");a.setStyle(n,o,v+"px");}u._setContainerSize(n);},_setContainerSize:function(q,m){var r=this,o=r.CONFIG,n=r.CLASSES,s,p;s=r.get("isVertical");q=q||r._clipEl;m=m||(s?"height":"width");p=parseFloat(a.getStyle(q,m),10);p=k.isNumber(p)?p:0;if(s){p+=Z(r._carouselEl,"marginTop")+Z(r._carouselEl,"marginBottom")+Z(r._carouselEl,"paddingTop")+Z(r._carouselEl,"paddingBottom")+Z(r._carouselEl,"borderTopWidth")+Z(r._carouselEl,"borderBottomWidth")+Z(r._navEl,"height");}else{p+=Z(q,"marginLeft")+Z(q,"marginRight")+Z(q,"paddingLeft")+Z(q,"paddingRight")+Z(q,"borderLeftWidth")+Z(q,"borderRightWidth");}if(!s){if(p<o.HORZ_MIN_WIDTH){p=o.HORZ_MIN_WIDTH;r.addClass(n.MIN_WIDTH);}}r.setStyle(m,p+"px");if(s){p=L.call(r,"width");if(p<o.VERT_MIN_WIDTH){p=o.VERT_MIN_WIDTH;r.addClass(n.MIN_WIDTH);}r.setStyle("width",p+"px");}},_setFirstVisible:function(n){var m=this;if(n>=0&&n<m.get("numItems")){m.scrollTo(n);}else{n=m.get("firstVisible");}return n;},_setNavigation:function(m){var n=this;if(m.prev){Y.on(m.prev,"click",d,n);}if(m.next){Y.on(m.next,"click",g,n);}},_setNumVisible:function(n){var m=this;m._setClipContainerSize(m._clipEl,n);},_setNumItems:function(o){var n=this,m=n._itemsTable.numItems;if(k.isArray(n._itemsTable.items)){if(n._itemsTable.items.length!=m){m=n._itemsTable.items.length;n._itemsTable.numItems=m;}}if(o<m){while(m>o){n.removeItem(m-1);m--;}}return o;},_setOrientation:function(o){var n=this,m=n.CLASSES;if(o){n.replaceClass(m.HORIZONTAL,m.VERTICAL);}else{n.replaceClass(m.VERTICAL,m.HORIZONTAL);}n._itemsTable.size=0;return o;},_setRevealAmount:function(n){var m=this;if(n>=0&&n<=100){n=parseInt(n,10);n=k.isNumber(n)?n:0;m._setClipContainerSize();}else{n=m.get("revealAmount");}return n;},_setSelectedItem:function(m){this._selectedItem=m;},_syncUiForItemAdd:function(p){var t=this,r=t._carouselEl,m,u,o=t._itemsTable,n,q,s;q=k.isUndefined(p.pos)?o.numItems-1:p.pos;if(!k.isUndefined(o.items[q])){u=o.items[q];if(u&&!k.isUndefined(u.id)){n=a.get(u.id);}}if(!n){m=t._createCarouselItem({className:u.className,content:u.item,id:u.id});if(k.isUndefined(p.pos)){if(!k.isUndefined(o.loading[q])){n=o.loading[q];}if(n){r.replaceChild(m,n);delete o.loading[q];}else{r.appendChild(m);}}else{if(!k.isUndefined(o.items[p.pos+1])){s=a.get(o.items[p.pos+1].id);
}if(s){r.insertBefore(m,s);}else{}}}else{if(k.isUndefined(p.pos)){if(!a.isAncestor(t._carouselEl,n)){r.appendChild(n);}}else{if(!a.isAncestor(r,n)){if(!k.isUndefined(o.items[p.pos+1])){r.insertBefore(n,a.get(o.items[p.pos+1].id));}}}}if(!t._hasRendered){t._refreshUi();}if(t.get("selectedItem")<0){t.set("selectedItem",t.get("firstVisible"));}},_syncUiForItemRemove:function(r){var q=this,m=q._carouselEl,o,p,n,s;n=q.get("numItems");p=r.item;s=r.pos;if(p&&(o=a.get(p.id))){if(o&&a.isAncestor(m,o)){Y.purgeElement(o,true);m.removeChild(o);}if(q.get("selectedItem")==s){s=s>=n?n-1:s;q.set("selectedItem",s);}}else{}},_syncUiForLazyLoading:function(s){var r=this,n=r._carouselEl,q,o,m=r._itemsTable,p;for(o=s.first;o<=s.last;o++){q=r._createCarouselItem({className:r.CLASSES.ITEM_LOADING,content:r.STRINGS.ITEM_LOADING_CONTENT,id:a.generateId()});if(q){if(!k.isUndefined(m.items[s.last+1])){p=a.get(m.items[s.last+1].id);if(p){n.insertBefore(q,p);}else{}}else{n.appendChild(q);}}m.loading[o]=q;}},_updateNavButtons:function(q,n){var o,m=this.CLASSES,r,p=q.parentNode;if(!p){return;}r=p.parentNode;if(q.nodeName.toUpperCase()=="BUTTON"&&a.hasClass(p,m.BUTTON)){if(n){if(r){o=a.getChildren(r);if(o){a.removeClass(o,m.FOCUSSED_BUTTON);}}a.addClass(p,m.FOCUSSED_BUTTON);}else{a.removeClass(p,m.FOCUSSED_BUTTON);}}},_updatePagerButtons:function(){var v=this,t=v.CLASSES,u=v._pages.cur,m,s,q,w,o=v.get("numVisible"),r=v._pages.num,p=v._pages.el;if(r===0||!p){return;}a.setStyle(p,"visibility","hidden");while(p.firstChild){p.removeChild(p.firstChild);}for(q=0;q<r;q++){if(k.isUndefined(v._itemsTable.items[q*o])){a.setStyle(p,"visibility","visible");break;}w=v._itemsTable.items[q*o].id;m=document.createElement("LI");if(!m){a.setStyle(p,"visibility","visible");break;}if(q===0){a.addClass(m,t.FIRST_PAGE);}if(q==u){a.addClass(m,t.SELECTED_NAV);}s='<a href="#'+w+'" tabindex="0"><em>'+v.STRINGS.PAGER_PREFIX_TEXT+" "+(q+1)+"</em></a>";m.innerHTML=s;p.appendChild(m);}a.setStyle(p,"visibility","visible");},_updatePagerMenu:function(){var u=this,t=u._pages.cur,o,r,v,p=u.get("numVisible"),s=u._pages.num,q=u._pages.el,m;if(s===0){return;}m=document.createElement("SELECT");if(!m){return;}a.setStyle(q,"visibility","hidden");while(q.firstChild){q.removeChild(q.firstChild);}for(r=0;r<s;r++){if(k.isUndefined(u._itemsTable.items[r*p])){a.setStyle(q,"visibility","visible");break;}v=u._itemsTable.items[r*p].id;o=document.createElement("OPTION");if(!o){a.setStyle(q,"visibility","visible");break;}o.value="#"+v;o.innerHTML=u.STRINGS.PAGER_PREFIX_TEXT+" "+(r+1);if(r==t){o.setAttribute("selected","selected");}m.appendChild(o);}o=document.createElement("FORM");if(!o){}else{o.appendChild(m);q.appendChild(o);}a.setStyle(q,"visibility","visible");},_updateTabIndex:function(m){var n=this;if(m){if(n._focusableItemEl){n._focusableItemEl.tabIndex=-1;}n._focusableItemEl=m;m.tabIndex=0;}},_validateAnimation:function(m){var n=true;if(k.isObject(m)){if(m.speed){n=n&&k.isNumber(m.speed);}if(m.effect){n=n&&k.isFunction(m.effect);}else{if(!k.isUndefined(YAHOO.util.Easing)){m.effect=YAHOO.util.Easing.easeOut;}}}else{n=false;}return n;},_validateFirstVisible:function(o){var n=this,m=n.get("numItems");if(k.isNumber(o)){if(m===0&&o==m){return true;}else{return(o>=0&&o<m);}}return false;},_validateNavigation:function(m){var n;if(!k.isObject(m)){return false;}if(m.prev){if(!k.isArray(m.prev)){return false;}for(n in m.prev){if(m.prev.hasOwnProperty(n)){if(!k.isString(m.prev[n].nodeName)){return false;}}}}if(m.next){if(!k.isArray(m.next)){return false;}for(n in m.next){if(m.next.hasOwnProperty(n)){if(!k.isString(m.next[n].nodeName)){return false;}}}}return true;},_validateNumItems:function(m){return k.isNumber(m)&&(m>=0);},_validateNumVisible:function(m){var n=false;if(k.isNumber(m)){n=m>0&&m<=this.get("numItems");}return n;},_validateRevealAmount:function(m){var n=false;if(k.isNumber(m)){n=m>=0&&m<100;}return n;},_validateScrollIncrement:function(m){var n=false;if(k.isNumber(m)){n=(m>0&&m<this.get("numItems"));}return n;}});})();YAHOO.register("carousel",YAHOO.widget.Carousel,{version:"2.7.0",build:"1796"});