<template>
    <div id="facebook-overview" class="page-overview">
        <div class="page-overview-wrapper">
            <div class="row">
                <div class="overview-panel-left col-md-4">
                    <div class="box box-widget widget-user overview-box-widget">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header" :class="getBackgroundBoxHeader">
                            <h3 class="widget-user-username">{{ facebookAnalytics.account_name }}</h3>
                        </div>
                        <div class="widget-user-image">
                            <img class="img-circle" :src="facebookAnalytics.account_picture" alt="User Avatar">
                        </div>
                        <div class="box-footer">
                            <ul class="nav nav-stacked">
                                <li style="text-align: center"><a :href="facebookAnalytics.account_link"><span class="badge bg-yellow">{{ facebookAnalytics.account_link }}</span></a></li>
                                <li><a href="#">Followers <span class="pull-right badge bg-aqua">{{ facebookAnalytics.total_page_followers | currency }}</span></a></li>
                                <li><a href="#">Fans <span class="pull-right badge bg-aqua">{{ facebookAnalytics.total_page_likes | currency }}</span></a></li>
                            </ul>
                        </div>
                    </div>
                    <section id="facebook-most-intereactions-post">
                        <facebook-post v-if="chartLoadSuccess" :facebookAnalytics="facebookAnalytics" :post="facebookBestPost"></facebook-post>
                    </section>
                </div>
                <div class="overview-panel-right col-md-8">
                    <section id="facebook-posts-analytics">
                        <div class="row">
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="likes" :color="'bg-aqua'" :title="'Like'" :icon="'/imgs/reactions/like.gif'" :value="facebookAnalytics.total_posts_likes"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="loves" :color="'bg-red'" :title="'Love'" :icon="'/imgs/reactions/love.gif'" :value="facebookAnalytics.total_posts_loves"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="haha" :color="'bg-blue'" :title="'Haha'" :icon="'/imgs/reactions/haha.gif'" :value="facebookAnalytics.total_posts_hahas"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="wow" :color="'bg-yellow'" :title="'Wow'" :icon="'/imgs/reactions/wow.gif'" :value="facebookAnalytics.total_posts_wows"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="sad" :color="'bg-green'" :title="'Sad'" :icon="'/imgs/reactions/sad.gif'" :value="facebookAnalytics.total_posts_sads"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="angry" :color="'bg-red'" :title="'Angry'" :icon="'/imgs/reactions/angry.gif'" :value="facebookAnalytics.total_posts_angries"></reactions-box>
                            </div>
                        </div>
                    </section>
                    <section id="facebook-growth-fans-chart">
                        <div class="row">
                            <growth-fans-chart v-if="chartLoadSuccess" :source="growthFans"></growth-fans-chart>
                        </div>
                    </section>
                    <section id="facebook-evolution-of-interactions-chart">
                        <div class="row">
                            <evolution-of-interactions-chart v-if="chartLoadSuccess" :source="evolutionOfInteractions"></evolution-of-interactions-chart>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'
import reactionsBox from '../../global/facebook/reactions_box'
import growthFansChart from '../../global/facebook/growth_fans_chart'
import evolutionOfInteractionsChart from '../../global/facebook/evolution_of_interactions_chart'
import facebookPost from '../../global/facebook/facebook_post'

export default {
    name: 'Page_Facebook__Overview',
    components: {
        'reactions-box': reactionsBox,
        'growth-fans-chart': growthFansChart,
        'evolution-of-interactions-chart': evolutionOfInteractionsChart,
        'facebook-post': facebookPost
    },
    data() {
        return {    
        }
    },
    computed: {
        ...mapState('facebook', [
            'facebookAnalytics',
            'facebookGrowthFans',
            'facebookEvolutionOfInteractions',
            'facebookBestPost'
        ]),
        getBackgroundBoxHeader() {
            let bgColors =  ['bg-blue', 'bg-aqua', 'bg-green', 'bg-yellow', 'bg-red']
            return bgColors[Math.floor(Math.random() * bgColors.length)];
        },
        growthFans() {
            var data = {
                labels: this.growthFansLabels,
                datasets: [
                    {
                        label               : 'Growth Of Fans',
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
            return this.facebookGrowthFans.map(function(item) {
                return item.date_sync
            })
        },
        growthFansValues() {
            return this.facebookGrowthFans.map(function(item) {
                return item.facebook_fans
            })
        },
        evolutionOfInteractions() {
            var data = {
                labels: this.evolutionOfInteractionsLabels,
                datasets: [
                    {
                        type                : 'bar',
                        label               : 'Reactions',
                        backgroundColor     : 'rgb(240, 82, 103)',
                        data: this.evolutionOfInteractionsReactionsValues
                    },
                    {
                        type                : 'bar',
                        label               : 'Comments',
                        backgroundColor     : 'rgb(0, 166, 90)',
                        data: this.evolutionOfInteractionsCommentsValues
                    },
                    {
                        type                : 'bar',
                        label               : 'Shares',
                        backgroundColor     : 'rgb(243, 156, 18)',
                        data: this.evolutionOfInteractionsSharesValues
                    }
                ]
            }
            return data;
        },
        evolutionOfInteractionsLabels() {
             return this.facebookEvolutionOfInteractions.map(function(item) {
                return item.date
            })
        },
        evolutionOfInteractionsReactionsValues() {
             return this.facebookEvolutionOfInteractions.map(function(item) {
                return item.reactions
            })
        },
        evolutionOfInteractionsCommentsValues() {
             return this.facebookEvolutionOfInteractions.map(function(item) {
                return item.comments
            })
        },
        evolutionOfInteractionsSharesValues() {
             return this.facebookEvolutionOfInteractions.map(function(item) {
                return item.shares
            })
        },
        chartLoadSuccess() {
            return this.growthFans.labels.length > 0
        }
        
    },
    methods: {
        ...mapActions('facebook', [
            
        ]),
    },
    beforeCreate() {
        this.$store.dispatch('facebook/getFacebookPageAnalytics', this.$route.params.username);
    },
    created() {
    }
}
</script>
