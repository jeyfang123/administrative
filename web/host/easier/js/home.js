
var scripts = [ null, null ]
$('.page-content-area').ace_ajax('loadScripts', scripts, function() {
    // inline scripts related to this page
    jQuery(function($) {

        var token = $.cookie("token");

        $.toast({
            heading: '欢迎登录',
            text: '您已成功登录!',
            position: 'top-right',
            loaderBg:'#03a9f3',
            icon: 'info',
            hideAfter: 3500,
            stack: 6
        });

        //查询所有统计信息
        Model.getPropertyStatistics({
            'token':token
        },function (data) {
            if('200'==data.code){
                $("#company").html(data.data.company);
                $("#property").html(data.data.property);
                $("#publicity").html(data.data.publicity);
                $("#complaints").html(data.data.complaints);
            }else{
                tip(data.msg);
            }
        });

        //加载物业项目等级分布
        Model.getPropertyGrade({
            'token':token
        },function(data) {
            if(data.code=='200'){
                var length = data.data.length;
                var ctx3 = document.getElementById("chart3").getContext("2d");
                var piedata = [];
                for(var i=0;i<length;i++){
                    if(data.data[i].areagrade=="开发区一级"){
                        piedata.push({
                            value: data.data[i].num,
                            color:"#25a6f7",
                            highlight: "#25a6f7",
                            label: data.data[i].areagrade
                        });
                    }
                    if(data.data[i].areagrade=="朝阳二级"){
                        piedata.push({
                            value: data.data[i].num,
                            color:"#edf1f5",
                            highlight: "#edf1f5",
                            label: data.data[i].areagrade
                        });
                    }
                    if(data.data[i].areagrade=="朝阳一级"){
                        piedata.push({
                            value: data.data[i].num,
                            color:"#b4c1d7",
                            highlight: "#b4c1d7",
                            label: data.data[i].areagrade
                        });
                    }
                    if(data.data[i].areagrade=="朝阳三级"){
                        piedata.push({
                            value: data.data[i].num,
                            color:"#b8edf0",
                            highlight: "#b8edf0",
                            label: data.data[i].areagrade
                        });
                    }
                    if(data.data[i].areagrade=="东城一级"){
                        piedata.push({
                            value: data.data[i].num,
                            color:"#fcc9ba",
                            highlight: "#fcc9ba",
                            label: data.data[i].areagrade
                        });
                    }
                    if(data.data[i].areagrade=="西城一级"){
                        piedata.push({
                            value: data.data[i].num,
                            color:"#6771c4",
                            highlight: "#6771c4",
                            label: data.data[i].areagrade
                        });
                    }
                }
                var myPieChart = new Chart(ctx3).Pie(piedata,{
                    segmentShowStroke : true,
                    segmentStrokeColor : "#fff",
                    segmentStrokeWidth : 0,
                    animationSteps : 100,
                    tooltipCornerRadius: 0,
                    animationEasing : "easeOutBounce",
                    animateRotate : true,
                    animateScale : false,
                    legendTemplate : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
                    responsive: true
                });
            }else{
                tip(data.msg);
            }
        });

        //查询物业投诉满意度分布 1非常满意、2较满意、3一般、4较不满意、5不满意
        Model.getPropertyComplaints({
            'token':token
        },function (data) {
            if('200'==data.code){
                var length = data.data.length;
                var ctx4 = document.getElementById("chart4").getContext("2d");
                var Doughnutdata = [];
                for(var i=0;i<length;i++){
                    if(1==data.data[i].handle_evaluation){
                        Doughnutdata.push({
                            value: data.data[i].num,
                            color:"#92d050",
                            highlight: "#92d050",
                            label: "非常满意"
                        });
                    }
                    if(2==data.data[i].handle_evaluation){
                        Doughnutdata.push({
                            value: data.data[i].num,
                            color:"#00b0f0",
                            highlight: "#00b0f0",
                            label: "较满意"
                        });
                    }
                    if(3==data.data[i].handle_evaluation){
                        Doughnutdata.push({
                            value: data.data[i].num,
                            color:"#ffc000",
                            highlight: "#ffc000",
                            label: "一般"
                        });
                    }
                    if(4==data.data[i].handle_evaluation){
                        Doughnutdata.push({
                            value: data.data[i].num,
                            color:"#c55a11",
                            highlight: "#c55a11",
                            label: "较不满意"
                        });
                    }
                    if(4==data.data[i].handle_evaluation){
                        Doughnutdata.push({
                            value: data.data[i].num,
                            color:"#76060e",
                            highlight: "#76060e",
                            label: "不满意"
                        });
                    }
                }

                var myDoughnutChart = new Chart(ctx4).Doughnut(Doughnutdata,{
                    segmentShowStroke : true,
                    segmentStrokeColor : "#fff",
                    segmentStrokeWidth : 0,
                    animationSteps : 100,
                    tooltipCornerRadius: 2,
                    animationEasing : "easeOutBounce",
                    animateRotate : true,
                    animateScale : false,
                    legendTemplate : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
                    responsive: true
                });
            }else{
                tip(data.msg);
            }
        });



        Morris.Area({
            element: 'morris-area-chart',
            data: [{
                period: '2010',
                iphone: 50,
                ipad: 80,
                itouch: 20
            }, {
                period: '2011',
                iphone: 130,
                ipad: 100,
                itouch: 80
            }, {
                period: '2012',
                iphone: 80,
                ipad: 60,
                itouch: 70
            }, {
                period: '2013',
                iphone: 70,
                ipad: 200,
                itouch: 140
            }, {
                period: '2014',
                iphone: 180,
                ipad: 150,
                itouch: 140
            }, {
                period: '2015',
                iphone: 105,
                ipad: 100,
                itouch: 80
            },
                {
                    period: '2016',
                    iphone: 250,
                    ipad: 150,
                    itouch: 200
                }],
            xkey: 'period',
            ykeys: ['iphone', 'ipad', 'itouch'],
            labels: ['小区', '写字楼', '总体'],
            pointSize: 3,
            fillOpacity: 0,
            pointStrokeColors:['#00bfc7', '#fdc006', '#9675ce'],
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            lineWidth: 1,
            hideHover: 'auto',
            lineColors: ['#00bfc7', '#fdc006', '#9675ce'],
            resize: true

        });

        var pageTotal = 0;

        //init(1,true);

        function init(page,ispagination) {
            Model.getPropertyList({
                'page':page,
                'size':5,
                'token':token
            },function(data) {
                if(data.code=='200'){
                    appendHtml(data,page,ispagination);
                }else{
                    tip(data.msg);
                }
            });
        }

        function appendHtml(data,page,ispagination) {
            var list = data.data.data;
            var length = list.length;
            pageTotal = data.data.count;
            var html = '';
            for(var i=0;i<length;i++){
                html += '<tr><td class="text-left">'+list[i].proname+'</td>';
                html += '<td class="text-left">'+list[i].title.substr(0,15)+'...</td>';
                html += '<td class="text-center">'+list[i].content.substr(0,15)+'...</td>';
                if("1"==list[i].status){
                    html += '<td class="text-center">已回复</td>';
                }else{
                    html += '<td class="text-center"></td>';
                }
                html += '<td class="text-center">'+list[i].createtime+'</td>';
                html += '<td class="text-center">'+list[i].replaycontent+'</td>';
                html += '<td class="text-center">'+list[i].replaytime+'</td></tr>';
            }
            $("#reportbody").html(html);
            if(ispagination){
                $("#reportpage").html('<ul class="pagination"style="float:right"></ul>');
            }
            if(0!=pageTotal){
                $('#reportpage .pagination').twbsPagination({
                    startPage:1,
                    currentPage:page,
                    totalPages: pageTotal,
                    visiblePages: 5,
                    first: '首页',
                    prev: '前一页',
                    next: '下一页',
                    last: '尾页',
                    lother: '共'+ pageTotal +'页',
                    onPageClick: function (event, page) {
                        init(page,false);
                    }
                });
            }
        }

    })
});