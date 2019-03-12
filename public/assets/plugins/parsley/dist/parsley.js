var _slice=Array.prototype.slice;function _toConsumableArray(t){if(Array.isArray(t)){for(var e=0,i=Array(t.length);e<t.length;e++)i[e]=t[e];return i}return Array.from(t)}!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e(require("jquery")):"function"==typeof define&&define.amd?define(["jquery"],e):t.parsley=e(t.jQuery)}(this,function(t){"use strict";var e,i=1,r={},n={attr:function(t,e,i){var r,n,s,a=new RegExp("^"+e,"i");if(void 0===i)i={};else for(r in i)i.hasOwnProperty(r)&&delete i[r];if(void 0===t||void 0===t[0])return i;for(r=(s=t[0].attributes).length;r--;)(n=s[r])&&n.specified&&a.test(n.name)&&(i[this.camelize(n.name.slice(e.length))]=this.deserializeValue(n.value));return i},checkAttr:function(t,e,i){return t.is("["+e+i+"]")},setAttr:function(t,e,i,r){t[0].setAttribute(this.dasherize(e+i),String(r))},generateID:function(){return""+i++},deserializeValue:function(e){var i;try{return e?"true"==e||"false"!=e&&("null"==e?null:isNaN(i=Number(e))?/^[\[\{]/.test(e)?t.parseJSON(e):e:i):e}catch(t){return e}},camelize:function(t){return t.replace(/-+(.)?/g,function(t,e){return e?e.toUpperCase():""})},dasherize:function(t){return t.replace(/::/g,"/").replace(/([A-Z]+)([A-Z][a-z])/g,"$1_$2").replace(/([a-z\d])([A-Z])/g,"$1_$2").replace(/_/g,"-").toLowerCase()},warn:function(){var t;window.console&&"function"==typeof window.console.warn&&(t=window.console).warn.apply(t,arguments)},warnOnce:function(t){r[t]||(r[t]=!0,this.warn.apply(this,arguments))},_resetWarnings:function(){r={}},trimString:function(t){return t.replace(/^\s+|\s+$/g,"")},namespaceEvents:function(e,i){return(e=this.trimString(e||"").split(/\s+/))[0]?t.map(e,function(t){return t+"."+i}).join(" "):""},difference:function(e,i){var r=[];return t.each(e,function(t,e){-1==i.indexOf(e)&&r.push(e)}),r},all:function(e){return t.when.apply(t,_toConsumableArray(e).concat([42,42]))},objectCreate:Object.create||(e=function(){},function(t){if(arguments.length>1)throw Error("Second argument not supported");if("object"!=typeof t)throw TypeError("Argument must be an object");e.prototype=t;var i=new e;return e.prototype=null,i}),_SubmitSelector:'input[type="submit"], button:submit'},s={namespace:"data-parsley-",inputs:"input, textarea, select",excluded:"input[type=button], input[type=submit], input[type=reset], input[type=hidden]",priorityEnabled:!0,multiple:null,group:null,uiEnabled:!0,validationThreshold:3,focus:"first",trigger:!1,triggerAfterFailure:"input",errorClass:"parsley-error",successClass:"parsley-success",classHandler:function(t){},errorsContainer:function(t){},errorsWrapper:'<ul class="parsley-errors-list"></ul>',errorTemplate:"<li></li>"},a=function(){this.__id__=n.generateID()};a.prototype={asyncSupport:!0,_pipeAccordingToValidationResult:function(){var e=this,i=function(){var i=t.Deferred();return!0!==e.validationResult&&i.reject(),i.resolve().promise()};return[i,i]},actualizeOptions:function(){return n.attr(this.$element,this.options.namespace,this.domOptions),this.parent&&this.parent.actualizeOptions&&this.parent.actualizeOptions(),this},_resetOptions:function(t){this.domOptions=n.objectCreate(this.parent.options),this.options=n.objectCreate(this.domOptions);for(var e in t)t.hasOwnProperty(e)&&(this.options[e]=t[e]);this.actualizeOptions()},_listeners:null,on:function(t,e){return this._listeners=this._listeners||{},(this._listeners[t]=this._listeners[t]||[]).push(e),this},subscribe:function(e,i){t.listenTo(this,e.toLowerCase(),i)},off:function(t,e){var i=this._listeners&&this._listeners[t];if(i)if(e)for(var r=i.length;r--;)i[r]===e&&i.splice(r,1);else delete this._listeners[t];return this},unsubscribe:function(e,i){t.unsubscribeTo(this,e.toLowerCase())},trigger:function(t,e,i){e=e||this;var r,n=this._listeners&&this._listeners[t];if(n)for(var s=n.length;s--;)if(!1===(r=n[s].call(e,e,i)))return r;return!this.parent||this.parent.trigger(t,e,i)},reset:function(){if("ParsleyForm"!==this.__class__)return this._resetUI(),this._trigger("reset");for(var t=0;t<this.fields.length;t++)this.fields[t].reset();this._trigger("reset")},destroy:function(){if(this._destroyUI(),"ParsleyForm"!==this.__class__)return this.$element.removeData("Parsley"),this.$element.removeData("ParsleyFieldMultiple"),void this._trigger("destroy");for(var t=0;t<this.fields.length;t++)this.fields[t].destroy();this.$element.removeData("Parsley"),this._trigger("destroy")},asyncIsValid:function(t,e){return n.warnOnce("asyncIsValid is deprecated; please use whenValid instead"),this.whenValid({group:t,force:e})},_findRelated:function(){return this.options.multiple?this.parent.$element.find("["+this.options.namespace+'multiple="'+this.options.multiple+'"]'):this.$element}};var o={string:function(t){return t},integer:function(t){if(isNaN(t))throw'Requirement is not an integer: "'+t+'"';return parseInt(t,10)},number:function(t){if(isNaN(t))throw'Requirement is not a number: "'+t+'"';return parseFloat(t)},reference:function(e){var i=t(e);if(0===i.length)throw'No such reference: "'+e+'"';return i},boolean:function(t){return"false"!==t},object:function(t){return n.deserializeValue(t)},regexp:function(t){var e="";return/^\/.*\/(?:[gimy]*)$/.test(t)?(e=t.replace(/.*\/([gimy]*)$/,"$1"),t=t.replace(new RegExp("^/(.*?)/"+e+"$"),"$1")):t="^"+t+"$",new RegExp(t,e)}},l=function(t,e){var i=o[t||"string"];if(!i)throw'Unknown requirement specification: "'+t+'"';return i(e)},u=function(e){t.extend(!0,this,e)};u.prototype={validate:function(e,i){if(this.fn)return arguments.length>3&&(i=[].slice.call(arguments,1,-1)),this.fn.call(this,e,i);if(t.isArray(e)){if(!this.validateMultiple)throw"Validator `"+this.name+"` does not handle multiple values";return this.validateMultiple.apply(this,arguments)}if(this.validateNumber)return!isNaN(e)&&(arguments[0]=parseFloat(arguments[0]),this.validateNumber.apply(this,arguments));if(this.validateString)return this.validateString.apply(this,arguments);throw"Validator `"+this.name+"` only handles multiple values"},parseRequirements:function(e,i){if("string"!=typeof e)return t.isArray(e)?e:[e];var r=this.requirementType;if(t.isArray(r)){for(var s=function(t,e){var i=t.match(/^\s*\[(.*)\]\s*$/);if(!i)throw'Requirement is not an array: "'+t+'"';var r=i[1].split(",").map(n.trimString);if(r.length!==e)throw"Requirement has "+r.length+" values when "+e+" are needed";return r}(e,r.length),a=0;a<s.length;a++)s[a]=l(r[a],s[a]);return s}return t.isPlainObject(r)?function(t,e,i){var r=null,n={};for(var s in t)if(s){var a=i(s);"string"==typeof a&&(a=l(t[s],a)),n[s]=a}else r=l(t[s],e);return[r,n]}(r,e,i):[l(r,e)]},requirementType:"string",priority:2};var d=function(t,e){this.__class__="ParsleyValidatorRegistry",this.locale="en",this.init(t||{},e||{})},h={email:/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,number:/^-?(\d*\.)?\d+(e[-+]?\d+)?$/i,integer:/^-?\d+$/,digits:/^\d+$/,alphanum:/^\w+$/i,url:new RegExp("^(?:(?:https?|ftp)://)?(?:\\S+(?::\\S*)?@)?(?:(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))|(?:(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)(?:\\.(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)*(?:\\.(?:[a-z\\u00a1-\\uffff]{2,})))(?::\\d{2,5})?(?:/\\S*)?$","i")};h.range=h.number;var p=function(t){var e=(""+t).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);return e?Math.max(0,(e[1]?e[1].length:0)-(e[2]?+e[2]:0)):0};d.prototype={init:function(e,i){this.catalog=i,this.validators=t.extend({},this.validators);for(var r in e)this.addValidator(r,e[r].fn,e[r].priority);window.Parsley.trigger("parsley:validator:init")},setLocale:function(t){if(void 0===this.catalog[t])throw new Error(t+" is not available in the catalog");return this.locale=t,this},addCatalog:function(t,e,i){return"object"==typeof e&&(this.catalog[t]=e),!0===i?this.setLocale(t):this},addMessage:function(t,e,i){return void 0===this.catalog[t]&&(this.catalog[t]={}),this.catalog[t][e]=i,this},addMessages:function(t,e){for(var i in e)this.addMessage(t,i,e[i]);return this},addValidator:function(t,e,i){if(this.validators[t])n.warn('Validator "'+t+'" is already defined.');else if(s.hasOwnProperty(t))return void n.warn('"'+t+'" is a restricted keyword and is not a valid validator name.');return this._setValidator.apply(this,arguments)},updateValidator:function(t,e,i){return this.validators[t]?this._setValidator.apply(this,arguments):(n.warn('Validator "'+t+'" is not already defined.'),this.addValidator.apply(this,arguments))},removeValidator:function(t){return this.validators[t]||n.warn('Validator "'+t+'" is not defined.'),delete this.validators[t],this},_setValidator:function(t,e,i){"object"!=typeof e&&(e={fn:e,priority:i}),e.validate||(e=new u(e)),this.validators[t]=e;for(var r in e.messages||{})this.addMessage(r,t,e.messages[r]);return this},getErrorMessage:function(t){var e;"type"===t.name?e=(this.catalog[this.locale][t.name]||{})[t.requirements]:e=this.formatMessage(this.catalog[this.locale][t.name],t.requirements);return e||this.catalog[this.locale].defaultMessage||this.catalog.en.defaultMessage},formatMessage:function(t,e){if("object"==typeof e){for(var i in e)t=this.formatMessage(t,e[i]);return t}return"string"==typeof t?t.replace(/%s/i,e):""},validators:{notblank:{validateString:function(t){return/\S/.test(t)},priority:2},required:{validateMultiple:function(t){return t.length>0},validateString:function(t){return/\S/.test(t)},priority:512},type:{validateString:function(t,e){var i=arguments.length<=2||void 0===arguments[2]?{}:arguments[2],r=i.step,n=void 0===r?"1":r,s=i.base,a=void 0===s?0:s,o=h[e];if(!o)throw new Error("validator type `"+e+"` is not supported");if(!o.test(t))return!1;if("number"===e&&!/^any$/i.test(n||"")){var l=Number(t),u=Math.max(p(n),p(a));if(p(l)>u)return!1;var d=function(t){return Math.round(t*Math.pow(10,u))};if((d(l)-d(a))%d(n)!=0)return!1}return!0},requirementType:{"":"string",step:"string",base:"number"},priority:256},pattern:{validateString:function(t,e){return e.test(t)},requirementType:"regexp",priority:64},minlength:{validateString:function(t,e){return t.length>=e},requirementType:"integer",priority:30},maxlength:{validateString:function(t,e){return t.length<=e},requirementType:"integer",priority:30},length:{validateString:function(t,e,i){return t.length>=e&&t.length<=i},requirementType:["integer","integer"],priority:30},mincheck:{validateMultiple:function(t,e){return t.length>=e},requirementType:"integer",priority:30},maxcheck:{validateMultiple:function(t,e){return t.length<=e},requirementType:"integer",priority:30},check:{validateMultiple:function(t,e,i){return t.length>=e&&t.length<=i},requirementType:["integer","integer"],priority:30},min:{validateNumber:function(t,e){return t>=e},requirementType:"number",priority:30},max:{validateNumber:function(t,e){return t<=e},requirementType:"number",priority:30},range:{validateNumber:function(t,e,i){return t>=e&&t<=i},requirementType:["number","number"],priority:30},equalto:{validateString:function(e,i){var r=t(i);return r.length?e===r.val():e===i},priority:256}}};var c={};c.Form={_actualizeTriggers:function(){var t=this;this.$element.on("submit.Parsley",function(e){t.onSubmitValidate(e)}),this.$element.on("click.Parsley",n._SubmitSelector,function(e){t.onSubmitButton(e)}),!1!==this.options.uiEnabled&&this.$element.attr("novalidate","")},focus:function(){if(this._focusedField=null,!0===this.validationResult||"none"===this.options.focus)return null;for(var t=0;t<this.fields.length;t++){var e=this.fields[t];if(!0!==e.validationResult&&e.validationResult.length>0&&void 0===e.options.noFocus&&(this._focusedField=e.$element,"first"===this.options.focus))break}return null===this._focusedField?null:this._focusedField.focus()},_destroyUI:function(){this.$element.off(".Parsley")}},c.Field={_reflowUI:function(){if(this._buildUI(),this._ui){var t=function t(e,i,r){for(var n=[],s=[],a=0;a<e.length;a++){for(var o=!1,l=0;l<i.length;l++)if(e[a].assert.name===i[l].assert.name){o=!0;break}o?s.push(e[a]):n.push(e[a])}return{kept:s,added:n,removed:r?[]:t(i,e,!0).added}}(this.validationResult,this._ui.lastValidationResult);this._ui.lastValidationResult=this.validationResult,this._manageStatusClass(),this._manageErrorsMessages(t),this._actualizeTriggers(),!t.kept.length&&!t.added.length||this._failedOnce||(this._failedOnce=!0,this._actualizeTriggers())}},getErrorsMessages:function(){if(!0===this.validationResult)return[];for(var t=[],e=0;e<this.validationResult.length;e++)t.push(this.validationResult[e].errorMessage||this._getErrorMessage(this.validationResult[e].assert));return t},addError:function(t){var e=arguments.length<=1||void 0===arguments[1]?{}:arguments[1],i=e.message,r=e.assert,n=e.updateClass,s=void 0===n||n;this._buildUI(),this._addError(t,{message:i,assert:r}),s&&this._errorClass()},updateError:function(t){var e=arguments.length<=1||void 0===arguments[1]?{}:arguments[1],i=e.message,r=e.assert,n=e.updateClass,s=void 0===n||n;this._buildUI(),this._updateError(t,{message:i,assert:r}),s&&this._errorClass()},removeError:function(t){var e=(arguments.length<=1||void 0===arguments[1]?{}:arguments[1]).updateClass,i=void 0===e||e;this._buildUI(),this._removeError(t),i&&this._manageStatusClass()},_manageStatusClass:function(){this.hasConstraints()&&this.needsValidation()&&!0===this.validationResult?this._successClass():this.validationResult.length>0?this._errorClass():this._resetClass()},_manageErrorsMessages:function(e){if(void 0===this.options.errorsMessagesDisabled){if(void 0!==this.options.errorMessage)return e.added.length||e.kept.length?(this._insertErrorWrapper(),0===this._ui.$errorsWrapper.find(".parsley-custom-error-message").length&&this._ui.$errorsWrapper.append(t(this.options.errorTemplate).addClass("parsley-custom-error-message")),this._ui.$errorsWrapper.addClass("filled").find(".parsley-custom-error-message").html(this.options.errorMessage)):this._ui.$errorsWrapper.removeClass("filled").find(".parsley-custom-error-message").remove();for(var i=0;i<e.removed.length;i++)this._removeError(e.removed[i].assert.name);for(i=0;i<e.added.length;i++)this._addError(e.added[i].assert.name,{message:e.added[i].errorMessage,assert:e.added[i].assert});for(i=0;i<e.kept.length;i++)this._updateError(e.kept[i].assert.name,{message:e.kept[i].errorMessage,assert:e.kept[i].assert})}},_addError:function(e,i){var r=i.message,n=i.assert;this._insertErrorWrapper(),this._ui.$errorsWrapper.addClass("filled").append(t(this.options.errorTemplate).addClass("parsley-"+e).html(r||this._getErrorMessage(n)))},_updateError:function(t,e){var i=e.message,r=e.assert;this._ui.$errorsWrapper.addClass("filled").find(".parsley-"+t).html(i||this._getErrorMessage(r))},_removeError:function(t){this._ui.$errorsWrapper.removeClass("filled").find(".parsley-"+t).remove()},_getErrorMessage:function(t){var e=t.name+"Message";return void 0!==this.options[e]?window.Parsley.formatMessage(this.options[e],t.requirements):window.Parsley.getErrorMessage(t)},_buildUI:function(){if(!this._ui&&!1!==this.options.uiEnabled){var e={};this.$element.attr(this.options.namespace+"id",this.__id__),e.$errorClassHandler=this._manageClassHandler(),e.errorsWrapperId="parsley-id-"+(this.options.multiple?"multiple-"+this.options.multiple:this.__id__),e.$errorsWrapper=t(this.options.errorsWrapper).attr("id",e.errorsWrapperId),e.lastValidationResult=[],e.validationInformationVisible=!1,this._ui=e}},_manageClassHandler:function(){if("string"==typeof this.options.classHandler&&t(this.options.classHandler).length)return t(this.options.classHandler);var e=this.options.classHandler.call(this,this);return void 0!==e&&e.length?e:this._inputHolder()},_inputHolder:function(){return!this.options.multiple||this.$element.is("select")?this.$element:this.$element.parent()},_insertErrorWrapper:function(){var e;if(0!==this._ui.$errorsWrapper.parent().length)return this._ui.$errorsWrapper.parent();if("string"==typeof this.options.errorsContainer){if(t(this.options.errorsContainer).length)return t(this.options.errorsContainer).append(this._ui.$errorsWrapper);n.warn("The errors container `"+this.options.errorsContainer+"` does not exist in DOM")}else"function"==typeof this.options.errorsContainer&&(e=this.options.errorsContainer.call(this,this));return void 0!==e&&e.length?e.append(this._ui.$errorsWrapper):this._inputHolder().after(this._ui.$errorsWrapper)},_actualizeTriggers:function(){var t,e=this,i=this._findRelated();i.off(".Parsley"),this._failedOnce?i.on(n.namespaceEvents(this.options.triggerAfterFailure,"Parsley"),function(){e.validate()}):(t=n.namespaceEvents(this.options.trigger,"Parsley"))&&i.on(t,function(t){e._eventValidate(t)})},_eventValidate:function(t){!(!/key|input/.test(t.type)||this._ui&&this._ui.validationInformationVisible)&&this.getValue().length<=this.options.validationThreshold||this.validate()},_resetUI:function(){this._failedOnce=!1,this._actualizeTriggers(),void 0!==this._ui&&(this._ui.$errorsWrapper.removeClass("filled").children().remove(),this._resetClass(),this._ui.lastValidationResult=[],this._ui.validationInformationVisible=!1)},_destroyUI:function(){this._resetUI(),void 0!==this._ui&&this._ui.$errorsWrapper.remove(),delete this._ui},_successClass:function(){this._ui.validationInformationVisible=!0,this._ui.$errorClassHandler.removeClass(this.options.errorClass).addClass(this.options.successClass)},_errorClass:function(){this._ui.validationInformationVisible=!0,this._ui.$errorClassHandler.removeClass(this.options.successClass).addClass(this.options.errorClass)},_resetClass:function(){this._ui.$errorClassHandler.removeClass(this.options.successClass).removeClass(this.options.errorClass)}};var f=function(e,i,r){this.__class__="ParsleyForm",this.$element=t(e),this.domOptions=i,this.options=r,this.parent=window.Parsley,this.fields=[],this.validationResult=null},m={pending:null,resolved:!0,rejected:!1};f.prototype={onSubmitValidate:function(t){var e=this;if(!0!==t.parsley){var i=this._$submitSource||this.$element.find(n._SubmitSelector).first();if(this._$submitSource=null,this.$element.find(".parsley-synthetic-submit-button").prop("disabled",!0),!i.is("[formnovalidate]")){var r=this.whenValidate({event:t});"resolved"===r.state()&&!1!==this._trigger("submit")||(t.stopImmediatePropagation(),t.preventDefault(),"pending"===r.state()&&r.done(function(){e._submit(i)}))}}},onSubmitButton:function(e){this._$submitSource=t(e.currentTarget)},_submit:function(e){if(!1!==this._trigger("submit")){if(e){var i=this.$element.find(".parsley-synthetic-submit-button").prop("disabled",!1);0===i.length&&(i=t('<input class="parsley-synthetic-submit-button" type="hidden">').appendTo(this.$element)),i.attr({name:e.attr("name"),value:e.attr("value")})}this.$element.trigger(t.extend(t.Event("submit"),{parsley:!0}))}},validate:function(e){if(arguments.length>=1&&!t.isPlainObject(e)){n.warnOnce("Calling validate on a parsley form without passing arguments as an object is deprecated.");var i=_slice.call(arguments);e={group:i[0],force:i[1],event:i[2]}}return m[this.whenValidate(e).state()]},whenValidate:function(){var e,i=this,r=arguments.length<=0||void 0===arguments[0]?{}:arguments[0],s=r.group,a=r.force,o=r.event;this.submitEvent=o,o&&(this.submitEvent=t.extend({},o,{preventDefault:function(){n.warnOnce("Using `this.submitEvent.preventDefault()` is deprecated; instead, call `this.validationResult = false`"),i.validationResult=!1}})),this.validationResult=!0,this._trigger("validate"),this._refreshFields();var l=this._withoutReactualizingFormOptions(function(){return t.map(i.fields,function(t){return t.whenValidate({force:a,group:s})})});return(e=n.all(l).done(function(){i._trigger("success")}).fail(function(){i.validationResult=!1,i.focus(),i._trigger("error")}).always(function(){i._trigger("validated")})).pipe.apply(e,_toConsumableArray(this._pipeAccordingToValidationResult()))},isValid:function(e){if(arguments.length>=1&&!t.isPlainObject(e)){n.warnOnce("Calling isValid on a parsley form without passing arguments as an object is deprecated.");var i=_slice.call(arguments);e={group:i[0],force:i[1]}}return m[this.whenValid(e).state()]},whenValid:function(){var e=this,i=arguments.length<=0||void 0===arguments[0]?{}:arguments[0],r=i.group,s=i.force;this._refreshFields();var a=this._withoutReactualizingFormOptions(function(){return t.map(e.fields,function(t){return t.whenValid({group:r,force:s})})});return n.all(a)},_refreshFields:function(){return this.actualizeOptions()._bindFields()},_bindFields:function(){var e=this,i=this.fields;return this.fields=[],this.fieldsMappedById={},this._withoutReactualizingFormOptions(function(){e.$element.find(e.options.inputs).not(e.options.excluded).each(function(t,i){var r=new window.Parsley.Factory(i,{},e);"ParsleyField"!==r.__class__&&"ParsleyFieldMultiple"!==r.__class__||!0===r.options.excluded||void 0===e.fieldsMappedById[r.__class__+"-"+r.__id__]&&(e.fieldsMappedById[r.__class__+"-"+r.__id__]=r,e.fields.push(r))}),t.each(n.difference(i,e.fields),function(t,e){e._trigger("reset")})}),this},_withoutReactualizingFormOptions:function(t){var e=this.actualizeOptions;this.actualizeOptions=function(){return this};var i=t();return this.actualizeOptions=e,i},_trigger:function(t){return this.trigger("form:"+t)}};var v=function(e,i,r,n,s){if(!/ParsleyField/.test(e.__class__))throw new Error("ParsleyField or ParsleyFieldMultiple instance expected");var a=window.Parsley._validatorRegistry.validators[i],o=new u(a);t.extend(this,{validator:o,name:i,requirements:r,priority:n||e.options[i+"Priority"]||o.priority,isDomConstraint:!0===s}),this._parseRequirements(e.options)};v.prototype={validate:function(t,e){var i;return(i=this.validator).validate.apply(i,[t].concat(_toConsumableArray(this.requirementList),[e]))},_parseRequirements:function(t){var e=this;this.requirementList=this.validator.parseRequirements(this.requirements,function(i){return t[e.name+(r=i,r[0].toUpperCase()+r.slice(1))];var r})}};var g=function(e,i,r,n){this.__class__="ParsleyField",this.$element=t(e),void 0!==n&&(this.parent=n),this.options=r,this.domOptions=i,this.constraints=[],this.constraintsByName={},this.validationResult=!0,this._bindConstraints()},y={pending:null,resolved:!0,rejected:!1};g.prototype={validate:function(e){arguments.length>=1&&!t.isPlainObject(e)&&(n.warnOnce("Calling validate on a parsley field without passing arguments as an object is deprecated."),e={options:e});var i=this.whenValidate(e);if(!i)return!0;switch(i.state()){case"pending":return null;case"resolved":return!0;case"rejected":return this.validationResult}},whenValidate:function(){var t,e=this,i=arguments.length<=0||void 0===arguments[0]?{}:arguments[0],r=i.force,n=i.group;if(this.refreshConstraints(),!n||this._isInGroup(n))return this.value=this.getValue(),this._trigger("validate"),(t=this.whenValid({force:r,value:this.value,_refreshed:!0}).always(function(){e._reflowUI()}).done(function(){e._trigger("success")}).fail(function(){e._trigger("error")}).always(function(){e._trigger("validated")})).pipe.apply(t,_toConsumableArray(this._pipeAccordingToValidationResult()))},hasConstraints:function(){return 0!==this.constraints.length},needsValidation:function(t){return void 0===t&&(t=this.getValue()),!(!t.length&&!this._isRequired()&&void 0===this.options.validateIfEmpty)},_isInGroup:function(e){return t.isArray(this.options.group)?-1!==t.inArray(e,this.options.group):this.options.group===e},isValid:function(e){if(arguments.length>=1&&!t.isPlainObject(e)){n.warnOnce("Calling isValid on a parsley field without passing arguments as an object is deprecated.");var i=_slice.call(arguments);e={force:i[0],value:i[1]}}var r=this.whenValid(e);return!r||y[r.state()]},whenValid:function(){var e=this,i=arguments.length<=0||void 0===arguments[0]?{}:arguments[0],r=i.force,s=void 0!==r&&r,a=i.value,o=i.group;if(i._refreshed||this.refreshConstraints(),!o||this._isInGroup(o)){if(this.validationResult=!0,!this.hasConstraints())return t.when();if(void 0!==a&&null!==a||(a=this.getValue()),!this.needsValidation(a)&&!0!==s)return t.when();var l=this._getGroupedConstraints(),u=[];return t.each(l,function(i,r){var s=n.all(t.map(r,function(t){return e._validateConstraint(a,t)}));if(u.push(s),"rejected"===s.state())return!1}),n.all(u)}},_validateConstraint:function(e,i){var r=this,s=i.validate(e,this);return!1===s&&(s=t.Deferred().reject()),n.all([s]).fail(function(t){r.validationResult instanceof Array||(r.validationResult=[]),r.validationResult.push({assert:i,errorMessage:"string"==typeof t&&t})})},getValue:function(){var t;return void 0===(t="function"==typeof this.options.value?this.options.value(this):void 0!==this.options.value?this.options.value:this.$element.val())||null===t?"":this._handleWhitespace(t)},refreshConstraints:function(){return this.actualizeOptions()._bindConstraints()},addConstraint:function(t,e,i,r){if(window.Parsley._validatorRegistry.validators[t]){var n=new v(this,t,e,i,r);"undefined"!==this.constraintsByName[n.name]&&this.removeConstraint(n.name),this.constraints.push(n),this.constraintsByName[n.name]=n}return this},removeConstraint:function(t){for(var e=0;e<this.constraints.length;e++)if(t===this.constraints[e].name){this.constraints.splice(e,1);break}return delete this.constraintsByName[t],this},updateConstraint:function(t,e,i){return this.removeConstraint(t).addConstraint(t,e,i)},_bindConstraints:function(){for(var t=[],e={},i=0;i<this.constraints.length;i++)!1===this.constraints[i].isDomConstraint&&(t.push(this.constraints[i]),e[this.constraints[i].name]=this.constraints[i]);this.constraints=t,this.constraintsByName=e;for(var r in this.options)this.addConstraint(r,this.options[r],void 0,!0);return this._bindHtml5Constraints()},_bindHtml5Constraints:function(){(this.$element.hasClass("required")||this.$element.attr("required"))&&this.addConstraint("required",!0,void 0,!0),"string"==typeof this.$element.attr("pattern")&&this.addConstraint("pattern",this.$element.attr("pattern"),void 0,!0),void 0!==this.$element.attr("min")&&void 0!==this.$element.attr("max")?this.addConstraint("range",[this.$element.attr("min"),this.$element.attr("max")],void 0,!0):void 0!==this.$element.attr("min")?this.addConstraint("min",this.$element.attr("min"),void 0,!0):void 0!==this.$element.attr("max")&&this.addConstraint("max",this.$element.attr("max"),void 0,!0),void 0!==this.$element.attr("minlength")&&void 0!==this.$element.attr("maxlength")?this.addConstraint("length",[this.$element.attr("minlength"),this.$element.attr("maxlength")],void 0,!0):void 0!==this.$element.attr("minlength")?this.addConstraint("minlength",this.$element.attr("minlength"),void 0,!0):void 0!==this.$element.attr("maxlength")&&this.addConstraint("maxlength",this.$element.attr("maxlength"),void 0,!0);var t=this.$element.attr("type");return void 0===t?this:"number"===t?this.addConstraint("type",["number",{step:this.$element.attr("step"),base:this.$element.attr("min")||this.$element.attr("value")}],void 0,!0):/^(email|url|range)$/i.test(t)?this.addConstraint("type",t,void 0,!0):this},_isRequired:function(){return void 0!==this.constraintsByName.required&&!1!==this.constraintsByName.required.requirements},_trigger:function(t){return this.trigger("field:"+t)},_handleWhitespace:function(t){return!0===this.options.trimValue&&n.warnOnce('data-parsley-trim-value="true" is deprecated, please use data-parsley-whitespace="trim"'),"squish"===this.options.whitespace&&(t=t.replace(/\s{2,}/g," ")),"trim"!==this.options.whitespace&&"squish"!==this.options.whitespace&&!0!==this.options.trimValue||(t=n.trimString(t)),t},_getGroupedConstraints:function(){if(!1===this.options.priorityEnabled)return[this.constraints];for(var t=[],e={},i=0;i<this.constraints.length;i++){var r=this.constraints[i].priority;e[r]||t.push(e[r]=[]),e[r].push(this.constraints[i])}return t.sort(function(t,e){return e[0].priority-t[0].priority}),t}};var _=g,w=function(){this.__class__="ParsleyFieldMultiple"};w.prototype={addElement:function(t){return this.$elements.push(t),this},refreshConstraints:function(){var e;if(this.constraints=[],this.$element.is("select"))return this.actualizeOptions()._bindConstraints(),this;for(var i=0;i<this.$elements.length;i++)if(t("html").has(this.$elements[i]).length){e=this.$elements[i].data("ParsleyFieldMultiple").refreshConstraints().constraints;for(var r=0;r<e.length;r++)this.addConstraint(e[r].name,e[r].requirements,e[r].priority,e[r].isDomConstraint)}else this.$elements.splice(i,1);return this},getValue:function(){if("function"==typeof this.options.value)return this.options.value(this);if(void 0!==this.options.value)return this.options.value;if(this.$element.is("input[type=radio]"))return this._findRelated().filter(":checked").val()||"";if(this.$element.is("input[type=checkbox]")){var e=[];return this._findRelated().filter(":checked").each(function(){e.push(t(this).val())}),e}return this.$element.is("select")&&null===this.$element.val()?[]:this.$element.val()},_init:function(){return this.$elements=[this.$element],this}};var b=function(e,i,r){this.$element=t(e);var n=this.$element.data("Parsley");if(n)return void 0!==r&&n.parent===window.Parsley&&(n.parent=r,n._resetOptions(n.options)),"object"==typeof i&&t.extend(n.options,i),n;if(!this.$element.length)throw new Error("You must bind Parsley on an existing element.");if(void 0!==r&&"ParsleyForm"!==r.__class__)throw new Error("Parent instance must be a ParsleyForm instance");return this.parent=r||window.Parsley,this.init(i)};b.prototype={init:function(t){return this.__class__="Parsley",this.__version__="2.4.4",this.__id__=n.generateID(),this._resetOptions(t),this.$element.is("form")||n.checkAttr(this.$element,this.options.namespace,"validate")&&!this.$element.is(this.options.inputs)?this.bind("parsleyForm"):this.isMultiple()?this.handleMultiple():this.bind("parsleyField")},isMultiple:function(){return this.$element.is("input[type=radio], input[type=checkbox]")||this.$element.is("select")&&void 0!==this.$element.attr("multiple")},handleMultiple:function(){var e,i,r=this;if(this.options.multiple||(void 0!==this.$element.attr("name")&&this.$element.attr("name").length?this.options.multiple=e=this.$element.attr("name"):void 0!==this.$element.attr("id")&&this.$element.attr("id").length&&(this.options.multiple=this.$element.attr("id"))),this.$element.is("select")&&void 0!==this.$element.attr("multiple"))return this.options.multiple=this.options.multiple||this.__id__,this.bind("parsleyFieldMultiple");if(!this.options.multiple)return n.warn("To be bound by Parsley, a radio, a checkbox and a multiple select input must have either a name or a multiple option.",this.$element),this;this.options.multiple=this.options.multiple.replace(/(:|\.|\[|\]|\{|\}|\$)/g,""),void 0!==e&&t('input[name="'+e+'"]').each(function(e,i){t(i).is("input[type=radio], input[type=checkbox]")&&t(i).attr(r.options.namespace+"multiple",r.options.multiple)});for(var s=this._findRelated(),a=0;a<s.length;a++)if(void 0!==(i=t(s.get(a)).data("Parsley"))){this.$element.data("ParsleyFieldMultiple")||i.addElement(this.$element);break}return this.bind("parsleyField",!0),i||this.bind("parsleyFieldMultiple")},bind:function(e,i){var r;switch(e){case"parsleyForm":r=t.extend(new f(this.$element,this.domOptions,this.options),new a,window.ParsleyExtend)._bindFields();break;case"parsleyField":r=t.extend(new _(this.$element,this.domOptions,this.options,this.parent),new a,window.ParsleyExtend);break;case"parsleyFieldMultiple":r=t.extend(new _(this.$element,this.domOptions,this.options,this.parent),new w,new a,window.ParsleyExtend)._init();break;default:throw new Error(e+"is not a supported Parsley type")}return this.options.multiple&&n.setAttr(this.$element,this.options.namespace,"multiple",this.options.multiple),void 0!==i?(this.$element.data("ParsleyFieldMultiple",r),r):(this.$element.data("Parsley",r),r._actualizeTriggers(),r._trigger("init"),r)}};var F=t.fn.jquery.split(".");if(parseInt(F[0])<=1&&parseInt(F[1])<8)throw"The loaded version of jQuery is too old. Please upgrade to 1.8.x or better.";F.forEach||n.warn("Parsley requires ES5 to run properly. Please include https://github.com/es-shims/es5-shim");var C=t.extend(new a,{$element:t(document),actualizeOptions:null,_resetOptions:null,Factory:b,version:"2.4.4"});t.extend(_.prototype,c.Field,a.prototype),t.extend(f.prototype,c.Form,a.prototype),t.extend(b.prototype,a.prototype),t.fn.parsley=t.fn.psly=function(e){if(this.length>1){var i=[];return this.each(function(){i.push(t(this).parsley(e))}),i}if(t(this).length)return new b(this,e);n.warn("You must bind Parsley on an existing element.")},void 0===window.ParsleyExtend&&(window.ParsleyExtend={}),C.options=t.extend(n.objectCreate(s),window.ParsleyConfig),window.ParsleyConfig=C.options,window.Parsley=window.psly=C,window.ParsleyUtils=n;var $=window.Parsley._validatorRegistry=new d(window.ParsleyConfig.validators,window.ParsleyConfig.i18n);window.ParsleyValidator={},t.each("setLocale addCatalog addMessage addMessages getErrorMessage formatMessage addValidator updateValidator removeValidator".split(" "),function(e,i){window.Parsley[i]=t.proxy($,i),window.ParsleyValidator[i]=function(){var t;return n.warnOnce("Accessing the method '"+i+"' through ParsleyValidator is deprecated. Simply call 'window.Parsley."+i+"(...)'"),(t=window.Parsley)[i].apply(t,arguments)}}),window.Parsley.UI=c,window.ParsleyUI={removeError:function(t,e,i){var r=!0!==i;return n.warnOnce("Accessing ParsleyUI is deprecated. Call 'removeError' on the instance directly. Please comment in issue 1073 as to your need to call this method."),t.removeError(e,{updateClass:r})},getErrorsMessages:function(t){return n.warnOnce("Accessing ParsleyUI is deprecated. Call 'getErrorsMessages' on the instance directly."),t.getErrorsMessages()}},t.each("addError updateError".split(" "),function(t,e){window.ParsleyUI[e]=function(t,i,r,s,a){var o=!0!==a;return n.warnOnce("Accessing ParsleyUI is deprecated. Call '"+e+"' on the instance directly. Please comment in issue 1073 as to your need to call this method."),t[e](i,{message:r,assert:s,updateClass:o})}}),!1!==window.ParsleyConfig.autoBind&&t(function(){t("[data-parsley-validate]").length&&t("[data-parsley-validate]").parsley()});var x=t({}),E=function(){n.warnOnce("Parsley's pubsub module is deprecated; use the 'on' and 'off' methods on parsley instances or window.Parsley")};function P(t,e){return t.parsleyAdaptedCallback||(t.parsleyAdaptedCallback=function(){var i=Array.prototype.slice.call(arguments,0);i.unshift(this),t.apply(e||x,i)}),t.parsleyAdaptedCallback}var V="parsley:";function M(t){return 0===t.lastIndexOf(V,0)?t.substr(V.length):t}return t.listen=function(t,e){var i;if(E(),"object"==typeof arguments[1]&&"function"==typeof arguments[2]&&(i=arguments[1],e=arguments[2]),"function"!=typeof e)throw new Error("Wrong parameters");window.Parsley.on(M(t),P(e,i))},t.listenTo=function(t,e,i){if(E(),!(t instanceof _||t instanceof f))throw new Error("Must give Parsley instance");if("string"!=typeof e||"function"!=typeof i)throw new Error("Wrong parameters");t.on(M(e),P(i))},t.unsubscribe=function(t,e){if(E(),"string"!=typeof t||"function"!=typeof e)throw new Error("Wrong arguments");window.Parsley.off(M(t),e.parsleyAdaptedCallback)},t.unsubscribeTo=function(t,e){if(E(),!(t instanceof _||t instanceof f))throw new Error("Must give Parsley instance");t.off(M(e))},t.unsubscribeAll=function(e){E(),window.Parsley.off(M(e)),t("form,input,textarea,select").each(function(){var i=t(this).data("Parsley");i&&i.off(M(e))})},t.emit=function(t,e){var i;E();var r=e instanceof _||e instanceof f,n=Array.prototype.slice.call(arguments,r?2:1);n.unshift(M(t)),r||(e=window.Parsley),(i=e).trigger.apply(i,_toConsumableArray(n))},t.extend(!0,C,{asyncValidators:{default:{fn:function(t){return t.status>=200&&t.status<300},url:!1},reverse:{fn:function(t){return t.status<200||t.status>=300},url:!1}},addAsyncValidator:function(t,e,i,r){return C.asyncValidators[t]={fn:e,url:i||!1,options:r||{}},this}}),C.addValidator("remote",{requirementType:{"":"string",validator:"string",reverse:"boolean",options:"object"},validateString:function(e,i,r,n){var s,a,o={},l=r.validator||(!0===r.reverse?"reverse":"default");if(void 0===C.asyncValidators[l])throw new Error("Calling an undefined async validator: `"+l+"`");(i=C.asyncValidators[l].url||i).indexOf("{value}")>-1?i=i.replace("{value}",encodeURIComponent(e)):o[n.$element.attr("name")||n.$element.attr("id")]=e;var u=t.extend(!0,r.options||{},C.asyncValidators[l].options);s=t.extend(!0,{},{url:i,data:o,type:"GET"},u),n.trigger("field:ajaxoptions",n,s),a=t.param(s),void 0===C._remoteCache&&(C._remoteCache={});var d=C._remoteCache[a]=C._remoteCache[a]||t.ajax(s),h=function(){var e=C.asyncValidators[l].fn.call(n,d,i,r);return e||(e=t.Deferred().reject()),t.when(e)};return d.then(h,h)},priority:-1}),C.on("form:submit",function(){C._remoteCache={}}),window.ParsleyExtend.addAsyncValidator=function(){return ParsleyUtils.warnOnce("Accessing the method `addAsyncValidator` through an instance is deprecated. Simply call `Parsley.addAsyncValidator(...)`"),C.addAsyncValidator.apply(C,arguments)},C.addMessages("en",{defaultMessage:"This value seems to be invalid.",type:{email:"This value should be a valid email.",url:"This value should be a valid url.",number:"This value should be a valid number.",integer:"This value should be a valid integer.",digits:"This value should be digits.",alphanum:"This value should be alphanumeric."},notblank:"This value should not be blank.",required:"This value is required.",pattern:"This value seems to be invalid.",min:"This value should be greater than or equal to %s.",max:"This value should be lower than or equal to %s.",range:"This value should be between %s and %s.",minlength:"This value is too short. It should have %s characters or more.",maxlength:"This value is too long. It should have %s characters or fewer.",length:"This value length is invalid. It should be between %s and %s characters long.",mincheck:"You must select at least %s choices.",maxcheck:"You must select %s choices or fewer.",check:"You must select between %s and %s choices.",equalto:"This value should be the same."}),C.setLocale("en"),(new function(){var e=this,i=window||global;t.extend(this,{isNativeEvent:function(t){return t.originalEvent&&!1!==t.originalEvent.isTrusted},fakeInputEvent:function(i){e.isNativeEvent(i)&&t(i.target).trigger("input")},misbehaves:function(i){e.isNativeEvent(i)&&(e.behavesOk(i),t(document).on("change.inputevent",i.data.selector,e.fakeInputEvent),e.fakeInputEvent(i))},behavesOk:function(i){e.isNativeEvent(i)&&t(document).off("input.inputevent",i.data.selector,e.behavesOk).off("change.inputevent",i.data.selector,e.misbehaves)},install:function(){if(!i.inputEventPatched){i.inputEventPatched="0.0.3";for(var r=["select",'input[type="checkbox"]','input[type="radio"]','input[type="file"]'],n=0;n<r.length;n++){var s=r[n];t(document).on("input.inputevent",s,{selector:s},e.behavesOk).on("change.inputevent",s,{selector:s},e.misbehaves)}}},uninstall:function(){delete i.inputEventPatched,t(document).off(".inputevent")}})}).install(),C});