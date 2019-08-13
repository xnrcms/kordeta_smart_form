(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["BaseTemplate"],{"55e0":function(t,e,a){"use strict";var r=a("b107"),o=a.n(r);o.a},"76ff":function(t,e,a){"use strict";a.r(e);var r=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("a-card",{attrs:{bordered:!1}},[a("SearchPart",{attrs:{searchConfig:t.searchItems},on:{searchFormSubmit:t.searchFormSubmit,searchFormReset:t.searchFormReset}}),a("k-btn",{staticClass:"mt20",attrs:{type:"primary",icon:"plus",code:"1"},on:{click:t.addItem}},[t._v("新建")]),a("k-btn",{staticClass:"mt20 ml20",attrs:{code:"5"},on:{click:t.openInputDialog}},[t._v("导入")]),a("k-btn",{staticClass:"mt20 ml20",attrs:{code:"6"},on:{click:t.outPutTable}},[t._v("导出")]),a("a-table",{ref:"table",staticClass:"mt20 table-row",attrs:{size:"default",rowKey:"id",loading:t.tableLoading,scroll:t.tableScrollConfig,columns:t.columns,dataSource:t.tableData,pagination:!1},scopedSlots:t._u([{key:"action",fn:function(e,r){return a("span",{},[[a("k-btn",{attrs:{type:"text",code:"2"},on:{click:function(e){return t.toDetail(r)}}},[t._v("查看")]),a("a-divider",{attrs:{type:"vertical"}}),a("k-btn",{attrs:{type:"text",code:"3"},on:{click:function(e){return t.toEdit(r)}}},[t._v("编辑")]),a("a-divider",{attrs:{type:"vertical"}}),a("k-btn",{attrs:{type:"text",code:"4"},on:{click:function(e){return t.toDelete(r)}}},[t._v("删除")])]],2)}}])}),a("a-pagination",{staticClass:"pagination",attrs:{total:t.total,pageSize:t.pageSize},on:{change:t.changePage},model:{value:t.queryParams.page,callback:function(e){t.$set(t.queryParams,"page",e)},expression:"queryParams.page"}})],1),a("a-modal",{attrs:{title:"批量导入",visible:t.showInputDialog},on:{ok:t.submitInputFile,cancel:t.cancelInputFile}},[a("div",{directives:[{name:"show",rawName:"v-show",value:t.beforeInput,expression:"beforeInput"}]},[t._v("\n            文件：\n            "),a("a-upload",{attrs:{name:"file",remove:t.handleRemove,beforeUpload:t.beforeUpload,fileList:t.fileList,multiple:!1,accept:".xls,.xlsx"},on:{change:t.handleUploadChange}},[a("a-button",[t._v("\n                    选择文件\n                ")])],1),a("p",{staticClass:"mt20"},[t._v("请按照模板文件填写完整信息，带*号的为必填项，缺少必填项的数据将无法被导入")]),a("a",{attrs:{href:"javascript:;"},on:{click:t.downloadTemp}},[t._v("下载模板")])],1),a("div",{directives:[{name:"show",rawName:"v-show",value:!t.beforeInput,expression:"!beforeInput"}]},[a("p",{staticClass:"tips-tit"},[t._v("正在导入，请稍候")]),a("a-spin",{attrs:{spinning:!t.beforeInput}},[a("div",{staticClass:"loading-part"})]),a("p",{staticClass:"tips-warning"},[t._v("请勿中途退出，退出后该文件所有数据将撤销导入")])],1)])],1)},o=[],i=(a("456d"),a("386d"),a("6762"),a("2fdb"),a("55dd"),a("ac6a"),a("7f7f"),a("c5f6"),a("ef5a")),n=a("ca00"),s=a("0e5c"),l=(a("8e6e"),a("7514"),a("bd86")),c=a("5efb"),u=a("2f62");function m(t,e){var a=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);e&&(r=r.filter(function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable})),a.push.apply(a,r)}return a}function d(t){for(var e=1;e<arguments.length;e++){var a=null!=arguments[e]?arguments[e]:{};e%2?m(a,!0).forEach(function(e){Object(l["a"])(t,e,a[e])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(a)):m(a).forEach(function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(a,e))})}return t}var p={1:"新增",2:"查看",3:"编辑",4:"删除",5:"导入",6:"导出"},f={props:{disabled:{type:Boolean,default:!1},ghost:{type:Boolean,default:!1},htmlType:{type:String},icon:{type:String},loading:{type:Boolean,default:!1},shape:{type:String},size:{type:String},type:{type:String},block:{type:Boolean,default:!1},code:{type:[String,Number]}},data:function(){return{id:this.$route.params.id,btnInfo:{}}},computed:d({},Object(u["e"])({permission:function(t){return t.user.permission}})),mounted:function(){var t=this,e=p[this.code];this.btnInfo=this.permission.find(function(a){return a.pid==t.id&&a.title==e})},methods:{handleClick:function(t){this.$emit("click")}},render:function(){var t=arguments[0],e=this.$props,a=this.$slots.default,r={click:this.handleClick};if(this.btnInfo){var o="text"!=this.type?c["a"]:"a";return t(o,{props:d({},e),on:d({},r)},[a])}}},h=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("el-form",{ref:"searchForm",attrs:{inline:!0,rules:t.formrules,model:t.formModels}},[t._l(t.searchConfig,function(e){return[a("SearchPartItem",{key:e.key,attrs:{formData:e,models:t.formModels},on:{"update:models":function(e){t.formModels=e}}})]}),t.searchConfig.length?a("el-form-item",[a("el-button",{attrs:{type:"primary",size:"small"},on:{click:t.onSubmit}},[t._v("查询")]),a("el-button",{attrs:{size:"small"},on:{click:t.reset}},[t._v("重置")])],1):t._e()],2)],1)},g=[],b=function(){var t=this,e=t.$createElement,a=t._self._c||e;return"input"===t.formData.type||"textarea"===t.formData.type?a("el-form-item",{attrs:{label:t.formData.name,prop:t.formData.model}},[a("el-input",{attrs:{placeholder:"请输入",size:"mini",clearable:""},model:{value:t.dataModel,callback:function(e){t.dataModel=e},expression:"dataModel"}})],1):"radio"===t.formData.type||"select"===t.formData.type&&!t.formData.options.multiple?a("el-form-item",{attrs:{label:t.formData.name}},[a("el-select",{attrs:{placeholder:"请选择",size:"mini",clearable:""},model:{value:t.dataModel,callback:function(e){t.dataModel=e},expression:"dataModel"}},t._l(t.formData.options.options,function(t,e){return a("el-option",{key:e,attrs:{value:t.value,label:t.value}})}),1)],1):"checkbox"===t.formData.type||"select"===t.formData.type&&t.formData.options.multiple?a("el-form-item",{attrs:{label:t.formData.name}},[a("el-select",{attrs:{placeholder:"请选择",size:"mini",multiple:"","collapse-tags":""},model:{value:t.dataModel,callback:function(e){t.dataModel=e},expression:"dataModel"}},t._l(t.formData.options.options,function(t,e){return a("el-option",{key:e,attrs:{value:t.value,label:t.value}})}),1)],1):"date"===t.formData.type?a("el-form-item",{attrs:{label:t.formData.name}},[a("el-date-picker",{attrs:{type:"daterange",align:"right","unlink-panels":"","range-separator":"至","start-placeholder":"开始日期","end-placeholder":"结束日期","value-format":"yyyy-MM-dd","picker-options":t.pickerOptions},model:{value:t.dataModel,callback:function(e){t.dataModel=e},expression:"dataModel"}})],1):t._e()},y=[];function v(t,e){var a=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);e&&(r=r.filter(function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable})),a.push.apply(a,r)}return a}function O(t){for(var e=1;e<arguments.length;e++){var a=null!=arguments[e]?arguments[e]:{};e%2?v(a,!0).forEach(function(e){Object(l["a"])(t,e,a[e])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(a)):v(a).forEach(function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(a,e))})}return t}var D={name:"SearchPartItem",props:{formData:{type:Object,default:function(){}},models:{type:Object,default:function(){}}},data:function(){return{dataModel:this.models[this.formData.model],pickerOptions:{shortcuts:[{text:"最近一周",onClick:function(t){var e=new Date,a=new Date;a.setTime(a.getTime()-6048e5),t.$emit("pick",[a,e])}},{text:"最近一个月",onClick:function(t){var e=new Date,a=new Date;a.setTime(a.getTime()-2592e6),t.$emit("pick",[a,e])}},{text:"最近三个月",onClick:function(t){var e=new Date,a=new Date;a.setTime(a.getTime()-7776e6),t.$emit("pick",[a,e])}}]}}},watch:{dataModel:{deep:!0,handler:function(t){this.models[this.formData.model]=t,this.$emit("update:models",O({},this.models,Object(l["a"])({},this.formData.model,t)))}},models:{deep:!0,handler:function(t){this.dataModel=this.models[this.formData.model]}}}},k=D,w=(a("8320"),a("2877")),$=Object(w["a"])(k,b,y,!1,null,"2074bafc",null),P=$.exports,j={name:"SearchPart",components:{SearchPartItem:P},props:{searchConfig:{type:Array,default:[]}},data:function(){return{formModels:{},formrules:{}}},watch:{searchConfig:function(){this.initFormModel()}},methods:{initFormModel:function(){var t=this;this.searchConfig.forEach(function(e){e.isSearch&&(Object.assign(t.formModels,Object(l["a"])({},e.model,"")),"textarea"!==e.type&&"input"!==e.type||Object.assign(t.formrules,Object(l["a"])({},e.model,[{max:20,message:"长度需为20个字符以内",trigger:"blur"}])))})},onSubmit:function(){var t=this;this.$refs["searchForm"].validate(function(e){if(!e)return console.log("error submit!!"),!1;var a={};t.searchConfig.forEach(function(e){Object.assign(a,Object(l["a"])({},e.model,t.formModels[e.model]))}),t.$emit("searchFormSubmit",a)})},reset:function(){var t=this;Object.keys(this.formModels).forEach(function(e){Array.isArray(t.formModels[e])?t.formModels[e]=[]:t.formModels[e]=""}),this.$emit("searchFormReset")}}},S=j,I=Object(w["a"])(S,h,g,!1,null,"9af1aa5e",null),x=I.exports,C={name:"base-template",components:{KBtn:f,SearchPart:x},data:function(){return{queryParams:{page:1,search:JSON.stringify({}),menuid:0},isFirstTime:!0,tableLoading:!1,tableScrollConfig:{x:2e3,y:300},columnsOriginal:[],columns:[],searchItems:[],tableData:[],total:0,pageSize:20,showInputDialog:!1,beforeInput:!0,fileList:[]}},created:function(){this.getTableColumns()},methods:{searchFormSubmit:function(t){this.queryParams.search=JSON.stringify(t),this.getTableColumns()},searchFormReset:function(){this.queryParams.search=JSON.stringify({}),this.queryParams.page=1,this.getTableColumns()},getTableColumns:function(){var t=this,e=this.$createElement;this.tableLoading=!0,isNaN(Number(this.$route.params.id))?this.$message.error("获取菜单数据出错"):(this.queryParams.menuid=Number(this.$route.params.id),Object(i["d"])(this.queryParams).then(function(a){t.total=a.Data.total,t.pageSize=a.Data.limit,t.columnsOriginal=JSON.parse(a.Data.tableHead),t.columns=t.columnsOriginal.map(function(t){return{title:t.name,dataIndex:t.model,width:150,align:"center",customRender:function(a){if("imgupload"===t.type){var r=[];return a.forEach(function(t){r.push(e("img",{attrs:{src:t,alt:""},class:"table-img"}))}),r}return"signature"!==t.type?e("a-tooltip",{attrs:{placement:"topLeft"}},[e("template",{slot:"title"},[a]),a]):a?e("img",{attrs:{src:a,alt:""},class:"table-img signature-img"}):void 0},sort:t.sort}}),t.columns.push({title:"操作",width:180,dataIndex:"action",scopedSlots:{customRender:"action"},align:"center",sort:-1}),t.columns.sort(t.getSort),t.tableScrollConfig.x=150*(t.columns.length-1)+180,t.isFirstTime&&(t.searchItems=t.columnsOriginal.filter(function(t){return t.isSearch}),t.searchItems.forEach(function(t){t.options.defaultValue=""})),a.Data.listData.length?t.tableData=JSON.parse(a.Data.listData):t.tableData=[],t.tableLoading=!1,t.isFirstTime=!1}).catch(function(e){console.log(e),t.tableLoading=!1}))},getSort:function(t,e){var a=t.sort,r=e.sort;return a||(a=0),r||(r=0),a<r?1:a>r?-1:0},addItem:function(){if(this.columnsOriginal.length)if(isNaN(Number(this.$route.params.id)))this.$message.error("获取菜单数据出错");else{var t=Number(this.$route.params.id);this.$route.path.includes("cardPage/general")?this.$router.push("/cardPage/general/add/".concat(t)):this.$router.push("/listtpl/getlist/add/".concat(t))}else this.$message.error("获取表格信息有误，无法新建")},toDetail:function(t){if(isNaN(Number(this.$route.params.id)))this.$message.error("获取菜单数据出错");else{var e=Number(this.$route.params.id);this.$route.path.includes("cardPage/general")?this.$router.push("/cardPage/general/check/".concat(e,"/").concat(t.id)):this.$router.push("/listtpl/getlist/check/".concat(e,"/").concat(t.id))}},toEdit:function(t){if(isNaN(Number(this.$route.params.id)))this.$message.error("获取菜单数据出错");else{var e=Number(this.$route.params.id);this.$route.path.includes("cardPage/general")?this.$router.push("/cardPage/general/edit/".concat(e,"/").concat(t.id)):this.$router.push("/listtpl/getlist/edit/".concat(e,"/").concat(t.id))}},toDelete:function(t){var e=this;this.$confirm("删除后将不可恢复","确定要删除该数据吗？",{confirmButtonText:"继续",cancelButtonText:"取消",type:"warning"}).then(function(){var a={id:t.id,menuid:e.queryParams.menuid};e.tableLoading=!0,Object(i["a"])(a).then(function(t){e.tableLoading=!1,e.getTableColumns()}).catch(function(t){console.log(t),e.tableLoading=!1})}).catch(function(){console.log("cancel")})},openInputDialog:function(){this.columnsOriginal.length?this.showInputDialog=!0:this.$message.error("获取表格信息有误，无法导入")},submitInputFile:function(){var t=this;if(this.fileList.length){this.beforeInput=!1;var e={menuid:this.$route.params.id},a=Object(s["a"])(this.fileList[0],e);Object(i["g"])(a).then(function(e){e.Data.isok&&2===e.Data.isok?t.$message.error(e.Msg):e.Data.isok&&1===e.Data.isok&&(t.showInputDialog=!1,t.$message.success("导入成功"),t.getTableColumns()),t.cancelInputFile()}).catch(function(e){console.log(e),t.beforeInput=!0})}else this.$message.warning("请选择导入的文件")},cancelInputFile:function(){this.fileList.length=0,this.showInputDialog=!1,this.beforeInput=!0},handleRemove:function(t){var e=this.fileList.indexOf(t),a=this.fileList.slice();a.splice(e,1),this.fileList=a},handleUploadChange:function(t){var e=[t.file];this.fileList=e},beforeUpload:function(t){return!1},downloadTemp:function(){var t={menuid:this.$route.params.id,dataType:2,search:{}},e=this.getDownLoadURL(t),a="".concat("","/api/tpldata/export?").concat(e);window.open(a)},outPutTable:function(){if(this.columnsOriginal.length){var t={menuid:this.$route.params.id,dataType:1,search:JSON.parse(this.queryParams.search)},e=this.getDownLoadURL(t),a="".concat("","/api/tpldata/export?").concat(e);window.open(a)}else this.$message.error("获取表格信息有误，无法导出")},getDownLoadURL:function(t){var e="";Object.keys(t.search).forEach(function(a,r){e+=a,e+="kds000",e+=t.search[a],r<Object.keys(t.search).length-1&&(e+="kds001")}),t.search=e;var a=Object(n["a"])(Object.assign({},t));return a},changePage:function(t){this.queryParams.page=t,this.getTableColumns()}}},M=C,T=(a("55e0"),Object(w["a"])(M,r,o,!1,null,"676b6bb9",null));e["default"]=T.exports},8320:function(t,e,a){"use strict";var r=a("d2c6"),o=a.n(r);o.a},b107:function(t,e,a){},d2c6:function(t,e,a){}}]);