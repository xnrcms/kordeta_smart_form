(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["IframeView"],{"58c3":function(e,t,r){"use strict";r.r(t);var n=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("iframe",{staticClass:"iframe",attrs:{frameborder:"0",srcdoc:e.html}})},c=[],o=(r("8e6e"),r("ac6a"),r("456d"),r("7514"),r("bd86")),a=r("2f62");function i(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable})),r.push.apply(r,n)}return r}function u(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?i(r,!0).forEach(function(t){Object(o["a"])(e,t,r[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):i(r).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))})}return e}var s={name:"IframeView",data:function(){return{html:""}},computed:u({},Object(a["e"])({userMenu:function(e){return e.user.permission}})),mounted:function(){var e=this;console.log(this.userMenu);var t=this.userMenu.find(function(t){return t.id==e.$route.params.id});this.html=t?t.url:""}},f=s,p=(r("b344"),r("2877")),l=Object(p["a"])(f,n,c,!1,null,"0607c6ea",null);t["default"]=l.exports},aa6d:function(e,t,r){},b344:function(e,t,r){"use strict";var n=r("aa6d"),c=r.n(n);c.a}}]);