(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["AddMenu"],{"067b":function(e,t,a){},"0f8f":function(e,t,a){},"14e3":function(e,t,a){e.exports=a.p+"img/g.90c6c449.png"},"2d75":function(e,t,a){"use strict";var n=a("067b"),i=a.n(n);i.a},"3e88":function(e,t,a){},"56e4":function(e,t,a){e.exports=a.p+"img/c.fd915442.png"},6858:function(e,t,a){e.exports=a.p+"img/f.84395bd4.png"},"6b70":function(e,t,a){"use strict";var n=a("0f8f"),i=a.n(n);i.a},9515:function(e,t,a){e.exports=a.p+"img/d.b3205aa1.png"},"985f":function(e,t,a){e.exports=a.p+"img/h.9a2efaa0.png"},a9c0:function(e,t,a){e.exports=a.p+"img/b.9474a753.png"},b0fc:function(e,t,a){e.exports=a.p+"img/e.18cf4ee5.png"},c0c0:function(e,t,a){"use strict";var n=a("e761"),i=a.n(n);i.a},d1f7:function(e,t,a){"use strict";a.d(t,"b",function(){return n}),a.d(t,"a",function(){return i});var n=["bar-chart","appstore","setting","user","bell","profile","safety-certificate","dollar","warning","star"],i=[{id:"a",src:a("fe28")},{id:"b",src:a("a9c0")},{id:"c",src:a("56e4")},{id:"d",src:a("9515")},{id:"e",src:a("b0fc")},{id:"f",src:a("6858")},{id:"g",src:a("14e3")},{id:"h",src:a("985f")}]},d39d:function(e,t,a){"use strict";a.r(t);var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("a-card",{attrs:{"body-style":{padding:"24px 32px"},bordered:!1}},[a("a-form",{attrs:{form:e.form,autocomplete:"off"},on:{submit:e.handleSubmit}},[a("a-form-item",{attrs:{label:"菜单名称",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["title",{initialValue:e.baseForm.title,rules:[{required:!0,message:"请输入菜单名称"},{max:10,message:"10个字符之内"}]}],expression:"[\n                        'title',\n                        {\n                            initialValue: baseForm.title,\n                            rules: [\n                                { required: true, message: '请输入菜单名称' },\n                                {max: 10, message: '10个字符之内'}\n                            ]\n                        }\n                    ]"}],attrs:{name:"name",placeholder:"10个字以内"}})],1),a("a-form-item",{directives:[{name:"show",rawName:"v-show",value:!1,expression:"false"}],attrs:{label:"打开方式",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:17},sm:{span:17}}}},[a("a-radio-group",{directives:[{name:"decorator",rawName:"v-decorator",value:["open_type",{initialValue:e.baseForm.open_type,rules:[{required:!0,message:"请选择打开方式"}]}],expression:"[\n                    'open_type',\n                    {\n                        initialValue: baseForm.open_type,\n                        rules: [{ required: true, message: '请选择打开方式' }]\n                    }\n                ]"}]},[a("a-radio",{attrs:{value:0}},[e._v("在当前页面打开")]),a("a-radio",{attrs:{value:1}},[e._v("在新页面打开")])],1)],1),a("a-form-item",{attrs:{label:"URL类型",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:17},sm:{span:17}}}},[a("a-radio-group",{directives:[{name:"decorator",rawName:"v-decorator",value:["url_type",{initialValue:e.baseForm.url_type,rules:[{required:!0,message:"请选择URL类型"}]}],expression:"[\n                    'url_type',\n                    {\n                        initialValue: baseForm.url_type,\n                        rules: [{ required: true, message: '请选择URL类型' }]\n                    }\n                ]"}]},[a("a-radio",{attrs:{value:0}},[e._v("内链 "),a("span",{staticClass:"tip"},[e._v("（通过表单关联菜单内容）")])]),a("a-radio",{attrs:{value:1}},[e._v("外链 "),a("span",{staticClass:"tip"},[e._v("（可打开外部链接地址）")])]),a("a-radio",{attrs:{value:2}},[e._v("无链接 "),a("span",{staticClass:"tip"},[e._v("（点击后，仅展开子菜单）")])]),a("a-radio",{attrs:{value:3}},[e._v("嵌入代码 "),a("span",{staticClass:"tip"},[e._v("（支持复制iframe、script代码，在页面内部打开该代码内容）")])])],1)],1),e.showURL?a("a-form-item",{attrs:{label:"菜单链接",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["url",{initialValue:e.baseForm.url,rules:[{required:!0,message:"请输入链接"},{type:"url",message:"请输入正确的网页链接"}]}],expression:"[\n                        'url',\n                        {\n                            initialValue: baseForm.url,\n                            rules: [\n                                { required: true, message: '请输入链接' },\n                                { type: 'url', message: '请输入正确的网页链接'}\n                            ]\n                        }\n                    ]"}],attrs:{name:"url",placeholder:"请输入菜单链接"}})],1):e._e(),e.isCode?a("a-form-item",{attrs:{label:"复制代码",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("a-textarea",{directives:[{name:"decorator",rawName:"v-decorator",value:["code",{initialValue:e.baseForm.code,rules:[{required:!0,message:"请输入代码"}]}],expression:"[\n                    'code',\n                    {\n                        initialValue: baseForm.code,\n                        rules: [\n                            { required: true, message: '请输入代码' }\n                        ]\n                    }\n                ]"}],attrs:{rows:4,placeholder:"请复制其他页面的iframe代码或script代码，粘贴到该处，请保证代码正确，否则可能影响展示效果"}})],1):e._e(),a("a-form-item",{attrs:{label:"上级菜单",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("MenuTree",{directives:[{name:"decorator",rawName:"v-decorator",value:["pid",{initialValue:e.pInfo.id,rules:[{required:!0,type:"number",message:"请选择上级菜单"}]}],expression:"[\n                        'pid',\n                        {\n                            initialValue: pInfo.id,\n                            rules: [{ required: true, type: 'number', message: '请选择上级菜单' }]\n                        }\n                    ]"}],attrs:{treeData:e.data,nodeLevel:0,nodeInfo:e.pInfo},on:{nodeClick:e.getPreMenu,visibleHandle:e.visibleHandle}})],1),e.showOpt?a("a-form-item",{attrs:{label:"数据操作",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("div",[a("a-checkbox-group",{directives:[{name:"decorator",rawName:"v-decorator",value:["operation",{initialValue:e.baseForm.operation}],expression:"[\n                            'operation',\n                            {\n                                initialValue: baseForm.operation\n                            }\n                        ]"}],attrs:{defaultChecked:"true",options:[{label:"新增",value:"1"},{label:"查看",value:"2"},{label:"编辑",value:"3"},{label:"删除",value:"4"},{label:"导入",value:"5"},{label:"导出",value:"6"}]}}),a("div",{staticClass:"tip",staticStyle:{"line-height":"1"}},[e._v("选中后将会以按钮形式显示在菜单页面，点击后可进入对应操作页面")])],1)]):e._e(),a("a-form-item",{attrs:{label:"是否启用",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("a-switch",{directives:[{name:"decorator",rawName:"v-decorator",value:["status",{initialValue:e.baseForm.status,valuePropName:"checked"}],expression:"[\n                    'status',\n                    {\n                        initialValue: baseForm.status,\n                        valuePropName: 'checked'\n                    }\n                ]"}],attrs:{checkedChildren:"是",unCheckedChildren:"否"}})],1),a("a-form-item",{attrs:{label:"排序",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["sort",{initialValue:e.baseForm.sort,rules:[{max:5,message:"5个字符之内"},{pattern:/^[0-9]\d*$/,message:"只能输入数字"}]}],expression:"[\n                    'sort',\n                    {\n                        initialValue: baseForm.sort,\n                        rules: [\n                            {max: 5, message: '5个字符之内'},\n                            {pattern: /^[0-9]\\d*$/, message: '只能输入数字'}\n                        ]\n                    }\n                ]"}],attrs:{placeholder:"数字越大，表示菜单在越前面"}})],1),a("a-form-item",{attrs:{label:"菜单描述",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("a-textarea",{directives:[{name:"decorator",rawName:"v-decorator",value:["describe",{initialValue:e.baseForm.describe,rules:[{max:50,message:"50个字符之内"}]}],expression:"[\n                    'describe',\n                    {\n                        initialValue: baseForm.describe,\n                        rules: [\n                            {max: 50, message: '50个字符之内'}\n                        ]\n                    }\n                ]"}],attrs:{placeholder:"请输入50个字以内的描述文案，将用于卡片视图展示"}})],1),a("a-form-item",{attrs:{label:"菜单图标",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("div",[a("a-radio-group",{directives:[{name:"decorator",rawName:"v-decorator",value:["icon",{initialValue:e.baseForm.icon}],expression:"[\n                        'icon',\n                        {\n                            initialValue: baseForm.icon\n                        }\n                    ]"}]},e._l(e.menuIcon,function(e){return a("a-radio",{key:e,attrs:{value:e}},[a("a-icon",{attrs:{type:e}})],1)}),1),a("div",{staticClass:"tip",staticStyle:{"line-height":"1"}},[e._v("用于列表展示时菜单名称前的图标")])],1)]),a("a-form-item",{attrs:{label:"卡片图标",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[a("div",[a("a-radio-group",{directives:[{name:"decorator",rawName:"v-decorator",value:["icon2",{initialValue:e.baseForm.icon2}],expression:"[\n                        'icon2',\n                        {\n                            initialValue: baseForm.icon2\n                        }\n                    ]"}]},e._l(e.cardIcon,function(e){return a("a-radio",{key:e.id,attrs:{value:e.id}},[a("a-avatar",{attrs:{shape:"square",src:e.src,size:"large"}})],1)}),1),a("div",{staticClass:"tip",staticStyle:{"line-height":"1"}},[e._v("用于卡片展示时菜单名称前的图标")])],1)]),a("a-form-item",{staticStyle:{"text-align":"center"},attrs:{wrapperCol:{span:24}}},[a("a-button",{attrs:{htmlType:"submit",type:"primary"}},[e._v("提交")]),a("a-button",{staticStyle:{"margin-left":"8px"},on:{click:e.cancel}},[e._v("取消")])],1)],1)],1)},i=[],s=(a("7514"),a("28a5"),a("55dd"),a("b775")),r=a("2971"),o=a("5d2d"),l=a("e2a5"),d=a("d1f7"),c=null,p={name:"AddMenu",components:{MenuTree:l["a"]},data:function(){return{form:this.$form.createForm(this,{onValuesChange:this.onValuesChange}),data:[],pInfo:{},baseForm:{title:"",open_type:0,url_type:0,url:"",pid:0,operation:[],status:!0,sort:"",describe:"",icon:d["b"][0]||"",icon2:d["a"][0].id||"",code:""},showURL:!1,showOpt:!0,cardIcon:d["a"],menuIcon:d["b"],isCode:!1}},computed:{optType:function(){return this.$route.params.type},id:function(){return this.$route.params.id||""}},created:function(){this.getMenuList(),"edit"===this.optType&&this.id&&this.initData(this.id)},methods:{initData:function(e){var t=this;s["b"].post("/admin/Devmenu/detailData",{uid:Object(o["b"])("UID"),hashid:Object(o["b"])("HASHID"),id:e}).then(function(e){if(console.log(e),"200"==e.Code){var a=e.Data,n=a.title,i=a.open_type,s=a.url_type,r=a.url,o=a.pid,l=a.operation,c=a.status,p=a.sort,u=a.describe,m=a.icon,h=a.icon2;t.baseForm={title:n,open_type:i,url_type:s,url:1==s?r:"",pid:o,operation:l.split(","),status:1==c,sort:String(p),describe:u,icon:m||d["b"][0],icon2:h||d["a"][0].id,code:3==s?r:""},1==s&&(t.showURL=!0),t.showOpt=0==s,t.isCode=3==s}})},handleSubmit:function(e){var t=this;e.preventDefault(),this.form.validateFields(function(e,a){if(console.log(a),!e){var n=JSON.parse(JSON.stringify(a));n.operation&&(n.operation=n.operation.filter(function(e){return e}).join(",")),n.status=n.status?1:2,n.sort=n.sort||1,0==n.url_type?n.url="/listtpl/getlist":2==n.url_type?n.url="###":3==n.url_type&&(n.url=n.code,delete n.code),s["b"].post("/admin/Devmenu/saveData",Object.assign({},{uid:Object(o["b"])("UID"),hashid:Object(o["b"])("HASHID")},{id:"edit"==t.optType?t.id:"",icon:"",fsize:"",sort:"",posttype:"",pos:"1",operation:""},n)).then(function(e){console.log(e),"200"===e.Code&&(t.$notification["success"]({message:"提示:",description:"add"===t.optType?"添加成功":"更新成功"}),t.$router.push("/designCenter/menu-design/menu"))})}})},getPreMenu:function(e){console.log(e),this.id&&this.id==e.id?this.$message.warning("上级菜单无法选择自身"):this.pInfo=e},onValuesChange:function(e,t){t.hasOwnProperty("url_type")&&(1==t.url_type?(this.baseForm.url="",this.showURL=!0):this.showURL=!1,0==t.url_type?this.showOpt=!0:this.showOpt=!1,3==t.url_type?(this.baseForm.url="",this.isCode=!0):this.isCode=!1)},cancel:function(){var e=this;this.$confirm("取消后将不保存修改后的内容","确定取消?",{confirmButtonText:"继续",cancelButtonText:"取消",type:"warning"}).then(function(){e.$router.go(-1)}).catch(function(){console.log("cancel")})},getMenuList:function(){var e=this;s["b"].post("/admin/Devmenu/listData",{page:"",search:""}).then(function(t){if(console.log(t.Data.lists),e.data=[{title:"顶级菜单",id:0,children:Object(r["a"])(t.Data.lists)}],e.id){var a=t.Data.lists.find(function(t){return t.id==e.id});"add"===e.optType?(e.pInfo=a,e.baseForm.pid=a.id):e.pInfo=t.Data.lists.find(function(e){return e.id==a.pid})||{title:"顶级菜单",id:0}}else e.pInfo={title:"顶级菜单",id:0},e.baseForm.pid=0}).finally(function(){c&&(c=null)})},visibleHandle:function(e){var t=this;console.log(e),console.log(c),e&&(c||this.data.length||(c=setTimeout(function(){t.getMenuList()},500)))}}},u=p,m=(a("c0c0"),a("2877")),h=Object(m["a"])(u,n,i,!1,null,"67e7a9a6",null);t["default"]=h.exports},e2a5:function(e,t,a){"use strict";var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"tree"},[a("a-popover",{attrs:{placement:"rightTop",overlayClassName:"tree-popover"},on:{visibleChange:e.visibleChange}},[a("template",{slot:"content"},[e.treeData.length?e._e():a("div",{staticClass:"no-data"},[e._v("暂无数据")]),a("TreeNode",{attrs:{treeData:e.treeData,nodeLevel:e.nodeLevel,activeNodeId:e.activeNodeId},on:{nodeClick:e.nodeClick}})],1),a("a-input",{attrs:{placeholder:e.placeholderInner,readOnly:!0,value:e.title},on:{change:e.handleChange}},[a("a-icon",{style:{color:"#999"},attrs:{slot:"suffix",type:e.down?"down":"up"},slot:"suffix"})],1)],2)],1)},i=[],s=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"tree"},e._l(e.treeData,function(t,n){return a("div",{key:t.id},[a("div",{staticClass:"tree-item",class:{active:t.id==e.activeNodeId},style:e.style,attrs:{href:"javascript:;"},on:{click:function(a){return e.nodeClick(t)}}},[t.children?a("a-icon",{attrs:{type:e.openNode[n]?"caret-up":"caret-down"},on:{click:function(t){return t.stopPropagation(),e.visChildren(n)}}}):e._e(),e._v("\n            "+e._s(t.title)+"\n        ")],1),a("div",{directives:[{name:"show",rawName:"v-show",value:e.openNode[n],expression:"openNode[index]"}]},[t.children&&t.children.length?a("TreeNode",{attrs:{treeData:t.children,nodeLevel:e.nodeLevel+1,activeNodeId:e.activeNodeId},on:{nodeClick:e.nodeClick}}):e._e()],1)])}),0)},r=[],o=(a("6c7b"),{name:"TreeNode",props:["treeData","nodeLevel","activeNodeId"],data:function(){return{openNode:[]}},computed:{style:function(){return{paddingLeft:20*this.nodeLevel+"px"}}},watch:{treeData:{handler:function(){var e=new Array(this.treeData.length).fill(!0);this.$set(this,"openNode",e)},deep:!0}},mounted:function(){var e=new Array(this.treeData.length).fill(!0);this.$set(this,"openNode",e)},methods:{visChildren:function(e){var t=this.openNode[e];this.$set(this.openNode,e,!t)},nodeClick:function(e){this.$emit("nodeClick",e)}}}),l=o,d=(a("e898"),a("2877")),c=Object(d["a"])(l,s,r,!1,null,"15b7a835",null),p=c.exports,u={name:"MenuTree",components:{TreeNode:p},props:["treeData","nodeLevel","nodeInfo","placeholder"],data:function(){var e=this.nodeInfo;return{down:!0,title:e.title,activeNodeId:e.id}},computed:{placeholderInner:function(){return this.placeholder?this.placeholder:"请选择上级菜单"}},watch:{nodeInfo:function(e){this.title=e.title,this.activeNodeId=e.id,this.triggerChange(e.id)}},mounted:function(){this.title=this.nodeInfo.title,this.activeNodeId=this.nodeInfo.id},methods:{nodeClick:function(e){this.$emit("nodeClick",e)},visibleChange:function(e){this.down=!e,this.$emit("visibleHandle",e)},handleChange:function(e){this.triggerChange(this.nodeInfo.id)},triggerChange:function(e){this.$emit("change",e)}}},m=u,h=(a("6b70"),a("2d75"),Object(d["a"])(m,n,i,!1,null,"6bd96ead",null));t["a"]=h.exports},e761:function(e,t,a){},e898:function(e,t,a){"use strict";var n=a("3e88"),i=a.n(n);i.a},fe28:function(e,t,a){e.exports=a.p+"img/a.a498fad5.png"}}]);