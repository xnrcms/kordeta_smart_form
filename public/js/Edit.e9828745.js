(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["Edit"],{"22d4":function(e,a,t){},"49a8":function(e,a,t){"use strict";var s=t("22d4"),n=t.n(s);n.a},"7fdb":function(e,a,t){"use strict";t.r(a);var s=function(){var e=this,a=e.$createElement,t=e._self._c||a;return t("a-card",{attrs:{"body-style":{padding:"24px 32px"},bordered:!1}},[t("a-form",{attrs:{form:e.form},on:{submit:e.handleSubmit}},[t("a-form-item",{attrs:{label:"分组名称",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["name",{rules:[{required:!0,message:"请输入名称"},{max:20,message:"请输入20个字以内"}],initialValue:e.formVal.title}],expression:"[\n          'name',\n          {rules: [{ required: true, message: '请输入名称' },\n          { max:20, message: '请输入20个字以内'}],\n          initialValue: formVal.title}\n        ]"}],attrs:{placeholder:"20个字以内"}})],1),t("a-form-item",{attrs:{label:"备注",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-textarea",{directives:[{name:"decorator",rawName:"v-decorator",value:["description",{rules:[{required:!1,message:"请输入角色备注"},{max:50,message:"请输入50个字以内"}],initialValue:e.formVal.description}],expression:"[\n          'description',\n          {rules: [{ required: false, message: '请输入角色备注' },\n          { max:50, message: '请输入50个字以内'}\n          ],initialValue: formVal.description}\n        ]"}],attrs:{rows:"4",placeholder:"请输入角色备注，50个字以内"}})],1),t("a-form-item",{attrs:{label:"是否启用",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-checkbox",{attrs:{checked:e.formVal.status},on:{change:e.handleStatus}},[e._v("\n              启用\n          ")]),t("a-button",{staticClass:"mr",on:{click:function(a){return e.$refs.addMember.add()}}},[e._v("添加组员")]),e._l(e.memberNames,function(a,s){return[t("a-tag",{key:a,attrs:{closable:s>-1,afterClose:function(){return e.handleClose(a)}}},[e._v("\n                  "+e._s(a)+"\n              ")])]})],2),t("a-form-item",{staticStyle:{"text-align":"center"},attrs:{wrapperCol:{span:24}}},[t("a-button",{attrs:{htmlType:"submit",type:"primary"}},[e._v("提交")]),t("a-button",{staticStyle:{"margin-left":"8px"},on:{click:e.cancel}},[e._v("取消")])],1)],1),t("add-member",{ref:"addMember",on:{ok:e.handleOk}})],1)},n=[],r=(t("6762"),t("2fdb"),t("ac6a"),t("28a5"),t("7f7f"),t("b4f6")),i=t("b775"),o={name:"BaseForm",components:{AddMember:r["a"]},data:function(){return{description:"表单页用于向用户收集或验证信息，基础表单常见于数据项较少的表单场景。",value:1,form:this.$form.createForm(this),formVal:{title:"",description:"",status:""},id:"",memberNames:[],memberIds:[],allMemberData:[]}},beforeRouteEnter:function(e,a,t){t(function(e){e.getParams()})},methods:{getParams:function(){var e=this;this.id=this.$route.params.id,i["b"].post("/api/user_group/detailData",{id:this.id}).then(function(a){if("200"===a.Code){if(e.formVal=a.Data,e.memberNames=[],e.memberIds=[],e.allMemberData=[],a.Data.guser){var t=a.Data.guser;t=JSON.parse(t),e.allMemberData=t;for(var s=0;s<t.length;s++)e.memberNames.push(t[s].username),e.memberIds.push(t[s].id)}"1"===a.Data.status?e.formVal.status=!0:e.formVal.status=!1}},function(e){console.log(e)}).catch(function(e){console.log(e)})},handleStatus:function(){this.formVal.status=!this.formVal.status},handleSubmit:function(e){var a=this;e.preventDefault(),this.form.validateFields(function(e,t){if(!e){console.log("Received values of form: ",t),a.formVal.status?t.status="1":t.status="2";var s=a.memberIds.join(",");i["b"].post("/api/user_group/saveData",{title:t.name,id:a.id,status:t.status,description:t.description,gusers:s}).then(function(e){"200"===e.Code&&(a.$message.success("修改成功"),a.$router.push({name:"Manage"}))},function(e){console.log(e)}).catch(function(e){console.log(e)})}})},handleClose:function(e){for(var a=this.allMemberData,t="",s=0;s<a.length;s++)a[s].username==e&&(t=a[s].id);var n=this.memberIds.indexOf(t);this.memberIds.splice(n,1);var r=this.memberNames.filter(function(a){return a!==e});this.memberNames=r,console.log(this.memberNames)},cancel:function(){var e=this;this.$confirm("是否放弃编辑?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){e.$router.push({name:"Manage"})})},handleOk:function(e){var a=this,t=e.id.split(","),s=e.member_name.split(",");t.forEach(function(e,t){a.memberIds.includes(e)||(a.memberIds.push(e),a.allMemberData.push({member_name:s[t],id:e}),a.memberNames.push(s[t]))})}}},m=o,l=(t("49a8"),t("2877")),u=Object(l["a"])(m,s,n,!1,null,"1c50eb5c",null);a["default"]=u.exports},b4f6:function(e,a,t){"use strict";var s=function(){var e=this,a=e.$createElement,t=e._self._c||a;return t("a-modal",{attrs:{title:"添加组员",width:640,visible:e.visible,confirmLoading:e.confirmLoading},on:{ok:e.handleSubmit,cancel:e.handleCancel}},[t("a-spin",{attrs:{spinning:e.confirmLoading}},[t("a-form",{attrs:{form:e.form}},[t("a-form-item",{attrs:{label:"账号名",labelCol:e.labelCol,wrapperCol:e.wrapperCol}},[t("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["member_name",{initialValue:e.memberName}],expression:"[\n              'member_name',\n              {initialValue: memberName}\n            ]"}],attrs:{type:"text",placeholder:"请输入"}}),t("div",[e._v('如需添加多条，请以英文逗号","分割')])],1)],1)],1)],1)},n=[],r=t("db72"),i=t("2f62"),o=t("b775"),m={data:function(){return{labelCol:{xs:{span:24},sm:{span:7}},wrapperCol:{xs:{span:24},sm:{span:13}},visible:!1,confirmLoading:!1,form:this.$form.createForm(this),memberName:"",memberId:""}},methods:Object(r["a"])({},Object(i["b"])(["ChangePassword"]),{checkAccount:function(e,a,t){var s=this;o["b"].post("/api/user_group/bindUser",{username:a}).then(function(e){"200"===e.Code?(s.memberId=e.Data.id,t()):t(e.Msg)},function(e){console.log(e)}).catch(function(e){console.log(e)})},add:function(){this.visible=!0},handleSubmit:function(e){var a=this;e.preventDefault();var t=this.form.getFieldValue("member_name");String(t)&&o["b"].post("/api/user_group/bindUser",{username:t}).then(function(e){if("200"===e.Code){a.visible=!1,a.confirmLoading=!1;var s={member_name:t,id:e.Data.id};a.$emit("ok",s),a.$message.success("添加成功")}})},handleCancel:function(){this.handleReset(),this.visible=!1},handleReset:function(){this.memberName="",this.memberId="",this.form.resetFields()}})},l=m,u=t("2877"),c=Object(u["a"])(l,s,n,!1,null,null,null);a["a"]=c.exports}}]);