(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["IframeView"],{"58c3":function(e,t,n){"use strict";n.r(t);var r=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("iframe",{staticClass:"iframe",attrs:{frameborder:"0",srcdoc:e.html}})},a=[],u=(n("7514"),n("cebc")),i=n("2f62"),s={name:"IframeView",data:function(){return{html:""}},computed:Object(u["a"])({},Object(i["e"])({userMenu:function(e){return e.user.permission}})),mounted:function(){var e=this;console.log(this.userMenu);var t=this.userMenu.find(function(t){return t.id==e.$route.params.id});this.html=t?t.url:""}},c=s,o=(n("b344"),n("2877")),f=Object(o["a"])(c,r,a,!1,null,"0607c6ea",null);t["default"]=f.exports},aa6d:function(e,t,n){},b344:function(e,t,n){"use strict";var r=n("aa6d"),a=n.n(r);a.a}}]);