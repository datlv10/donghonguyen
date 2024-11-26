// js phả đồ dọc
$(function () {
    $(".tree li:has(ul)").addClass("parent_li").find(" > span").attr("title", "Thu gọn các đời sau");
    $(".tree li.parent_li > span").on("click", function (e) {
        var children = $(this).parent("li.parent_li").find(" > ul > li");
        if (children.is(":visible")) {
            children.hide("fast");
            $(this).attr("title", "Mở rộng các đời sau").find(" > i").addClass("fa-plus-square").removeClass("fa-minus-square");
        } else {
            children.show("fast");
            $(this).attr("title", "Thu gọn các đời sau").find(" > i").addClass("fa-minus-square").removeClass("fa-plus-square");
        }
        e.stopPropagation();
    });
});
// js phả đồ dọc

$("[detail-genealogy]").on('click', function(e){
    var _id = $(this).attr('data-id');
    console.log(1);
    if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false

    window.location.href = '/giapha/' + _id;
});