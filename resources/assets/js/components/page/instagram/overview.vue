<template>
    <div id="facebook-overview" class="page-overview">
        <div class="page-overview-wrapper">
            <div class="row">
                <box-instagram-profile class="box-overview-instagram" :profile="instagramProfile"></box-instagram-profile>
            </div>
            <div class="instagram-analytic-content row">
                <div class="row">
                    <section id="box-interactions">
                        <div class="row">
                            <div class="col-md-6">
                                <box-interaction v-if="instagramTotalInteractionLoadSuccess" class="box-interaction-ele pull-right" :value="instagramTotalInteractionLikes" :bg="'bg-green'" :title="'Likes'" :icon="'fa-thumbs-o-up'"></box-interaction>
                            </div>
                            <div class="col-md-6">
                                <box-interaction v-if="instagramTotalInteractionLoadSuccess" class="box-interaction-ele pull-left" :value="instagramTotalInteractionComments" :bg="'bg-red'" :title="'Comments'" :icon="'fa-comments'"></box-interaction>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12"> 
                        <section id="instagram-growth-fans-chart">
                            <growth-fans-chart v-if="growthFansLoadSuccess" :boxStyle="'box-success'" :title="'Growth of Followers'" :source="growthFans"></growth-fans-chart>
                        </section>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12"> 
                        <section id="instagram-media-by-type">
                            <media-group-type v-if="totalMediaGroupByTypeLoadSuccess" :boxStyle="'box-info'" :title="'Distribution Of Media Type'" :source="totalMediaGroupByType"></media-group-type>
                        </section>
                    </div>
                </div>
                <div class="row">
                    <section id="distribution-tags">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12"> 
                                <section id="instagram-top-hashtags">
                                    <distribution-tags :idWrapper="'distributionOfTagsChart'" v-if="distributionOfTagsLoadSuccess" :boxStyle="'box-warning'" :title="'Top 5 HashTags'" :source="distributionOfTags"></distribution-tags>
                                </section>
                            </div>  
                            <div class="col-md-6 col-sm-6 col-xs-12" style="height: 300px; overflow: scroll;">
                                <section id="instagram-popular-hastags">
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Popular Hashtags</h3>
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                                <button v-if="false" type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="popularHashtags" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Tags</th>
                                                        <th>Count</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="tag in instagramDistributionTagsOrder" :key="tag.tag">
                                                        <td><a href="#">#{{ tag.tag }}</a></td>
                                                        <td> {{ tag.count }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <!-- /.box-body -->
                                    </div>
                                </section>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="row">
                    <section id="instagram-post-per-day">
                        <total-posts-per-day-chart :idWrapper="'totalPostPerDay'" :options="totalMediaPerDayOption" :title="'Number of Profile Posts'" :boxStyle="'box-success'" v-if="totalMediaPerDayLoadSuccess" :source="totalMediaPerDay"></total-posts-per-day-chart>
                    </section>
                </div>
                <div class="row">
                    <section id="instagram-interaction--per-day">
                        <evolution-of-interactions-chart :idWrapper="'evolutionOfInteractionBarChartStacked'" :options="evolutionOfInteractionOption" :title="'Evolution Of Interactions'" :boxStyle="'box-info'" v-if="evolutionOfInteractionsLoadSuccess" :source="evolutionOfInteractions"></evolution-of-interactions-chart>
                    </section>
                </div>
                <div class="row masonry-grid">
                    <masonry
                        :cols="{default: 3, 800: 2, 400: 1}"
                        :gutter="{default: '15px', 800: '5px'}"
                        >
                        <box-instagram-media v-for="media in instagramLastMedias" :key="media.instagram_created_at" :instagramMedia="media" :instagramProfile="instagramProfile"></box-instagram-media>
                    </masonry>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions, mapGetters } from 'vuex'
import BoxInstagramProfile from '../../global/instagram/box_profile'
import BoxInstagramMedia from '../../global/instagram/instagram_post'
import BoxInteraction from '../../global/instagram/box_interaction'

import growthFansChart from '../../global/area-chart'
import totalPostsPerDay from '../../global/bar-chart'
import evolutionOfInteractionChart from '../../global/bar-chart'
import totalMediaGroupByType from '../../global/pie-chart'
import distributionOfTagsChart from '../../global/pie-chart'

