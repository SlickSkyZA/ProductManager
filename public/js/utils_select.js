'use strict';

jQuery(document).ready(function () {

});

/**
* 新增页面所有中项
*
*/
function selected2_tag_addnew_initial(str, list, str2) {
    $(str).empty();
    for(let key in list){
        $(str).append("<option value='"+key+"'>"+list[key]+"</option>");
    }
    $(str).prepend("<option value='' selected>"+str2+"</option>");
}

/**
* 新增页面所有中项,多标签
*
*/
function selected2_tag_addopt_initial(str, list) {
    $(str).empty();
    for(let key in list){
        $(str).append("<option value='"+key+"'>"+list[key]+"</option>");
    }
}

/**
 * 多选框 编辑页面更新
 * @param  {[type]} str  [description]
 * @param  {[type]} list [description]
 * @param  {[type]} item [description]
 * @return {[type]}      [description]
 */
function selected2_tag_addopt_update(str, list, item) {
    $(str).empty();
    var temp = item.split(",");
    for(let key in list){
        var flag = false;
        for (let item_key in temp) {
            if (list[key] == temp[item_key]) {
                flag = true;
                break;
            }
        }
        if (flag) {
            $(str).append("<option value='"+key+"' selected>"+list[key]+"</option>");
        } else {
            $(str).append("<option value='"+key+"'>"+list[key]+"</option>");
        }
    }
}

/**
* 编辑页面指定对应选中项
*
*/
function selected2_tag_update_optional(str, list, item, str2) {
    $(str).empty();
    for(let key in list){
    	if (list[key] == item) {
    		$(str).append("<option value='"+key+"' selected>"+list[key]+"</option>");
        } else {
        	$(str).append("<option value='"+key+"'>"+list[key]+"</option>");
        }
    }
    if (item == '') {
        $(str).prepend("<option value='' selected>"+ str2 +"</option>");
    } else {
        $(str).prepend("<option value=''>"+ str2 +"</option>");
    }
}

/**
* 新增页面新加项目添加至列表
*
*/
function selected2_tag_addnew_optional(str, strVal, list, item, itemVal, str2) {
    if(!inArray(item, list)){
        if (itemVal != '') {
            list.push(item);
            $(str).empty();
            for(let key in list){
                if (list[key] == item) {
                    $(str).append("<option value='"+key+"' selected>"+list[key]+"</option>");
                } else {
                    $(str).append("<option value='"+key+"'>"+list[key]+"</option>");
                }
            }
            $(str).prepend("<option value=''>"+str2+"</option>");
        }
    } else {
        $(strVal).val(itemVal);
    }
}

/**
* 更新tag数据到list
*
*/
function selected2_tag_update_array(list, item, itemVal) {
    if(!inArray(item, list)){
        if (itemVal != '') {
            list.push(item);
        }
    }
}
