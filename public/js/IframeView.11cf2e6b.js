(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["IframeView"],{"4fbf":function(e,t,r){"use strict";var n=r("ecc4"),s=r.n(n);s.a},"58c3":function(e,t,r){"use strict";r.r(t);var n=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"wrap"},[e.isIframe?r("div",{staticClass:"iframe",domProps:{innerHTML:e._s(e.html)}}):r("iframe",{ref:"iframe",staticClass:"iframe",attrs:{frameborder:"0",srcdoc:e.html},on:{load:e.setIframeDocStyle}})])},s=[],i=(r("8e6e"),r("ac6a"),r("456d"),r("7514"),r("bd86")),c=r("2f62");function a(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable})),r.push.apply(r,n)}return r}function o(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?a(r,!0).forEach(function(t){Object(i["a"])(e,t,r[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):a(r).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))})}return e}var u={name:"IframeView",data:function(){return{html:"",isIframe:!0}},computed:o({},Object(c["e"])({userMenu:function(e){return e.user.permission}})),mounted:function(){var e=this;console.log(this.userMenu);var t=this.userMenu.find(function(t){return t.id==e.$route.params.id});this.isIframe=/^<iframe/.test(t.url),this.html=t?t.url:""},methods:{setIframeDocStyle:function(){var e=this.$refs.iframe.contentWindow.document,t="html,body{margin: 0;padding: 0;width: 100%; height: 100%}",r=document.createElement("style");r.type="text/css",r.id="styles_js",e.getElementsByTagName("head")[0].appendChild(r),e.querySelector("#styles_js").appendChild(document.createTextNode(t))}}},f=u,l=(r("4fbf"),r("2877")),m=Object(l["a"])(f,n,s,!1,null,"2795268c",null);t["default"]=m.exports},ecc4:function(e,t,r){}}]);