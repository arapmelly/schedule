<?php
$this->pageTitle = 'График твитов в день';
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/highcharts/highcharts.js');
?>
<div id="history"></div>

<script type="text/javascript">
    var chart;
    $(document).ready(function () {
        chart = new Highcharts.Chart({
            chart:{
                renderTo:'history',
                defaultSeriesType:'line',
                marginRight:130,
                marginBottom:25
            },
            title:{
                text:null
            },
            xAxis:{
                type:'datetime',
                tickPixelInterval:80
            },
            yAxis:{
                title:{
                    text:'Tweets'
                },
                plotLines:[
                    {
                        value:0,
                        width:1,
                        color:'#808080'
                    }
                ]
            },
            legend:{
                layout:'vertical',
                align:'right',
                verticalAlign:'top',
                x:-10,
                y:100,
                borderWidth:0
            },
            series:[
                {
                    name:'Tweets history',
                    data:<?php echo CJSON::encode($history)?>
                }
            ]
        });


    });

</script>
