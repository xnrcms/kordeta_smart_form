(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["FormItem"],{7152:function(a,e,t){"use strict";var i=t("f833"),n=t.n(i);n.a},e6f5:function(a,e,t){"use strict";t.r(e);var i=function(){var a=this,e=a.$createElement,t=a._self._c||e;return t("div",[t("a-spin",{attrs:{spinning:a.pageLoading}},[t("a-card",{attrs:{bordered:!1}},[t("h5",{staticClass:"page-title"},[a._v(a._s(a.pageTitle))]),t("fm-generate-form",{ref:"generateForm",attrs:{readOnly:a.readOnly,data:a.jsonData,formId:a.formId,linkageArr:a.linkageArr}}),t("div",{staticClass:"bottom-btn"},["check"!==a.pageType?t("a-button",{staticClass:"mr20 wide-btn",attrs:{type:"primary"},on:{click:a.submit}},[a._v("提交")]):a._e(),t("a-button",{staticClass:"wide-btn",on:{click:a.back}},[a._v("返回")])],1)],1)],1)],1)},n=[],o=(t("28a5"),t("4f7f"),t("5df3"),t("1c4c"),t("456d"),t("ac6a"),t("c5f6"),t("7f7f"),t("ef5a")),s={name:"handleFormItem",data:function(){return{menuid:0,pageType:"",pageTitle:"",pageLoading:!1,readOnly:!1,jsonData:{},formId:"",linkageArr:[]}},created:function(){this.initPage()},methods:{initPage:function(){switch(this.$route.name){case"addBaseFormItem":case"addBaseFormItemCard":this.pageType="add",this.pageTitle="新建",this.readOnly=!1,this.getPageConfig();break;case"editBaseFormItem":case"editBaseFormItemCard":this.pageType="edit",this.pageTitle="编辑",this.readOnly=!1,this.getPageConfig();break;case"checkBaseFormItem":case"checkBaseFormItemCard":this.pageType="check",this.pageTitle="查看",this.readOnly=!0,this.getPageConfig();break}},getPageConfig:function(){var a=this;this.menuid=Number(this.$route.params.menuid),this.pageLoading=!0;var e={menuid:this.menuid,id:0};"add"!==this.pageType&&(e.id=Number(this.$route.params.id)),Object(o["c"])(e).then(function(e){a.jsonData=JSON.parse(e.Data.formInfo);var t=e.Data.dataInfo?JSON.parse(e.Data.dataInfo):"";if(a.formId=e.Data.formId,e.Data.linkageInfo){var i=JSON.parse(e.Data.linkageInfo),n=[];Object.keys(i).forEach(function(a){n.push(i[a].field1)}),a.linkageArr=Array.from(new Set(n))}""!==t?(a.jsonData.list&&a.jsonData.list.length&&a.jsonData.list.forEach(function(a){for(var e=0,i=Object.keys(t);e<i.length;e++){var n=i[e];if(a.model===n){"imgupload"===a.model.split("_")[0]?a.options.defaultValue=t[n]:"checkbox"===a.model.split("_")[0]||a.options.multiple?a.options.defaultValue=t[n].split(","):a.options.defaultValue=t[n];break}}}),a.pageLoading=!1):a.pageLoading=!1}).catch(function(e){console.log(e),a.pageLoading=!1})},submit:function(){var a=this;this.$refs.generateForm.getData().then(function(e){for(var t=0,i=Object.keys(e);t<i.length;t++){var n=i[t];if("imgupload"===n.split("_")[0])for(var s=0;s<a.jsonData.list.length;s++)if(n===a.jsonData.list[s].model&&e[n].length<a.jsonData.list[s].options.min){a.$message.warning("“".concat(a.jsonData.list[s].name,"”项需要至少上传").concat(a.jsonData.list[s].options.min,"张图片"));break}}var r=JSON.parse(JSON.stringify(e));Object.keys(r).forEach(function(a){if("checkbox"===a.split("_")[0]&&(r[a]=r[a].join()),"select"===a.split("_")[0]&&Array.isArray(r[a])&&(r[a]=r[a].join()),"imgupload"===a.split("_")[0]){var e=[];r[a].forEach(function(a){e.push(a.id)}),r[a]=e.join()}}),a.pageLoading=!0,"add"===a.pageType?Object.assign(r,{id:0}):"edit"===a.pageType&&Object.assign(r,{id:Number(a.$route.params.id)});var c={menuid:a.menuid,formData:JSON.stringify(r)};Object(o["h"])(c).then(function(e){a.pageLoading=!1,a.$router.go(-1)}).catch(function(e){console.log(e),a.pageLoading=!1})}).catch(function(a){console.log(a)})},back:function(){this.$router.go(-1)}}},r=s,c=(t("7152"),t("2877")),d=Object(c["a"])(r,i,n,!1,null,"5f68d633",null);e["default"]=d.exports},f833:function(a,e,t){}}]);