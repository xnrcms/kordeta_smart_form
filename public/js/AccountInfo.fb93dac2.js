(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["AccountInfo"],{"27a6":function(a,e,t){},"8a7c":function(a,e,t){"use strict";var s=t("27a6"),n=t.n(s);n.a},"97a0":function(a,e,t){"use strict";t.r(e);var s=function(){var a=this,e=a.$createElement,t=a._self._c||e;return t("div",{staticClass:"account-info"},[t("div",{staticClass:"account-info-inner"},[t("div",{staticClass:"inner-body"},[t("a-card",{attrs:{"body-style":{padding:"24px 32px"},bordered:!1}},[t("a-form",{attrs:{form:a.form},on:{submit:a.handleSubmit}},[t("a-form-item",{attrs:{label:"账号名称",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["username",{rules:[{required:!0,pattern:/^\w{2,10}$/,message:"2-10 个字符, 只能输入数字、字母和下划线"}],initialValue:a.formVal.username}],expression:"[\n                            'username',\n                            {\n                                rules: [{\n                                    required: true,\n                                    pattern: /^\\w{2,10}$/,\n                                    message: '2-10 个字符, 只能输入数字、字母和下划线'\n                                }],\n                                initialValue: formVal.username\n                            }\n                        ]"}],attrs:{name:"username",placeholder:"2-10 个字符, 只能输入数字、字母和下划线"}})],1),t("a-form-item",{attrs:{label:"密码",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["password",{rules:[{required:!a.isDisabled,pattern:/^(?=.*\d)(?=.*[a-z]).{6,20}$/,message:"6-20 个字符, 由数字和字母组成"}],initialValue:a.formVal.password}],expression:"[\n                                'password',\n                                {\n                                    rules: [{\n                                        required: !isDisabled,\n                                        pattern: /^(?=.*\\d)(?=.*[a-z]).{6,20}$/,\n                                        message: '6-20 个字符, 由数字和字母组成',\n                                    }],\n                                    initialValue: formVal.password\n                                }\n                            ]"}],attrs:{disabled:a.isDisabled,name:"password",placeholder:"6-20 个字符, 由数字和字母组成"}})],1),t("a-form-item",{attrs:{label:"账号类型",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-select",{directives:[{name:"decorator",rawName:"v-decorator",value:["group_id",{rules:[{required:!0,message:"必选"}],initialValue:a.formVal.group_id}],expression:"[\n                                'group_id',\n                                {rules: [{ required: true, message: '必选' }],\n                                    initialValue: formVal.group_id}\n                            ]"}],attrs:{placeholder:"请选择"}},[t("a-select-option",{attrs:{value:1}},[a._v("KDS 管理员")]),t("a-select-option",{attrs:{value:2}},[a._v("机构管理员")]),t("a-select-option",{attrs:{value:3}},[a._v("普通用户")])],1)],1),t("a-form-item",{attrs:{label:"手机号码",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["mobile",{rules:[{pattern:/^1\d{10}$/,message:"请输入正确手机号码"}],initialValue:a.formVal.mobile}],expression:"[\n                                'mobile',\n                                {\n                                    rules: [{\n                                        pattern: /^1\\d{10}$/,\n                                        message: '请输入正确手机号码',\n                                    }],\n                                    initialValue: formVal.mobile\n                                }\n                            ]"}],attrs:{name:"mobile",placeholder:"可用于登录"}})],1),t("a-form-item",{attrs:{label:"邮箱地址",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["email",{rules:[{pattern:/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/,message:"请输入正确邮箱地址"},{max:50,message:"邮箱长度需在50字以内"},{}],initialValue:a.formVal.email}],expression:"[\n                                'email',\n                                {\n                                    rules: [{\n                                        pattern: /^([a-z0-9_\\.-]+)@([\\da-z\\.-]+)\\.([a-z\\.]{2,6})$/,\n                                        message: '请输入正确邮箱地址',\n                                    },{\n                                        max: 50,\n                                        message: '邮箱长度需在50字以内'\n                                    },{\n                                    }],\n                                    initialValue: formVal.email\n                                }\n                            ]"}],attrs:{name:"email",placeholder:"可用于登录"}})],1),t("a-form-item",{attrs:{label:"是否启用",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-checkbox",{attrs:{checked:a.formVal.status},on:{change:a.handleStatus}},[a._v("\n                            启用\n                        ")])],1),t("a-form-item",{attrs:{label:"备注",labelCol:{lg:{span:7},sm:{span:7}},wrapperCol:{lg:{span:10},sm:{span:17}}}},[t("a-textarea",{directives:[{name:"decorator",rawName:"v-decorator",value:["mark",{rules:[{pattern:/^.{0,50}$/,message:"50 字以内"}],initialValue:a.formVal.mark}],expression:"[\n                                'mark',\n                                {\n                                    rules: [{\n                                        pattern: /^.{0,50}$/,\n                                        message: '50 字以内',\n                                    }],\n                                    initialValue: formVal.mark\n                                }\n                            ]"}],attrs:{rows:"4",placeholder:"50 字以内",name:"mark"}})],1),t("a-form-item",{staticStyle:{"text-align":"center"},attrs:{wrapperCol:{span:24}}},[t("a-button",{attrs:{htmlType:"submit",type:"primary"}},[a._v("提交")]),t("a-button",{staticStyle:{"margin-left":"8px"},on:{click:a.back}},[a._v("取消")])],1)],1)],1)],1)])])},n=[],r=(t("7f7f"),t("b775")),o={name:"AccountInfo",components:{},props:{},data:function(){return{isDisabled:!1,pageType:"add",description:"表单页用于向用户收集或验证信息，基础表单常见于数据项较少的表单场景。",value:1,form:this.$form.createForm(this),formVal:{username:"",password:"",group_id:3,mobile:"",email:"",status:!0,mark:""}}},computed:{},watch:{},beforeRouteEnter:function(a,e,t){t(function(e){e.getType(a.name),e.getUserDet()})},beforeRouteLeave:function(a,e,t){this.formVal={username:"",password:"",group_id:3,mobile:"",email:"",status:!0,mark:""},this.form.resetFields(),this.$emit("change",null),t()},methods:{handleSubmit:function(a){var e=this;a.preventDefault(),this.form.validateFields(function(a,t){if(!a){var s=t;s.status||(s.status=e.formVal.status),s.status=s.status?"1":"2",s.repeatpwd=s.password,"accountInfoAdd"!==e.$route.name?(s.password="",s.repeatpwd="",s.id=e.$route.params.id):s.id="0",r["b"].post("/api/user_group/glistData",{id:s.group_id}).then(function(a){if("200"!==a.Code)return e.$message.error("获取用户组列表失败"),!1},function(a){return console.log(a),!1}).catch(function(a){console.log(a)}),r["b"].post("/api/User/saveData",s).then(function(a){"200"===a.Code&&(e.$message.success("提交成功"),e.$router.go(-1))},function(a){console.log(a)}).catch(function(a){console.log(a)})}})},getUserDet:function(){var a=this;"accountInfoAdd"!==this.$route.name&&r["b"].post("/api/User/userDetail",{id:this.$route.params.id}).then(function(e){"200"===e.Code&&(a.formVal=e.Data,a.formVal.status="1"===a.formVal.status)},function(a){console.log(a)}).catch(function(a){console.log(a)})},back:function(){var a=this;this.$confirm("是否放弃当前操作?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){a.$router.go(-1)})},getType:function(a){"accountInfoAdd"===a?(this.isDisabled=!1,this.pageType="add"):(this.isDisabled=!0,this.pageType="edit")},handleStatus:function(){this.formVal.status=!this.formVal.status}}},l=o,i=(t("8a7c"),t("2877")),m=Object(i["a"])(l,s,n,!1,null,"0fbaaa54",null);e["default"]=m.exports}}]);