var handleSlimScroll=function(){"use strict";$("[data-scrollbar=true]").each(function(){generateSlimScroll($(this))})},generateSlimScroll=function(e){if(!$(e).attr("data-init")){var a=$(e).attr("data-height"),t={height:a=a||$(e).height(),alwaysVisible:!0};/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)?($(e).css("height",a),$(e).css("overflow-x","scroll")):$(e).slimScroll(t),$(e).attr("data-init",!0)}},handleSidebarMenu=function(){"use strict";$(".sidebar .nav > .has-sub > a").click(function(){var e=$(this).next(".sub-menu");0===$(".page-sidebar-minified").length&&($(".sidebar .nav > li.has-sub > .sub-menu").not(e).slideUp(250,function(){$(this).closest("li").removeClass("expand")}),$(e).slideToggle(250,function(){var e=$(this).closest("li");$(e).hasClass("expand")?$(e).removeClass("expand"):$(e).addClass("expand")}))}),$(".sidebar .nav > .has-sub .sub-menu li.has-sub > a").click(function(){if(0===$(".page-sidebar-minified").length){var e=$(this).next(".sub-menu");$(e).slideToggle(250)}})},handleMobileSidebarToggle=function(){var e=!1;$(".sidebar").bind("click touchstart",function(a){0!==$(a.target).closest(".sidebar").length?e=!0:(e=!1,a.stopPropagation())}),$(document).bind("click touchstart",function(a){0===$(a.target).closest(".sidebar").length&&(e=!1),a.isPropagationStopped()||!0===e||($("#page-container").hasClass("page-sidebar-toggled")&&(e=!0,$("#page-container").removeClass("page-sidebar-toggled")),$(window).width()<=767&&$("#page-container").hasClass("page-right-sidebar-toggled")&&(e=!0,$("#page-container").removeClass("page-right-sidebar-toggled")))}),$("[data-click=right-sidebar-toggled]").click(function(a){a.stopPropagation();var t="page-right-sidebar-collapsed";t=$(window).width()<979?"page-right-sidebar-toggled":t,$("#page-container").hasClass(t)?$("#page-container").removeClass(t):!0!==e?$("#page-container").addClass(t):e=!1,$(window).width()<480&&$("#page-container").removeClass("page-sidebar-toggled"),$(window).trigger("resize")}),$("[data-click=sidebar-toggled]").click(function(a){a.stopPropagation(),$("#page-container").hasClass("page-sidebar-toggled")?$("#page-container").removeClass("page-sidebar-toggled"):!0!==e?$("#page-container").addClass("page-sidebar-toggled"):e=!1,$(window).width()<480&&$("#page-container").removeClass("page-right-sidebar-toggled")})},handleSidebarMinify=function(){$("[data-click=sidebar-minify]").click(function(e){e.preventDefault(),$('#sidebar [data-scrollbar="true"]').css("margin-top","0"),$('#sidebar [data-scrollbar="true"]').removeAttr("data-init"),$("#sidebar [data-scrollbar=true]").stop(),$("#page-container").hasClass("page-sidebar-minified")?($("#page-container").removeClass("page-sidebar-minified"),$("#page-container").hasClass("page-sidebar-fixed")?(0!==$("#sidebar .slimScrollDiv").length&&($('#sidebar [data-scrollbar="true"]').slimScroll({destroy:!0}),$('#sidebar [data-scrollbar="true"]').removeAttr("style")),generateSlimScroll($('#sidebar [data-scrollbar="true"]')),$("#sidebar [data-scrollbar=true]").trigger("mouseover")):/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)&&(0!==$("#sidebar .slimScrollDiv").length&&($('#sidebar [data-scrollbar="true"]').slimScroll({destroy:!0}),$('#sidebar [data-scrollbar="true"]').removeAttr("style")),generateSlimScroll($('#sidebar [data-scrollbar="true"]')))):($("#page-container").addClass("page-sidebar-minified"),/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)?($('#sidebar [data-scrollbar="true"]').css("margin-top","0"),$('#sidebar [data-scrollbar="true"]').css("overflow","visible")):($("#page-container").hasClass("page-sidebar-fixed")&&($('#sidebar [data-scrollbar="true"]').slimScroll({destroy:!0}),$('#sidebar [data-scrollbar="true"]').removeAttr("style")),$("#sidebar [data-scrollbar=true]").trigger("mouseover"))),$(window).trigger("resize")})},handlePageContentView=function(){"use strict";$.when($("#page-loader").addClass("hide")).done(function(){$("#page-container").addClass("in")})},panelActionRunning=!1,handlePanelAction=function(){"use strict";if(panelActionRunning)return!1;panelActionRunning=!0,$(document).on("hover","[data-click=panel-remove]",function(e){$(this).attr("data-init")||($(this).tooltip({title:"Remove",placement:"bottom",trigger:"hover",container:"body"}),$(this).tooltip("show"),$(this).attr("data-init",!0))}),$(document).on("click","[data-click=panel-remove]",function(e){e.preventDefault(),$(this).tooltip("destroy"),$(this).closest(".panel").remove()}),$(document).on("hover","[data-click=panel-collapse]",function(e){$(this).attr("data-init")||($(this).tooltip({title:"Collapse / Expand",placement:"bottom",trigger:"hover",container:"body"}),$(this).tooltip("show"),$(this).attr("data-init",!0))}),$(document).on("click","[data-click=panel-collapse]",function(e){e.preventDefault(),$(this).closest(".panel").find(".panel-body").slideToggle()}),$(document).on("hover","[data-click=panel-reload]",function(e){$(this).attr("data-init")||($(this).tooltip({title:"Reload",placement:"bottom",trigger:"hover",container:"body"}),$(this).tooltip("show"),$(this).attr("data-init",!0))}),$(document).on("click","[data-click=panel-reload]",function(e){e.preventDefault();var a=$(this).closest(".panel");if(!$(a).hasClass("panel-loading")){var t=$(a).find(".panel-body");$(a).addClass("panel-loading"),$(t).prepend('<div class="panel-loader"><span class="spinner-small"></span></div>'),setTimeout(function(){$(a).removeClass("panel-loading"),$(a).find(".panel-loader").remove()},2e3)}}),$(document).on("hover","[data-click=panel-expand]",function(e){$(this).attr("data-init")||($(this).tooltip({title:"Expand / Compress",placement:"bottom",trigger:"hover",container:"body"}),$(this).tooltip("show"),$(this).attr("data-init",!0))}),$(document).on("click","[data-click=panel-expand]",function(e){e.preventDefault();var a=$(this).closest(".panel"),t=$(a).find(".panel-body"),i=40;if(0!==$(t).length){var n=$(a).offset().top;i=$(t).offset().top-n}if($("body").hasClass("panel-expand")&&$(a).hasClass("panel-expand"))$("body, .panel").removeClass("panel-expand"),$(".panel").removeAttr("style"),$(t).removeAttr("style");else if($("body").addClass("panel-expand"),$(this).closest(".panel").addClass("panel-expand"),0!==$(t).length&&40!=i){var o=40;$(a).find(" > *").each(function(){var e=$(this).attr("class");"panel-heading"!=e&&"panel-body"!=e&&(o+=$(this).height()+30)}),40!=o&&$(t).css("top",o+"px")}$(window).trigger("resize")})},handleDraggablePanel=function(){"use strict";var e=$(".panel").parent("[class*=col]");$(e).sortable({handle:".panel-heading",connectWith:".row > [class*=col]",stop:function(e,a){a.item.find(".panel-title").append('<i class="fa fa-refresh fa-spin m-l-5" data-id="title-spinner"></i>'),handleSavePanelPosition(a.item)}})},handelTooltipPopoverActivation=function(){"use strict";0!==$('[data-toggle="tooltip"]').length&&$("[data-toggle=tooltip]").tooltip(),0!==$('[data-toggle="popover"]').length&&$("[data-toggle=popover]").popover()},handleScrollToTopButton=function(){"use strict";$(document).scroll(function(){$(document).scrollTop()>=200?$("[data-click=scroll-top]").addClass("in"):$("[data-click=scroll-top]").removeClass("in")}),$("[data-click=scroll-top]").click(function(e){e.preventDefault(),$("html, body").animate({scrollTop:0},300),$("html, body").animate({scrollTop:40},150),$("html, body").animate({scrollTop:0},100),$("html, body").animate({scrollTop:20},100),$("html, body").animate({scrollTop:0},100),$("html, body").animate({scrollTop:10},50),$("html, body").animate({scrollTop:0},100),$("html, body").animate({scrollTop:5},50),$("html, body").animate({scrollTop:0},100)})},handleAfterPageLoadAddClass=function(){0!==$("[data-pageload-addclass]").length&&$(window).load(function(){$("[data-pageload-addclass]").each(function(){var e=$(this).attr("data-pageload-addclass");$(this).addClass(e)})})},handleSavePanelPosition=function(e){"use strict";if(0!==$(".ui-sortable").length){var a=[];$.when($(".ui-sortable").each(function(){var e=$(this).find("[data-sortable-id]");if(0!==e.length){var t=[];$(e).each(function(){var e=$(this).attr("data-sortable-id");t.push({id:e})}),a.push(t)}else a.push([]);0})).done(function(){var t=window.location.href;t=(t=t.split("?"))[0],localStorage.setItem(t,JSON.stringify(a)),$(e).find('[data-id="title-spinner"]').delay(500).fadeOut(500,function(){$(this).remove()})})}},handleIEFullHeightContent=function(){(window.navigator.userAgent.indexOf("MSIE ")>0||navigator.userAgent.match(/Trident.*rv\:11\./))&&$('.vertical-box-row [data-scrollbar="true"][data-height="100%"]').each(function(){var e=$(this).closest(".vertical-box-row"),a=$(e).height();$(e).find(".vertical-box-cell").height(a)})},handleUnlimitedTabsRender=function(){function e(e,a){var t=$(e).closest(".tab-overflow"),i=parseInt($(t).find(".nav.nav-tabs").css("margin-left")),n=$(t).width(),o=0,l=0;switch($(t).find("li").each(function(){$(this).hasClass("next-button")||$(this).hasClass("prev-button")||(o+=$(this).width())}),a){case"next":(s=o+i-n)<=n?(l=s-i,setTimeout(function(){$(t).removeClass("overflow-right")},150)):l=n-i-80,0!=l&&$(t).find(".nav.nav-tabs").animate({marginLeft:"-"+l+"px"},150,function(){$(t).addClass("overflow-left")});break;case"prev":var s=-i;s<=n?($(t).removeClass("overflow-left"),l=0):l=s-n+80,$(t).find(".nav.nav-tabs").animate({marginLeft:"-"+l+"px"},150,function(){$(t).addClass("overflow-right")})}}function a(){$(".tab-overflow").each(function(){var e=$(this).width(),a=0,t=$(this);$(t).find("li").each(function(){var t=$(this);a+=$(t).width(),$(t).hasClass("active")&&a>e&&a}),function(e,a){parseInt($(e).css("margin-left"));var t=$(e).width(),i=$(e).find("li.active").width(),n=a>-1?a:150,o=0;if($(e).find("li.active").prevAll().each(function(){i+=$(this).width()}),$(e).find("li").each(function(){o+=$(this).width()}),i>=t){var l=i-t;o!=i&&(l+=40),$(e).find(".nav.nav-tabs").animate({marginLeft:"-"+l+"px"},n)}i!=o&&o>=t?$(e).addClass("overflow-right"):$(e).removeClass("overflow-right"),i>=t&&o>=t?$(e).addClass("overflow-left"):$(e).removeClass("overflow-left")}(this,0)})}$('[data-click="next-tab"]').click(function(a){a.preventDefault(),e(this,"next")}),$('[data-click="prev-tab"]').click(function(a){a.preventDefault(),e(this,"prev")}),$(window).resize(function(){$(".tab-overflow .nav.nav-tabs").removeAttr("style"),a()}),a()},handleMobileSidebar=function(){"use strict";/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)&&$("#page-container").hasClass("page-sidebar-minified")&&($('#sidebar [data-scrollbar="true"]').css("overflow","visible"),$('.page-sidebar-minified #sidebar [data-scrollbar="true"]').slimScroll({destroy:!0}),$('.page-sidebar-minified #sidebar [data-scrollbar="true"]').removeAttr("style"),$(".page-sidebar-minified #sidebar [data-scrollbar=true]").trigger("mouseover"));var e=0;$(".page-sidebar-minified .sidebar [data-scrollbar=true] a").bind("touchstart",function(a){var t=(a.originalEvent.touches[0]||a.originalEvent.changedTouches[0]).pageY;e=t-parseInt($(this).closest("[data-scrollbar=true]").css("margin-top"))}),$(".page-sidebar-minified .sidebar [data-scrollbar=true] a").bind("touchmove",function(a){if(a.preventDefault(),/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){var t=(a.originalEvent.touches[0]||a.originalEvent.changedTouches[0]).pageY-e;$(this).closest("[data-scrollbar=true]").css("margin-top",t+"px")}}),$(".page-sidebar-minified .sidebar [data-scrollbar=true] a").bind("touchend",function(a){var t=$(this).closest("[data-scrollbar=true]"),i=$(window).height(),n=parseInt($("#sidebar").css("padding-top")),o=$("#sidebar").height();e=$(t).css("margin-top");var l=n;if($(".sidebar").not(".sidebar-right").find(".nav").each(function(){l+=$(this).height()}),-parseInt(e)+$(".sidebar").height()>=l&&i<=l&&o<=l){var s=i-l-20;$(t).animate({marginTop:s+"px"})}else parseInt(e)>=0||o>=l?$(t).animate({marginTop:"0px"}):(s=e,$(t).animate({marginTop:s+"px"}))})},handleUnlimitedTopMenuRender=function(){"use strict";function e(e,a){var t=$(e).closest(".nav"),i=parseInt($(t).css("margin-left")),n=$(".top-menu").width()-88,o=0,l=0;switch($(t).find("li").each(function(){$(this).hasClass("menu-control")||(o+=$(this).width())}),a){case"next":(s=o+i-n)<=n?(l=s-i+128,setTimeout(function(){$(t).find(".menu-control.menu-control-right").removeClass("show")},150)):l=n-i-128,0!=l&&$(t).animate({marginLeft:"-"+l+"px"},150,function(){$(t).find(".menu-control.menu-control-left").addClass("show")});break;case"prev":var s=-i;s<=n?($(t).find(".menu-control.menu-control-left").removeClass("show"),l=0):l=s-n+88,$(t).animate({marginLeft:"-"+l+"px"},150,function(){$(t).find(".menu-control.menu-control-right").addClass("show")})}}function a(){var e=$(".top-menu .nav"),a=$(".top-menu .nav > li"),t=$(".top-menu .nav > li.active"),i=$(".top-menu"),n=(parseInt($(e).css("margin-left")),$(i).width()-128),o=$(".top-menu .nav > li.active").width(),l=0;if($(t).prevAll().each(function(){o+=$(this).width()}),$(a).each(function(){$(this).hasClass("menu-control")||(l+=$(this).width())}),o>=n){var s=o-n+128;$(e).animate({marginLeft:"-"+s+"px"},0)}o!=l&&l>=n?$(e).find(".menu-control.menu-control-right").addClass("show"):$(e).find(".menu-control.menu-control-right").removeClass("show"),o>=n&&l>=n?$(e).find(".menu-control.menu-control-left").addClass("show"):$(e).find(".menu-control.menu-control-left").removeClass("show")}$('[data-click="next-menu"]').click(function(a){a.preventDefault(),e(this,"next")}),$('[data-click="prev-menu"]').click(function(a){a.preventDefault(),e(this,"prev")}),$(window).resize(function(){$(".top-menu .nav").removeAttr("style"),a()}),a()},handleTopMenuSubMenu=function(){"use strict";$(".top-menu .sub-menu .has-sub > a").click(function(){var e=$(this).closest("li").find(".sub-menu").first(),a=$(this).closest("ul").find(".sub-menu").not(e);$(a).not(e).slideUp(250,function(){$(this).closest("li").removeClass("expand")}),$(e).slideToggle(250,function(){var e=$(this).closest("li");$(e).hasClass("expand")?$(e).removeClass("expand"):$(e).addClass("expand")})})},handleMobileTopMenuSubMenu=function(){"use strict";$(".top-menu .nav > li.has-sub > a").click(function(){if($(window).width()<=767){var e=$(this).closest("li").find(".sub-menu").first(),a=$(this).closest("ul").find(".sub-menu").not(e);$(a).not(e).slideUp(250,function(){$(this).closest("li").removeClass("expand")}),$(e).slideToggle(250,function(){var e=$(this).closest("li");$(e).hasClass("expand")?$(e).removeClass("expand"):$(e).addClass("expand")})}})},handleTopMenuMobileToggle=function(){"use strict";$('[data-click="top-menu-toggled"]').click(function(){$(".top-menu").slideToggle(250)})},handleClearSidebarSelection=function(){$(".sidebar .nav > li, .sidebar .nav .sub-menu").removeClass("expand").removeAttr("style")},handleClearSidebarMobileSelection=function(){$("#page-container").removeClass("page-sidebar-toggled")},App=function(){"use strict";return{init:function(){this.initSidebar(),this.initTopMenu(),this.initPageLoad(),this.initComponent()},initSidebar:function(){handleSidebarMenu(),handleMobileSidebarToggle(),handleSidebarMinify(),handleMobileSidebar()},initSidebarSelection:function(){handleClearSidebarSelection()},initSidebarMobileSelection:function(){handleClearSidebarMobileSelection()},initTopMenu:function(){handleUnlimitedTopMenuRender(),handleTopMenuSubMenu(),handleMobileTopMenuSubMenu(),handleTopMenuMobileToggle()},initPageLoad:function(){handlePageContentView()},initComponent:function(){handleDraggablePanel(),handleIEFullHeightContent(),handleSlimScroll(),handleUnlimitedTabsRender(),handlePanelAction(),handelTooltipPopoverActivation(),handleScrollToTopButton(),handleAfterPageLoadAddClass()},initLocalStorage:function(){},initThemePanel:function(){},scrollTop:function(){$("html, body").animate({scrollTop:$("body").offset().top},0)}}}();