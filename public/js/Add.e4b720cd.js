(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["Add"],{"99ce":function(e,a,t){"use strict";t.r(a);var n=function(){var e=this,a=e.$createElement,t=e._self._c||a;return t("a-card",{attrs:{"body-style":{padding:"24px 32px"},bordered:!1}},[t("a-form",{attrs:{form:e.form,autocomplete:"off"},on:{submit:e.handleSubmit}},[t("a-form-item",{attrs:{label:"分组名称",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["name",{rules:[{required:!0,message:"请输入名称"},{max:20,message:"请输入20个字以内"}],initialValue:e.formVal.title}],expression:"[\n          'name',\n          {rules: [{ required: true, message: '请输入名称' },\n          { max:20, message: '请输入20个字以内'}],\n          initialValue: formVal.title}\n        ]"}],attrs:{placeholder:"20个字以内"}})],1),t("a-form-item",{attrs:{label:"备注",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-textarea",{directives:[{name:"decorator",rawName:"v-decorator",value:["description",{rules:[{required:!1,message:"请输入角色备注"},{max:50,message:"请输入50个字以内"}],initialValue:e.formVal.description}],expression:"[\n          'description',\n          {rules: [{ required: false, message: '请输入角色备注' },\n          { max:50, message: '请输入50个字以内'}\n          ],initialValue: formVal.description}\n        ]"}],attrs:{rows:"4",placeholder:"请输入角色备注，50个字以内"}})],1),t("a-form-item",{attrs:{label:"是否启用",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-checkbox",{attrs:{checked:e.formVal.status},on:{change:e.handleStatus}},[e._v("\n              启用\n          ")]),t("a-button",{staticClass:"mr",on:{click:function(a){return e.$refs.addMember.add()}}},[e._v("添加组员")]),e._l(e.memberNames,function(a,n){return[t("a-tag",{key:a,attrs:{closable:n>-1,afterClose:function(){return e.handleClose(a)}}},[e._v("\n                  "+e._s(a)+"\n              ")])]})],2),t("a-form-item",{staticStyle:{"text-align":"center"},attrs:{wrapperCol:{span:24}}},[t("a-button",{attrs:{htmlType:"submit",type:"primary"}},[e._v("提交")]),t("a-button",{staticStyle:{"margin-left":"8px"},on:{click:e.cancel}},[e._v("取消")])],1)],1),t("add-member",{ref:"addMember",on:{ok:e.handleOk}})],1)},s=[],r=t("75fc"),i=(t("7f7f"),t("b4f6")),o=t("b775"),m={name:"BaseForm",components:{AddMember:i["a"]},data:function(){return{description:"表单页用于向用户收集或验证信息，基础表单常见于数据项较少的表单场景。",value:1,form:this.$form.createForm(this),formVal:{title:"",description:"",status:!0},id:"",memberNames:[],memberIds:[],allMemberData:[]}},methods:{handleStatus:function(){this.formVal.status=!this.formVal.status},handleSubmit:function(e){var a=this;e.preventDefault(),this.form.validateFields(function(e,t){if(!e){console.log("Received values of form: ",t),a.formVal.status?t.status="1":t.status="2";var n=a.memberIds.join(",");o["b"].post("/api/user_group/saveData",{title:t.name,id:a.id,status:t.status,description:t.description,gusers:n}).then(function(e){"200"===e.Code&&(a.$message.success("修改成功"),a.$router.push({name:"Manage"}))},function(e){console.log(e)}).catch(function(e){console.log(e)})}})},handleClose:function(e){for(var a=this.allMemberData,t="",n=0;n<a.length;n++)a[n].member_name===e&&(t=a[n].id);var s=this.memberIds.indexOf(t);this.memberIds.splice(s,1);var r=this.memberNames.filter(function(a){return a!==e});this.memberNames=r},cancel:function(){var e=this;this.$confirm("是否放弃新增?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){e.$router.push({name:"Manage"})})},handleOk:function(e){this.memberIds.push(e.id),this.allMemberData.push(e);var a=e.member_name,t=this.memberNames;a&&-1===t.indexOf(a)&&(t=[].concat(Object(r["a"])(t),[a])),Object.assign(this,{memberNames:t,inputVisible:!1,inputValue:""})}}},l=m,c=(t("ba68"),t("2877")),u=Object(c["a"])(l,n,s,!1,null,"efb1836c",null);a["default"]=u.exports},b4f6:function(e,a,t){"use strict";var n=function(){var e=this,a=e.$createElement,t=e._self._c||a;return t("a-modal",{attrs:{title:"添加组员",width:640,visible:e.visible,confirmLoading:e.confirmLoading},on:{ok:e.handleSubmit,cancel:e.handleCancel}},[t("a-spin",{attrs:{spinning:e.confirmLoading}},[t("a-form",{attrs:{form:e.form}},[t("a-form-item",{attrs:{label:"账号名",labelCol:e.labelCol,wrapperCol:e.wrapperCol}},[t("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["member_name",{initialValue:e.memberName}],expression:"[\n              'member_name',\n              {initialValue: memberName}\n            ]"}],attrs:{type:"text",placeholder:"请输入"}}),t("div",[e._v('如需添加多条，请以英文逗号","分割')])],1)],1)],1)],1)},s=[],r=t("cebc"),i=t("2f62"),o=t("b775"),m={data:function(){return{labelCol:{xs:{span:24},sm:{span:7}},wrapperCol:{xs:{span:24},sm:{span:13}},visible:!1,confirmLoading:!1,form:this.$form.createForm(this),memberName:"",memberId:""}},methods:Object(r["a"])({},Object(i["b"])(["ChangePassword"]),{checkAccount:function(e,a,t){var n=this;o["b"].post("/api/user_group/bindUser",{username:a}).then(function(e){"200"===e.Code?(n.memberId=e.Data.id,t()):t(e.Msg)},function(e){console.log(e)}).catch(function(e){console.log(e)})},add:function(){this.visible=!0},handleSubmit:function(e){var a=this;e.preventDefault();var t=this.form.getFieldValue("member_name");String(t)&&o["b"].post("/api/user_group/bindUser",{username:t}).then(function(e){if("200"===e.Code){a.visible=!1,a.confirmLoading=!1;var n={member_name:t,id:e.Data.id};a.$emit("ok",n),a.$message.success("添加成功")}})},handleCancel:function(){this.handleReset(),this.visible=!1},handleReset:function(){this.memberName="",this.memberId="",this.form.resetFields()}})},l=m,c=t("2877"),u=Object(c["a"])(l,n,s,!1,null,null,null);a["a"]=u.exports},ba68:function(e,a,t){"use strict";var n=t("df23"),s=t.n(n);s.a},df23:function(e,a,t){}}]);