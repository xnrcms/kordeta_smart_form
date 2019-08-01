(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["BaseTemplate"],{"4aa8":function(t,e,a){},"500e":function(t,e,a){"use strict";var i=a("4aa8"),n=a.n(i);n.a},"76ff":function(t,e,a){"use strict";a.r(e);var i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("a-card",{attrs:{bordered:!1}},[a("SearchPart",{attrs:{searchConfig:t.searchItems},on:{searchFormSubmit:t.searchFormSubmit,searchFormReset:t.searchFormReset}}),a("k-btn",{staticClass:"mt20",attrs:{type:"primary",icon:"plus",code:"1"},on:{click:t.addItem}},[t._v("新建")]),a("k-btn",{staticClass:"mt20 ml20",attrs:{code:"5"},on:{click:t.openInputDialog}},[t._v("导入")]),a("k-btn",{staticClass:"mt20 ml20",attrs:{code:"6"},on:{click:t.outPutTable}},[t._v("导出")]),a("a-table",{ref:"table",staticClass:"mt20 table-row",attrs:{size:"default",rowKey:"id",loading:t.tableLoading,scroll:t.tableScrollConfig,columns:t.columns,dataSource:t.tableData,pagination:!1},scopedSlots:t._u([{key:"action",fn:function(e,i){return a("span",{},[[a("k-btn",{attrs:{type:"text",code:"2"},on:{click:function(e){return t.toDetail(i)}}},[t._v("查看")]),a("a-divider",{attrs:{type:"vertical"}}),a("k-btn",{attrs:{type:"text",code:"3"},on:{click:function(e){return t.toEdit(i)}}},[t._v("编辑")]),a("a-divider",{attrs:{type:"vertical"}}),a("k-btn",{attrs:{type:"text",code:"4"},on:{click:function(e){return t.toDelete(i)}}},[t._v("删除")])]],2)}}])}),a("a-pagination",{staticClass:"pagination",attrs:{total:t.total,pageSize:t.pageSize},on:{change:t.changePage},model:{value:t.queryParams.page,callback:function(e){t.$set(t.queryParams,"page",e)},expression:"queryParams.page"}})],1),a("a-modal",{attrs:{title:"批量导入",visible:t.showInputDialog},on:{ok:t.submitInputFile,cancel:t.cancelInputFile}},[a("div",{directives:[{name:"show",rawName:"v-show",value:t.beforeInput,expression:"beforeInput"}]},[t._v("\n            文件：\n            "),a("a-upload",{attrs:{name:"file",remove:t.handleRemove,beforeUpload:t.beforeUpload,fileList:t.fileList,multiple:!1,accept:".xls,.xlsx"},on:{change:t.handleUploadChange}},[a("a-button",[t._v("\n                    选择文件\n                ")])],1),a("p",{staticClass:"mt20"},[t._v("请按照模板文件填写完整信息，带*号的为必填项，缺少必填项的数据将无法被导入")]),a("a",{attrs:{href:"javascript:;"},on:{click:t.downloadTemp}},[t._v("下载模板")])],1),a("div",{directives:[{name:"show",rawName:"v-show",value:!t.beforeInput,expression:"!beforeInput"}]},[a("p",{staticClass:"tips-tit"},[t._v("正在导入，请稍候")]),a("a-spin",{attrs:{spinning:!t.beforeInput}},[a("div",{staticClass:"loading-part"})]),a("p",{staticClass:"tips-warning"},[t._v("请勿中途退出，退出后该文件所有数据将撤销导入")])],1)])],1)},n=[],o=(a("456d"),a("386d"),a("6762"),a("2fdb"),a("ac6a"),a("55dd"),a("7f7f"),a("c5f6"),a("ef5a")),s=a("ca00"),r=a("0e5c"),l=(a("7514"),a("cebc")),c=a("5efb"),u=a("2f62"),m={1:"新增",2:"查看",3:"编辑",4:"删除",5:"导入",6:"导出"},p={props:{disabled:{type:Boolean,default:!1},ghost:{type:Boolean,default:!1},htmlType:{type:String},icon:{type:String},loading:{type:Boolean,default:!1},shape:{type:String},size:{type:String},type:{type:String},block:{type:Boolean,default:!1},code:{type:[String,Number]}},data:function(){return{id:this.$route.params.id,btnInfo:{}}},computed:Object(l["a"])({},Object(u["e"])({permission:function(t){return t.user.permission}})),mounted:function(){var t=this,e=m[this.code];this.btnInfo=this.permission.find(function(a){return a.pid==t.id&&a.title==e})},methods:{handleClick:function(t){this.$emit("click")}},render:function(){var t=arguments[0],e=this.$props,a=this.$slots.default,i={click:this.handleClick};if(this.btnInfo){var n="text"!=this.type?c["a"]:"a";return t(n,{props:Object(l["a"])({},e),on:Object(l["a"])({},i)},[a])}}},f=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("el-form",{attrs:{inline:!0}},[t._l(t.searchConfig,function(t){return[a("SearchPartItem",{key:t.key,attrs:{formData:t}})]}),t.searchConfig.length?a("el-form-item",[a("el-button",{attrs:{type:"primary",size:"small"},on:{click:t.onSubmit}},[t._v("查询")]),a("el-button",{attrs:{size:"small"},on:{click:t.reset}},[t._v("重置")])],1):t._e()],2)],1)},d=[],h=a("bd86"),g=function(){var t=this,e=t.$createElement,a=t._self._c||e;return"input"===t.formData.type||"textarea"===t.formData.type?a("el-form-item",{attrs:{label:t.formData.name}},[a("el-input",{attrs:{placeholder:"请输入",size:"mini",clearable:""},model:{value:t.formData.options.defaultValue,callback:function(e){t.$set(t.formData.options,"defaultValue",e)},expression:"formData.options.defaultValue"}})],1):"radio"===t.formData.type||"select"===t.formData.type&&!t.formData.options.multiple?a("el-form-item",{attrs:{label:t.formData.name}},[a("el-select",{attrs:{placeholder:"请选择",size:"mini",clearable:""},model:{value:t.formData.options.defaultValue,callback:function(e){t.$set(t.formData.options,"defaultValue",e)},expression:"formData.options.defaultValue"}},t._l(t.formData.options.options,function(t,e){return a("el-option",{key:e,attrs:{value:t.value,label:t.value}})}),1)],1):"checkbox"===t.formData.type||"select"===t.formData.type&&t.formData.options.multiple?a("el-form-item",{attrs:{label:t.formData.name}},[a("el-select",{attrs:{placeholder:"请选择",size:"mini",multiple:"","collapse-tags":""},model:{value:t.formData.options.defaultValue,callback:function(e){t.$set(t.formData.options,"defaultValue",e)},expression:"formData.options.defaultValue"}},t._l(t.formData.options.options,function(t,e){return a("el-option",{key:e,attrs:{value:t.value,label:t.value}})}),1)],1):"date"===t.formData.type?a("el-form-item",{attrs:{label:t.formData.name}},[a("el-date-picker",{attrs:{type:"daterange",align:"right","unlink-panels":"","range-separator":"至","start-placeholder":"开始日期","end-placeholder":"结束日期","value-format":"yyyy-MM-dd","picker-options":t.pickerOptions},model:{value:t.formData.options.defaultValue,callback:function(e){t.$set(t.formData.options,"defaultValue",e)},expression:"formData.options.defaultValue"}})],1):t._e()},b=[],v={name:"SearchPartItem",props:{formData:{type:Object,default:function(){}}},data:function(){return{pickerOptions:{shortcuts:[{text:"最近一周",onClick:function(t){var e=new Date,a=new Date;a.setTime(a.getTime()-6048e5),t.$emit("pick",[a,e])}},{text:"最近一个月",onClick:function(t){var e=new Date,a=new Date;a.setTime(a.getTime()-2592e6),t.$emit("pick",[a,e])}},{text:"最近三个月",onClick:function(t){var e=new Date,a=new Date;a.setTime(a.getTime()-7776e6),t.$emit("pick",[a,e])}}]}}}},y=v,D=(a("500e"),a("2877")),k=Object(D["a"])(y,g,b,!1,null,"894458b0",null),$=k.exports,w={name:"SearchPart",components:{SearchPartItem:$},props:{searchConfig:{type:Array,default:[]}},data:function(){return{}},methods:{onSubmit:function(){var t={};this.searchConfig.forEach(function(e){Object.assign(t,Object(h["a"])({},e.model,e.options.defaultValue))}),this.$emit("searchFormSubmit",t)},reset:function(){this.$emit("searchFormReset")}}},S=w,I=Object(D["a"])(S,f,d,!1,null,"65b6f66f",null),O=I.exports,x={name:"base-template",components:{KBtn:p,SearchPart:O},data:function(){return{queryParams:{page:1,search:JSON.stringify({}),menuid:0},isFirstTime:!0,tableLoading:!1,tableScrollConfig:{x:2e3,y:300},columnsOriginal:[],columns:[],searchItems:[],tableData:[],total:0,pageSize:20,showInputDialog:!1,beforeInput:!0,fileList:[]}},created:function(){this.getTableColumns()},methods:{searchFormSubmit:function(t){this.queryParams.search=JSON.stringify(t),this.getTableColumns()},searchFormReset:function(){this.queryParams.search=JSON.stringify({}),this.queryParams.page=1,this.getTableColumns()},getTableColumns:function(){var t=this,e=this.$createElement;this.tableLoading=!0,isNaN(Number(this.$route.params.id))?this.$message.error("获取菜单数据出错"):(this.queryParams.menuid=Number(this.$route.params.id),Object(o["c"])(this.queryParams).then(function(a){t.total=a.Data.total,t.pageSize=a.Data.limit,t.columnsOriginal=JSON.parse(a.Data.tableHead),t.columns=t.columnsOriginal.map(function(t){return{title:t.name,dataIndex:t.model,width:150,align:"center",customRender:function(t){return e("a-tooltip",{attrs:{placement:"topLeft"}},[e("template",{slot:"title"},[t]),t])},sort:t.sort}}),t.columns.push({title:"操作",width:180,dataIndex:"action",scopedSlots:{customRender:"action"},align:"center",sort:-1}),t.columns.sort(t.getSort),t.tableScrollConfig.x=150*(t.columns.length-1)+180,t.isFirstTime&&(t.searchItems=t.columnsOriginal.filter(function(t){return t.isSearch}),t.searchItems.forEach(function(t){t.options.defaultValue=""})),a.Data.listData.length?t.tableData=JSON.parse(a.Data.listData):t.tableData=[],t.tableLoading=!1,t.isFirstTime=!1}).catch(function(e){console.log(e),t.tableLoading=!1}))},getSort:function(t,e){var a=t.sort,i=e.sort;return a||(a=0),i||(i=0),a<i?1:a>i?-1:0},addItem:function(){if(this.columnsOriginal.length)if(isNaN(Number(this.$route.params.id)))this.$message.error("获取菜单数据出错");else{var t=Number(this.$route.params.id);this.$route.path.includes("cardPage/general")?this.$router.push("/cardPage/general/add/".concat(t)):this.$router.push("/listtpl/getlist/add/".concat(t))}else this.$message.error("获取表格信息有误，无法新建")},toDetail:function(t){if(isNaN(Number(this.$route.params.id)))this.$message.error("获取菜单数据出错");else{var e=Number(this.$route.params.id);this.$route.path.includes("cardPage/general")?this.$router.push("/cardPage/general/check/".concat(e,"/").concat(t.id)):this.$router.push("/listtpl/getlist/check/".concat(e,"/").concat(t.id))}},toEdit:function(t){if(isNaN(Number(this.$route.params.id)))this.$message.error("获取菜单数据出错");else{var e=Number(this.$route.params.id);this.$route.path.includes("cardPage/general")?this.$router.push("/cardPage/general/edit/".concat(e,"/").concat(t.id)):this.$router.push("/listtpl/getlist/edit/".concat(e,"/").concat(t.id))}},toDelete:function(t){var e=this;this.$confirm("删除后将不可恢复","确定要删除该数据吗？",{confirmButtonText:"继续",cancelButtonText:"取消",type:"warning"}).then(function(){var a={id:t.id,menuid:e.queryParams.menuid};e.tableLoading=!0,Object(o["a"])(a).then(function(t){e.tableLoading=!1,e.getTableColumns()}).catch(function(t){console.log(t),e.tableLoading=!1})}).catch(function(){console.log("cancel")})},openInputDialog:function(){this.columnsOriginal.length?this.showInputDialog=!0:this.$message.error("获取表格信息有误，无法导入")},submitInputFile:function(){var t=this;if(this.fileList.length){this.beforeInput=!1;var e={menuid:this.$route.params.id},a=Object(r["a"])(this.fileList[0],e);Object(o["e"])(a).then(function(e){e.Data.isok&&2===e.Data.isok?t.$message.error(e.Msg):e.Data.isok&&1===e.Data.isok&&(t.showInputDialog=!1,t.$message.success("导入成功"),t.getTableColumns()),t.cancelInputFile()}).catch(function(e){console.log(e),t.beforeInput=!0})}else this.$message.warning("请选择导入的文件")},cancelInputFile:function(){this.fileList.length=0,this.showInputDialog=!1,this.beforeInput=!0},handleRemove:function(t){var e=this.fileList.indexOf(t),a=this.fileList.slice();a.splice(e,1),this.fileList=a},handleUploadChange:function(t){var e=[t.file];this.fileList=e},beforeUpload:function(t){return!1},downloadTemp:function(){var t={menuid:this.$route.params.id,dataType:2,search:{}},e=this.getDownLoadURL(t),a="".concat("","/api/tpldata/export?").concat(e);window.open(a)},outPutTable:function(){if(this.columnsOriginal.length){var t={menuid:this.$route.params.id,dataType:1,search:JSON.parse(this.queryParams.search)},e=this.getDownLoadURL(t),a="".concat("","/api/tpldata/export?").concat(e);window.open(a)}else this.$message.error("获取表格信息有误，无法导出")},getDownLoadURL:function(t){var e="";Object.keys(t.search).forEach(function(a,i){e+=a,e+="kds000",e+=t.search[a],i<Object.keys(t.search).length-1&&(e+="kds001")}),t.search=e;var a=Object(s["a"])(Object.assign({},t));return a},changePage:function(t){this.queryParams.page=t,this.getTableColumns()}}},C=x,P=(a("dfc5"),Object(D["a"])(C,i,n,!1,null,"fd8626aa",null));e["default"]=P.exports},bed7:function(t,e,a){},dfc5:function(t,e,a){"use strict";var i=a("bed7"),n=a.n(i);n.a}}]);