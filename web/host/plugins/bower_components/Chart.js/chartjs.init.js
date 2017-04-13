$( document ).ready(function() {
    

    var ctx3 = document.getElementById("chart3").getContext("2d");
    var data3 = [
        {
            value: 2,
            color:"#25a6f7",
            highlight: "#25a6f7",
            label: "开发区一级"
        },
        {
            value: 4,
            color: "#edf1f5",
            highlight: "#edf1f5",
            label: "朝阳二级"
        },
		 {
            value: 1,
            color: "#b4c1d7",
            highlight: "#b4c1d7",
            label: "朝阳一级"
        },
		 {
            value: 1,
            color: "#b8edf0",
            highlight: "#b8edf0",
            label: "朝阳三级"
        },
        {
            value: 1,
            color: "#fcc9ba",
            highlight: "#fcc9ba",
            label: "东城一级"
        },
        {
            value: 1,
            color: "#fcc9ba",
            highlight: "#fcc9ba",
            label: "西城一级"
        }
    ];
    
    var myPieChart = new Chart(ctx3).Pie(data3,{
        segmentShowStroke : true,
        segmentStrokeColor : "#fff",
        segmentStrokeWidth : 0,
        animationSteps : 100,
		tooltipCornerRadius: 0,
        animationEasing : "easeOutBounce",
        animateRotate : true,
        animateScale : false,
        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
        responsive: true
    });
    
    var ctx4 = document.getElementById("chart4").getContext("2d");
    var data4 = [
        {
            value: 300,
            color:"#92d050",
            highlight: "#92d050",
            label: "优"
        },
        {
            value: 50,
            color: "#00b0f0",
            highlight: "#00b0f0",
            label: "良好"
        },
        {
            value: 100,
            color: "#ffc000",
            highlight: "#ffc000",
            label: "一般"
        },
        {
            value: 100,
            color: "#c55a11",
            highlight: "#c55a11",
            label: "差"
        }
    ];
    
    var myDoughnutChart = new Chart(ctx4).Doughnut(data4,{
        segmentShowStroke : true,
        segmentStrokeColor : "#fff",
        segmentStrokeWidth : 0,
        animationSteps : 100,
		tooltipCornerRadius: 2,
        animationEasing : "easeOutBounce",
        animateRotate : true,
        animateScale : false,
        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
        responsive: true
    });
    

    
});