export default {
    name: 'Page_Instagram__Overview',
    components: {
        'box-instagram-profile': BoxInstagramProfile,
        'box-instagram-media': BoxInstagramMedia,
        'growth-fans-chart': growthFansChart,
        'total-posts-per-day-chart': totalPostsPerDay,
        'media-group-type': totalMediaGroupByType,
        'box-interaction': BoxInteraction,
        'evolution-of-interactions-chart': evolutionOfInteractionChart,
        'distribution-tags': distributionOfTagsChart
    },
    data() {
        return {    
        }
    },
    computed: {
        ...mapGetters('instagram', [
            'instagramProfile',
            'instagramGrowthFans',
            'instagramTotalMediaPerDay',
            'instagramTotalMediaGroupByType',
            'instagramLastMedias',
            'instagramTotalInteraction',
            'instagramEvolutionOfInteractions',
            'instagramDistributionTags'
        ]),
        growthFans() {
            let data = {
                labels: this.growthFansLabels,
                datasets: [
                    {
                        label               : 'Growth Of Followers',
                        backgroundColor     : 'rgb(3, 192, 239, 0.75)',
                        pointColor          : 'rgb(243, 156, 18)',
                        pointStrokeColor    : 'rgb(243, 156, 18)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: this.growthFansValues
                    }
                ]
            }
            return data;
        },
        growthFansLabels() {
            return this.instagramGrowthFans.map(function(item) {
                return item.date_sync
            })
        },
        growthFansValues() {
            return this.instagramGrowthFans.map(function(item) {
                return item.instagram_followers
            })
        },
        totalMediaPerDay() {
            let data = {
                labels: this.totalMediaPerDayLabels,
                datasets: [
                    {
                        type                : 'bar',
                        label               : 'Number of Profile Posts',
                        backgroundColor     : 'rgb(240, 82, 103)',
                        data: this.totalMediaPerDayValues
                    }
                ]
            }
            return data;
        },
        totalMediaPerDayOption() {
            let barChartOptions = {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1,
                        },
                    }],
                },
            }
            return barChartOptions
        },
        totalMediaPerDayLabels() {
             return this.instagramTotalMediaPerDay.map(function(item) {
                return item.date
            })
        },
        totalMediaPerDayValues() {
             return this.instagramTotalMediaPerDay.map(function(item) {
                return item.value
            })
        },
        totalMediaGroupByType() {
            let data = {
                labels: this.totalMediaGroupByTypeLabels,
                datasets: [
                    {
                        type: 'pie',
                        backgroundColor: [
                            "rgb(0, 166, 90)",
                            "rgb(243, 156, 18)",
                            "rgb(221, 75, 57)"
                        ],
                        borderColor: [
                            "rgb(255, 255, 255)",
                            "rgb(255, 255, 255)",
                            "rgb(255, 255, 255)",
                        ],
                        data: this.totalMediaGroupByTypeValues
                    }
                ]
            }
            return data;
        },
        totalMediaGroupByTypeLabels() {
            return this.instagramTotalMediaGroupByType.map(function(item) {
                return item.media_type
            })
        },
        totalMediaGroupByTypeValues() {
            return this.instagramTotalMediaGroupByType.map(function(item) {
                return item.value
            })
        },
        growthFansLoadSuccess() {
            return this.growthFansLabels.length > 0
        },
        totalMediaGroupByTypeLoadSuccess() {
            return this.totalMediaGroupByTypeLabels.length > 0
        },
        totalMediaPerDayLoadSuccess() {
            return this.totalMediaPerDayLabels.length > 0
        },
        instagramTotalInteractionLikes() {
            return this.$options.filters.currency(parseInt(this.instagramTotalInteraction.likes))
        },
        instagramTotalInteractionComments() {
            return this.$options.filters.currency(parseInt(this.instagramTotalInteraction.comments))
        },
        instagramTotalInteractionLoadSuccess() {
            return this.instagramTotalInteraction.likes != String.empty;
        },
        evolutionOfInteractionOption() {
            let barChartOptions = {
                scales: {
                    xAxes: [{
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return numeral(value).format('0,0')
                            }
                        },
                    }],
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, chart){
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return numeral(tooltipItem.yLabel).format('0,0')
                        }
                    }
                },
            }
            return barChartOptions
        },
        evolutionOfInteractions() {
            let data = {
                labels: this.evolutionOfInteractionsLabels,
                datasets: [
                    {
                        type                : 'bar',
                        label               : 'Likes',
                        backgroundColor     : 'rgb(243, 156, 17)',
                        data: this.evolutionOfInteractionsReactionsValues
                    },
                    {
                        type                : 'bar',
                        label               : 'Comments',
                        backgroundColor     : 'rgb(0, 166, 90)',
                        data: this.evolutionOfInteractionsCommentsValues
                    },
                ]
            }
            return data;
        },
        evolutionOfInteractionsLabels() {
             return this.instagramEvolutionOfInteractions.map(function(item) {
                return item.date
            })
        },
        evolutionOfInteractionsReactionsValues() {
             return this.instagramEvolutionOfInteractions.map(function(item) {
                return item.likes
            })
        },
        evolutionOfInteractionsCommentsValues() {
             return this.instagramEvolutionOfInteractions.map(function(item) {
                return item.comments
            })
        },
        evolutionOfInteractionsLoadSuccess() {
            return this.evolutionOfInteractionsLabels.length > 0
        },
        instagramDistributionTagsOrder() {
            let orders = this.instagramDistributionTags.sort(function(obj1, obj2) {
                return obj2.count - obj1.count
            }); 
            return orders;
        },
        distributionOfTags() {
            let data = {
                labels: this.distributionOfTagsLabels,
                datasets: [
                    {
                        type: 'pie',
                        backgroundColor: [
                            "rgb(0, 166, 90)",
                            "rgb(243, 156, 18)",
                            "rgb(221, 75, 57)",
                            "#0D3B66",
                            "EE964B"
                        ],
                        borderColor: [
                            "rgb(255, 255, 255)",
                            "rgb(255, 255, 255)",
                            "rgb(255, 255, 255)",
                            "rgb(255, 255, 255)",
                            "rgb(255, 255, 255)"
                        ],
                        data: this.distributionOfTagsValues
                    }
                ]
            }
            return data;
        },
        distributionOfTagsLabels() {
            let sourceLabel = this.instagramDistributionTagsOrder
            return sourceLabel.slice(0, 5).map(function(item) {
                return item.tag
            })
        },
        distributionOfTagsValues() {
            let sourceValue = this.instagramDistributionTagsOrder
            return sourceValue.slice(0, 5).map(function(item) {
                return item.count
            })
        },
        distributionOfTagsLoadSuccess() {
            return this.growthFansLabels.length > 0
        },
    },
    methods: {
        ...mapActions('instagram', [
            'resetGrowthFans',
            'resetTotalMediaPerDay',
            'resetTotalMediaGroupByType'
        ]),
    },
    beforeCreate() {
        this.$store.dispatch('instagram/getInstagramProfileAnalytics', this.$route.params.username)
    },
    created() {
        
    },
    beforeDestroy() {
        this.resetGrowthFans()
        this.resetTotalMediaPerDay()
    }
}
</script>
