$.extend(!0,$.fn.dataTable.defaults,{sDom:"<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",oLanguage:{sLengthMenu:"_MENU_ records per page"}}),$.extend($.fn.dataTableExt.oStdClasses,{sWrapper:"dataTables_wrapper form-inline",sFilterInput:"form-control input-sm",sLengthSelect:"form-control input-sm"}),$.fn.dataTable.Api?($.fn.dataTable.defaults.renderer="bootstrap",$.fn.dataTable.ext.renderer.pageButton.bootstrap=function(a,b,c,d,e,f){var j,k,g=new $.fn.dataTable.Api(a),h=a.oClasses,i=a.oLanguage.oPaginate,l=function(b,d){var m,n,o,p,q=function(a){a.preventDefault(),"ellipsis"!==a.data.action&&g.page(a.data.action).draw(!1)};for(m=0,n=d.length;n>m;m++)if(p=d[m],$.isArray(p))l(b,p);else{switch(j="",k="",p){case"ellipsis":j="&hellip;",k="disabled";break;case"first":j=i.sFirst,k=p+(e>0?"":" disabled");break;case"previous":j=i.sPrevious,k=p+(e>0?"":" disabled");break;case"next":j=i.sNext,k=p+(f-1>e?"":" disabled");break;case"last":j=i.sLast,k=p+(f-1>e?"":" disabled");break;default:j=p+1,k=e===p?"active":""}j&&(o=$("<li>",{"class":h.sPageButton+" "+k,"aria-controls":a.sTableId,tabindex:a.iTabIndex,id:0===c&&"string"==typeof p?a.sTableId+"_"+p:null}).append($("<a>",{href:"#"}).html(j)).appendTo(b),a.oApi._fnBindAction(o,{action:p},q))}};l($(b).empty().html('<ul class="pagination"/>').children("ul"),d)}):($.fn.dataTable.defaults.sPaginationType="bootstrap",$.fn.dataTableExt.oApi.fnPagingInfo=function(a){return{iStart:a._iDisplayStart,iEnd:a.fnDisplayEnd(),iLength:a._iDisplayLength,iTotal:a.fnRecordsTotal(),iFilteredTotal:a.fnRecordsDisplay(),iPage:-1===a._iDisplayLength?0:Math.ceil(a._iDisplayStart/a._iDisplayLength),iTotalPages:-1===a._iDisplayLength?0:Math.ceil(a.fnRecordsDisplay()/a._iDisplayLength)}},$.extend($.fn.dataTableExt.oPagination,{bootstrap:{fnInit:function(a,b,c){var d=a.oLanguage.oPaginate,e=function(b){b.preventDefault(),a.oApi._fnPageChange(a,b.data.action)&&c(a)};$(b).append('<ul class="pagination"><li class="prev disabled"><a href="#">&larr; '+d.sPrevious+"</a></li>"+'<li class="next disabled"><a href="#">'+d.sNext+" &rarr; </a></li>"+"</ul>");var f=$("a",b);$(f[0]).bind("click.DT",{action:"previous"},e),$(f[1]).bind("click.DT",{action:"next"},e)},fnUpdate:function(a,b){var f,g,h,i,j,k,c=5,d=a.oInstance.fnPagingInfo(),e=a.aanFeatures.p,l=Math.floor(c/2);for(d.iTotalPages<c?(j=1,k=d.iTotalPages):d.iPage<=l?(j=1,k=c):d.iPage>=d.iTotalPages-l?(j=d.iTotalPages-c+1,k=d.iTotalPages):(j=d.iPage-l+1,k=j+c-1),f=0,g=e.length;g>f;f++){for($("li:gt(0)",e[f]).filter(":not(:last)").remove(),h=j;k>=h;h++)i=h==d.iPage+1?'class="active"':"",$("<li "+i+'><a href="#">'+h+"</a></li>").insertBefore($("li:last",e[f])[0]).bind("click",function(c){c.preventDefault(),a._iDisplayStart=(parseInt($("a",this).text(),10)-1)*d.iLength,b(a)});0===d.iPage?$("li:first",e[f]).addClass("disabled"):$("li:first",e[f]).removeClass("disabled"),d.iPage===d.iTotalPages-1||0===d.iTotalPages?$("li:last",e[f]).addClass("disabled"):$("li:last",e[f]).removeClass("disabled")}}}})),$.fn.DataTable.TableTools&&($.extend(!0,$.fn.DataTable.TableTools.classes,{container:"DTTT btn-group",buttons:{normal:"btn btn-default",disabled:"disabled"},collection:{container:"DTTT_dropdown dropdown-menu",buttons:{normal:"",disabled:"disabled"}},print:{info:"DTTT_print_info modal"},select:{row:"active"}}),$.extend(!0,$.fn.DataTable.TableTools.DEFAULTS.oTags,{collection:{container:"ul",button:"li",liner:"a"}}));