<div class="mt-5 mb-20">
    <div class="d-flex align-items-center">
        <div class="me-2">
            <h3><span class="material-symbols-rounded me-2">schedule</span></h3>
        </div>
        <div>
            <h3>{{ trans('messages.campaign.last_performance') }}</h3>
        </div>
        <div class="ms-auto">
            <div class="d-flex align-items-center">
                <span class="me-3">{{ trans('messages.performance.period') }}</span>
                <select id="PerformancePeriod" class="select select-control" name="last_performance_time">
                    <option value="24h">{{ trans('messages.campaign.24_hour') }}</option>
                    <option value="3_days">{{ trans('messages.campaign.last_3_days') }}</option>
                    <option value="7_days">{{ trans('messages.campaign.last_7_days') }}</option>
                    <option value="last_month">{{ trans('messages.campaign.last_month') }}</option>
                </select>
            </div>
        </div>
        
    </div>
    
</div>
<p class="mb-4">{{ trans('messages.campaign.performance_chart.info') }}</p>
<div class="border shadow-sm rounded">
    <div class="p-3">
        <div id="Campaigns24hChart"
            class=""
            style="width:100%; height:350px;"
        ></div>
    </div>
</div>

<script>
    var Campaigns24hChart = {
        url: '{{ action('CampaignController@chart24h', $campaign->uid) }}',

        getChart: function() {
            return $('#Campaigns24hChart');
        },

        getTimePeriod: function() {
            return $('[name="last_performance_time"]').val();
        },

        showChart: function() {
            $.ajax({
                method: "GET",
                url: this.url,
                data: {
                    period: this.getTimePeriod()
                }
            })
            .done(function( response ) {
                Campaigns24hChart.renderChart( response );
            });
        },

        renderChart: function(data) {
                // based on prepared DOM, initialize echarts instance
                var my2Chart = echarts.init(Campaigns24hChart.getChart()[0], ECHARTS_THEME);

                var option = {
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['{{ trans('messages.opened') }}', '{{ trans('messages.clicked') }}']
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    toolbox: {
                        show: false
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: data.columns
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name: '{{ trans('messages.opened') }}',
                            type: 'line',
                            itemStyle: {
                                color: '#5cb2b2'
                            },
                            data: data.opened
                        },
                        {
                            name: '{{ trans('messages.clicked') }}',
                            type: 'line',
                            itemStyle: {
                                color: '#b26e59'
                            },
                            data: data.clicked
                        }
                    ]
                };

                // use configuration item and data specified to show chart
                my2Chart.setOption(option);
        }
    }

    $(function() {
        $('#PerformancePeriod').select2({
            dropdownAutoWidth : true,
            width: 'auto'
        })

        Campaigns24hChart.showChart();

        $('[name="last_performance_time"]').on('change', function() {
            Campaigns24hChart.showChart();
        });
    })
</script>