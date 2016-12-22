define(["services/suluautomation/task-manager","text!./form.html"],function(a,b){"use strict";return{defaults:{options:{id:null,entityClass:null,saveCallback:function(a){}},templates:{form:b},translations:{task:"sulu_automation.task",handlerClass:"sulu_automation.task.name",time:"sulu_automation.task.schedule.time",date:"sulu_automation.task.schedule.date",choose:"sulu_automation.task.choose",remove:"public.delete"}},initialize:function(){this.$container=$("<div/>"),this.$formContainer=$(this.templates.form({translations:this.translations,entityClass:this.options.entityClass})),this.$el.append(this.$container);var a=[];this.options.saveCallback&&a.push({type:"ok",align:"right"}),this.options.removeCallback&&a.push({text:this.translations.remove,align:"center",classes:"just-text",callback:this.removeTask.bind(this)}),a.push({type:"cancel",align:"left"}),this.sandbox.start([{name:"overlay@husky",options:{el:this.$container,instanceName:"task-overlay",openOnStart:!0,removeOnClose:!0,skin:"medium",slides:[{title:this.translations.task,buttons:a,data:this.$formContainer,okCallback:this.save.bind(this)}]}}]),this.sandbox.once("husky.overlay.task-overlay.opened",function(){this.sandbox.form.create(this.$formContainer).initialized.then(function(){this.sandbox.form.setData(this.$formContainer,this.decodeData(this.data)).then(function(){this.sandbox.start(this.$formContainer)}.bind(this))}.bind(this))}.bind(this))},decodeData:function(a){var b=a.schedule?new Date(a.schedule):null;return{handlerClass:a.handlerClass,date:b?Globalize.format(b,"yyyy'-'MM'-'dd"):"",time:b?Globalize.format(b,"HH':'mm':'ss"):""}},encodeData:function(a){return{id:this.options.id,handlerClass:a.handlerClass,schedule:Globalize.format(new Date(a.date+" "+a.time),"yyyy'-'MM'-'dd'T'HH':'mm':'ssz'00'")}},removeTask:function(){return this.sandbox.emit("husky.overlay.task-overlay.show-loader"),this.options.removeCallback().then(function(){this.sandbox.stop()}.bind(this)).fail(function(){this.sandbox.emit("husky.overlay.task-overlay.hide-loader")}.bind(this)),!1},save:function(){if(!this.sandbox.form.validate(this.$formContainer))return!1;var a=this.encodeData(this.sandbox.form.getData(this.$formContainer));return this.sandbox.emit("husky.overlay.task-overlay.show-loader"),this.options.saveCallback(a).then(function(){this.sandbox.stop()}.bind(this)).fail(function(){this.sandbox.emit("husky.overlay.task-overlay.hide-loader")}.bind(this)),!1},loadComponentData:function(){return this.options.id?a.load(this.options.id):{}}}});